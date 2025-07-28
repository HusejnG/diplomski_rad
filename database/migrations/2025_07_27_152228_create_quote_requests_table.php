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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('contact_name'); 
            $table->string('contact_email'); 
            $table->string('contact_phone')->nullable(); 

            $table->string('address');
            $table->string('city');
            $table->string('country')->default('Bosnia and Herzegovina'); 
            $table->decimal('latitude', 10, 7)->nullable(); 
            $table->decimal('longitude', 10, 7)->nullable(); 

            $table->string('roof_type')->nullable();
            $table->decimal('roof_area_sqm', 8, 2)->nullable(); 
            $table->decimal('avg_monthly_consumption_kwh', 8, 2); 
            $table->text('notes')->nullable(); 

            $table->string('status')->default('pending'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
