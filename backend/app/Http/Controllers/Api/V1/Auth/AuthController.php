<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Services\OtpService;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private OtpService $otpService) {}

    /**
     * Send 6-digit OTP code to the user's email for registration verification.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'      => ['required', 'string', 'email', 'max:255'],
            'name'       => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'password'   => ['nullable', 'string', 'min:6', 'max:100'],
            'role'       => ['nullable', 'string', 'max:50'],
            'channel'    => ['nullable', 'string', 'in:email,sms,both'],
        ]);

        $email = strtolower(trim($validated['email']));

        // Check if user already exists
        $existing = User::where('email', $email)->whereNull('deleted_at')->first();
        if ($existing) {
            return $this->error('An account with this email address already exists. Please sign in instead.', 422);
        }

        // Check if phone number already exists
        if (!empty($validated['phone'])) {
            $rawPhone = trim($validated['phone']);
            $cleanPhone = preg_replace('/[^\d+]/', '', $rawPhone);
            $digitsOnly = preg_replace('/\D/', '', $rawPhone);
            $last9 = substr($digitsOnly, -9);

            $phoneExisting = User::whereNull('deleted_at')
                ->where(function ($q) use ($rawPhone, $cleanPhone, $last9) {
                    $q->where('phone', $rawPhone)
                      ->orWhere('phone', $cleanPhone);
                    if (strlen($last9) === 9) {
                        $q->orWhere('phone', 'like', "%{$last9}");
                    }
                })
                ->exists();

            if ($phoneExisting) {
                return $this->error('This phone number is already registered with another account. Please sign in or use a different phone number.', 422, [
                    'phone' => ['This phone number is already registered with another account. Please sign in or use a different phone number.']
                ]);
            }
        }

        $fullName = trim($validated['name'] ?? (($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? '')));
        if (empty($fullName)) {
            $fullName = explode('@', $email)[0];
        }

        $payload = [
            'name'     => $fullName,
            'email'    => $email,
            'phone'    => $validated['phone'] ?? null,
            'password' => $validated['password'] ?? null,
            'role'     => $validated['role'] ?? 'buyer',
        ];

        $channel = $validated['channel'] ?? 'both';
        $otpResult = $this->otpService->generateAndSend($email, $payload, $channel);

        $channelDesc = match($channel) {
            'sms'   => 'your phone number via SMS',
            'email' => 'your email address',
            default => 'your email and SMS'
        };

        $message = "A 6-digit verification code has been dispatched to {$channelDesc}.";
        if (!empty($otpResult['dev_otp'])) {
            $message .= " (Verification code: {$otpResult['dev_otp']})";
        }

        return $this->success([
            'email'           => $email,
            'phone'           => $otpResult['phone'],
            'channel'         => $otpResult['channel'],
            'email_sent'      => $otpResult['email_sent'],
            'sms_sent'        => $otpResult['sms_sent'],
            'dev_otp'         => $otpResult['dev_otp'],
            'otp'             => $otpResult['dev_otp'],
            'expires_in_secs' => $otpResult['expires_in_secs'],
            'expires_at'      => $otpResult['expires_at'],
            'message'         => $message,
        ], 'Verification code dispatched successfully');
    }

    /**
     * Verify the 6-digit OTP and activate/create the account.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp'   => ['required', 'string', 'min:6', 'max:6'],
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);

        $payload = $this->otpService->verify($email, $otp);
        if (!$payload) {
            return $this->error('Invalid or expired 6-digit verification code. Please request a new code.', 422);
        }

        // Check if user was created while OTP was in flight
        $user = User::where('email', $email)->whereNull('deleted_at')->first();
        if (!$user) {
            $rawPassword = $payload['password'] ?? null;
            $isAutoGenerated = false;
            if (empty($rawPassword)) {
                $isAutoGenerated = true;
                $rawPassword = 'BetLink#' . strtoupper(\Illuminate\Support\Str::random(4)) . rand(100, 999) . '!';
            }

            $roleName = match(strtolower((string)($payload['role'] ?? 'buyer'))) {
                'buyer', 'buyer / tenant', 'buyer_tenant', 'buyer / renter', 'buyer_renter' => 'buyer',
                'owner', 'property owner', 'property_owner' => 'owner',
                'agent', 'real estate agent', 'real_estate_agent' => 'agent',
                'admin', 'administrator' => 'admin',
                default => 'buyer'
            };

            try {
                $user = User::create([
                    'name'              => $payload['name'] ?? explode('@', $email)[0],
                    'email'             => $email,
                    'phone'             => $payload['phone'] ?? null,
                    'password'          => Hash::make($rawPassword),
                    'role'              => $roleName,
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]);

                $user->assignRole($roleName);
                UserProfile::create(['user_id' => $user->id]);
            } catch (\Illuminate\Database\QueryException $e) {
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                    return $this->error('This phone number is already registered with another account. Please sign in or use a different phone number.', 422);
                }
                return $this->error('Registration could not be completed. Please try again or use different contact details.', 422);
            }

            // Dispatch welcome credentials mail if password was auto-generated
            if ($isAutoGenerated) {
                try {
                    Mail::to($user->email)->send(
                        new \App\Mail\WelcomeUserCredentialsMail($user, $rawPassword)
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to dispatch welcome credentials email: " . $e->getMessage());
                }
            }
        } else {
            $user->update([
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user'    => $this->formatUser($user),
            'token'   => $token,
            'email'   => $user->email,
            'message' => 'Your email has been verified and your account is now fully activated!',
        ], 'Account activated successfully');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $rawPassword = $request->password;
        $isAutoGenerated = false;

        if (empty($rawPassword)) {
            // Auto-generate strong secure temporary password
            $isAutoGenerated = true;
            $rawPassword = 'BetLink#' . strtoupper(\Illuminate\Support\Str::random(4)) . rand(100, 999) . '!';
        }

        $roleName = match(strtolower((string)($request->role ?? 'buyer'))) {
            'buyer', 'buyer / tenant', 'buyer_tenant', 'buyer / renter', 'buyer_renter' => 'buyer',
            'owner', 'property owner', 'property_owner' => 'owner',
            'agent', 'real estate agent', 'real_estate_agent' => 'agent',
            'admin', 'administrator' => 'admin',
            default => 'buyer'
        };

        $user = User::create([
            'name'     => $request->name ?? trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')),
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($rawPassword),
            'role'     => $roleName,
        ]);

        $user->assignRole($roleName);
        UserProfile::create(['user_id' => $user->id]);

        // Dispatch Welcome & Access Credentials Email to user's real email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\WelcomeUserCredentialsMail($user, $rawPassword)
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to dispatch welcome credentials email to {$user->email}: " . $e->getMessage());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user'             => $this->formatUser($user),
            'token'            => $token,
            'email'            => $user->email,
            'auto_generated'   => $isAutoGenerated,
            'message'          => 'Registration successful!',
        ], 'Registration successful');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return $this->error('Invalid credentials', 401);
        }

        $isValid = Auth::attempt($credentials);
        
        // Support standard dev/demo passwords for demo accounts
        if (!$isValid && in_array(strtolower($user->email), ['admin@betlink.et', 'owner@betlink.et', 'buyer@betlink.et', 'agent@betlink.et', 'sara.owner@betlink.et', 'almaz.owner@betlink.et'])) {
            $demoPasswords = ['admin@123456', 'admin@123', 'owner@123456', 'buyer@123456', 'agent@123456', 'password', 'admin123', '12345678', 'admin', 'owner', 'buyer', 'agent'];
            if (in_array(strtolower($request->password), $demoPasswords) || Hash::check($request->password, $user->password)) {
                $isValid = true;
                $user->update(['password' => Hash::make($request->password)]);
            }
        }

        if (!$isValid) {
            return $this->error('Invalid credentials', 401);
        }

        if ($user->status === 'banned') {
            return $this->error('Your account has been banned by the administrator. Access is permanently denied.', 403);
        }

        if ($user->status === 'suspended') {
            return $this->error('Your account has been suspended by the administrator. Please contact support.', 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user'  => $this->formatUser($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->noContent('Logged out successfully');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile', 'roles');
        return $this->success($this->formatUser($user));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $request->user()->update(['password' => Hash::make($request->password)]);
        $request->user()->tokens()->delete();

        return $this->success(null, 'Password updated. Please login again.');
    }

    private function formatUser(User $user): array
    {
        $roles = $user->getRoleNames()->toArray();
        $primaryRole = $roles[0] ?? 'buyer';

        return [
            'id'                 => $user->id,
            'name'               => $user->name,
            'first_name'         => $user->first_name ?? (explode(' ', $user->name)[0] ?? ''),
            'last_name'          => $user->last_name ?? (explode(' ', $user->name, 2)[1] ?? ''),
            'email'              => $user->email,
            'phone'              => $user->phone,
            'avatar'             => $user->avatar_url,
            'avatar_url'         => $user->avatar_url,
            'status'             => $user->status,
            'email_verified_at'  => $user->email_verified_at,
            'role'               => $primaryRole,
            'roles'              => $roles,
            'created_at'         => $user->created_at,
        ];
    }
}
