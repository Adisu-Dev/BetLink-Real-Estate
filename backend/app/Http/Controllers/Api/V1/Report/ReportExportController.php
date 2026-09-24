<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\Property;
use App\Models\SavedSearch;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    use ApiResponse;

    /**
     * Apply date/time range filter to a query
     */
    private function applyTimeRange($query, ?string $timeRange, string $column = 'created_at')
    {
        if (!$timeRange || $timeRange === 'all') {
            return $query;
        }

        return match ($timeRange) {
            'today' => $query->whereDate($column, Carbon::today()),
            'week'  => $query->where($column, '>=', Carbon::now()->startOfWeek()),
            'month' => $query->where($column, '>=', Carbon::now()->startOfMonth()),
            'year'  => $query->where($column, '>=', Carbon::now()->startOfYear()),
            default => $query,
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. ADMIN REPORTS & EXPORTS
    // ──────────────────────────────────────────────────────────────────────────

    private function applyDateRange($query, $timeRange, $dateColumn = 'created_at')
    {
        return match ($timeRange) {
            'today' => $query->whereDate($dateColumn, now()->toDateString()),
            'week'  => $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereBetween($dateColumn, [now()->startOfMonth(), now()->endOfMonth()]),
            'year'  => $query->whereBetween($dateColumn, [now()->startOfYear(), now()->endOfYear()]),
            default => $query,
        };
    }

    public function adminReport(Request $request): JsonResponse
    {
        $timeRange = $request->input('time_range', 'all');

        $userQuery = User::whereDoesntHave('roles', fn($r) => $r->where('name', 'admin'));
        $this->applyDateRange($userQuery, $timeRange);
        $totalUsers = (clone $userQuery)->count();

        $propQuery = Property::query();
        $this->applyDateRange($propQuery, $timeRange);
        $totalProperties = (clone $propQuery)->count();
        $activeProperties = (clone $propQuery)->where('status', 'active')->count();
        $pendingProperties = (clone $propQuery)->where('status', 'pending')->count();

        $apptQuery = Appointment::query();
        $this->applyDateRange($apptQuery, $timeRange, 'scheduled_at');
        $totalAppointments = $apptQuery->count();

        $inqQuery = Conversation::query();
        $this->applyDateRange($inqQuery, $timeRange);
        $totalInquiries = $inqQuery->count();

        $payQuery = Payment::query();
        $this->applyDateRange($payQuery, $timeRange);
        $totalRevenue = (float) ($payQuery->sum('amount') ?? 0);

        // Role Breakdown (filtered by time range)
        $roleQuery = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id');
        $this->applyDateRange($roleQuery, $timeRange, 'users.created_at');
        $roleCounts = $roleQuery->select('roles.name', DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->pluck('total', 'roles.name');

        // Recent Properties List
        $recentPropsQuery = Property::with(['propertyType:id,name', 'owner:id,name,email']);
        $this->applyDateRange($recentPropsQuery, $timeRange);
        $recentProperties = $recentPropsQuery->latest()
            ->take(50)
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'title'       => $p->title,
                'type'        => $p->propertyType?->name ?? 'Residential',
                'listing_type'=> ucfirst($p->listing_type ?? 'sale'),
                'price'       => (float) $p->price,
                'status'      => ucfirst($p->status ?? 'active'),
                'owner'       => $p->owner?->name ?? 'Property Owner',
                'views'       => (int) ($p->views_count ?? 0),
                'created_at'  => $p->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ]);

        // Users Directory List (exclude admin)
        $usersListQuery = User::whereDoesntHave('roles', fn($r) => $r->where('name', 'admin'))->with(['roles', 'profile']);
        $this->applyDateRange($usersListQuery, $timeRange);
        $usersList = $usersListQuery->latest()
            ->take(50)
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'phone'      => $u->phone ?? '—',
                'avatar'     => $u->avatar,
                'role'       => ucfirst($u->getRoleNames()->first() ?? 'Buyer'),
                'status'     => ucfirst($u->status ?? 'active'),
                'created_at' => $u->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ]);

        return $this->success([
            'summary' => [
                'totalUsers'        => $totalUsers,
                'totalProperties'   => $totalProperties,
                'activeProperties'  => $activeProperties,
                'pendingProperties' => $pendingProperties,
                'totalAppointments' => $totalAppointments,
                'totalInquiries'    => $totalInquiries,
                'totalRevenue'      => $totalRevenue,
                'activeAgents'      => (int) ($roleCounts['agent'] ?? 0),
            ],
            'usersByRole' => [
                'buyers' => (int) ($roleCounts['buyer'] ?? $roleCounts['renter'] ?? 0),
                'owners' => (int) ($roleCounts['owner'] ?? 0),
                'agents' => (int) ($roleCounts['agent'] ?? 0),
                'admins' => (int) ($roleCounts['admin'] ?? 0),
            ],
            'time_range'       => $timeRange,
            'recentProperties' => $recentProperties,
            'usersList'        => $usersList,
            'generated_at'     => now()->toDateTimeString(),
        ], 'Admin analytical reports retrieved successfully');
    }

    public function adminExportExcel(Request $request): StreamedResponse
    {
        $properties = Property::with(['propertyType', 'user'])->latest()->take(100)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="BetLink_Properties_Table_Report_' . date('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($properties) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Property ID',
                'Title',
                'Category',
                'Listing Type',
                'Price (ETB)',
                'Status',
                'Owner / Landlord',
                'Views',
                'Created Date'
            ]);

            foreach ($properties as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->title,
                    $p->propertyType?->name ?? 'Residential',
                    ucfirst($p->listing_type ?? 'sale'),
                    number_format($p->price, 2),
                    ucfirst($p->status ?? 'active'),
                    $p->user?->name ?? 'N/A',
                    $p->views_count ?? 0,
                    $p->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function adminExportPdf(Request $request)
    {
        $reportData = $this->adminReport($request)->getData(true)['data'];
        $headers = ['Property', 'Category', 'Price (ETB)', 'Owner', 'Views', 'Status'];
        $rows = [];
        foreach ($reportData['recentProperties'] ?? [] as $p) {
            $rows[] = [$p['title'], $p['type'], number_format($p['price']), $p['owner'], $p['views'], $p['status']];
        }

        return $this->generateBinaryPdfResponse('Properties & Listings Table Report', 'Marketplace Inventory Records', $headers, $rows, 'BetLink_Properties_Table_Report_');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. OWNER REPORTS & EXPORTS
    // ──────────────────────────────────────────────────────────────────────────

    public function ownerReport(Request $request): JsonResponse
    {
        $ownerId = $request->user()->id;
        $timeRange = $request->input('time_range', 'all');

        $propQuery = Property::where('user_id', $ownerId)->with(['propertyType', 'primaryImage', 'address.city', 'address.subCity']);
        $this->applyTimeRange($propQuery, $timeRange);
        $properties = $propQuery->latest()->get();

        $totalProperties = $properties->count();
        $activeListings = $properties->where('status', 'active')->count();
        $totalViews = (int) $properties->sum('views_count');

        $apptQuery = Appointment::where('owner_id', $ownerId)->with(['property', 'visitor']);
        $this->applyTimeRange($apptQuery, $timeRange, 'scheduled_at');
        $appointments = $apptQuery->latest('scheduled_at')->get();
        $totalAppointments = $appointments->count();

        $inqQuery = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $ownerId))->with(['property', 'participants']);
        $this->applyTimeRange($inqQuery, $timeRange);
        $inquiries = $inqQuery->latest('updated_at')->get();
        $totalInquiries = $inquiries->count();

        $favQuery = Favorite::whereHas('property', fn($q) => $q->where('user_id', $ownerId))
            ->where('user_id', '!=', $ownerId)
            ->with(['property.propertyType', 'property.primaryImage', 'user']);
        $this->applyTimeRange($favQuery, $timeRange, 'created_at');
        $favorites = $favQuery->latest('created_at')->get();
        $totalFavorites = $favorites->count();

        $totalPortfolioValue = (float) $properties->sum('price');

        $propertyBreakdown = $properties->map(fn($p) => [
            'id'             => $p->id,
            'title'          => $p->title,
            'type'           => $p->propertyType?->name ?? 'Residential',
            'listing_type'   => ucfirst($p->listing_type ?? 'sale'),
            'price'          => (float) $p->price,
            'status'         => ucfirst($p->status ?? 'active'),
            'views'          => (int) ($p->views_count ?? 0),
            'favorites'      => (int) ($p->favorites_count ?? 0),
            'tours_booked'   => Appointment::where('property_id', $p->id)->count(),
            'created_at'     => $p->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'image'          => $p->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
            'location'       => ($p->address?->subCity?->name ? $p->address->subCity->name . ', ' : '') . ($p->address?->city?->name ?? 'Addis Ababa'),
        ]);

        $appointmentsList = $appointments->map(fn($a) => [
            'id'           => $a->id,
            'property'     => $a->property?->title ?? 'Property Tour',
            'visitor'      => $a->visitor?->name ?? 'Prospective Buyer',
            'visitor_phone'=> $a->visitor?->phone ?? '—',
            'type'         => ucfirst($a->type ?? 'in_person'),
            'status'       => ucfirst($a->status ?? 'pending'),
            'scheduled_at' => $a->scheduled_at ? Carbon::parse($a->scheduled_at)->format('Y-m-d H:i') : '—',
        ]);

        $inquiriesList = $inquiries->map(fn($c) => [
            'id'           => $c->id,
            'property'     => $c->property?->title ?? 'General Inquiry',
            'buyer'        => $c->participants->where('id', '!=', $ownerId)->first()?->name ?? 'Prospective Client',
            'updated_at'   => $c->updated_at?->format('Y-m-d H:i') ?? '—',
        ]);

        $favoritesList = $favorites->map(fn($f) => [
            'id'             => $f->id,
            'property_id'    => $f->property_id,
            'property'       => $f->property?->title ?? 'Saved Property',
            'property_type'  => $f->property?->propertyType?->name ?? 'Residential',
            'property_price' => (float) ($f->property?->price ?? 0),
            'property_image' => $f->property?->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
            'user_name'      => $f->user?->name ?? 'Interested Buyer',
            'user_email'     => $f->user?->email ?? '—',
            'user_phone'     => $f->user?->phone ?? '—',
            'saved_at'       => $f->created_at ? Carbon::parse($f->created_at)->format('Y-m-d H:i') : '—',
        ]);

        return $this->success([
            'summary' => [
                'totalProperties'     => $totalProperties,
                'activeListings'      => $activeListings,
                'totalViews'          => $totalViews,
                'totalFavorites'      => $totalFavorites,
                'totalAppointments'   => $totalAppointments,
                'totalInquiries'      => $totalInquiries,
                'totalPortfolioValue' => $totalPortfolioValue,
                'inquiryRate'         => $totalViews > 0 ? round(($totalInquiries / $totalViews) * 100, 1) : 0,
            ],
            'propertyBreakdown' => $propertyBreakdown,
            'appointmentsList'  => $appointmentsList,
            'inquiriesList'     => $inquiriesList,
            'favoritesList'     => $favoritesList,
            'time_range'        => $timeRange,
            'generated_at'      => now()->toDateTimeString(),
        ], 'Owner property performance report retrieved successfully');
    }

    public function ownerExportExcel(Request $request): StreamedResponse
    {
        $reportData = $this->ownerReport($request)->getData(true)['data'];
        $category = $request->input('category', 'properties');

        if ($category === 'appointments' || $category === 'tours') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BetLink_Owner_Tours_Report_' . date('Ymd_His') . '.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            return response()->stream(function () use ($reportData) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['Tour ID', 'Property Title', 'Client Name', 'Contact Phone', 'Format', 'Scheduled Time', 'Status']);

                foreach ($reportData['appointmentsList'] ?? [] as $a) {
                    fputcsv($handle, [
                        $a['id'],
                        $a['property'],
                        $a['visitor'],
                        $a['visitor_phone'],
                        $a['type'],
                        $a['scheduled_at'],
                        $a['status'],
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        if ($category === 'inquiries') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BetLink_Owner_Inquiries_Report_' . date('Ymd_His') . '.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            return response()->stream(function () use ($reportData) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['Inquiry ID', 'Property Title', 'Client Name', 'Last Updated Date']);

                foreach ($reportData['inquiriesList'] ?? [] as $inq) {
                    fputcsv($handle, [
                        $inq['id'],
                        $inq['property'],
                        $inq['buyer'],
                        $inq['updated_at'],
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        if ($category === 'favorites' || $category === 'saved') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BetLink_Owner_Saved_Properties_Report_' . date('Ymd_His') . '.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            return response()->stream(function () use ($reportData) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['Save ID', 'Property Title', 'Property Type', 'Price (ETB)', 'Saved By Client', 'Client Email', 'Client Phone', 'Date Saved']);

                foreach ($reportData['favoritesList'] ?? [] as $f) {
                    fputcsv($handle, [
                        $f['id'],
                        $f['property'],
                        $f['property_type'],
                        number_format($f['property_price'], 2),
                        $f['user_name'],
                        $f['user_email'],
                        $f['user_phone'],
                        $f['saved_at'],
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        // Default: properties
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="BetLink_Owner_Properties_Report_' . date('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($reportData) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Property ID',
                'Title',
                'Category',
                'Listing Type',
                'Price (ETB)',
                'Status',
                'Views',
                'Favorites',
                'Tours Booked',
                'Listing Date'
            ]);

            foreach ($reportData['propertyBreakdown'] ?? [] as $p) {
                fputcsv($handle, [
                    $p['id'],
                    $p['title'],
                    $p['type'],
                    $p['listing_type'],
                    number_format($p['price'], 2),
                    $p['status'],
                    $p['views'],
                    $p['favorites'],
                    $p['tours_booked'],
                    $p['created_at'],
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function ownerExportPdf(Request $request)
    {
        $reportData = $this->ownerReport($request)->getData(true)['data'];
        $category = $request->input('category', 'properties');

        if ($category === 'appointments' || $category === 'tours') {
            $headers = ['No', 'Property', 'Visitor / Client', 'Contact', 'Format', 'Date & Time', 'Status'];
            $rows = [];
            foreach (($reportData['appointmentsList'] ?? []) as $idx => $a) {
                $rows[] = [$idx + 1, $a['property'], $a['visitor'], $a['visitor_phone'], $a['type'], $a['scheduled_at'], $a['status']];
            }
            return $this->generateBinaryPdfResponse('Property Tour Bookings Report', 'Scheduled Client Viewings Table Records', $headers, $rows, 'BetLink_Owner_Tours_Report_');
        }

        if ($category === 'inquiries') {
            $headers = ['No', 'Property', 'Client / Buyer', 'Last Activity Date'];
            $rows = [];
            foreach (($reportData['inquiriesList'] ?? []) as $idx => $inq) {
                $rows[] = [$idx + 1, $inq['property'], $inq['buyer'], $inq['updated_at']];
            }
            return $this->generateBinaryPdfResponse('Property Client Inquiries Report', 'Lead Communications & Inquiry Activity Table', $headers, $rows, 'BetLink_Owner_Inquiries_Report_');
        }

        if ($category === 'favorites' || $category === 'saved') {
            $headers = ['No', 'Property', 'Type', 'Price (ETB)', 'Saved By Client', 'Date Saved'];
            $rows = [];
            foreach (($reportData['favoritesList'] ?? []) as $idx => $f) {
                $dateStr = isset($f['saved_at']) ? substr($f['saved_at'], 0, 10) : '—';
                $rows[] = [$idx + 1, $f['property'], $f['property_type'], number_format($f['property_price']), $f['user_name'], $dateStr];
            }
            return $this->generateBinaryPdfResponse('Property Favorites & Saved Interest Report', 'Interested Buyers Who Bookmarked Properties', $headers, $rows, 'BetLink_Owner_Saved_Properties_Report_');
        }

        // Default: properties
        $headers = ['No', 'Property', 'Type', 'Price (ETB)', 'Views', 'Saves', 'Tours', 'Status'];
        $rows = [];
        foreach (($reportData['propertyBreakdown'] ?? []) as $idx => $p) {
            $rows[] = [$idx + 1, $p['title'], $p['type'], number_format($p['price']), $p['views'], $p['favorites'], $p['tours_booked'], $p['status']];
        }

        return $this->generateBinaryPdfResponse('Property Owner Listings Report', 'Personal Portfolio Table Records', $headers, $rows, 'BetLink_Owner_Properties_Report_');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. BUYER / TENANT REPORTS & EXPORTS
    // ──────────────────────────────────────────────────────────────────────────

    public function buyerReport(Request $request): JsonResponse
    {
        $buyerId = $request->user()->id;

        $favoritesCount = Favorite::where('user_id', $buyerId)->count();
        $appointments = Appointment::where('visitor_id', $buyerId)
            ->with(['property.primaryImage', 'property.address.city', 'property.address.subCity', 'owner'])
            ->latest('scheduled_at')
            ->get();

        $totalAppointments = $appointments->count();
        $confirmedAppointments = $appointments->where('status', 'confirmed')->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $totalChats = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $buyerId))->count();
        $savedSearchesCount = SavedSearch::where('user_id', $buyerId)->count();

        $toursHistory = $appointments->map(fn($a) => [
            'id'             => $a->id,
            'property_id'    => $a->property_id,
            'property'       => $a->property?->title ?? 'Property Viewing',
            'property_image' => $a->property?->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
            'location'       => ($a->property?->address?->subCity?->name ? $a->property->address->subCity->name . ', ' : '') . ($a->property?->address?->city?->name ?? 'Addis Ababa'),
            'price'          => (float) ($a->property?->price ?? 0),
            'listing_type'   => ucfirst($a->property?->listing_type ?? 'sale'),
            'host'           => $a->owner?->name ?? 'Property Host',
            'host_phone'     => $a->owner?->phone ?? '+251 91 100 0000',
            'scheduled_at'   => $a->scheduled_at?->format('Y-m-d H:i') ?? 'TBD',
            'status'         => ucfirst($a->status ?? 'pending'),
            'type'           => ucfirst($a->type ?? 'in_person'),
        ]);

        // Live Saved Favorites
        $favorites = Favorite::where('user_id', $buyerId)
            ->with(['property.primaryImage', 'property.address.city', 'property.address.subCity', 'property.propertyType'])
            ->latest()
            ->get()
            ->map(fn($f) => [
                'id'          => $f->property?->id ?? $f->id,
                'title'       => $f->property?->title ?? 'Saved Property',
                'image'       => $f->property?->primaryImage?->url ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80',
                'location'    => ($f->property?->address?->subCity?->name ? $f->property->address->subCity->name . ', ' : '') . ($f->property?->address?->city?->name ?? 'Addis Ababa'),
                'price'       => (float) ($f->property?->price ?? 0),
                'listing_type'=> ucfirst($f->property?->listing_type ?? 'sale'),
                'bedrooms'    => $f->property?->bedrooms ?? 0,
                'bathrooms'   => $f->property?->bathrooms ?? 0,
                'status'      => ucfirst($f->property?->status ?? 'active'),
                'saved_at'    => $f->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ]);

        // Live Inquiries / Conversations
        $conversations = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $buyerId))
            ->with(['property.primaryImage', 'participants', 'messages' => fn($q) => $q->latest()->take(1)])
            ->latest('updated_at')
            ->get()
            ->map(function ($c) use ($buyerId) {
                $otherUser = $c->participants->firstWhere('id', '!=', $buyerId);
                $lastMsg = $c->messages->first();
                return [
                    'id'            => $c->id,
                    'property'      => $c->property?->title ?? 'General Inquiry',
                    'contact_name'  => $otherUser?->name ?? 'Property Landlord',
                    'contact_email' => $otherUser?->email ?? '—',
                    'last_message'  => $lastMsg?->body ?? 'Conversation started',
                    'updated_at'    => $c->updated_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
                ];
            });

        // Live Saved Searches / Alerts
        $savedSearches = SavedSearch::where('user_id', $buyerId)
            ->latest()
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'name'          => $s->name,
                'filters'       => is_array($s->filters) ? $s->filters : json_decode($s->filters, true),
                'alert_enabled' => (bool)$s->alert_enabled,
                'created_at'    => $s->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ]);

        return $this->success([
            'summary' => [
                'savedFavorites'        => $favoritesCount,
                'totalAppointments'     => $totalAppointments,
                'confirmedAppointments' => $confirmedAppointments,
                'completedAppointments' => $completedAppointments,
                'activeConversations'   => $totalChats,
                'savedSearches'         => $savedSearchesCount,
            ],
            'toursHistory'   => $toursHistory,
            'favorites'      => $favorites,
            'conversations'  => $conversations,
            'saved_searches' => $savedSearches,
            'generated_at'   => now()->toDateTimeString(),
        ], 'Buyer activity report retrieved successfully');
    }

    public function buyerExportExcel(Request $request): StreamedResponse
    {
        $buyerId = $request->user()->id;
        $appointments = Appointment::where('visitor_id', $buyerId)->with(['property', 'owner'])->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="BetLink_Tour_Appointments_Report_' . date('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($appointments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Appointment ID',
                'Property Title',
                'Host / Landlord',
                'Tour Date & Time',
                'Tour Type',
                'Status'
            ]);

            foreach ($appointments as $a) {
                fputcsv($handle, [
                    $a->id,
                    $a->property?->title ?? 'N/A',
                    $a->owner?->name ?? 'N/A',
                    $a->scheduled_at?->format('Y-m-d H:i') ?? '',
                    ucfirst($a->type ?? 'In-Person'),
                    ucfirst($a->status ?? 'Pending'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function buyerExportPdf(Request $request)
    {
        $reportData = $this->buyerReport($request)->getData(true)['data'];
        $headers = ['Property', 'Host / Landlord', 'Date & Time', 'Format', 'Status'];
        $rows = [];
        foreach ($reportData['toursHistory'] ?? [] as $t) {
            $rows[] = [$t['property'], $t['host'], $t['scheduled_at'], $t['type'], $t['status']];
        }

        return $this->generateBinaryPdfResponse('Tour Bookings & Activity Report', 'Personal Property Viewings Table', $headers, $rows, 'BetLink_Tour_Appointments_Report_');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 4. AGENT REPORTS & EXPORTS
    // ──────────────────────────────────────────────────────────────────────────

    public function agentReport(Request $request): JsonResponse
    {
        $agentId = $request->user()->id;
        $agentPropertyIds = Property::where('user_id', $agentId)->pluck('id');

        $assignedProperties = Property::where('user_id', $agentId)->count();
        $activeListings = Property::where('user_id', $agentId)->where('status', 'active')->count();

        // 1. Live Leads from Inquiries / Conversations strictly for this Agent's properties
        $conversations = Conversation::with(['participants', 'property', 'messages' => fn($q) => $q->latest()])
            ->whereIn('property_id', $agentPropertyIds)
            ->latest()
            ->get();

        $leadsBreakdown = $conversations->map(function ($conv) use ($agentId) {
            $client = $conv->participants->firstWhere('id', '!=', $agentId) ?? $conv->participants->first();
            $stage = 'Hot Lead';
            if ($conv->messages->count() > 3) {
                $stage = 'Tour Scheduled';
            } elseif ($conv->messages->count() > 1) {
                $stage = 'Contacted';
            }

            return [
                'id'           => $conv->id,
                'client'       => $client?->name ?? 'Prospective Client',
                'property'     => $conv->property?->title ?? 'Property Inquiry',
                'listing_type' => ucfirst($conv->property?->listing_type ?? 'sale'),
                'price'        => (float) ($conv->property?->price ?? 0),
                'status'       => $stage,
                'date'         => $conv->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ];
        })->values()->all();

        $totalLeads = count($leadsBreakdown);

        // 2. Real Tour Requests & Closed Deals
        $tourRequests = Appointment::where(function ($q) use ($agentId, $agentPropertyIds) {
            $q->where('owner_id', $agentId)
              ->orWhere('visitor_id', $agentId)
              ->orWhereIn('property_id', $agentPropertyIds);
        })->count();

        $dealsClosed = Appointment::where(function ($q) use ($agentId, $agentPropertyIds) {
            $q->where('owner_id', $agentId)
              ->orWhere('visitor_id', $agentId)
              ->orWhereIn('property_id', $agentPropertyIds);
        })->where('status', 'completed')->count();

        $estimatedCommissions = $dealsClosed * 25000;

        // 3. Properties Breakdown
        $propertyBreakdown = Property::with(['propertyType', 'address.city', 'address.subCity'])
            ->where('user_id', $agentId)
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'title'        => $p->title,
                'type'         => $p->propertyType?->name ?? 'Residential',
                'listing_type' => ucfirst($p->listing_type ?? 'sale'),
                'price'        => (float) ($p->price ?? 0),
                'views'        => (int) ($p->views_count ?? $p->views ?? 0),
                'status'       => ucfirst($p->status ?? 'active'),
                'created_at'   => $p->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            ])->values()->all();

        // 4. Appointments List
        $appointmentsList = Appointment::with(['property', 'visitor'])
            ->where(function ($q) use ($agentId, $agentPropertyIds) {
                $q->where('owner_id', $agentId)
                  ->orWhere('visitor_id', $agentId)
                  ->orWhereIn('property_id', $agentPropertyIds);
            })
            ->latest('scheduled_at')
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'property'      => $a->property?->title ?? 'Property Viewing',
                'visitor'       => $a->visitor?->name ?? 'Client',
                'visitor_phone' => $a->visitor?->phone ?? '—',
                'type'          => ucfirst($a->type ?? 'in_person'),
                'scheduled_at'  => $a->scheduled_at?->format('Y-m-d H:i') ?? 'TBD',
                'status'        => ucfirst($a->status ?? 'pending'),
            ])->values()->all();

        return $this->success([
            'summary' => [
                'assignedProperties'   => $assignedProperties,
                'activeListings'       => $activeListings,
                'totalLeads'           => $totalLeads,
                'tourRequests'         => $tourRequests,
                'dealsClosed'          => $dealsClosed,
                'estimatedCommissions' => $estimatedCommissions,
                'conversionRate'       => $totalLeads > 0 ? round(($dealsClosed / $totalLeads) * 100, 1) : 0,
            ],
            'propertyBreakdown' => $propertyBreakdown,
            'appointmentsList'  => $appointmentsList,
            'leadsBreakdown'    => $leadsBreakdown,
            'generated_at'      => now()->toDateTimeString(),
        ], 'Agent performance report retrieved successfully');
    }

    public function agentExportExcel(Request $request): StreamedResponse
    {
        $reportData = $this->agentReport($request)->getData(true)['data'];
        $category = $request->input('category', 'leads');

        if ($category === 'properties' || $category === 'listings') {
            $properties = $reportData['propertyBreakdown'] ?? [];
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BetLink_Agent_Managed_Listings_Report_' . date('Ymd_His') . '.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            return response()->stream(function () use ($properties) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['No', 'Property ID', 'Title', 'Category', 'Listing Type', 'Price (ETB)', 'Views', 'Status', 'Date Listed']);
                foreach ($properties as $idx => $p) {
                    fputcsv($handle, [
                        $idx + 1,
                        $p['id'],
                        $p['title'],
                        $p['type'],
                        $p['listing_type'],
                        number_format($p['price'], 2),
                        $p['views'],
                        $p['status'],
                        $p['created_at']
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        if ($category === 'appointments' || $category === 'tours') {
            $tours = $reportData['appointmentsList'] ?? [];
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BetLink_Agent_Tours_Report_' . date('Ymd_His') . '.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            return response()->stream(function () use ($tours) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['No', 'Tour ID', 'Property Title', 'Client Name', 'Client Phone', 'Format', 'Scheduled At', 'Status']);
                foreach ($tours as $idx => $t) {
                    fputcsv($handle, [
                        $idx + 1,
                        $t['id'],
                        $t['property'],
                        $t['visitor'],
                        $t['visitor_phone'],
                        $t['type'],
                        $t['scheduled_at'],
                        $t['status']
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        // Default: leads
        $leads = $reportData['leadsBreakdown'] ?? [];
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="BetLink_Agent_Leads_Report_' . date('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['No', 'Lead ID', 'Client Name', 'Inquired Property', 'Listing Type', 'Price (ETB)', 'Pipeline Status', 'Date']);
            if (empty($leads)) {
                fputcsv($handle, ['1', '—', 'No client leads recorded', '—', '—', '—', '—', date('Y-m-d')]);
            } else {
                foreach ($leads as $idx => $l) {
                    fputcsv($handle, [
                        $idx + 1,
                        $l['id'] ?? ($idx + 1),
                        $l['client'] ?? 'Client',
                        $l['property'] ?? 'Property',
                        $l['listing_type'] ?? 'Sale',
                        number_format($l['price'] ?? 0, 2),
                        $l['status'] ?? 'Active',
                        $l['date'] ?? date('Y-m-d')
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function agentExportPdf(Request $request)
    {
        $reportData = $this->agentReport($request)->getData(true)['data'];
        $category = $request->input('category', 'leads');

        if ($category === 'properties' || $category === 'listings') {
            $headers = ['No', 'Property Title', 'Type', 'Price (ETB)', 'Views', 'Status'];
            $rows = [];
            foreach ($reportData['propertyBreakdown'] ?? [] as $idx => $p) {
                $rows[] = [$idx + 1, $p['title'], $p['type'], number_format($p['price']), $p['views'], $p['status']];
            }
            if (empty($rows)) $rows[] = ['1', 'No properties recorded', '—', '0', '0', 'Active'];
            return $this->generateBinaryPdfResponse('Agent Managed Listings Report', 'Active & Managed Property Portfolio', $headers, $rows, 'BetLink_Agent_Managed_Listings_Report_');
        }

        if ($category === 'appointments' || $category === 'tours') {
            $headers = ['No', 'Property', 'Client Name', 'Format', 'Scheduled Time', 'Status'];
            $rows = [];
            foreach ($reportData['appointmentsList'] ?? [] as $idx => $t) {
                $rows[] = [$idx + 1, $t['property'], $t['visitor'], $t['type'], $t['scheduled_at'], $t['status']];
            }
            if (empty($rows)) $rows[] = ['1', 'No scheduled tours', '—', '—', date('Y-m-d'), 'Completed'];
            return $this->generateBinaryPdfResponse('Agent Tour Bookings Report', 'Client Property Viewings & Appointments', $headers, $rows, 'BetLink_Agent_Tours_Report_');
        }

        $headers = ['No', 'Client Name', 'Inquired Property', 'Type', 'Pipeline Status', 'Date'];
        $rows = [];
        foreach ($reportData['leadsBreakdown'] ?? [] as $idx => $l) {
            $rows[] = [$idx + 1, $l['client'], $l['property'], $l['listing_type'] ?? 'Sale', $l['status'], $l['date']];
        }

        if (empty($rows)) {
            $rows[] = ['1', 'No client leads recorded yet', '—', '—', '—', date('Y-m-d')];
        }

        return $this->generateBinaryPdfResponse('Agent Client Leads Pipeline Report', 'Inquiries & Client Activity Table', $headers, $rows, 'BetLink_Agent_Leads_Report_');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helper: Valid PDF-1.4 Binary Generator (Clean Light Aesthetic & Proportional Columns)
    // ──────────────────────────────────────────────────────────────────────────

    private function getColumnWidths(array $headers): array
    {
        $count = count($headers);
        if ($count === 8) { // Properties: No, Property, Type, Price, Views, Saves, Tours, Status
            return [25, 175, 65, 80, 40, 40, 40, 50];
        }
        if ($count === 7) { // Appointments: No, Property, Visitor, Contact, Format, Date & Time, Status
            return [25, 135, 95, 75, 55, 80, 50];
        }
        if ($count === 6) { // Favorites / Saved: No, Property, Type, Price, Saved By Client, Date Saved
            return [25, 180, 60, 75, 105, 70];
        }
        if ($count === 5) { // Buyer: No, Property, Host, Date & Time, Status
            return [25, 185, 115, 110, 80];
        }
        if ($count === 4) { // Inquiries: No, Property, Client, Date
            return [30, 225, 140, 120];
        }

        $w = (int) floor(515 / max(1, $count));
        return array_fill(0, $count, $w);
    }

    private function generateBinaryPdfResponse(string $title, string $subtitle, array $headers, array $rows, string $filePrefix)
    {
        $escape = function ($str) {
            return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $str);
        };

        $safeChars = function ($text, $colWidth) {
            $maxLen = max(4, (int) floor(($colWidth - 4) / 3.9));
            $str = (string) $text;
            if (mb_strlen($str) > $maxLen) {
                return mb_substr($str, 0, $maxLen - 2) . '..';
            }
            return $str;
        };

        $colWidths = $this->getColumnWidths($headers);
        $colStarts = [];
        $curX = 40;
        foreach ($colWidths as $w) {
            $colStarts[] = $curX;
            $curX += $w;
        }

        // 1. Clean Light Header (No solid dark banner)
        $stream = "";
        
        // Brand Title
        $stream .= "BT\n/F1 16 Tf\n0.06 0.09 0.16 rg\n40 808 Td\n(BetLink Real Estate) Tj\nET\n";
        
        // Report Title
        $stream .= "BT\n/F1 11 Tf\n0.18 0.23 0.32 rg\n40 790 Td\n(" . $escape($title) . ") Tj\nET\n";
        
        // Subtitle & Export timestamp
        $stream .= "BT\n/F2 8 Tf\n0.45 0.52 0.62 rg\n40 774 Td\n(" . $escape($subtitle . "  |  Exported: " . now()->format('M d, Y H:i')) . ") Tj\nET\n";

        // Thin Header Accent Divider Line
        $stream .= "q\n0.82 0.86 0.92 RG\n0.75 w\n40 762 m 555 762 l S\nQ\n";

        // 2. Table Headers (Light Slate Pill Background)
        $tableY = 738;
        if (!empty($headers)) {
            $stream .= "q\n0.94 0.96 0.98 rg\n40 " . ($tableY - 4) . " 515 20 re\nf\nQ\n";
            $stream .= "q\n0.82 0.86 0.92 RG\n0.5 w\n40 " . ($tableY - 4) . " 515 20 re\nS\nQ\n";
            
            foreach ($headers as $idx => $h) {
                $xPos = $colStarts[$idx] + 4;
                $stream .= "BT\n/F1 7.5 Tf\n0.2 0.25 0.35 rg\n" . $xPos . " " . ($tableY + 3) . " Td\n(" . $escape(strtoupper($h)) . ") Tj\nET\n";
            }
            $tableY -= 20;
        }

        // 3. Table Rows (Crisp typography with zero collision)
        if (!empty($rows)) {
            foreach (array_slice($rows, 0, 34) as $rIdx => $row) {
                // Subtle bottom border
                $stream .= "q\n0.9 0.92 0.95 RG\n0.5 w\n40 " . ($tableY - 3) . " m 555 " . ($tableY - 3) . " l S\nQ\n";

                foreach ($row as $cIdx => $cell) {
                    $w = $colWidths[$cIdx] ?? 60;
                    $cellText = $safeChars($cell, $w);
                    $font = $cIdx === 0 ? '/F1 7.5 Tf' : '/F2 7.5 Tf';
                    $xPos = $colStarts[$cIdx] + 4;
                    $stream .= "BT\n{$font}\n0.1 0.15 0.22 rg\n" . $xPos . " " . ($tableY + 3) . " Td\n(" . $escape($cellText) . ") Tj\nET\n";
                }
                $tableY -= 19;
            }
        }

        // 4. Footer
        $stream .= "q\n0.85 0.88 0.92 RG\n0.5 w\n40 45 m 555 45 l S\nQ\n";
        $stream .= "BT\n/F2 7.5 Tf\n0.5 0.55 0.62 rg\n40 30 Td\n(BetLink Official Platform Audit Report  |  Document ID: " . strtoupper(substr(md5(now()), 0, 8)) . "  |  Page 1 of 1) Tj\nET\n";

        // 5. Build Valid PDF-1.4 Binary
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        $addObj = function ($content) use (&$pdf, &$offsets) {
            $offsets[] = strlen($pdf);
            $pdf .= (count($offsets)) . " 0 obj\n" . $content . "\nendobj\n";
        };

        $addObj("<< /Type /Catalog /Pages 2 0 R >>");
        $addObj("<< /Type /Pages /Kids [3 0 R] /Count 1 >>");
        $addObj("<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>");
        $addObj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>");
        $addObj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>");
        $addObj("<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream");

        $startXref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($offsets) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size " . (count($offsets) + 1) . " /Root 1 0 R >>\nstartxref\n{$startXref}\n%%EOF";

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filePrefix . date('Ymd_His') . '.pdf"',
            'Content-Length'      => strlen($pdf),
        ]);
    }
}
