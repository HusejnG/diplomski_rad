<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('model');
            $table->string('technology')->default('monokristalni'); // monokristalni, polikristalni
            $table->unsignedInteger('power_w'); // nazivna snaga panela u W
            $table->decimal('efficiency_percent', 5, 2)->nullable();
            $table->unsignedInteger('length_mm')->default(1900);
            $table->unsignedInteger('width_mm')->default(1100);
            $table->decimal('price_bam', 10, 2); // orijentaciona cijena po komadu u BAM
            $table->unsignedTinyInteger('warranty_years')->default(12);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panels');
    }
};
