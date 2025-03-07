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
        // Schema::create('products', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        //     $table->foreignId('product_name_id')->constrained('product_names')->onDelete('cascade'); // Relasi ke product_names
        //     $table->foreignId('product_in_id')->nullable()->constrained('product_ins')->onDelete('set null'); // Sumber stok dari product_ins (opsional)
        //     $table->text('description')->nullable();
        //     $table->string('image')->nullable();
        //     $table->decimal('price', 10, 2); // Harga jual
        //     $table->integer('stock')->default(0); // Stok awal 0, bertambah dari productIns
        //     $table->boolean('status')->default(1);
        //     $table->boolean('is_favorite')->default(0);
        //     $table->timestamps();
        // });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('product_in_id')->nullable()->constrained('product_ins')->onDelete('set null'); // Relasi ke product_in
            $table->string('name'); // Nama produk (diambil dari product_in)
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2); // Harga jual
            $table->integer('stock')->default(0); // Stok dihitung dari product_in
            $table->boolean('status')->default(1);
            $table->boolean('is_favorite')->default(0);
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
