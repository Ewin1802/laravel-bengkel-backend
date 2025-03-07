<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductName;
use App\Models\ProductIn;
use App\Models\Product;
use Illuminate\Support\Facades\Log; // Logging untuk debugging

class ProductNamesController extends Controller
{
    // Menampilkan daftar product names
    public function index() {
        $productNames = ProductName::latest()->paginate(10);
        return view('pages.product_names.index', compact('productNames'));
    }

    // Menampilkan halaman edit product name
    public function edit(ProductName $productName) {
        return view('pages.product_names.edit', compact('productName'));
    }

    // Menyimpan data product name baru
    public function store(Request $request) {
        try {
            // Log request untuk debugging
            Log::info('Data diterima:', $request->all());

            // Validasi input sebelum melakukan pengecekan database
            $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'required|string|max:50'
            ]);

            // Normalisasi input (format title case)
            $name = ucwords(strtolower(trim($request->name)));
            $unit = ucwords(strtolower(trim($request->unit)));

            // Cek apakah kombinasi `name` dan `unit` sudah ada
            $existingProduct = ProductName::where('name', $name)
                ->where('unit', $unit)
                ->exists();

            if ($existingProduct) {
                return response()->json(['error' => 'Nama barang dengan satuan yang sama sudah ada.'], 422);
            }

            // Simpan ke database jika kombinasi belum ada
            $productName = ProductName::create([
                'name' => $name,
                'unit' => $unit,
            ]);

            // Log sukses
            Log::info('Barang berhasil disimpan:', $productName->toArray());

            return response()->json($productName);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Gagal menyimpan barang:', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan barang.'], 500);
        }
    }

    // Mengupdate data product name
    // public function update(Request $request, ProductName $productName) {
    //     try {
    //         // Validasi input
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'unit' => 'required|string|max:50',
    //         ]);

    //         // Normalisasi input
    //         $name = ucwords(strtolower(trim($request->name)));
    //         $unit = ucwords(strtolower(trim($request->unit)));

    //         // Cek apakah kombinasi `name` dan `unit` sudah ada di data lain
    //         $existingProduct = ProductName::where('name', $name)
    //             ->where('unit', $unit)
    //             ->where('id', '!=', $productName->id) // Hindari cek terhadap dirinya sendiri
    //             ->exists();

    //         if ($existingProduct) {
    //             return redirect()->back()->with('error', 'Nama barang dengan satuan yang sama sudah ada.');
    //         }

    //         // Update data product name
    //         $productName->update([
    //             'name' => $name,
    //             'unit' => $unit,
    //         ]);

    //         return redirect()->route('product_names.index')->with('success', 'Barang berhasil diperbarui.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui barang.');
    //     }
    // }
    public function update(Request $request, ProductName $productName) {
        Log::info('Memulai proses update product name', ['product_id' => $productName->id]);

        try {
            // Simpan nama lama sebelum diubah
            $oldName = $productName->name;

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'required|string|max:50',
            ]);

            Log::info('Validasi berhasil', ['product_id' => $productName->id]);

            // Normalisasi input
            $name = ucwords(strtolower(trim($request->name)));
            $unit = ucwords(strtolower(trim($request->unit)));

            // Cek apakah kombinasi `name` dan `unit` sudah ada di data lain
            $existingProduct = ProductName::where('name', $name)
                ->where('unit', $unit)
                ->where('id', '!=', $productName->id)
                ->exists();

            if ($existingProduct) {
                return redirect()->back()->with('error', 'Nama barang dengan satuan yang sama sudah ada.');
            }

            // Update ProductName
            $productName->update([
                'name' => $name,
                'unit' => $unit,
            ]);

            Log::info('Product Name berhasil diperbarui', ['product_id' => $productName->id]);

            // Cari semua ProductIn yang berhubungan dengan productName ini
            $productInIds = ProductIn::where('product_name_id', $productName->id)->pluck('id');

            // Update produk berdasarkan product_in_id yang terkait
            $affectedRows = Product::whereIn('product_in_id', $productInIds)->update(['name' => $name]);

            Log::info('Jumlah produk yang diperbarui:', ['affected_rows' => $affectedRows]);

            if ($affectedRows === 0) {
                Log::warning('Tidak ada produk yang diperbarui. Periksa apakah data terkait sesuai.', [
                    'old_name' => $oldName,
                    'new_name' => $name,
                    'product_name_id' => $productName->id,
                    'product_in_ids' => $productInIds
                ]);
            }

            return redirect()->route('product_names.index')->with('success', 'Barang berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat update produk', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui barang.');
        }
    }





    // Menghapus product name
    public function destroy(ProductName $productName) {
        try {
            $productName->delete();
            return redirect()->route('product_names.index')->with('success', 'Barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus barang.');
        }
    }
}
