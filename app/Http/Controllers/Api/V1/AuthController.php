<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * API Authentication Controller
 * Kullanıcı kayıt, giriş ve çıkış işlemleri
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Kullanıcı kaydı
     * POST /api/v1/register
     */
    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kayıt başarılı',
            'data' => $result,
        ], 201);
    }

    /**
     * Kullanıcı girişi
     * POST /api/v1/login
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $result = $this->authService->login(
            $validated['email'],
            $validated['password']
        );

        return response()->json([
            'success' => true,
            'message' => 'Giriş başarılı',
            'data' => $result,
        ]);
    }

    /**
     * Kullanıcı çıkışı
     * POST /api/v1/logout
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı',
        ]);
    }

    /**
     * Kullanıcı bilgilerini getir
     * GET /api/v1/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['profile', 'device']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
