<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('price_override', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_availability');
    }
};
