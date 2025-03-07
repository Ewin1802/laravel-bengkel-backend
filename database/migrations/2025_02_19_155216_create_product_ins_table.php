<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_name_id')->nullable()->constrained('product_names')->onDelete('set null'); // Nama bahan harus dari product_names
            $table->decimal('amount', 10, 2); // Harga total transaksi
            $table->timestamp('tanggal_masuk')->useCurrent();
            $table->integer('quantity'); // Jumlah barang yang dibeli
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->boolean('is_received')->default(false); // Barang sudah diterima?
            $table->string('receiver')->nullable(); // Siapa yang menerima barang
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_ins');
    }
};
