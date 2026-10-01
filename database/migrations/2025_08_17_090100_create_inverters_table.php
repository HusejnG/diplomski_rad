<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inverters', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('model');
            $table->string('type')->default('string'); // string, hybrid, mikroinverter
            $table->decimal('rated_power_kw', 6, 2); // nazivna AC snaga
            $table->decimal('max_pv_power_kw', 6, 2); // preporučena maks. snaga DC (panela) na invertor
            $table->unsignedTinyInteger('phases')->default(1); // 1 ili 3
            $table->decimal('price_bam', 10, 2);
            $table->unsignedTinyInteger('warranty_years')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inverters');
    }
};
