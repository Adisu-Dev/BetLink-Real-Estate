<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Property;
use App\Models\User;
use App\Models\PropertyView;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function adminDashboard(): array
    {
        // Monthly listings trends for last 6 months
        $monthlyListings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Property::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyListings[] = [
                'month' => $date->format('M'),
                'value' => $count,
            ];
        }

        // Users by role (only counting active, non-deleted users)
        $roleCounts = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->whereNull('users.deleted_at')
            ->select('roles.name', DB::raw('count(distinct users.id) as total'))
            ->groupBy('roles.name')
            ->pluck('total', 'roles.name');

        // Recent pending verification requests
        $recentVerifications = \App\Models\VerificationRequest::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($v) => [
                'id'        => $v->id,
                'name'      => $v->user?->name ?? 'User #' . $v->user_id,
                'email'     => $v->user?->email,
                'type'      => ucfirst($v->type ?? 'Identity'),
                'status'    => $v->status,
                'avatar'    => 'https://ui-avatars.com/api/?name=' . urlencode($v->user?->name ?? 'User') . '&background=1e293b&color=fff&size=64',
                'created_at'=> $v->created_at?->diffForHumans() ?? 'Recently',
            ]);

        // Recent fraud reports
        $recentReports = \App\Models\Report::with('reporter')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'reason'      => ucfirst($r->reason ?? 'Flagged'),
                'description' => $r->description ?? 'Report submitted for review',
                'reporter'    => $r->reporter?->name ?? 'Anonymous',
                'status'      => $r->status,
                'time'        => $r->created_at?->diffForHumans() ?? 'Just now',
            ]);

        // Recent appointments
        $recentAppointments = Appointment::with(['property', 'visitor', 'owner'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'id'       => $a->id,
                'property' => $a->property?->title ?? 'Property Viewing',
                'user'     => $a->visitor?->name ?? 'Buyer',
                'owner'    => $a->owner?->name ?? 'Owner',
                'date'     => $a->scheduled_at?->format('M d, Y g:i A') ?? 'Scheduled',
                'status'   => ucfirst($a->status),
            ]);

        return [
            'total_users'               => User::whereDoesntHave('roles', fn($r) => $r->where('name', 'admin'))->count(),
            'total_all_users'           => User::count(),
            'total_properties'          => Property::count(),
            'active_properties'         => Property::where('status', 'active')->count(),
            'pending_properties'        => Property::where('status', 'pending')->count(),
            'pending_verifications'     => \App\Models\VerificationRequest::where('status', 'pending')->count(),
            'pending_reports'           => \App\Models\Report::where('status', 'pending')->count(),
            'total_appointments'        => Appointment::count(),
            'new_users_this_month'      => User::whereMonth('created_at', now()->month)->count(),
            'new_properties_this_month' => Property::whereMonth('created_at', now()->month)->count(),
            'properties_by_type'        => Property::select('property_type_id', DB::raw('count(*) as total'))
                                            ->with('propertyType:id,name')
                                            ->groupBy('property_type_id')->get(),
            'properties_by_status'      => Property::select('status', DB::raw('count(*) as total'))
                                            ->groupBy('status')->pluck('total', 'status'),
            'monthly_listings'          => $monthlyListings,
            'users_by_role'             => [
                'buyers' => (int) ($roleCounts['buyer'] ?? $roleCounts['renter'] ?? 0),
                'owners' => (int) ($roleCounts['owner'] ?? 0),
                'agents' => (int) ($roleCounts['agent'] ?? 0),
                'admins' => (int) ($roleCounts['admin'] ?? 0),
            ],
            'recent_verifications'      => $recentVerifications,
            'recent_reports'            => $recentReports,
            'recent_appointments'       => $recentAppointments,
            'sales_analytics'           => [
                'total_sales_portfolio'  => (float) Property::where('listing_type', 'for_sale')->sum('price'),
                'total_rental_portfolio' => (float) Property::where('listing_type', 'for_rent')->sum('price'),
                'closed_deals_count'     => Property::whereIn('status', ['sold', 'rented'])->count(),
                'active_deals_count'     => Property::where('status', 'active')->count(),
                'avg_sale_price'         => round((float) (Property::where('listing_type', 'for_sale')->avg('price') ?? 0), 2),
                'avg_rental_price'       => round((float) (Property::where('listing_type', 'for_rent')->avg('price') ?? 0), 2),
            ],
        ];
    }

    public function ownerDashboard(int $ownerId): array
    {
        return [
            'total_properties'   => Property::where('user_id', $ownerId)->count(),
            'active_properties'  => Property::where('user_id', $ownerId)->where('status', 'active')->count(),
            'total_views'        => PropertyView::whereHas('property', fn($q) => $q->where('user_id', $ownerId))->count(),
            'total_appointments' => Appointment::where('owner_id', $ownerId)->count(),
            'pending_appointments' => Appointment::where('owner_id', $ownerId)->where('status', 'pending')->count(),
            'total_favorites'    => Property::where('user_id', $ownerId)->sum('favorites_count'),
        ];
    }

    public function propertyViews(int $propertyId, string $period = '30days'): array
    {
        $days = match($period) {
            '7days'  => 7,
            '90days' => 90,
            default  => 30,
        };

        return PropertyView::where('property_id', $propertyId)
            ->where('viewed_at', '>=', now()->subDays($days))
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
