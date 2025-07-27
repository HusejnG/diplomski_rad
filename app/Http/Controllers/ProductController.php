<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Za rad sa slikama

class ProductController extends Controller
{
    /**
     * Prikaz svih proizvoda (za administraciju/CRUD).
     */
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    /**
     * Prikaz forme za kreiranje novog proizvoda.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Skladištenje novog proizvoda u bazi.
     */
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
            'image' => 'nullable|image|max:2048', // Max 2MB slika
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        Product::create(array_merge($validatedData, ['image_path' => $imagePath]));

        return redirect()->route('products.index')->with('success', 'Proizvod uspešno dodan!');
    }

    /**
     * Prikaz specifičnog proizvoda.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Prikaz forme za editovanje proizvoda.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Ažuriranje proizvoda u bazi.
     */
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
            'image' => 'nullable|image|max:2048', // Max 2MB slika
        ]);

        if ($request->hasFile('image')) {
            // Obriši staru sliku ako postoji
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('product_images', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Proizvod uspešno ažuriran!');
    }

    /**
     * Brisanje proizvoda.
     */
    public function destroy(Product $product)
    {
        // Obriši sliku proizvoda
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Proizvod uspešno obrisan!');
    }

    /**
     * Prikaz svih proizvoda za web shop.
     */
    public function shopIndex()
    {
        $products = Product::latest()->get(); // Možeš dodati paginaciju, filtere itd.
        return view('shop.index', compact('products'));
    }
}
