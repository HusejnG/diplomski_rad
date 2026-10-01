<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateSolarProjectRequest;
use App\Models\SolarProject;
use App\Services\ProjectCalculationService;
use DomainException;
use Illuminate\Support\Facades\Auth;

/**
 * Upravlja solarnim projektima prijavljenog korisnika (kupca): snimanje
 * proračuna, pregled statusa i slanje narudžbe projektantu.
 */
class SolarProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->solarProjects()->latest()->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Snima proračun sa kalkulatora kao novi projekat (status: calculated).
     * Proračun se uvijek ponovo izvršava na serveru (ne vjeruje se klijentu).
     */
    public function store(CalculateSolarProjectRequest $request, ProjectCalculationService $calculationService)
    {
        $validated = $request->validated();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $result = $calculationService->run($validated);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $attributes = $calculationService->toProjectAttributes($result);

        $project = Auth::user()->solarProjects()->create(array_merge($validated, $attributes, [
            'name' => $request->input('name') ?: 'Solarni sistem - '.($request->input('city') ?: 'lokacija'),
            'contact_name' => $request->input('contact_name'),
            'contact_email' => $request->input('contact_email'),
            'contact_phone' => $request->input('contact_phone'),
        ]));

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projekat je izračunat i sačuvan. Kada budete spremni, jednim klikom pošaljite narudžbu.');
    }

    public function show(SolarProject $project)
    {
        $this->authorizeView($project);
        $project->load(['panel', 'inverter', 'designer', 'statusChanges.user']);

        return view('projects.show', compact('project'));
    }

    public function edit(SolarProject $project)
    {
        $this->authorizeOwner($project);

        if (! $project->isEditableByCustomer()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Projekat je već poslan na obradu i više se ne može mijenjati.');
        }

        return view('calculator.index', [
            'surfaceTypes' => SolarProject::SURFACE_TYPES,
            'project' => $project,
        ]);
    }

    public function update(CalculateSolarProjectRequest $request, SolarProject $project, ProjectCalculationService $calculationService)
    {
        $this->authorizeOwner($project);

        if (! $project->isEditableByCustomer()) {
            abort(403);
        }

        $validated = $request->validated();

        try {
            $result = $calculationService->run($validated);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $attributes = $calculationService->toProjectAttributes($result);

        $project->update(array_merge($validated, $attributes, [
            'name' => $request->input('name') ?: $project->name,
            'contact_name' => $request->input('contact_name', $project->contact_name),
            'contact_email' => $request->input('contact_email', $project->contact_email),
            'contact_phone' => $request->input('contact_phone', $project->contact_phone),
        ]));

        return redirect()->route('projects.show', $project)->with('success', 'Proračun je ažuriran.');
    }

    /**
     * Slanje narudžbe jednim klikom - projekat prelazi iz "izračunato" u
     * "poslana narudžba" i postaje vidljiv projektantima u obradi zahtjeva.
     */
    public function submit(SolarProject $project)
    {
        $this->authorizeOwner($project);

        try {
            $project->transitionTo(SolarProject::STATUS_SUBMITTED, ['submitted_at' => now()]);
        } catch (DomainException $e) {
            return back()->with('error', 'Narudžba se može poslati samo za izračunat, još neposlan projekat.');
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Narudžba je poslana! Projektant će uskoro pregledati i potvrditi vaš sistem.');
    }

    public function destroy(SolarProject $project)
    {
        $this->authorizeOwner($project);

        if (! $project->isEditableByCustomer()) {
            return back()->with('error', 'Poslana narudžba se ne može obrisati - kontaktirajte projektanta.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projekat je obrisan.');
    }

    /**
     * Pregled: vlasnik, admin, projektant kojem je projekat dodijeljen i
     * projektant koji gleda nepreuzetu narudžbu.
     */
    private function authorizeView(SolarProject $project): void
    {
        $user = Auth::user();

        if ($project->user_id === $user->id || $user->isAdmin()) {
            return;
        }

        if ($user->isDesigner() && (
            $project->designer_id === $user->id
            || ($project->designer_id === null && $project->status === SolarProject::STATUS_SUBMITTED)
        )) {
            return;
        }

        abort(403);
    }

    /**
     * Izmjena, brisanje i slanje narudžbe: samo vlasnik projekta. Projektant
     * i admin mijenjaju projekat kroz svoj radni prostor (ProjectReviewController).
     */
    private function authorizeOwner(SolarProject $project): void
    {
        if ($project->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
