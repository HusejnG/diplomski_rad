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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ko je poslao zahtev
            $table->string('contact_name'); // Ime osobe koja traži ponudu
            $table->string('contact_email'); // Email za kontakt
            $table->string('contact_phone')->nullable(); // Telefon za kontakt

            // Lokacija
            $table->string('address');
            $table->string('city');
            $table->string('country')->default('Bosnia and Herzegovina'); // Pretpostavljena država
            $table->decimal('latitude', 10, 7)->nullable(); // Opciono, za buduću mapu
            $table->decimal('longitude', 10, 7)->nullable(); // Opciono, za buduću mapu

            // Informacije o objektu
            $table->string('roof_type')->nullable(); // Npr. 'kosi', 'ravan', 'drugi'
            $table->decimal('roof_area_sqm', 8, 2)->nullable(); // Površina krova u m²
            $table->decimal('avg_monthly_consumption_kwh', 8, 2); // Prosečna mesečna potrošnja u kWh
            $table->text('notes')->nullable(); // Dodatne napomene korisnika

            // Status zahteva (za projektanta)
            $table->string('status')->default('pending'); // 'pending', 'in_progress', 'completed', 'rejected'

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
