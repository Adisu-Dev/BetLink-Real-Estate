<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $users = User::with(['roles', 'profile', 'verificationRequests'])
            ->withCount(['properties', 'appointmentsAsVisitor'])
            ->when($request->q, function ($q, $val) {
                $q->where(function ($sub) use ($val) {
                    $sub->where('name', 'like', "%{$val}%")
                        ->orWhere('email', 'like', "%{$val}%")
                        ->orWhere('phone', 'like', "%{$val}%");
                });
                $q->orderByRaw("
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN email LIKE ? THEN 3
                        ELSE 4
                    END ASC", 
                    ["{$val}%", "% {$val}%", "{$val}%"]
                );
            })
            ->when($request->role, fn($q, $v) => $q->role($v), fn($q) => $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'admin')))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->sort, function ($q, $sort) {
                match ($sort) {
                    'oldest' => $q->oldest(),
                    'alphabetical', 'name_asc', 'az' => $q->orderBy('name', 'asc'),
                    'reverse', 'name_desc', 'za' => $q->orderBy('name', 'desc'),
                    default => $q->latest(),
                };
            }, function ($q) use ($request) {
                if (!$request->q) {
                    $q->latest();
                }
            })
            ->paginate($request->per_page ?? 20);

        return $this->paginated($users);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success($user->load(['roles', 'profile', 'properties', 'subscription']));
    }

    public function suspend(User $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return $this->forbidden('Cannot suspend an admin');
        }
        $user->update(['status' => 'suspended']);
        $user->tokens()->delete();
        return $this->success(null, "User {$user->name} suspended");
    }

    public function ban(User $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return $this->forbidden('Cannot ban an admin');
        }
        $user->update(['status' => 'banned']);
        $user->tokens()->delete();
        return $this->success(null, "User {$user->name} banned");
    }

    public function activate(User $user): JsonResponse
    {
        $user->update(['status' => 'active']);
        return $this->success(null, "User {$user->name} activated");
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'role'     => ['required', 'string', 'in:admin,owner,agent,buyer'],
            'status'   => ['nullable', 'string', 'in:active,suspended,banned'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'status'   => $validated['status'] ?? 'active',
            'role'     => $validated['role'],
        ]);

        $user->assignRole($validated['role']);
        \App\Models\UserProfile::firstOrCreate(['user_id' => $user->id]);

        return $this->created($user->load('roles'), 'User created successfully');
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,suspended,banned']
        ]);

        if ($user->isAdmin() && $validated['status'] !== 'active') {
            return $this->forbidden('Cannot change status of an admin');
        }

        $user->update(['status' => $validated['status']]);

        if ($validated['status'] !== 'active') {
            $user->tokens()->delete();
        }

        return $this->success($user, "User status updated to {$validated['status']}");
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return $this->forbidden('Cannot delete an admin');
        }
        $user->tokens()->delete();
        $user->delete();
        return $this->noContent('User deleted');
    }
}
