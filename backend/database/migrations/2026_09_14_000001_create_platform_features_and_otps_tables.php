<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Email OTPs Table
        if (!Schema::hasTable('email_otps')) {
            Schema::create('email_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('otp_code', 10);
                $table->longText('payload')->nullable();
                $table->dateTime('expires_at');
                $table->dateTime('verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Featured property enhancements
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'featured_from')) {
                $table->dateTime('featured_from')->nullable()->after('is_featured');
            }
            if (!Schema::hasColumn('properties', 'featured_until')) {
                $table->dateTime('featured_until')->nullable()->after('featured_from');
            }
            if (!Schema::hasColumn('properties', 'featured_priority')) {
                $table->integer('featured_priority')->default(0)->after('featured_until');
            }
            if (!Schema::hasColumn('properties', 'featured_reason')) {
                $table->string('featured_reason')->nullable()->after('featured_priority');
            }
        });

        // 3. Seller Availabilities Table
        if (!Schema::hasTable('seller_availabilities')) {
            Schema::create('seller_availabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 1=Monday... 6=Saturday
                $table->time('start_time')->default('09:00:00');
                $table->time('end_time')->default('17:00:00');
                $table->integer('slot_duration_minutes')->default(30);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['user_id', 'day_of_week']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_availabilities');

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['featured_from', 'featured_until', 'featured_priority', 'featured_reason']);
        });

        Schema::dropIfExists('email_otps');
    }
};
