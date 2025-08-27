<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{
    /**
     * Prikazuje listu proizvoda s opcijama za pretraživanje i filtriranje.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                  ->orWhere('description', 'like', "%$searchTerm%")
                  ->orWhere('model', 'like', "%$searchTerm%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('manufacturer')) {
            $manufacturerTerm = $request->manufacturer;
            $query->where('manufacturer', 'like', "%$manufacturerTerm%");
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'power_w' => 'nullable|numeric|min:0',
            'length_mm' => 'nullable|numeric|min:0',
            'width_mm' => 'nullable|numeric|min:0',
            'height_mm' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        Product::create(array_merge($validatedData, ['image_path' => $imagePath]));

        return redirect()->route('products.index')->with('success', 'Proizvod uspješno dodan!');
    }
    
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'power_w' => 'nullable|numeric|min:0',
            'length_mm' => 'nullable|numeric|min:0',
            'width_mm' => 'nullable|numeric|min:0',
            'height_mm' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('product_images', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Proizvod uspješno ažuriran!');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Proizvod uspješno obrisan!');
    }

    public function shopIndex(Request $request)
    {
        // Dohvati sve jedinstvene tipove, uključujući one koji su NULL
        $availableTypes = Product::distinct()->pluck('type')->sort()->values()->all();

        // Ukloni prazne stringove, ali zadrži null
        $availableTypes = array_filter($availableTypes, function($value) {
            return !is_string($value) || strlen($value) > 0;
        });

        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                  ->orWhere('description', 'like', "%$searchTerm%")
                  ->orWhere('model', 'like', "%$searchTerm%");
            });
        }
        
        // Ažurirani uvjet za filtriranje
        if ($request->filled('type')) {
            if ($request->type === 'null') {
                $query->whereNull('type');
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('manufacturer')) {
            $manufacturerTerm = $request->manufacturer;
            $query->where('manufacturer', 'like', "%$manufacturerTerm%");
        }

        $products = $query->latest()->get();

        // Prosljeđivanje varijable s tipovima prikazu.
        return view('shop.index', compact('products', 'availableTypes'));
    }
}
