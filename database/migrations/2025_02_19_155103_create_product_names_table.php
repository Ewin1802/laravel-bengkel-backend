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
        Schema::create('product_names', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama bahan unik
            $table->string('unit'); // Satuan bahan
            $table->timestamps();

            // Pastikan kombinasi nama_bahan dan satuan unik
            $table->unique(['name', 'unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_names');
    }
};
