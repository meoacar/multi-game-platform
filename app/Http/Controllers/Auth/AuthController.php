<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Login sayfasını göster
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login işlemini gerçekleştir
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre gereklidir.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Kullanıcı banned mi kontrol et
            if (Auth::user()->status === 'banned') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Hesabınız engellenmiştir. Lütfen yönetici ile iletişime geçin.',
                ]);
            }

            // Son giriş zamanını güncelle
            Auth::user()->update(['last_login_at' => now()]);

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Girdiğiniz bilgiler hatalı.',
        ])->onlyInput('email');
    }

    /**
     * Register sayfasını göster
     */
    public function showRegister()
    {
        // Kayıt kapalı mı kontrol et
        $registrationOpen = \App\Models\Setting::where('key', 'registration_open')->first();
        
        if ($registrationOpen && !$registrationOpen->value) {
            return redirect()->route('login')
                ->with('error', 'Kayıt işlemleri şu anda kapalıdır.');
        }
        
        return view('auth.register');
    }

    /**
     * Register işlemini gerçekleştir
     */
    public function register(Request $request)
    {
        // Kayıt kapalı mı kontrol et
        $registrationOpen = \App\Models\Setting::where('key', 'registration_open')->first();
        
        if ($registrationOpen && !$registrationOpen->value) {
            return redirect()->route('login')
                ->with('error', 'Kayıt işlemleri şu anda kapalıdır.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Kullanıcı adı gereklidir.',
            'name.unique' => 'Bu kullanıcı adı zaten kullanılıyor.',
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre gereklidir.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'terms.accepted' => 'Kullanım koşullarını kabul etmelisiniz.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'onboarding_step' => 0, // Oyun seçimi ile başla
            'onboarding_completed' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Hoş geldin bildirimi gönder
        $user->notify(new \App\Notifications\WelcomeNotification());

        // Yeni kullanıcıyı onboarding sürecine yönlendir
        return redirect()->route('onboarding.index')->with('success', 'Hesabın başarıyla oluşturuldu! Hadi profilini tamamlayalım 🎉');
    }

    /**
     * Logout işlemini gerçekleştir
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Forgot password sayfasını göster
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Şifre sıfırlama linki gönder
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Şifre sıfırlama linki e-posta adresinize gönderildi.')
            : back()->withErrors(['email' => 'Bu e-posta adresi ile kayıtlı bir hesap bulunamadı.']);
    }

    /**
     * Reset password sayfasını göster
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Şifreyi sıfırla
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required' => 'E-posta adresi gereklidir.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre gereklidir.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Şifreniz başarıyla sıfırlandı. Giriş yapabilirsiniz.')
            : back()->withErrors(['email' => 'Şifre sıfırlama işlemi başarısız oldu.']);
    }

    /**
     * Email verification notice sayfasını göster
     */
    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    /**
     * Email'i doğrula
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('home')->with('success', 'E-posta adresin doğrulandı! 🎉');
    }

    /**
     * Doğrulama linkini tekrar gönder
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
