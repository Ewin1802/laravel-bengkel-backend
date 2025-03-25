<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::all();
        $products = Product::with(['productIn', 'category', 'productName'])
            ->when($request->input('name'), function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->input('category_id'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10);

        return view('pages.products.index', compact('products', 'categories'));
    }

    // CREATE: Form tambah produk
    public function create()
    {
        $categories = Category::all();

        // Ambil hanya barang yang sudah diterima (is_received = 1)
        $productIns = ProductIn::with(['productName', 'supplier'])
            ->where('is_received', 0)
            ->get();

        return view('pages.products.create', compact('categories', 'productIns'));
    }

    public function checkStock(Request $request)
    {
        Log::info('Cek stok dipanggil dengan:', $request->all());

        $request->validate([
            'name' => 'required|string',
            'unit' => 'required|string',
        ]);

        // Cari produk berdasarkan nama dan satuan
        $product = Product::where('name', $request->name)
            ->whereHas('productName', function ($query) use ($request) {
                $query->where('unit', $request->unit);
            })
            ->first();

        if ($product) {
            Log::info('Produk ditemukan:', [
                'stock' => $product->stock,
                'price' => $product->price,
                'category_id' => $product->category_id
            ]);

            return response()->json([
                'stock' => $product->stock,
                'price' => $product->price,
                'category_id' => $product->category_id // Kirim kategori sebelumnya
            ]);
        }

        Log::warning('Produk tidak ditemukan:', ['name' => $request->name, 'unit' => $request->unit]);
        return response()->json([
            'stock' => 0,
            'price' => null,
            'category_id' => null // Kategori kosong jika produk belum ada
        ]);
    }

    // STORE: Menyimpan produk baru
    public function store(Request $request)
    {
        try {
            // Tetapkan nilai default status = 1 (Active)
            $request->merge([
                'status' => 1,
                'is_favorite' => 0,
            ]);

            Log::info('Data yang diterima:', $request->all());

            // Validasi input
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'product_in_id' => 'required|exists:product_ins,id',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'is_favorite' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Ambil data ProductIn
            $productIn = ProductIn::findOrFail($request->product_in_id);

            // Cek apakah produk dengan nama & kategori yang sama sudah ada
            $existingProduct = Product::where('name', $productIn->productName->name)
                ->where('category_id', $request->category_id)
                ->first();

            if ($existingProduct) {
                // Jika produk sudah ada, update stok & harga jual terbaru
                $existingProduct->increment('stock', $productIn->quantity);
                $existingProduct->update([
                    'price' => $request->price, // Simpan harga terbaru
                ]);
                $product = $existingProduct;
            } else {
                // Jika produk belum ada, buat produk baru
                $product = Product::create([
                    'category_id' => $request->category_id,
                    'product_in_id' => $productIn->id,
                    'name' => $productIn->productName->name,
                    'description' => $request->description,
                    'price' => $request->price, // Simpan harga baru
                    'stock' => $productIn->quantity,
                    'status' => 1,
                    'is_favorite' => $request->is_favorite,
                ]);
            }

            // Simpan gambar jika ada
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());

                // Simpan path ke database
                $product->update(['image' => $imagePath]);
            }

            // **Ubah is_received menjadi 1 di tabel product_ins**
            $productIn->update(['is_received' => 1]);

            Log::info('Produk berhasil disimpan dan is_received diubah menjadi 1', [
                'product_id' => $product->id,
                'product_in_id' => $productIn->id,
                'is_received' => $productIn->is_received
            ]);

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan produk:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->withErrors($e->validator)->withInput();
            // return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan produk.');
        }
    }

    // EDIT: Form edit produk
    public function edit(Product $product)
    {
        $categories = Category::all();

        // Ambil semua product_in yang sudah diterima + tambahkan milik produk yang sedang diedit
        $productIns = ProductIn::with('productName')
            ->where('is_received', true)
            ->orWhere('id', $product->product_in_id) // Pastikan data milik produk tetap muncul
            ->get();

        return view('pages.products.edit', compact('product', 'categories', 'productIns'));
    }


    public function update(Request $request, Product $product)
    {
        Log::info('Memulai proses update produk', ['product_id' => $product->id]);

        try {
            // Pastikan status dan is_favorite selalu ada
            $request->merge([
                'status' => $request->input('status', 1),
                'is_favorite' => $request->input('is_favorite', 0),
            ]);

            Log::info('Data yang diterima:', $request->all());

            // Validasi input
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            Log::info('Validasi berhasil untuk produk', ['product_id' => $product->id]);

            // Update produk tanpa gambar terlebih dahulu
            $product->update([
                'category_id' => $request->category_id,
                'price' => $request->price,
                'status' => $request->status,
                'is_favorite' => $request->is_favorite,
            ]);

            Log::info('Produk berhasil diperbarui di database', ['product_id' => $product->id]);

            // Simpan gambar baru jika ada
            if ($request->hasFile('image')) {
                Log::info('Menyimpan gambar baru', ['product_id' => $product->id]);

                // Hapus gambar lama jika ada
                if ($product->image && Storage::exists($product->image)) {
                    Storage::delete($product->image);
                }

                // Simpan gambar baru
                $image = $request->file('image');
                $imagePath = 'public/products/' . $product->id . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());

                // Update path gambar di database
                $product->update(['image' => 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension()]);
            }

            Log::info('Produk berhasil diperbarui dan dialihkan ke halaman index', ['product_id' => $product->id]);

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat update produk', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui produk.');
        }
    }

    // DESTROY: Hapus produk
    public function destroy(Product $product)
    {
        // Hapus semua order_items yang terkait dengan produk ini
        OrderItem::where('product_id', $product->id)->delete();

        // Hapus gambar produk jika ada
        if ($product->image && Storage::exists($product->image)) {
            Storage::delete($product->image);
        }

        // Hapus produk
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk dan order terkait berhasil dihapus.');
    }

}
