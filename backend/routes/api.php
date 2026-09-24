<?php

use App\Http\Controllers\Api\V1\Admin\AdminAnalyticsController;
use App\Http\Controllers\Api\V1\Admin\AdminPropertyController;
use App\Http\Controllers\Api\V1\Admin\AdminReportController;
use App\Http\Controllers\Api\V1\Admin\AdminSettingsController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Admin\AdminVerificationController;
use App\Http\Controllers\Api\V1\Agent\AgentDashboardController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Blog\BlogController;
use App\Http\Controllers\Api\V1\Booking\BookingController;
use App\Http\Controllers\Api\V1\Buyer\BuyerDashboardController;
use App\Http\Controllers\Api\V1\Buyer\BuyerPropertyController;
use App\Http\Controllers\Api\V1\Buyer\BuyerSearchController;
use App\Http\Controllers\Api\V1\Buyer\BuyerSettingsController;
use App\Http\Controllers\Api\V1\Contact\ContactController;
use App\Http\Controllers\Api\V1\Location\LocationController;
use App\Http\Controllers\Api\V1\Message\ConversationController;
use App\Http\Controllers\Api\V1\Message\MessageController;
use App\Http\Controllers\Api\V1\Owner\OwnerAnalyticsController;
use App\Http\Controllers\Api\V1\Owner\OwnerAppointmentController;
use App\Http\Controllers\Api\V1\Owner\OwnerAvailabilityController;
use App\Http\Controllers\Api\V1\Owner\OwnerDashboardController;
use App\Http\Controllers\Api\V1\Owner\OwnerPropertyController;
use App\Http\Controllers\Api\V1\Owner\OwnerVerificationController;
use App\Http\Controllers\Api\V1\Property\FavoriteController;
use App\Http\Controllers\Api\V1\Property\PropertyController;
use App\Http\Controllers\Api\V1\Property\PropertyImageController;
use App\Http\Controllers\Api\V1\Property\PropertySearchController;
use App\Http\Controllers\Api\V1\Report\ReportController;
use App\Http\Controllers\Api\V1\Report\ReportExportController;
use App\Http\Controllers\Api\V1\Review\ReviewController;
use App\Http\Controllers\Api\V1\User\NotificationController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public Auth ───────────────────────────────────────────────────
    Route::prefix('auth')->middleware('throttle:auth')->group(function () {
        Route::post('register',        [AuthController::class, 'register']);
        Route::post('send-otp',        [AuthController::class, 'sendOtp']);
        Route::post('verify-otp',      [AuthController::class, 'verifyOtp']);
        Route::post('login',           [AuthController::class, 'login']);
        Route::post('forgot-password', [PasswordController::class, 'forgotPassword']);
        Route::post('reset-password',  [PasswordController::class, 'resetPassword']);
    });

    // ── Public Properties & Meta ──────────────────────────────────────
    Route::get('properties',           [PropertyController::class, 'index']);
    Route::get('properties/featured',  [PropertyController::class, 'featured']);
    Route::get('properties/{slug}',    [PropertyController::class, 'show']);
    Route::get('property-types',       [PropertyController::class, 'types']);
    Route::get('categories',           [PropertyController::class, 'categories']);
    Route::get('amenities',            [PropertyController::class, 'amenities']);
    Route::get('agents',               [AgentDashboardController::class, 'publicAgents']);

    // ── Public Search ─────────────────────────────────────────────────
    Route::prefix('search')->group(function () {
        Route::get('/',           [PropertySearchController::class, 'search']);
        Route::get('map',         [PropertySearchController::class, 'mapSearch']);
        Route::get('suggestions', [PropertySearchController::class, 'suggestions']);
    });

    // ── Public Locations ──────────────────────────────────────────────
    Route::prefix('locations')->group(function () {
        Route::get('all',                              [LocationController::class, 'allLocations']);
        Route::get('cities',                           [LocationController::class, 'cities']);
        Route::get('cities/{cityId}/sub-cities',       [LocationController::class, 'subCities']);
        Route::get('sub-cities/{subCityId}/neighborhoods', [LocationController::class, 'neighborhoods']);
    });

    // ── Public Reviews ────────────────────────────────────────────────
    Route::get('properties/{propertyId}/reviews', [ReviewController::class, 'index']);

    // ── Public Blog ───────────────────────────────────────────────────
    Route::prefix('blogs')->group(function () {
        Route::get('/',            [BlogController::class, 'index']);
        Route::get('categories',   [BlogController::class, 'categories']);
        Route::get('{slug}',       [BlogController::class, 'show']);
    });

    // ── Public Contact & FAQ ──────────────────────────────────────────
    Route::post('contact',  [ContactController::class, 'store']);
    Route::get('faqs',      [ContactController::class, 'faqs']);

    // ── Public Booking Availability ───────────────────────────────────
    Route::get('properties/{property}/availability',    [BookingController::class, 'availability']);
    Route::get('properties/{property}/available-slots', [OwnerAvailabilityController::class, 'getAvailableSlots']);

    // ── Authenticated Routes ──────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);
        Route::put('auth/password',[AuthController::class, 'updatePassword']);

        // Profile
        Route::prefix('profile')->group(function () {
            Route::get('/',         [ProfileController::class, 'show']);
            Route::put('/',         [ProfileController::class, 'update']);
            Route::post('avatar',   [ProfileController::class, 'uploadAvatar']);
            Route::delete('/',      [ProfileController::class, 'destroy']);
        });
        Route::get('users/{userId}', [ProfileController::class, 'publicProfile']);

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/',               [NotificationController::class, 'index']);
            Route::get('unread-count',    [NotificationController::class, 'unreadCount']);
            Route::put('{id}/read',       [NotificationController::class, 'markRead']);
            Route::put('read-all',        [NotificationController::class, 'markAllRead']);
        });

        // Favorites
        Route::prefix('favorites')->group(function () {
            Route::get('/',                  [FavoriteController::class, 'index']);
            Route::post('{propertyId}',      [FavoriteController::class, 'store']);
            Route::delete('{propertyId}',    [FavoriteController::class, 'destroy']);
            Route::get('{propertyId}/check', [FavoriteController::class, 'check']);
        });

        // Reviews (authenticated)
        Route::post('properties/{propertyId}/reviews',  [ReviewController::class, 'store']);
        Route::put('reviews/{review}',                  [ReviewController::class, 'update']);
        Route::delete('reviews/{review}',               [ReviewController::class, 'destroy']);
        Route::post('reviews/{review}/response',        [ReviewController::class, 'respond']);

        // Reports
        Route::post('reports', [ReportController::class, 'store']);

        // Appointments
        Route::prefix('appointments')->group(function () {
            Route::get('/',                    [AppointmentController::class, 'index']);
            Route::post('/',                   [AppointmentController::class, 'store']);
            Route::get('{appointment}',        [AppointmentController::class, 'show']);
            Route::put('{appointment}',         [AppointmentController::class, 'update']);
            Route::put('{appointment}/confirm', [AppointmentController::class, 'confirm']);
            Route::put('{appointment}/cancel',  [AppointmentController::class, 'cancel']);
            Route::put('{appointment}/complete',[AppointmentController::class, 'complete']);
            Route::delete('{appointment}',      [AppointmentController::class, 'destroy']);
        });

        // Owner appointments
        Route::prefix('owner/appointments')->group(function () {
            Route::get('/',        [AppointmentController::class, 'ownerAppointments']);
            Route::get('calendar', [AppointmentController::class, 'calendar']);
        });

        // Conversations & Messages
        Route::prefix('conversations')->group(function () {
            Route::get('/',                           [ConversationController::class, 'index']);
            Route::post('/',                          [ConversationController::class, 'store']);
            Route::get('{conversation}',              [ConversationController::class, 'show']);
            Route::put('{conversation}/read',         [ConversationController::class, 'markRead']);
            Route::post('{conversation}/read',        [ConversationController::class, 'markRead']);
            Route::delete('{conversation}',           [ConversationController::class, 'destroy']);
            Route::get('{conversation}/messages',     [MessageController::class, 'index']);
            Route::post('{conversation}/messages',    [MessageController::class, 'store']);
            Route::put('{conversation}/messages/{message}', [MessageController::class, 'update']);
            Route::delete('{conversation}/messages/{message}', [MessageController::class, 'destroy']);
        });

        // Bookings (short rentals)
        Route::prefix('bookings')->group(function () {
            Route::get('/',             [BookingController::class, 'index']);
            Route::post('/',            [BookingController::class, 'store']);
            Route::get('{booking}',     [BookingController::class, 'show']);
            Route::put('{booking}/cancel', [BookingController::class, 'cancel']);
        });

        // Favorites
        Route::prefix('favorites')->group(function () {
            Route::get('/',             [FavoriteController::class, 'index']);
            Route::post('{property}',   [FavoriteController::class, 'store']);
            Route::delete('{property}', [FavoriteController::class, 'destroy']);
            Route::get('{property}/check', [FavoriteController::class, 'check']);
        });

        // Buyer/Universal dashboard, search, properties, appointments, messages, and settings
        Route::middleware(['auth:sanctum', 'role:buyer|renter|buyer_tenant|Buyer / Tenant|buyer / tenant|owner|Owner|Property Owner|agent|admin'])->prefix('buyer')->group(function () {
            Route::get('dashboard',       [BuyerDashboardController::class, 'index']);
            Route::get('search',          [BuyerSearchController::class, 'search']);
            Route::get('properties',      [BuyerPropertyController::class, 'index']);
            Route::get('favorites',       [FavoriteController::class, 'index']);
            Route::post('favorites/{property}', [FavoriteController::class, 'store']);
            Route::delete('favorites/{property}', [FavoriteController::class, 'destroy']);

            // Appointments
            Route::get('appointments',    [AppointmentController::class, 'index']);
            Route::post('appointments',   [AppointmentController::class, 'store']);
            Route::put('appointments/{appointment}', [AppointmentController::class, 'update']);
            Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
            Route::put('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
            Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);

            // Conversations & Messages
            Route::get('conversations',   [ConversationController::class, 'index']);
            Route::post('conversations',  [ConversationController::class, 'store']);
            Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
            Route::put('conversations/{conversation}/read', [ConversationController::class, 'markRead']);
            Route::post('conversations/{conversation}/read', [ConversationController::class, 'markRead']);
            Route::delete('conversations/{conversation}', [ConversationController::class, 'destroy']);
            Route::get('conversations/{conversation}/messages', [MessageController::class, 'index']);
            Route::post('conversations/{conversation}/messages', [MessageController::class, 'store']);
            Route::put('conversations/{conversation}/messages/{message}', [MessageController::class, 'update']);
            Route::delete('conversations/{conversation}/messages/{message}', [MessageController::class, 'destroy']);

            // Settings & Profile
            Route::get('profile',         [BuyerSettingsController::class, 'getProfile']);
            Route::put('profile',         [BuyerSettingsController::class, 'updateProfile']);
            Route::put('password',        [BuyerSettingsController::class, 'updatePassword']);
            Route::get('export-data',     [BuyerSettingsController::class, 'exportData']);
            Route::delete('account',      [BuyerSettingsController::class, 'deleteAccount']);

            // User Complaints & Abuse Reports
            Route::post('reports', [App\Http\Controllers\Api\V1\Report\ReportController::class, 'store']);

            // Reports & Exports
            Route::prefix('reports')->group(function () {
                Route::get('/',            [ReportExportController::class, 'buyerReport']);
                Route::get('analytics',    [ReportExportController::class, 'buyerReport']);
                Route::get('export/pdf',   [ReportExportController::class, 'buyerExportPdf']);
                Route::get('export/excel', [ReportExportController::class, 'buyerExportExcel']);
            });
        });

        // Owner and Agent dashboard, properties, appointments, analytics, and verifications
        Route::prefix('owner')->middleware(['auth:sanctum', 'role:owner|Owner|Property Owner|agent|Agent|buyer|Buyer|buyer_tenant|Buyer / Tenant'])->group(function () {
            Route::get('dashboard',                 [OwnerDashboardController::class, 'index']);

            // Properties
            Route::prefix('properties')->group(function () {
                Route::get('/',                      [OwnerPropertyController::class, 'index']);
                Route::post('/',                     [OwnerPropertyController::class, 'store']);
                Route::post('upload-image',          [OwnerPropertyController::class, 'uploadImage']);
                Route::put('{property}',             [OwnerPropertyController::class, 'update']);
                Route::delete('{property}',          [OwnerPropertyController::class, 'destroy']);
                Route::post('{property}/toggle-status', [OwnerPropertyController::class, 'toggleStatus']);
            });

            // Appointments / Tour Requests
            Route::prefix('appointments')->group(function () {
                Route::get('/',                      [OwnerAppointmentController::class, 'index']);
                Route::patch('{appointment}/approve', [OwnerAppointmentController::class, 'approve']);
                Route::put('{appointment}/approve',   [OwnerAppointmentController::class, 'approve']);
                Route::patch('{appointment}/reject',  [OwnerAppointmentController::class, 'reject']);
                Route::put('{appointment}/reject',    [OwnerAppointmentController::class, 'reject']);
                Route::patch('{appointment}/complete', [OwnerAppointmentController::class, 'complete']);
                Route::put('{appointment}/complete',   [OwnerAppointmentController::class, 'complete']);
            });

            // Seller Availability Schedule
            Route::prefix('availability')->group(function () {
                Route::get('/',                      [OwnerAvailabilityController::class, 'getAvailability']);
                Route::post('/',                     [OwnerAvailabilityController::class, 'saveAvailability']);
            });

            // Analytics
            Route::get('analytics',                 [OwnerAnalyticsController::class, 'index']);

            // Reports & Exports
            Route::prefix('reports')->group(function () {
                Route::get('/',            [ReportExportController::class, 'ownerReport']);
                Route::get('analytics',    [ReportExportController::class, 'ownerReport']);
                Route::get('export/pdf',   [ReportExportController::class, 'ownerExportPdf']);
                Route::get('export/excel', [ReportExportController::class, 'ownerExportExcel']);
            });

            // Verifications
            Route::get('verifications',             [OwnerVerificationController::class, 'index']);
            Route::post('verifications',            [OwnerVerificationController::class, 'store']);
        });

        // Agent dashboard, leads, properties, analytics, verifications
        Route::prefix('agent')->middleware(['auth:sanctum', 'role:agent|admin'])->group(function () {
            Route::get('dashboard',                 [AgentDashboardController::class, 'index']);
            Route::get('leads',                     [AgentDashboardController::class, 'leads']);
            Route::put('leads/{id}/status',          [AgentDashboardController::class, 'updateLeadStatus']);
            Route::get('properties',                [AgentDashboardController::class, 'properties']);
            Route::get('analytics',                 [AgentDashboardController::class, 'analytics']);
            Route::get('verifications',             [AgentDashboardController::class, 'verifications']);
            Route::post('verifications',            [AgentDashboardController::class, 'submitVerification']);

            // Reports & Exports
            Route::prefix('reports')->group(function () {
                Route::get('/',            [ReportExportController::class, 'agentReport']);
                Route::get('analytics',    [ReportExportController::class, 'agentReport']);
                Route::get('export/pdf',   [ReportExportController::class, 'agentExportPdf']);
                Route::get('export/excel', [ReportExportController::class, 'agentExportExcel']);
            });
        });

        // Admin
        Route::prefix('admin')->middleware('role:admin')->group(function () {

            Route::get('dashboard', [AdminAnalyticsController::class, 'dashboard']);

            // Users
            Route::prefix('users')->group(function () {
                Route::get('/',                  [AdminUserController::class, 'index']);
                Route::post('/',                 [AdminUserController::class, 'store']);
                Route::get('{user}',             [AdminUserController::class, 'show']);
                Route::put('{user}/status',      [AdminUserController::class, 'updateStatus']);
                Route::post('{user}/suspend',    [AdminUserController::class, 'suspend']);
                Route::post('{user}/ban',        [AdminUserController::class, 'ban']);
                Route::post('{user}/activate',   [AdminUserController::class, 'activate']);
                Route::delete('{user}',          [AdminUserController::class, 'destroy']);
            });

            // Properties
            Route::prefix('properties')->group(function () {
                Route::get('/',                      [AdminPropertyController::class, 'index']);
                Route::get('{id}',                   [AdminPropertyController::class, 'show']);
                Route::put('{property}/status',      [AdminPropertyController::class, 'updateStatus']);
                Route::post('{property}/approve',    [AdminPropertyController::class, 'approve']);
                Route::post('{property}/reject',     [AdminPropertyController::class, 'reject']);
                Route::post('{property}/feature',    [AdminPropertyController::class, 'feature']);
                Route::delete('{property}',          [AdminPropertyController::class, 'destroy']);
            });

            // Abuse Reports
            Route::get('reports',            [AdminReportController::class, 'index']);
            Route::put('reports/{report}',   [AdminReportController::class, 'update']);

            // System Analytical Reports & Exports
            Route::prefix('analytics-reports')->group(function () {
                Route::get('/',            [ReportExportController::class, 'adminReport']);
                Route::get('analytics',    [ReportExportController::class, 'adminReport']);
                Route::get('export/pdf',   [ReportExportController::class, 'adminExportPdf']);
                Route::get('export/excel', [ReportExportController::class, 'adminExportExcel']);
            });
            Route::get('reports/analytics',    [ReportExportController::class, 'adminReport']);
            Route::get('reports/export/pdf',   [ReportExportController::class, 'adminExportPdf']);
            Route::get('reports/export/excel', [ReportExportController::class, 'adminExportExcel']);

            // Verifications
            Route::get('verifications',                       [AdminVerificationController::class, 'index']);
            Route::post('verifications/{verification}/approve', [AdminVerificationController::class, 'approve']);
            Route::post('verifications/{verification}/reject',  [AdminVerificationController::class, 'reject']);

            // Analytics
            Route::get('analytics',            [AdminAnalyticsController::class, 'dashboard']);
            Route::get('analytics/properties', [AdminAnalyticsController::class, 'properties']);
            Route::get('analytics/users',      [AdminAnalyticsController::class, 'users']);

            // Settings
            Route::get('settings',  [AdminSettingsController::class, 'index']);
            Route::put('settings',  [AdminSettingsController::class, 'update']);
        });
    });
});
