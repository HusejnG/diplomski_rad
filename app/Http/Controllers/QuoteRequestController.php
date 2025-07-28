<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate; 
// use Illuminate\Support\Facades\Log; 


class QuoteRequestController extends Controller
{
  
    public function index()
    {
 
        $quoteRequests = QuoteRequest::latest()->get();

        return view('quote_requests.index', compact('quoteRequests'));
    }


    public function create()
    {
        return view('quote_requests.create');
    }


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


    public function show(QuoteRequest $quoteRequest)
    {
        //  Gate::authorize('view-quote-request', $quoteRequest);

        $proposal = $quoteRequest->proposal;

        return view('quote_requests.show', compact('quoteRequest', 'proposal'));
    }

    
    public function edit(QuoteRequest $quoteRequest)
    {
        //Gate::authorize('view-quote-request', $quoteRequest);
        return view('quote_requests.edit', compact('quoteRequest'));
    }

    
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

    
    public function destroy(QuoteRequest $quoteRequest)
    {
        // Gate::authorize('view-quote-request', $quoteRequest);
        $quoteRequest->delete();
        return redirect()->route('quote-requests.index')->with('success', 'Zahtjev za ponudu je uspješno obrisan!');
    }
}
