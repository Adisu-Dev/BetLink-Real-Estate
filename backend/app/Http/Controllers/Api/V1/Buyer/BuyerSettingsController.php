<?php

namespace App\Http\Controllers\Api\V1\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Favorite;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerSettingsController extends Controller
{
    use ApiResponse;

    /**
     * Get authenticated buyer profile and preferences
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['profile', 'roles']);

        return $this->success([
            'id'                       => $user->id,
            'name'                     => $user->name,
            'email'                    => $user->email,
            'phone'                    => $user->phone ?? '+251 91 123 4567',
            'avatar_url'               => $user->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80',
            'role'                     => $user->getRoleNames()->first() ?? 'buyer',
            'preferred_contact_method' => $user->profile?->preferred_contact_method ?? 'phone',
            'city'                     => $user->profile?->city ?? 'Addis Ababa',
            'sub_city'                 => $user->profile?->sub_city ?? 'Bole',
            'bio'                      => $user->profile?->bio ?? '',
            'created_at'               => $user->created_at?->toIso8601String(),
        ], 'Buyer profile retrieved successfully');
    }

    /**
     * Update buyer profile info and avatar
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'                     => ['nullable', 'string', 'max:100'],
            'phone'                    => ['nullable', 'string', 'max:30'],
            'email'                    => ['nullable', 'email', 'max:150', "unique:users,email,{$user->id}"],
            'preferred_contact_method' => ['nullable', 'string', 'in:phone,whatsapp,email,in_app'],
            'city'                     => ['nullable', 'string', 'max:100'],
            'sub_city'                 => ['nullable', 'string', 'max:100'],
            'bio'                      => ['nullable', 'string', 'max:500'],
            'avatar_url'               => ['nullable', 'string'],
        ]);

        if (isset($validated['name'])) $user->name = $validated['name'];
        if (isset($validated['phone'])) $user->phone = $validated['phone'];
        if (isset($validated['email'])) $user->email = $validated['email'];
        if (isset($validated['avatar_url'])) $user->avatar = $validated['avatar_url'];
        if (isset($validated['avatar'])) $user->avatar = $validated['avatar'];
        $user->save();

        if ($user->profile) {
            $user->profile->update([
                'preferred_contact_method' => $validated['preferred_contact_method'] ?? $user->profile->preferred_contact_method,
                'city'                     => $validated['city'] ?? $user->profile->city,
                'sub_city'                 => $validated['sub_city'] ?? $user->profile->sub_city,
                'bio'                      => $validated['bio'] ?? $user->profile->bio,
            ]);
        } else {
            $user->profile()->create([
                'preferred_contact_method' => $validated['preferred_contact_method'] ?? 'phone',
                'city'                     => $validated['city'] ?? 'Addis Ababa',
                'sub_city'                 => $validated['sub_city'] ?? 'Bole',
                'bio'                      => $validated['bio'] ?? '',
            ]);
        }

        return $this->success([
            'id'                       => $user->id,
            'name'                     => $user->name,
            'email'                    => $user->email,
            'phone'                    => $user->phone,
            'avatar_url'               => $user->avatar_url,
            'preferred_contact_method' => $validated['preferred_contact_method'] ?? 'phone',
            'city'                     => $validated['city'] ?? 'Addis Ababa',
            'sub_city'                 => $validated['sub_city'] ?? 'Bole',
            'bio'                      => $validated['bio'] ?? '',
        ], 'Profile updated successfully');
    }

    /**
     * Validate and update buyer account password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('The provided current password does not match our records.', 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'Password updated successfully');
    }

    /**
     * Export all personal user data in JSON format
     */
    public function exportData(Request $request): JsonResponse
    {
        $user = $request->user();

        $favorites = Favorite::where('user_id', $user->id)->with('property:id,title,price')->get();
        $appointments = Appointment::where('visitor_id', $user->id)->with('property:id,title')->get();

        $export = [
            'account'      => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'created_at' => $user->created_at,
            ],
            'favorites'    => $favorites,
            'appointments' => $appointments,
            'exported_at'  => now()->toIso8601String(),
        ];

        return response()->json($export);
    }

    /**
     * Delete account (Danger Zone) with password confirmation
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('Invalid password confirmation', 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return $this->success(null, 'Account deleted successfully');
    }
}
