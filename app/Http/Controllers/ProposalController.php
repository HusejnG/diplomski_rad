<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\QuoteRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate; 

class ProposalController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:manage-proposals')->except(['index', 'show']);
    }

    public function index()
    {
        $user = Auth::user();

        // Admin vidi sve ponude
        if ($user->isAdmin()) {
            $proposals = Proposal::with(['quoteRequest', 'designer'])->latest()->get();
        }
        // Dizajner vidi samo svoje ponude
        else if ($user->isDesigner()) {
            $proposals = Proposal::where('designer_id', $user->id)
                                ->with(['quoteRequest', 'designer'])
                                ->latest()
                                ->get();
        }
        // Kupac vidi ponude koje su povezane s njegovim zahtjevima za ponudu
        else if ($user->isCustomer()) {
            $proposals = Proposal::whereHas('quoteRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['quoteRequest', 'designer'])->latest()->get();
        }
        // Za ostale uloge, ne prikazuju se ponude
        else {
            $proposals = collect();
        }

        return view('proposals.index', compact('proposals'));
    }

    public function create(Request $request)
    {
        $quoteRequestId = $request->query('quote_request_id');
        $quoteRequest = null;

        if ($quoteRequestId) {
            $quoteRequest = QuoteRequest::findOrFail($quoteRequestId);
        }

        $products = Product::all(); 
        return view('proposals.create', compact('quoteRequest', 'products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quote_request_id' => 'required|exists:quote_requests,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $quoteRequest = QuoteRequest::findOrFail($validatedData['quote_request_id']);

        $proposal = Proposal::create([
            'quote_request_id' => $quoteRequest->id,
            'designer_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'status' => 'sent', 
        ]);

        $totalPrice = 0;
        $syncData = [];

        foreach ($validatedData['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $quantity = $validatedData['quantities'][$index];

            if ($product && $quantity > 0) {
                $syncData[$productId] = [
                    'quantity' => $quantity,
                    'price_at_time_of_proposal' => $product->price,
                ];
                $totalPrice += ($product->price * $quantity);
            }
        }

        $proposal->products()->sync($syncData);
        $proposal->update(['total_price' => $totalPrice]);

        $quoteRequest->update(['status' => 'in_progress']);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda uspješno kreirana!');
    }

    public function show(Proposal $proposal)
    {
        return view('proposals.show', compact('proposal'));
    }

    
    public function edit(Proposal $proposal)
    {
        $products = Product::all();
        $selectedProducts = $proposal->products->pluck('pivot.quantity', 'id')->toArray();

        return view('proposals.edit', compact('proposal', 'products', 'selectedProducts'));
    }

    
    public function update(Request $request, Proposal $proposal)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            // Uklonjen 'status' iz validacije
        ]);

        $totalPrice = 0;
        $syncData = [];

        foreach ($validatedData['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $quantity = $validatedData['quantities'][$index];

            if ($product && $quantity > 0) {
                $syncData[$productId] = [
                    'quantity' => $quantity,
                    'price_at_time_of_proposal' => $product->price,
                ];
                $totalPrice += ($product->price * $quantity);
            }
        }

        // Ažurira se samo sadržaj, ne i status
        $proposal->update(array_merge($validatedData, ['total_price' => $totalPrice]));
        $proposal->products()->sync($syncData);

        // Status zahtjeva se automatski ažurira samo ako ponuda bude prihvaćena ili odbijena,
        // što se dešava preko accept() i reject() metoda, ne preko update() metode
        if ($proposal->status === 'accepted' || $proposal->status === 'rejected') {
             $proposal->quoteRequest->update(['status' => $proposal->status]);
        } else {
             $proposal->quoteRequest->update(['status' => 'in_progress']);
        }

        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda uspješno ažurirana!');
    }


    
    public function destroy(Proposal $proposal)
    {
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Ponuda uspješno obrisana!');
    }

    public function accept(Proposal $proposal)
    {
        if (Auth::id() !== $proposal->quoteRequest->user_id || $proposal->status !== 'sent') {
            return redirect()->back()->with('error', 'Niste ovlašteni za ovu akciju ili ponuda nije u statusu za prihvatanje.');
        }

        $proposal->update(['status' => 'accepted']);
        $proposal->quoteRequest->update(['status' => 'completed']); 

        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda je uspješno prihvaćena!');
    }

    public function reject(Proposal $proposal)
    {
        if (Auth::id() !== $proposal->quoteRequest->user_id || $proposal->status !== 'sent') {
            return redirect()->back()->with('error', 'Niste ovlašteni za ovu akciju ili ponuda nije u statusu za odbijanje.');
        }

        $proposal->update(['status' => 'rejected']);
        $proposal->quoteRequest->update(['status' => 'rejected']);

        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda je uspješno odbijena!');
    }
}
