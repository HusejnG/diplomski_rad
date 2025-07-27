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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->onDelete('cascade'); // Povezuje sa zahtevom
            $table->foreignId('designer_id')->constrained('users')->onDelete('cascade'); // Ko je kreirao ponudu (projektant)
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('currency')->default('EUR');
            $table->string('status')->default('draft'); // 'draft', 'sent', 'accepted', 'rejected'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
