<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;


class QuoteRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Administrator vidi sve zahtjeve
            $quoteRequests = QuoteRequest::latest()->get();
        } elseif ($user->isDesigner()) {
            // Projektant vidi zahtjeve koji nemaju ponude i one za koje je on kreirao ponudu
            $quoteRequests = QuoteRequest::whereDoesntHave('proposal') // Nema povezanih ponuda
                                        ->orWhereHas('proposal', function ($query) use ($user) {
                                            $query->where('designer_id', $user->id); // Ponuda pripada trenutnom dizajneru
                                        })
                                        ->latest()
                                        ->get();
        } else {
            // Obični korisnik vidi samo svoje zahtjeve
            $quoteRequests = QuoteRequest::where('user_id', $user->id)->latest()->get();
        }

        return view('quote_requests.index', compact('quoteRequests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quote_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        return redirect()->route('quote-requests.index')->with('success', 'Vaš zahtjev za ponudu je uspješno poslan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuoteRequest  $quoteRequest
     * @return \Illuminate\Http\Response
     */
    public function show(QuoteRequest $quoteRequest)
    {
        //  Gate::authorize('view-quote-request', $quoteRequest);

        $proposal = $quoteRequest->proposal;

        return view('quote_requests.show', compact('quoteRequest', 'proposal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuoteRequest  $quoteRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(QuoteRequest $quoteRequest)
    {
        //Gate::authorize('view-quote-request', $quoteRequest);
        return view('quote_requests.edit', compact('quoteRequest'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuoteRequest  $quoteRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuoteRequest $quoteRequest)
    {
        // Gate::authorize('view-quote-request', $quoteRequest);

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

        return redirect()->route('quote-requests.index')->with('success', 'Zahtjev za ponudu je uspješno ažuriran!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuoteRequest  $quoteRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuoteRequest $quoteRequest)
    {
        // Gate::authorize('view-quote-request', $quoteRequest);
        $quoteRequest->delete();
        return redirect()->route('quote-requests.index')->with('success', 'Zahtjev za ponudu je uspješno obrisan!');
    }
}
