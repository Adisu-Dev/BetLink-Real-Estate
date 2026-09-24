<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $roleName = match(strtolower((string)($request->role ?? 'buyer'))) {
            'buyer', 'buyer / tenant', 'buyer_tenant', 'buyer / renter', 'buyer_renter' => 'buyer',
            'owner', 'property owner', 'property_owner' => 'owner',
            'agent', 'real estate agent', 'real_estate_agent' => 'agent',
            default => 'buyer'
        };

        $user = User::create([
            'name'     => $request->name ?? trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $roleName,
        ]);

        $user->assignRole($roleName);
        UserProfile::create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'role'       => $roleName,
                'roles'      => [$roleName],
                'created_at' => $user->created_at,
            ],
            'token' => $token,
        ], 'Registration successful');
    }
}
