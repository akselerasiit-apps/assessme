<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting
        $key = 'login-attempt:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        // Log login attempt
        $this->logLoginAttempt($request, $user, false);

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // Success - clear rate limiter
        RateLimiter::clear($key);

        // Log successful login
        $this->logLoginAttempt($request, $user, true);

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout user (revoke token)
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete all tokens for the user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     * GET /api/auth/user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * Refresh token
     * POST /api/auth/refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Delete old token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Log login attempt
     */
    private function logLoginAttempt(Request $request, ?User $user, bool $success): void
    {
        LoginAttempt::create([
            'user_id' => $user?->id,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $success ? 'SUCCESS' : 'FAILED',
            'failure_reason' => $success ? null : 'Invalid credentials',
            'attempted_at' => now(),
        ]);
    }
}
