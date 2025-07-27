<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\QuoteRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProposalController extends Controller
{
    public function __construct()
    {
        // Samo projektanti i admini mogu upravljati ponudama
        $this->middleware('can:manage-proposals')->except(['index', 'show']);
        // Index i show imaju posebne Gate provere
    }

    /**
     * Prikaz svih ponuda (za projektante i admine).
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $proposals = Proposal::latest()->get();
        } elseif (Auth::user()->isDesigner()) {
            $proposals = Auth::user()->createdProposals()->latest()->get();
        } else {
            // Za kupce, prikazuje ponude vezane za njihove zahteve
            $proposals = Auth::user()->receivedProposals()->latest()->get();
        }

        return view('proposals.index', compact('proposals'));
    }

    /**
     * Prikaz forme za kreiranje nove ponude za specifičan zahtev.
     */
    public function create(Request $request)
    {
        Gate::authorize('create-proposal'); // Samo projektanti mogu kreirati

        $quoteRequestId = $request->query('quote_request_id');
        $quoteRequest = null;

        if ($quoteRequestId) {
            $quoteRequest = QuoteRequest::findOrFail($quoteRequestId);
            // Provera da li projektant ima pravo da kreira ponudu za ovaj zahtev
            // (npr. da zahtev nije već obrađen ili dodeljen drugom projektantu)
            // Za sada, dozvoljavamo ako je status 'pending'
            if ($quoteRequest->status !== 'pending') {
                return redirect()->route('quote-requests.show', $quoteRequest)->with('error', 'Ponuda za ovaj zahtev je već u obradi ili završena.');
            }
        }

        $products = Product::all(); // Svi dostupni proizvodi
        return view('proposals.create', compact('quoteRequest', 'products'));
    }

    /**
     * Skladištenje nove ponude u bazi.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-proposal');

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

        // Kreiraj ponudu
        $proposal = Proposal::create([
            'quote_request_id' => $quoteRequest->id,
            'designer_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'status' => 'sent', // Postavi status na 'sent' kada je kreirana
        ]);

        $totalPrice = 0;
        $syncData = [];

        foreach ($validatedData['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $quantity = $validatedData['quantities'][$index];

            if ($product && $quantity > 0) {
                $syncData[$productId] = [
                    'quantity' => $quantity,
                    'price_at_time_of_proposal' => $product->price, // Sačuvaj trenutnu cenu
                ];
                $totalPrice += ($product->price * $quantity);
            }
        }

        $proposal->products()->sync($syncData);
        $proposal->update(['total_price' => $totalPrice]);

        // Ažuriraj status zahteva za ponudu
        $quoteRequest->update(['status' => 'in_progress']); // Ili 'completed' ako je ponuda odmah finalna

        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda uspešno kreirana!');
    }

    /**
     * Prikaz specifične ponude.
     */
    public function show(Proposal $proposal)
    {
        Gate::authorize('view-proposal', $proposal); // Provera pristupa

        return view('proposals.show', compact('proposal'));
    }

    /**
     * Prikaz forme za editovanje ponude.
     */
    public function edit(Proposal $proposal)
    {
        Gate::authorize('manage-proposals', $proposal); // Provera pristupa

        $products = Product::all(); // Svi dostupni proizvodi
        $selectedProducts = $proposal->products->pluck('pivot.quantity', 'id')->toArray(); // Količine odabranih proizvoda

        return view('proposals.edit', compact('proposal', 'products', 'selectedProducts'));
    }

    /**
     * Ažuriranje ponude u bazi.
     */
    public function update(Request $request, Proposal $proposal)
    {
        Gate::authorize('manage-proposals', $proposal); // Provera pristupa

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'status' => 'required|in:draft,sent,accepted,rejected', // Projektant može menjati status
        ]);

        $totalPrice = 0;
        $syncData = [];

        foreach ($validatedData['product_ids'] as $index => $productId) {
            $product = Product::find($productId);
            $quantity = $validatedData['quantities'][$index];

            if ($product && $quantity > 0) {
                $syncData[$productId] = [
                    'quantity' => $quantity,
                    'price_at_time_of_proposal' => $product->price, // Sačuvaj trenutnu cenu
                ];
                $totalPrice += ($product->price * $quantity);
            }
        }

        $proposal->update(array_merge($validatedData, ['total_price' => $totalPrice]));
        $proposal->products()->sync($syncData);

        // Ažuriraj status zahteva za ponudu ako je ponuda završena
        if ($proposal->status === 'accepted' || $proposal->status === 'rejected') {
             $proposal->quoteRequest->update(['status' => $proposal->status]);
        } else {
             $proposal->quoteRequest->update(['status' => 'in_progress']);
        }


        return redirect()->route('proposals.show', $proposal)->with('success', 'Ponuda uspešno ažurirana!');
    }

    /**
     * Brisanje ponude.
     */
    public function destroy(Proposal $proposal)
    {
        Gate::authorize('manage-proposals', $proposal); // Provera pristupa

        // Razmisli o tome šta se dešava sa statusom QuoteRequest-a kada se ponuda obriše
        // Možda ga vratiti na 'pending' ili 'rejected'
        $proposal->delete();

        return redirect()->route('proposals.index')->with('success', 'Ponuda uspešno obrisana!');
    }
}