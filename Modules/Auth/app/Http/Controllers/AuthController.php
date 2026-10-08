<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Resources\AuthResource;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Authenticate user and issue access token.
     */
    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return new AuthResource($result);
    }

    /**
     * Terminate current user session and revoke token.
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    /**
     * Retrieve authenticated user details.
     */
    public function me(Request $request)
    {
        $user = $this->authService->getAuthenticatedUser($request->user());

        return new UserResource($user);
    }
}
