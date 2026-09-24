<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OwnerAnalyticsController extends Controller
{
    use ApiResponse;

    /**
     * Get owner portfolio analytics & performance
     */
    public function index(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id;

        if ($request->has('refresh') || $request->has('force')) {
            \Illuminate\Support\Facades\Cache::forget("owner_analytics_{$ownerId}");
        }

        $data = \Illuminate\Support\Facades\Cache::remember("owner_analytics_{$ownerId}", 15, function () use ($ownerId) {
            $propStats = \Illuminate\Support\Facades\DB::table('properties')
                ->where('user_id', $ownerId)
                ->selectRaw("
                    COUNT(*) as total,
                    COALESCE(SUM(views_count), 0) as total_views,
                    COALESCE(SUM(favorites_count), 0) as total_favorites
                ")
                ->first();

            $totalProperties = (int) ($propStats->total ?? 0);
            $totalViews = (int) ($propStats->total_views ?? 0);
            $totalFavorites = (int) \Illuminate\Support\Facades\DB::table('favorites')
                ->join('properties', 'favorites.property_id', '=', 'properties.id')
                ->where('properties.user_id', $ownerId)
                ->count();
            $totalTours = Appointment::where('owner_id', $ownerId)->count();
            $totalInquiries = \Illuminate\Support\Facades\DB::table('conversation_participants')->where('user_id', $ownerId)->count();

            // Top performing properties
            $topListings = Property::where('user_id', $ownerId)
                ->with(['primaryImage', 'address.city', 'address.subCity'])
                ->withCount('favorites')
                ->orderByDesc('views_count')
                ->orderByDesc('favorites_count')
                ->latest('id')
                ->take(5)
                ->get()
                ->map(function ($p) {
                    return [
                        'id'              => $p->id,
                        'title'           => $p->title,
                        'views_count'     => (int) ($p->views_count ?? 0),
                        'favorites_count' => (int) ($p->favorites_count ?? 0),
                        'price'           => (float) $p->price,
                        'image'           => $p->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
                        'location'        => ($p->address?->subCity?->name ? $p->address->subCity->name . ', ' : '') . ($p->address?->city?->name ?? 'Addis Ababa'),
                    ];
                });

            // 6 Months Monthly Trends dynamically computed
            $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
            $inquiriesByMonth = \Illuminate\Support\Facades\DB::table('conversation_participants')
                ->join('conversations', 'conversations.id', '=', 'conversation_participants.conversation_id')
                ->where('conversation_participants.user_id', $ownerId)
                ->where('conversations.created_at', '>=', $sixMonthsAgo)
                ->selectRaw("DATE_FORMAT(conversations.created_at, '%Y-%m') as ym, COUNT(*) as count")
                ->groupBy('ym')
                ->pluck('count', 'ym');

            $viewsTrends = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthDate = Carbon::now()->subMonths($i);
                $ym = $monthDate->format('Y-m');
                $viewsTrends[] = [
                    'month'     => $monthDate->format('M'),
                    'views'     => $i === 0 ? $totalViews : 0,
                    'inquiries' => (int) ($inquiriesByMonth[$ym] ?? 0),
                ];
            }

            return [
                'metrics' => [
                    'totalProperties' => $totalProperties,
                    'totalViews'      => $totalViews,
                    'totalFavorites'  => $totalFavorites,
                    'totalTours'      => $totalTours,
                    'totalInquiries'  => $totalInquiries,
                    'inquiryRate'     => $totalViews > 0 ? round(($totalInquiries / $totalViews) * 100, 1) : 0,
                ],
                'viewsTrends'  => $viewsTrends,
                'topListings'  => $topListings,
            ];
        });

        return $this->success($data, 'Owner analytics retrieved successfully');
    }
}
