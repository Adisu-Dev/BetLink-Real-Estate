<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('visitor_id');
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('visitor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->enum('type', ['in_person', 'virtual'])->default('in_person');
            $table->text('message')->nullable();
            $table->text('owner_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['visitor_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
