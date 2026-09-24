<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private ImageService $imageService) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['profile', 'roles', 'subscription']);
        return $this->success([
            'id'                => $user->id,
            'name'              => $user->name,
            'first_name'        => $user->first_name ?? (explode(' ', $user->name)[0] ?? ''),
            'last_name'         => $user->last_name ?? (explode(' ', $user->name, 2)[1] ?? ''),
            'email'             => $user->email,
            'phone'             => $user->phone,
            'avatar_url'        => $user->avatar_url,
            'status'            => $user->status,
            'email_verified_at' => $user->email_verified_at,
            'roles'             => $user->getRoleNames(),
            'profile'           => $user->profile,
            'subscription'      => $user->subscription,
            'created_at'        => $user->created_at,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $fullName = $data['name'] ?? null;
        if (!$fullName && (isset($data['first_name']) || isset($data['last_name']))) {
            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }

        if ($fullName) {
            $user->name = $fullName;
        }

        if (isset($data['phone'])) {
            $user->phone = $data['phone'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['avatar']) || isset($data['avatar_url'])) {
            $rawAv = $data['avatar'] ?? $data['avatar_url'];
            if ($rawAv && str_starts_with($rawAv, 'data:image')) {
                if (preg_match('/^data:image\/(\w+);base64,/', $rawAv, $matches)) {
                    $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
                    $cleanData = substr($rawAv, strpos($rawAv, ',') + 1);
                    $decoded = base64_decode($cleanData);
                    $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
                    \Illuminate\Support\Facades\Storage::disk('public')->put("images/avatars/{$filename}", $decoded);
                    $user->avatar = "images/avatars/{$filename}";
                }
            } elseif (!empty($rawAv)) {
                $user->avatar = $rawAv;
            }
        }

        $user->save();

        // Update profile model
        $profileFields = array_diff_key($data, array_flip(['name', 'first_name', 'last_name', 'email', 'phone', 'avatar', 'avatar_url']));

        if (!empty($profileFields)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileFields
            );
        }

        return $this->success([
            'id'         => $user->id,
            'name'       => $user->name,
            'first_name' => explode(' ', $user->name)[0] ?? '',
            'last_name'  => explode(' ', $user->name, 2)[1] ?? '',
            'email'      => $user->email,
            'phone'      => $user->phone,
            'avatar'     => $user->avatar_url,
            'avatar_url' => $user->avatar_url,
            'roles'      => $user->getRoleNames(),
        ], 'Profile updated successfully');
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            ]);
            $path = $this->imageService->upload($request->file('avatar'), 'avatars');
            $user->avatar = $path;
            $user->save();
        } elseif ($request->filled('avatar') && str_starts_with($request->avatar, 'data:image')) {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->avatar, $matches)) {
                $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
                $cleanData = substr($request->avatar, strpos($request->avatar, ',') + 1);
                $decoded = base64_decode($cleanData);
                $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
                \Illuminate\Support\Facades\Storage::disk('public')->put("images/avatars/{$filename}", $decoded);
                $user->avatar = "images/avatars/{$filename}";
                $user->save();
            }
        } elseif ($request->filled('avatar') || $request->filled('avatar_url')) {
            $user->avatar = $request->avatar ?? $request->avatar_url;
            $user->save();
        } else {
            return $this->error('No avatar image provided', 422);
        }

        return $this->success([
            'avatar_url' => $user->avatar_url,
            'avatar'     => $user->avatar_url,
        ], 'Avatar uploaded successfully');
    }
}
