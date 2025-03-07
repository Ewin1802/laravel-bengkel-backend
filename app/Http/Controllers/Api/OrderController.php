<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function saveOrder(Request $request)
    {
        DB::beginTransaction(); // Mulai transaksi database

        try {
            \Log::info("Proses order dimulai...");

            // Validasi request
            $request->validate([
                'payment_amount' => 'required',
                'sub_total' => 'required',
                'tax' => 'required',
                'discount' => 'required',
                'discount_amount' => 'required',
                'service_charge' => 'required',
                'total' => 'required',
                'payment_method' => 'required',
                'total_item' => 'required',
                'id_kasir' => 'required',
                'nama_kasir' => 'required',
                'transaction_time' => 'required',
                'customer_name' => 'nullable|string',
                'order_items' => 'required|array',
                'order_items.*.id_product' => 'required|exists:products,id',
                'order_items.*.quantity' => 'required|integer|min:1',
                'order_items.*.price' => 'required|numeric|min:0',
            ]);

            \Log::info("Validasi berhasil!");

            // Cek apakah order sudah ada
            $existingOrder = Order::where('transaction_time', $request->transaction_time)
                                ->where('id_kasir', $request->id_kasir)
                                ->first();

            if ($existingOrder) {
                \Log::warning("Order sudah ada, transaksi dibatalkan.");
                return response()->json([
                    'status' => 'exists',
                    'message' => 'Order already exists, ignoring duplicate entry.',
                    'data' => $existingOrder
                ], 200);
            }

            // Buat order baru
            $order = Order::create([
                'payment_amount' => $request->payment_amount,
                'sub_total' => $request->sub_total,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'discount_amount' => $request->discount_amount,
                'service_charge' => $request->service_charge,
                'total' => $request->total,
                'payment_method' => $request->payment_method,
                'total_item' => $request->total_item,
                'id_kasir' => $request->id_kasir,
                'nama_kasir' => $request->nama_kasir,
                'transaction_time' => $request->transaction_time,
                'customer_name' => $request->customer_name ?? null,
            ]);

            \Log::info("Order berhasil dibuat dengan ID: " . $order->id);

            // Simpan order_items dan kurangi stok produk
            foreach ($request->order_items as $item) {
                $product = Product::findOrFail($item['id_product']);

                // Cek stok produk sebelum dikurangi
                \Log::info("Cek stok produk: " . $product->name . " | Stok saat ini: " . $product->stock);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak cukup untuk produk: " . $product->name);
                }

                // Kurangi stok produk
                $product->decrement('stock', $item['quantity']);
                \Log::info("Stok produk setelah dikurangi: " . $product->stock);

                // Simpan order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            DB::commit(); // Simpan transaksi
            \Log::info("Transaksi berhasil disimpan!");

            return response()->json([
                'status' => 'success',
                'message' => 'Order berhasil dibuat!',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            \Log::error("Transaksi gagal: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function index(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if ($start_date && $end_date) {
            $orders = Order::whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $orders = Order::all();
        }
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }

    public function summary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Order::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalRevenue = $query->sum('payment_amount');
        $totalDiscount = $query->sum('discount_amount');
        $totalTax = $query->sum('tax');
        $totalServiceCharge = $query->sum('service_charge');
        $totalSubtotal = $query->sum('sub_total');
        $total = $totalSubtotal - $totalDiscount - $totalTax + $totalServiceCharge;
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'total_subtotal' => $totalSubtotal,
                'total_service_charge' => $totalServiceCharge,
                'total' => $total,
            ]
        ], 200);
    }
}
