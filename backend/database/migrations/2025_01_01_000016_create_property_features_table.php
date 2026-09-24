<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('feature'); // has_pool, has_garden, has_gym, etc.
            $table->string('value')->nullable(); // true/false or numeric
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_features');
    }
};
