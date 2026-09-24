<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug', 280)->unique();
            $table->text('description');
            $table->enum('listing_type', ['sale', 'rent', 'short_rent']);
            $table->decimal('price', 15, 2);
            $table->enum('price_type', ['total', 'per_month', 'per_night', 'per_sqm'])->default('total');
            $table->string('currency', 10)->default('ETB');
            $table->boolean('negotiable')->default(false);
            $table->tinyInteger('bedrooms')->unsigned()->nullable();
            $table->tinyInteger('bathrooms')->unsigned()->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->enum('area_unit', ['sqm', 'sqft', 'hectare'])->default('sqm');
            $table->tinyInteger('floor_number')->nullable();
            $table->tinyInteger('total_floors')->nullable();
            $table->year('year_built')->nullable();
            $table->enum('furnished', ['furnished', 'semi_furnished', 'unfurnished'])->nullable();
            $table->tinyInteger('parking_spaces')->unsigned()->default(0);
            $table->enum('status', ['draft', 'pending', 'active', 'rejected', 'sold', 'rented', 'inactive'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('contact_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'listing_type']);
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
