<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historija statusa projekta: svaki prelaz (ko, kada, iz kojeg u koji
     * status i uz koju napomenu) ostaje zapisan i prikazuje se kupcu i
     * projektantu kao vremenska linija.
     */
    public function up(): void
    {
        Schema::create('project_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_project_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_status_changes');
    }
};
