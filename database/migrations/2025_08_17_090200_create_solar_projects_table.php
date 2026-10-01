<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solar_projects', function (Blueprint $table) {
            $table->id();

            // Vlasnik projekta (korisnik koji je napravio proračun) i dodijeljeni projektant
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('designer_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('name')->nullable(); // npr. "Kuća - Ilidža"
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Lokacija
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Bosna i Hercegovina');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Površina za ugradnju
            $table->string('surface_type'); // kosi_krov, ravni_krov, krov_zgrade, zemljiste_ravno, zemljiste_brdovito, poljana, ostalo
            $table->decimal('available_area_sqm', 8, 2); // ukupno raspoloživa površina
            $table->decimal('usable_area_sqm', 8, 2)->nullable(); // površina nakon umanjenja za razmake/prepreke
            $table->decimal('tilt_deg', 5, 2)->default(30); // nagib
            $table->decimal('azimuth_deg', 5, 2)->default(0); // 0 = jug, - istok, + zapad (PVGIS konvencija)
            $table->string('shading')->default('nema'); // nema, malo, srednje, mnogo
            $table->string('mounting_place')->default('free'); // 'building' ili 'free' (PVGIS parametar)

            // Potrošnja i cijena struje
            $table->decimal('avg_monthly_consumption_kwh', 8, 2);
            $table->decimal('electricity_price_bam_kwh', 6, 3)->default(0.180);

            // Predloženi/odabrani sistem (dizajnira se automatski, projektant može prilagoditi)
            $table->foreignId('panel_id')->nullable()->constrained('panels')->onDelete('set null');
            $table->unsignedInteger('panel_quantity')->nullable();
            $table->foreignId('inverter_id')->nullable()->constrained('inverters')->onDelete('set null');
            $table->unsignedInteger('inverter_quantity')->nullable();
            $table->decimal('system_power_kwp', 6, 2)->nullable();

            // Rezultati PVGIS proračuna (snapshot)
            $table->decimal('annual_production_kwh', 10, 2)->nullable();
            $table->json('monthly_production')->nullable();

            // Finansijski rezultati (snapshot)
            $table->decimal('equipment_cost_bam', 10, 2)->nullable();
            $table->decimal('installation_cost_bam', 10, 2)->nullable();
            $table->decimal('total_investment_bam', 10, 2)->nullable();
            $table->decimal('self_consumption_percent', 5, 2)->default(65);
            $table->decimal('annual_savings_year1_bam', 10, 2)->nullable();
            $table->decimal('simple_payback_years', 5, 2)->nullable();
            $table->decimal('npv_25y_bam', 10, 2)->nullable();
            $table->json('cashflow_25y')->nullable();

            // Status radnog toka
            // draft -> calculated -> submitted -> under_review -> approved -> scheduled -> completed
            // (ili -> rejected u bilo kojem trenutku prije completed)
            $table->string('status')->default('draft');
            $table->text('customer_notes')->nullable();
            $table->text('designer_notes')->nullable();
            $table->date('installation_scheduled_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solar_projects');
    }
};
