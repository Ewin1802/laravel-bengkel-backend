<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suppliers;
use Illuminate\Database\QueryException;

class SupplierController extends Controller
{
    // INDEX - Menampilkan daftar supplier
    public function index()
    {
        $suppliers = Suppliers::paginate(10);
        return view('pages.suppliers.index', compact('suppliers'));
    }

    // CREATE - Menampilkan form tambah supplier
    public function create()
    {
        return view('pages.suppliers.create');
    }

    // STORE - Menyimpan data supplier baru ke database
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Simpan data supplier
        Suppliers::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    // SHOW - Menampilkan detail supplier
    public function show($id)
    {
        $supplier = Suppliers::findOrFail($id);
        return view('pages.suppliers.show', compact('supplier'));
    }

    // EDIT - Menampilkan form edit supplier
    public function edit($id)
    {
        $supplier = Suppliers::findOrFail($id);
        return view('pages.suppliers.edit', compact('supplier'));
    }

    // UPDATE - Memperbarui data supplier
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Update data supplier
        $supplier = Suppliers::findOrFail($id);
        $supplier->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    // DESTROY - Menghapus supplier
    public function destroy($id)
    {
        try {
            $supplier = Suppliers::findOrFail($id);
            $supplier->delete();
            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
        } catch (QueryException $e) {
            return redirect()->route('suppliers.index')->with('error', 'Cannot delete this supplier. It may be linked to other data.');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
}
