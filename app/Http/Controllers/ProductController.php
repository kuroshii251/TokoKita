<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(12);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required',
            'harga' => 'required|numeric',
        ]);

        $fotoPath = 'noimage.png';
        if ($request->file('foto')) {
            $fotoPath = $request->file('foto')->store('images', 'public');
        }

        Product::create([
            'nama'      => $request->nama,
            'harga'     => str_replace(".", "", $request->harga),
            'deskripsi' => $request->deskripsi,
            'foto'      => $fotoPath,
        ]);

        return redirect()->route('products.index')->with('success', 'Add Product Success');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama'  => 'required',
            'harga' => 'required|numeric'
        ]);

        $product->nama = $request->nama;
        $product->harga = str_replace(".", "", $request->harga);
        $product->deskripsi = $request->deskripsi;

        if ($request->file('foto')) {
            if ($product->foto && $product->foto !== "noimage.png") {
                Storage::disk('public')->delete($product->foto);
            }
            $product->foto = $request->file('foto')->store('images', 'public');
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Update Product Success');
    }

    public function destroy(Product $product)
    {
        if ($product->foto && $product->foto !== "noimage.png") {
            Storage::disk('public')->delete($product->foto);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Delete Product Success');
    }
}