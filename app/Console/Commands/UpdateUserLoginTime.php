<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUserLoginTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-login {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kullanıcının son giriş zamanını günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update(['last_login_at' => now()]);
                $this->info("✅ {$user->name} için last_login_at güncellendi!");
            } else {
                $this->error("❌ Kullanıcı bulunamadı!");
            }
        } else {
            // Tüm kullanıcıları güncelle
            User::query()->update(['last_login_at' => now()]);
            $this->info("✅ Tüm kullanıcılar güncellendi!");
        }
    }
}
