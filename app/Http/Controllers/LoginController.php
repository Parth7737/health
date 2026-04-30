<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Validation\ValidationException;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 5;
    private const RESET_OTP_EXPIRY_MINUTES = 10;

    public function checkUser(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string',
        ]);

        $check = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('userid', $request->email);
        })->first();
        if(!$check) {
            return response()->json(['success' => false, 'message' => 'Please enter valid EmailId or UserId!!']);
        }
        return response()->json(['success' => true, 'message' => 'User Valid!!']);
    }

    public function login(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('userid', $request->email);
        })->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The provided username or password is incorrect.',
            ]);
        }

        if (!$user->hasRole($request->role)) {
            throw ValidationException::withMessages([
                'role' => 'Selected role is not assigned to this user.',
            ]);
        }
    
        // Log in the user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $url = Helpers::getDashboardRedirect($user);
        return response()->json(['success' => true, 'message' => 'Login SuccessFully!!', 'data' => auth()->user(),'url'=>$url]);     
    }

    public function sendOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        $user = $this->resolveUser($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The provided username or password is incorrect.',
            ]);
        }

        if (!$user->hasRole($request->role)) {
            throw ValidationException::withMessages([
                'role' => 'Selected role is not assigned to this user.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);
        $challengeToken = (string) Str::uuid();

        Cache::put(
            $this->getOtpCacheKey($challengeToken),
            [
                'user_id' => $user->id,
                'role' => $request->role,
                'otp_hash' => Hash::make($otp),
            ],
            now()->addMinutes(self::OTP_EXPIRY_MINUTES)
        );

        $this->dispatchOtp($user, $otp);

        $response = [
            'success' => true,
            'message' => 'OTP sent successfully.',
            'challenge_token' => $challengeToken,
            'expires_in' => self::OTP_EXPIRY_MINUTES * 60,
            'masked_mobile' => $this->maskMobile((string) $user->mobile_no),
        ];

        if (config('app.debug')) {
            $response['debug_otp'] = $otp;
        }

        return response()->json($response);
    }

    public function verifyOtp(Request $request)
    {
        $validatedData = $request->validate([
            'challenge_token' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        $cacheKey = $this->getOtpCacheKey($request->challenge_token);
        $challenge = Cache::get($cacheKey);

        if (!$challenge) {
            throw ValidationException::withMessages([
                'otp' => 'OTP expired. Please request a new OTP.',
            ]);
        }

        if (!Hash::check((string) $request->otp, $challenge['otp_hash'])) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP. Please try again.',
            ]);
        }

        $user = User::find($challenge['user_id']);

        if (!$user || !$user->hasRole($challenge['role'])) {
            Cache::forget($cacheKey);
            throw ValidationException::withMessages([
                'role' => 'Role validation failed. Please login again.',
            ]);
        }

        Cache::forget($cacheKey);

        Auth::login($user);
        $request->session()->regenerate();

        $url = Helpers::getDashboardRedirect($user);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'url' => $url,
        ]);
    }

    public function sendForgotOtp(Request $request)
    {
        $validatedData = $request->validate([
            'identifier' => 'required|string',
            'method' => 'required|in:email,sms',
        ]);

        $user = $this->resolveUser($request->identifier);

        if (!$user) {
            throw ValidationException::withMessages([
                'identifier' => 'No user found with provided Employee ID or email.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);
        $challengeToken = (string) Str::uuid();

        Cache::put(
            $this->getResetOtpCacheKey($challengeToken),
            [
                'user_id' => $user->id,
                'method' => $request->method,
                'otp_hash' => Hash::make($otp),
            ],
            now()->addMinutes(self::RESET_OTP_EXPIRY_MINUTES)
        );

        $this->dispatchResetOtp($user, $otp, $request->method);

        $response = [
            'success' => true,
            'message' => 'Reset OTP sent successfully.',
            'challenge_token' => $challengeToken,
            'expires_in' => self::RESET_OTP_EXPIRY_MINUTES * 60,
            'masked_email' => $this->maskEmail((string) $user->email),
            'masked_mobile' => $this->maskMobile((string) $user->mobile_no),
            'method' => $request->method,
        ];

        if (config('app.debug')) {
            $response['debug_otp'] = $otp;
        }

        return response()->json($response);
    }

    public function verifyForgotOtp(Request $request)
    {
        $validatedData = $request->validate([
            'challenge_token' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        $cacheKey = $this->getResetOtpCacheKey($request->challenge_token);
        $challenge = Cache::get($cacheKey);

        if (!$challenge) {
            throw ValidationException::withMessages([
                'otp' => 'Reset OTP expired. Please request a new OTP.',
            ]);
        }

        if (!Hash::check((string) $request->otp, $challenge['otp_hash'])) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP. Please try again.',
            ]);
        }

        $user = User::find($challenge['user_id']);

        if (!$user) {
            Cache::forget($cacheKey);
            throw ValidationException::withMessages([
                'identifier' => 'User not found. Please start reset flow again.',
            ]);
        }

        Cache::forget($cacheKey);

        $resetToken = (string) Str::uuid();

        Cache::put(
            $this->getResetSessionCacheKey($resetToken),
            [
                'user_id' => $user->id,
            ],
            now()->addMinutes(self::RESET_OTP_EXPIRY_MINUTES)
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken,
        ]);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $validatedData = $request->validate([
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cacheKey = $this->getResetSessionCacheKey($request->reset_token);
        $session = Cache::get($cacheKey);

        if (!$session) {
            throw ValidationException::withMessages([
                'reset_token' => 'Password reset session expired. Please restart forgot password flow.',
            ]);
        }

        $user = User::find($session['user_id']);

        if (!$user) {
            Cache::forget($cacheKey);
            throw ValidationException::withMessages([
                'identifier' => 'User not found. Please restart forgot password flow.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(60);
        $user->save();

        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful.',
        ]);
    }

    private function resolveUser(string $identifier): ?User
    {
        return User::where(function ($query) use ($identifier) {
            $query->where('email', $identifier)
                ->orWhere('userid', $identifier);
        })->first();
    }

    private function getOtpCacheKey(string $challengeToken): string
    {
        return 'login_otp:' . $challengeToken;
    }

    private function getResetOtpCacheKey(string $challengeToken): string
    {
        return 'reset_otp:' . $challengeToken;
    }

    private function getResetSessionCacheKey(string $resetToken): string
    {
        return 'reset_session:' . $resetToken;
    }

    private function dispatchOtp(User $user, string $otp): void
    {
        // Placeholder for third-party OTP provider integration.
        Log::info('Login OTP generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mobile_no' => $user->mobile_no,
            'otp' => $otp,
        ]);
    }

    private function dispatchResetOtp(User $user, string $otp, string $method): void
    {
        // Placeholder for reset OTP delivery provider integration.
        Log::info('Reset OTP generated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mobile_no' => $user->mobile_no,
            'method' => $method,
            'otp' => $otp,
        ]);
    }

    private function maskMobile(string $mobileNo): string
    {
        $normalized = preg_replace('/\D+/', '', $mobileNo);

        if (!$normalized || strlen($normalized) < 4) {
            return 'N/A';
        }

        return '******' . substr($normalized, -4);
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return 'N/A';
        }

        [$name, $domain] = explode('@', $email, 2);
        if (strlen($name) <= 3) {
            return str_repeat('*', strlen($name)) . '@' . $domain;
        }

        return substr($name, 0, 3) . '***@' . $domain;
    }
}
