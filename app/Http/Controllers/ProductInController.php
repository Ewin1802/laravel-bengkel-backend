<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductIn;
use App\Models\ProductName;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductInController extends Controller
{
    // INDEX: Menampilkan daftar product_ins
    public function index(Request $request)
    {
        $productIns = ProductIn::with(['productName', 'supplier'])
            ->when($request->input('product_name'), function ($query, $productName) {
                $query->whereHas('productName', function ($q) use ($productName) {
                    $q->where('name', 'like', '%' . $productName . '%');
                });
            })
            ->when($request->input('is_received') !== null, function ($query) use ($request) {
                $query->where('is_received', $request->input('is_received'));
            })
            ->latest()
            ->paginate(10);

        return view('pages.product_ins.index', compact('productIns'));
    }

    // CREATE: Menampilkan form tambah data
    public function create()
    {
        $productNames = ProductName::all();
        $suppliers = Suppliers::all();
        return view('pages.product_ins.create', compact('productNames', 'suppliers'));
    }

    // STORE: Menyimpan data product_in baru
    public function store(Request $request)
    {
        $request->validate([
            'product_name_id' => 'required|exists:product_names,id',
            'amount' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_received' => 'required|boolean',
            'receiver' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $productIn = ProductIn::create($request->all());

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/product_ins');
            $productIn->update(['image' => Storage::url($imagePath)]);
        }

        return redirect()->route('product_ins.index')->with('success', 'Barang masuk berhasil ditambahkan.');
    }
    // public function store(Request $request)
    // {
    //     // Validasi dengan pesan error kustom
    //     $request->validate([
    //         'product_name_id' => 'required|exists:product_names,id',
    //         'amount' => 'required|numeric',
    //         'quantity' => 'required|integer|min:1',
    //         'supplier_id' => 'nullable|exists:suppliers,id',
    //         'is_received' => 'required|boolean',
    //         'receiver' => 'nullable|string|max:255',
    //         'description' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ], [
    //         'product_name_id.required' => 'Nama produk harus diisi.',
    //         'product_name_id.exists' => 'Nama produk tidak valid.',
    //         'amount.required' => 'Jumlah harus diisi.',
    //         'amount.numeric' => 'Jumlah harus berupa angka.',
    //         'quantity.required' => 'Kuantitas harus diisi.',
    //         'quantity.integer' => 'Kuantitas harus berupa angka bulat.',
    //         'quantity.min' => 'Kuantitas minimal adalah 1.',
    //         'supplier_id.exists' => 'Supplier tidak valid.',
    //         'is_received.required' => 'Status penerimaan harus diisi.',
    //         'is_received.boolean' => 'Status penerimaan harus bernilai benar atau salah.',
    //         'receiver.string' => 'Penerima harus berupa teks.',
    //         'receiver.max' => 'Penerima maksimal 255 karakter.',
    //         'description.string' => 'Deskripsi harus berupa teks.',
    //         'image.image' => 'File harus berupa gambar.',
    //         'image.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
    //         'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
    //     ]);

    //     // Simpan data tanpa gambar terlebih dahulu
    //     $productIn = new ProductIn();
    //     $productIn->product_name_id = $request->product_name_id;
    //     $productIn->amount = $request->amount;
    //     $productIn->quantity = $request->quantity;
    //     $productIn->supplier_id = $request->supplier_id;
    //     $productIn->is_received = $request->is_received;
    //     $productIn->receiver = $request->receiver;
    //     $productIn->description = $request->description;
    //     $productIn->save();

    //     // Simpan gambar jika ada
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imagePath = 'public/product_ins/' . $productIn->id . '.' . $image->getClientOriginalExtension();
    //         $image->storeAs('public/product_ins', $productIn->id . '.' . $image->getClientOriginalExtension());

    //         // Simpan path gambar ke dalam database
    //         $productIn->image = 'storage/product_ins/' . $productIn->id . '.' . $image->getClientOriginalExtension();
    //         $productIn->save();
    //     }

    //     return redirect()->route('product_ins.index')->with('success', 'Barang masuk berhasil ditambahkan.');
    // }


    // SHOW: Menampilkan detail barang masuk (Opsional)
    public function show(ProductIn $productIn)
    {
        return view('pages.product_ins.show', compact('productIn'));
    }

    // EDIT: Menampilkan form edit data product_in
    public function edit(ProductIn $productIn)
    {
        $productNames = ProductName::all();
        $suppliers = Suppliers::all();
        return view('pages.product_ins.edit', compact('productIn', 'productNames', 'suppliers'));
    }

    // UPDATE: Menyimpan perubahan data product_in
    public function update(Request $request, ProductIn $productIn)
    {
        $request->validate([
            'product_name_id' => 'required|exists:product_names,id',
            'amount' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_received' => 'required|boolean',
            'receiver' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $productIn->update($request->all());

        if ($request->hasFile('image')) {
            if ($productIn->image && Storage::exists($productIn->image)) {
                Storage::delete($productIn->image);
            }
            $imagePath = $request->file('image')->store('public/product_ins');
            $productIn->update(['image' => Storage::url($imagePath)]);
        }

        return redirect()->route('product_ins.index')->with('success', 'Data barang masuk berhasil diperbarui.');
    }

    // DESTROY: Menghapus product_in
    public function destroy(ProductIn $productIn)
    {
        if ($productIn->image && Storage::exists($productIn->image)) {
            Storage::delete($productIn->image);
        }
        $productIn->delete();

        return redirect()->route('product_ins.index')->with('success', 'Barang masuk berhasil dihapus.');
    }
}
