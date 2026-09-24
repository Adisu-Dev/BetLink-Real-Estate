<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('guest_id');
            $table->foreign('guest_id')->references('id')->on('users')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('nights');
            $table->tinyInteger('guests_count')->unsigned()->default(1);
            $table->decimal('total_price', 15, 2);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->enum('status', ['pending','confirmed','checked_in','completed','cancelled'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_rental_bookings');
    }
};
