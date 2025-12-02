<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Ayarlar sayfasını göster
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('settings.index', [
            'user' => $user,
            'settings' => $user->settings ?? []
        ]);
    }

    /**
     * Profil bilgilerini güncelle
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        $user = auth()->user();
        $user->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Profil bilgileriniz başarıyla güncellendi!');
    }

    /**
     * Şifreyi değiştir
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Şifreniz başarıyla değiştirildi!');
    }

    /**
     * Bildirim tercihlerini güncelle
     */
    public function updateNotifications(Request $request)
    {
        $settings = [
            'email_notifications' => $request->boolean('email_notifications'),
            'lfg_notifications' => $request->boolean('lfg_notifications'),
            'clan_invites' => $request->boolean('clan_invites'),
            'message_notifications' => $request->boolean('message_notifications'),
        ];

        $user = auth()->user();
        $user->settings = array_merge($user->settings ?? [], $settings);
        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'Bildirim tercihleriniz güncellendi!');
    }

    /**
     * Gizlilik ayarlarını güncelle
     */
    public function updatePrivacy(Request $request)
    {
        $settings = [
            'profile_public' => $request->boolean('profile_public'),
            'show_stats' => $request->boolean('show_stats'),
            'show_online_status' => $request->boolean('show_online_status'),
        ];

        $user = auth()->user();
        $user->settings = array_merge($user->settings ?? [], $settings);
        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'Gizlilik ayarlarınız güncellendi!');
    }

    /**
     * Hesabı dondur
     */
    public function freezeAccount(Request $request)
    {
        $user = auth()->user();
        $user->update(['status' => 'frozen']);

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Hesabınız donduruldu. Tekrar giriş yaparak aktif edebilirsiniz.');
    }

    /**
     * Hesabı sil
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();
        
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete(); // Soft delete

        return redirect()->route('home')
            ->with('success', 'Hesabınız başarıyla silindi.');
    }
}
