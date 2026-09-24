<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Message;
use App\Models\Property;
use App\Models\Review;
use App\Policies\AppointmentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PropertyPolicy;
use App\Policies\ReviewPolicy;
use App\Services\AnalyticsService;
use App\Services\AppointmentService;
use App\Services\ImageService;
use App\Services\NotificationService;
use App\Services\PropertyService;
use App\Services\SearchService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageService::class);
        $this->app->singleton(PropertyService::class);
        $this->app->singleton(SearchService::class);
        $this->app->singleton(AppointmentService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(AnalyticsService::class);
    }

    public function boot(): void
    {
        // Policies
        Gate::policy(Property::class,    PropertyPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Message::class,     MessagePolicy::class);
        Gate::policy(Review::class,      ReviewPolicy::class);

        // Rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Sanctum guard for Spatie
        config(['auth.guards.sanctum' => [
            'driver'   => 'sanctum',
            'provider' => 'users',
        ]]);
    }
}
