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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del producto
            $table->text('description'); // Descripción del producto
            $table->decimal('price', 10, 2); // Precio con 2 decimales
            $table->string('category'); // Categoría del producto
            $table->string('brand')->nullable(); // Marca del producto
            $table->string('sku')->unique(); // Código único del producto
            $table->integer('stock')->default(0); // Cantidad en inventario
            $table->json('images')->nullable(); // URLs de imágenes del producto
            $table->boolean('is_active')->default(true); // Estado del producto
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que creó el producto
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
