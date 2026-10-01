<?php

namespace App\Http\Controllers;

use App\Models\Inverter;
use App\Models\Panel;
use App\Models\SolarProject;
use App\Services\FinancialCalculationService;
use App\Services\SystemDesignService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Radni prostor projektanta (i admina): pregled pristiglih narudžbi,
 * preuzimanje u obradu, po potrebi prilagođavanje predloženog sistema,
 * odobravanje/odbijanje i zakazivanje ugradnje.
 */
class ProjectReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $queue = SolarProject::with(['user'])
            ->where('status', SolarProject::STATUS_SUBMITTED)
            ->latest('submitted_at')
            ->get();

        $myProjectsQuery = SolarProject::with(['user'])->whereIn('status', [
            SolarProject::STATUS_UNDER_REVIEW,
            SolarProject::STATUS_APPROVED,
            SolarProject::STATUS_SCHEDULED,
        ]);

        if (! $user->isAdmin()) {
            $myProjectsQuery->where('designer_id', $user->id);
        }

        $myProjects = $myProjectsQuery->latest('reviewed_at')->get();

        $completed = SolarProject::with(['user'])
            ->whereIn('status', [SolarProject::STATUS_COMPLETED, SolarProject::STATUS_REJECTED])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('designer_id', $user->id))
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('review.index', compact('queue', 'myProjects', 'completed'));
    }

    public function show(SolarProject $project)
    {
        $this->authorizeReviewer($project, allowUnclaimedSubmission: true);
        $project->load(['panel', 'inverter', 'user', 'statusChanges.user']);

        return view('review.show', [
            'project' => $project,
            'panels' => Panel::active()->orderBy('power_w')->get(),
            'inverters' => Inverter::active()->orderBy('rated_power_kw')->get(),
        ]);
    }

    public function claim(SolarProject $project)
    {
        if ($project->status !== SolarProject::STATUS_SUBMITTED) {
            return back()->with('error', 'Ovaj zahtjev je već preuzet.');
        }

        $project->transitionTo(SolarProject::STATUS_UNDER_REVIEW, [
            'designer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('review.show', $project)->with('success', 'Preuzeli ste zahtjev u obradu.');
    }

    /**
     * Projektant može ručno prilagoditi predloženu opremu (npr. drugi panel
     * ili invertor, drugačiji broj komada) prije odobravanja. Proizvodnja i
     * finansijski rezultati se ponovo izračunavaju.
     */
    public function updateDesign(
        Request $request,
        SolarProject $project,
        SystemDesignService $designService,
        FinancialCalculationService $financialService,
    ) {
        $this->authorizeReviewer($project);

        if ($project->status !== SolarProject::STATUS_UNDER_REVIEW) {
            return back()->with('error', 'Sistem se može prilagoditi samo dok je projekat u obradi (prije odobravanja).');
        }

        $data = $request->validate([
            'panel_id' => ['required', 'exists:panels,id'],
            'panel_quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'inverter_id' => ['required', 'exists:inverters,id'],
            'inverter_quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'electricity_price_bam_kwh' => ['required', 'numeric', 'min:0.01', 'max:2'],
            'self_consumption_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'installation_cost_bam' => ['required', 'numeric', 'min:0'],
            'designer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $panel = Panel::findOrFail($data['panel_id']);
        $inverter = Inverter::findOrFail($data['inverter_id']);

        $systemPowerKwp = round(($data['panel_quantity'] * $panel->power_w) / 1000, 3);

        $production = $designService->estimateProduction(
            latitude: (float) $project->latitude,
            longitude: (float) $project->longitude,
            systemPowerKwp: $systemPowerKwp,
            tiltDeg: (float) $project->tilt_deg,
            azimuthDeg: (float) $project->azimuth_deg,
            mountingPlace: $project->mounting_place,
            shading: $project->shading,
        );

        $equipmentCost = round($data['panel_quantity'] * $panel->price_bam + $data['inverter_quantity'] * $inverter->price_bam, 2);
        $totalInvestment = round($equipmentCost + $data['installation_cost_bam'], 2);

        $financials = $financialService->calculate(
            totalInvestmentBam: $totalInvestment,
            annualProductionKwh: $production['annual_kwh'],
            electricityPriceBamKwh: $data['electricity_price_bam_kwh'],
            selfConsumptionPercent: $data['self_consumption_percent'],
            inverterWarrantyYears: $inverter->warranty_years,
            inverterReplacementCostBam: $inverter->price_bam * $data['inverter_quantity'],
        );

        $project->update([
            'panel_id' => $panel->id,
            'panel_quantity' => $data['panel_quantity'],
            'inverter_id' => $inverter->id,
            'inverter_quantity' => $data['inverter_quantity'],
            'system_power_kwp' => $systemPowerKwp,
            'annual_production_kwh' => $production['annual_kwh'],
            'monthly_production' => $production['monthly'],
            'equipment_cost_bam' => $equipmentCost,
            'installation_cost_bam' => $data['installation_cost_bam'],
            'total_investment_bam' => $totalInvestment,
            'electricity_price_bam_kwh' => $data['electricity_price_bam_kwh'],
            'self_consumption_percent' => $financials['self_consumption_percent'],
            'annual_savings_year1_bam' => $financials['annual_savings_year1_bam'],
            'simple_payback_years' => $financials['simple_payback_years'],
            'npv_25y_bam' => $financials['npv_25y_bam'],
            'cashflow_25y' => $financials['cashflow'],
            'designer_notes' => $data['designer_notes'] ?? $project->designer_notes,
        ]);

        return redirect()->route('review.show', $project)->with('success', 'Sistem je prilagođen i ponovo izračunat.');
    }

    public function approve(Request $request, SolarProject $project)
    {
        $this->authorizeReviewer($project);

        try {
            $project->transitionTo(SolarProject::STATUS_APPROVED, [
                'designer_notes' => $request->input('designer_notes', $project->designer_notes),
            ], note: $request->input('designer_notes'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('review.show', $project)->with('success', 'Projekat je odobren.');
    }

    public function reject(Request $request, SolarProject $project)
    {
        $this->authorizeReviewer($project);

        $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        try {
            $project->transitionTo(SolarProject::STATUS_REJECTED, [
                'designer_notes' => $request->input('reason'),
            ], note: $request->input('reason'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('review.index')->with('success', 'Zahtjev je odbijen.');
    }

    public function schedule(Request $request, SolarProject $project)
    {
        $this->authorizeReviewer($project);

        $request->validate(['installation_scheduled_at' => ['required', 'date', 'after_or_equal:today']]);

        try {
            $project->transitionTo(SolarProject::STATUS_SCHEDULED, [
                'installation_scheduled_at' => $request->input('installation_scheduled_at'),
            ]);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('review.show', $project)->with('success', 'Ugradnja je zakazana i kupac je obaviješten emailom.');
    }

    public function complete(SolarProject $project)
    {
        $this->authorizeReviewer($project);

        try {
            $project->transitionTo(SolarProject::STATUS_COMPLETED, [
                'completed_at' => now(),
            ]);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('review.index')->with('success', 'Projekat je označen kao završen.');
    }

    /**
     * Admin smije sve. Projektant radi samo na projektima koje je sam preuzeo;
     * nepreuzetu narudžbu iz reda čekanja smije samo pogledati (da bi odlučio
     * hoće li je preuzeti).
     */
    private function authorizeReviewer(SolarProject $project, bool $allowUnclaimedSubmission = false): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if (! $user->isDesigner()) {
            abort(403);
        }

        if ($project->designer_id === $user->id) {
            return;
        }

        if ($allowUnclaimedSubmission
            && $project->designer_id === null
            && $project->status === SolarProject::STATUS_SUBMITTED) {
            return;
        }

        abort(403);
    }
}
