<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Auth Service
 * Kullanıcı kimlik doğrulama ve yetkilendirme işlemleri
 */
class AuthService
{
    /**
     * Yeni kullanıcı kaydı oluştur
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Kullanıcı oluştur
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Boş profil oluştur
        Profile::create([
            'user_id' => $user->id,
        ]);

        // Token oluştur
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }

    /**
     * Kullanıcı girişi
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws ValidationException
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        // Kullanıcı kontrolü ve şifre doğrulama
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email veya şifre hatalı.'],
            ]);
        }

        // Yasaklı kullanıcı kontrolü
        if ($user->isBanned()) {
            throw ValidationException::withMessages([
                'email' => ['Hesabınız yasaklanmış durumda.'],
            ]);
        }

        // Son giriş zamanını güncelle
        $user->updateLastLogin();

        // Token oluştur
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }

    /**
     * Kullanıcı çıkışı
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        // Mevcut token'ı sil
        $user->currentAccessToken()->delete();

        return true;
    }

    /**
     * Tüm token'ları sil (tüm cihazlardan çıkış)
     *
     * @param User $user
     * @return bool
     */
    public function logoutAllDevices(User $user): bool
    {
        $user->tokens()->delete();

        return true;
    }

    /**
     * Token yenile
     *
     * @param User $user
     * @return string
     */
    public function refreshToken(User $user): string
    {
        // Eski token'ı sil
        $user->currentAccessToken()->delete();

        // Yeni token oluştur
        return $user->createToken('auth_token')->plainTextToken;
    }
}
