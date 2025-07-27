<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // Potrebno za Gate::allows()
use Illuminate\Support\Facades\Log; // Za logove, možeš ga ukloniti kasnije


class QuoteRequestController extends Controller
{
    /**
     * Prikaz svih zahteva za ponudu trenutnog korisnika.
     */
    public function index()
    {
        // --- DEBUG LINIJE START ---
        $user = Auth::user();
        Log::info('QuoteRequestController@index: Prijavljeni korisnik ID: ' . $user->id . ', Email: ' . $user->email . ', Uloga: ' . $user->role);

        if ($user->isAdmin() || $user->isDesigner()) {
            Log::info('QuoteRequestController@index: Korisnik je Admin ili Dizajner. Pokušavam dohvatiti SVE zahteve.');
            $quoteRequests = QuoteRequest::latest()->get();
            Log::info('QuoteRequestController@index: Broj dohvatajenih zahteva (Admin/Dizajner): ' . $quoteRequests->count());
        } else {
            Log::info('QuoteRequestController@index: Korisnik je Kupac. Pokušavam dohvatiti SAMO svoje zahteve.');
            $quoteRequests = $user->quoteRequests()->latest()->get();
            Log::info('QuoteRequestController@index: Broj dohvatajenih zahteva (Kupac): ' . $quoteRequests->count());
        }
        // --- DEBUG LINIJE END ---

        return view('quote_requests.index', compact('quoteRequests'));
    }

    /**
     * Prikaz forme za kreiranje novog zahteva za ponudu.
     */
    public function create()
    {
        return view('quote_requests.create');
    }

    /**
     * Skladištenje novog zahteva za ponudu u bazi.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'roof_type' => 'nullable|string|max:255',
            'roof_area_sqm' => 'nullable|numeric|min:0',
            'avg_monthly_consumption_kwh' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Auth::user()->quoteRequests()->create($validatedData);

        return redirect()->route('quote-requests.index')->with('success', 'Vaš zahtev za ponudu je uspešno poslat!');
    }

    /**
     * Prikaz specifičnog zahteva za ponudu.
     */
    public function show(QuoteRequest $quoteRequest)
    {
        // --- AGRESIVNO DEBUG LINIJE START ---
        $user = Auth::user();
        $canView = Gate::allows('view-quote-request', $quoteRequest);

        dd("DEBUG: QuoteRequestController@show - Provera Gate-a 'view-quote-request'." .
           "\nKorisnik: " . $user->email . " (Uloga: " . $user->role . ")" .
           "\nZahtev ID: " . $quoteRequest->id . " (Vlasnik ID: " . $quoteRequest->user_id . ")" .
           "\nGate dozvola: " . ($canView ? 'DA' : 'NE'));
        // --- AGRESIVNO DEBUG LINIJE END ---

        // Ako je Gate::allows() vratio false, možemo ručno baciti 403
        if (!$canView) {
            abort(403, 'Ova akcija nije autorizovana.');
        }

        // Učitaj ponudu ako postoji (relacija 1:1)
        $proposal = $quoteRequest->proposal;

        return view('quote_requests.show', compact('quoteRequest', 'proposal'));
    }

    /**
     * Prikaz forme za editovanje zahteva (ako dozvoljavamo korisniku da menja).
     */
    public function edit(QuoteRequest $quoteRequest)
    {
        Gate::authorize('view-quote-request', $quoteRequest);
        if ($quoteRequest->status !== 'pending' && !Auth::user()->isAdmin()) {
            return redirect()->route('quote-requests.show', $quoteRequest)->with('error', 'Ne možete menjati zahtev koji je u obradi ili završen.');
        }
        return view('quote_requests.edit', compact('quoteRequest'));
    }

    /**
     * Ažuriranje zahteva za ponudu.
     */
    public function update(Request $request, QuoteRequest $quoteRequest)
    {
        Gate::authorize('view-quote-request', $quoteRequest);
        if ($quoteRequest->status !== 'pending' && !Auth::user()->isAdmin()) {
            return redirect()->route('quote-requests.show', $quoteRequest)->with('error', 'Ne možete menjati zahtev koji je u obradi ili završen.');
        }

        $validatedData = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'roof_type' => 'nullable|string|max:255',
            'roof_area_sqm' => 'nullable|numeric|min:0',
            'avg_monthly_consumption_kwh' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $quoteRequest->update($validatedData);

        return redirect()->route('quote-requests.index')->with('success', 'Zahtev za ponudu je uspešno ažuriran!');
    }

    /**
     * Brisanje zahtjeva za ponudu.
     */
    public function destroy(QuoteRequest $quoteRequest)
    {
        Gate::authorize('view-quote-request', $quoteRequest);
        if ($quoteRequest->status !== 'pending' && !Auth::user()->isAdmin()) {
            return redirect()->route('quote-requests.show', $quoteRequest)->with('error', 'Ne možete obrisati zahtev koji je u obradi ili završen.');
        }

        $quoteRequest->delete();
        return redirect()->route('quote-requests.index')->with('success', 'Zahtev za ponudu je uspešno obrisan!');
    }
}
