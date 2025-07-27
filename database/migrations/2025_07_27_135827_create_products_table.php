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
            $table->string('name');
            $table->string('type')->nullable(); // Npr. 'panel', 'inverter', 'battery', 'cable', 'other'
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable(); // Cena, npr. 1234.50
            $table->string('currency')->default('EUR'); // Valuta
            $table->decimal('power_w', 8, 2)->nullable(); // Snaga u Watima (za panele, invertere)
            $table->decimal('length_mm', 8, 2)->nullable(); // Dužina u milimetrima
            $table->decimal('width_mm', 8, 2)->nullable(); // Širina u milimetrima
            $table->decimal('height_mm', 8, 2)->nullable(); // Visina u milimetrima (za baterije, invertere)
            $table->string('image_path')->nullable(); // Putanja do slike proizvoda
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

