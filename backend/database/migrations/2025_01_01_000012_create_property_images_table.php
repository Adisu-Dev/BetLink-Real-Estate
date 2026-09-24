<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('url', 500);
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('cloudinary_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['property_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
