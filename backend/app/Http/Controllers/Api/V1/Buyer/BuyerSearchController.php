<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuyerSearchController extends Controller
{
    use ApiResponse;

    /**
     * Real-time global search for buyer dashboard across Properties, Appointments, and Messages.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = trim((string)($request->input('q') ?? $request->input('query') ?? ''));

        if ($query === '') {
            return $this->success([
                'properties'   => [],
                'appointments' => [],
                'messages'     => [],
                'total'        => 0,
            ], 'Query is empty');
        }

        $results = [
            'properties'   => [],
            'appointments' => [],
            'messages'     => [],
        ];

        // 1. Search Properties
        try {
            $properties = Property::with(['primaryImage', 'propertyType', 'category', 'address'])
                ->where(function ($q) {
                    $q->where('status', 'active')
                      ->orWhereNull('status');
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('listing_type', 'like', "%{$query}%")
                      ->orWhereHas('propertyType', function ($typeQ) use ($query) {
                          $typeQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('category', function ($catQ) use ($query) {
                          $catQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('address', function ($addrQ) use ($query) {
                          $addrQ->where('city', 'like', "%{$query}%")
                                ->orWhere('sub_city', 'like', "%{$query}%")
                                ->orWhere('district', 'like', "%{$query}%")
                                ->orWhere('street', 'like', "%{$query}%");
                      });
                })
                ->latest()
                ->take(8)
                ->get()
                ->map(function ($prop) {
                    $primaryImg = $prop->primaryImage ? $prop->primaryImage->image_url : null;
                    $addressStr = '';
                    if ($prop->address) {
                        $parts = array_filter([$prop->address->sub_city, $prop->address->city]);
                        $addressStr = implode(', ', $parts);
                    }

                    return [
                        'id'            => $prop->id,
                        'title'         => $prop->title,
                        'price'         => (float)$prop->price,
                        'currency'      => $prop->currency ?? 'ETB',
                        'listing_type'  => $prop->listing_type ?? 'sale',
                        'property_type' => $prop->propertyType ? $prop->propertyType->name : 'Property',
                        'bedrooms'      => $prop->bedrooms,
                        'bathrooms'     => $prop->bathrooms,
                        'location'      => $addressStr ?: 'Addis Ababa',
                        'image'         => $primaryImg ?: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&q=80',
                        'url'           => "/buyer/properties/{$prop->id}",
                    ];
                });

            $results['properties'] = $properties;
        } catch (\Throwable $e) {
            $results['properties'] = [];
        }

        // 2. Search Appointments for Current User
        try {
            $appointments = Appointment::with(['property', 'owner'])
                ->where(function ($q) use ($user) {
                    $q->where('visitor_id', $user->id)
                      ->orWhere('owner_id', $user->id);
                })
                ->where(function ($q) use ($query) {
                    $q->where('status', 'like', "%{$query}%")
                      ->orWhere('message', 'like', "%{$query}%")
                      ->orWhere('owner_notes', 'like', "%{$query}%")
                      ->orWhereHas('property', function ($propQ) use ($query) {
                          $propQ->where('title', 'like', "%{$query}%");
                      })
                      ->orWhereHas('owner', function ($ownerQ) use ($query) {
                          $ownerQ->where('name', 'like', "%{$query}%");
                      });
                })
                ->latest('scheduled_at')
                ->take(6)
                ->get()
                ->map(function ($appt) {
                    return [
                        'id'             => $appt->id,
                        'property_id'    => $appt->property_id,
                        'property_title' => $appt->property ? $appt->property->title : 'Viewing Appointment',
                        'owner_name'     => $appt->owner ? $appt->owner->name : 'Property Owner',
                        'scheduled_at'   => $appt->scheduled_at ? $appt->scheduled_at->toIso8601String() : null,
                        'duration'       => $appt->duration_minutes ?? 30,
                        'status'         => $appt->status ?? 'pending',
                        'url'            => '/buyer/appointments',
                    ];
                });

            $results['appointments'] = $appointments;
        } catch (\Throwable $e) {
            $results['appointments'] = [];
        }

        // 3. Search Messages / Conversations for Current User
        try {
            $conversations = Conversation::with(['property', 'latestMessage', 'participants'])
                ->whereHas('participants', function ($pQ) use ($user) {
                    $pQ->where('users.id', $user->id);
                })
                ->where(function ($q) use ($query) {
                    $q->whereHas('property', function ($propQ) use ($query) {
                        $propQ->where('title', 'like', "%{$query}%");
                    })
                    ->orWhereHas('messages', function ($msgQ) use ($query) {
                        $msgQ->where('body', 'like', "%{$query}%");
                    })
                    ->orWhereHas('participants', function ($pQ) use ($query) {
                        $pQ->where('name', 'like', "%{$query}%");
                    });
                })
                ->latest('updated_at')
                ->take(6)
                ->get()
                ->map(function ($conv) use ($user) {
                    $otherUser = $conv->participants->firstWhere('id', '!=', $user->id);
                    $latest = $conv->latestMessage;

                    return [
                        'id'              => $conv->id,
                        'property_id'     => $conv->property_id,
                        'property_title'  => $conv->property ? $conv->property->title : 'Property Discussion',
                        'other_user_name' => $otherUser ? $otherUser->name : 'User',
                        'latest_message'  => $latest ? $latest->body : 'Conversation started',
                        'unread_count'    => method_exists($conv, 'getUnreadCountForUser') ? $conv->getUnreadCountForUser($user->id) : 0,
                        'updated_at'      => $conv->updated_at ? $conv->updated_at->toIso8601String() : null,
                        'url'             => "/buyer/messages?conversation={$conv->id}",
                    ];
                });

            $results['messages'] = $conversations;
        } catch (\Throwable $e) {
            $results['messages'] = [];
        }

        $totalCount = count($results['properties']) + count($results['appointments']) + count($results['messages']);

        return $this->success([
            'properties'   => $results['properties'],
            'appointments' => $results['appointments'],
            'messages'     => $results['messages'],
            'total'        => $totalCount,
        ], 'Search completed successfully');
    }
}
