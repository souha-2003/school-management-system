<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user with flexible identifier (username, email, phone) and password.
     */
    public function login(array $credentials)
    {
        $identifier = trim($credentials['identifier']);

        // 1. Locate User by username, email, or phone_number
        $user = User::with('school')->where(function ($query) use ($identifier) {
            $query->where('username', $identifier)
                ->orWhere('email', $identifier)
                ->orWhere('phone_number', $identifier);
        })->first();

        // 2. Validate Password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['بيانات الدخول غير صحيحة، يرجى التأكد من اسم المستخدم أو كلمة المرور.'],
            ]);
        }

        // 3. Check Account Status
        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'identifier' => ['هذا الحساب معلق، يرجى مراجعة إدارة المدرسة.'],
            ]);
        }

        if ($user->status === 'pending_activation') {
            throw ValidationException::withMessages([
                'identifier' => ['هذا الحساب بانتظار التفعيل.'],
            ]);
        }

        if ($user->status === 'inactive') {
            throw ValidationException::withMessages([
                'identifier' => ['هذا الحساب غير نشط حالياً.'],
            ]);
        }

        // 4. Check School Status (if tenant user)
        if ($user->school_id && $user->school && $user->school->status === 'suspended') {
            throw ValidationException::withMessages([
                'identifier' => ['تم تعليق خدمات هذه المدرسة مؤقتاً، يرجى مراجعة إدارة المنصة.'],
            ]);
        }

        // 5. Update Last Login Timestamp
        $user->update(['last_login_at' => now()]);

        // 6. Generate Sanctum Personal Access Token
        $deviceName = $credentials['device_name'] ?? 'web-client';
        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Revoke current user session token.
     */
    public function logout($user)
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return true;
    }

    /**
     * Retrieve authenticated user profile with relations.
     */
    public function getAuthenticatedUser($user)
    {
        return $user->load('school');
    }
}
