<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Device;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Kullanıcıları oluştur
     * 1 Admin + 5 Normal Kullanıcı
     */
    public function run(): void
    {
        // Admin Kullanıcı
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@pubgcommunity.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'status' => 'active',
            'xp_total' => 1000,
            'email_verified_at' => now(),
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'nickname' => 'AdminPro',
            'rank' => 'Conqueror',
            'server_region' => 'EU',
            'city' => 'İstanbul',
            'age_range' => '25-30',
            'gender' => 'male',
            'play_style' => 'try-hard',
            'bio' => 'Platform yöneticisi ve profesyonel oyuncu',
            'is_profile_completed' => true,
        ]);

        Device::create([
            'user_id' => $admin->id,
            'device_name' => 'iPhone 15 Pro Max',
            'graphics_settings' => 'HDR + Extreme',
            'fps_setting' => '120 FPS',
            'gyro_enabled' => true,
            'sensitivity_settings' => [
                'general' => 85,
                'ads' => 65,
                'scope_2x' => 60,
                'scope_4x' => 55,
                'scope_8x' => 50,
                'gyro' => 300,
            ],
            'notes' => 'Profesyonel ayarlar - Gyro ile oyna',
        ]);

        $this->command->info('✅ Admin kullanıcı oluşturuldu: admin@pubgcommunity.com / password');

        // Normal Kullanıcılar
        $users = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet@test.com',
                'profile' => [
                    'nickname' => 'AhmetGG',
                    'rank' => 'Ace',
                    'server_region' => 'EU',
                    'city' => 'Ankara',
                    'age_range' => '18-24',
                    'gender' => 'male',
                    'play_style' => 'try-hard',
                    'bio' => 'Ace rank oyuncu, squad arıyorum',
                ],
                'device' => [
                    'device_name' => 'Poco X6 Pro',
                    'graphics_settings' => 'Smooth + Extreme',
                    'fps_setting' => '90 FPS',
                    'gyro_enabled' => false,
                    'sensitivity_settings' => [
                        'general' => 80,
                        'ads' => 60,
                        'scope_2x' => 55,
                        'scope_4x' => 50,
                    ],
                ],
            ],
            [
                'name' => 'Ayşe Demir',
                'email' => 'ayse@test.com',
                'profile' => [
                    'nickname' => 'AyşeQueen',
                    'rank' => 'Crown',
                    'server_region' => 'MENA',
                    'city' => 'İzmir',
                    'age_range' => '18-24',
                    'gender' => 'female',
                    'play_style' => 'chill',
                    'bio' => 'Eğlenceli oyun arıyorum, tilt yok',
                ],
                'device' => [
                    'device_name' => 'Samsung Galaxy S23',
                    'graphics_settings' => 'Balanced + High',
                    'fps_setting' => '60 FPS',
                    'gyro_enabled' => true,
                    'sensitivity_settings' => [
                        'general' => 75,
                        'ads' => 55,
                        'gyro' => 250,
                    ],
                ],
            ],
            [
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet@test.com',
                'profile' => [
                    'nickname' => 'MehmetPro',
                    'rank' => 'Diamond',
                    'server_region' => 'EU',
                    'city' => 'Bursa',
                    'age_range' => '25-30',
                    'gender' => 'male',
                    'play_style' => 'fun-first',
                    'bio' => 'Akşamları aktif, eğlenceli takım arıyorum',
                ],
                'device' => [
                    'device_name' => 'Xiaomi Redmi Note 12 Pro',
                    'graphics_settings' => 'Smooth + High',
                    'fps_setting' => '60 FPS',
                    'gyro_enabled' => false,
                    'sensitivity_settings' => [
                        'general' => 70,
                        'ads' => 50,
                    ],
                ],
            ],
            [
                'name' => 'Zeynep Arslan',
                'email' => 'zeynep@test.com',
                'profile' => [
                    'nickname' => 'ZeynepGamer',
                    'rank' => 'Platinum',
                    'server_region' => 'EU',
                    'city' => 'Antalya',
                    'age_range' => '18-24',
                    'gender' => 'female',
                    'play_style' => 'chill',
                    'bio' => 'Yeni başladım, öğrenmek istiyorum',
                ],
                'device' => [
                    'device_name' => 'iPhone 13',
                    'graphics_settings' => 'Balanced + Medium',
                    'fps_setting' => '60 FPS',
                    'gyro_enabled' => false,
                    'sensitivity_settings' => [
                        'general' => 65,
                        'ads' => 45,
                    ],
                ],
            ],
            [
                'name' => 'Can Öztürk',
                'email' => 'can@test.com',
                'profile' => [
                    'nickname' => 'CanTheKing',
                    'rank' => 'Ace',
                    'server_region' => 'ASIA',
                    'city' => 'İstanbul',
                    'age_range' => '25-30',
                    'gender' => 'male',
                    'play_style' => 'try-hard',
                    'bio' => 'Profesyonel oyuncu, turnuva deneyimi var',
                    'twitch_username' => 'cantheking',
                    'discord_username' => 'CanTheKing#1234',
                ],
                'device' => [
                    'device_name' => 'iPad Pro 2023',
                    'graphics_settings' => 'HDR + Ultra',
                    'fps_setting' => '120 FPS',
                    'gyro_enabled' => true,
                    'sensitivity_settings' => [
                        'general' => 90,
                        'ads' => 70,
                        'scope_2x' => 65,
                        'scope_4x' => 60,
                        'scope_8x' => 55,
                        'gyro' => 350,
                    ],
                    'notes' => 'iPad ile oynuyorum, 4 parmak claw grip',
                ],
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'is_admin' => false,
                'status' => 'active',
                'xp_total' => rand(50, 500),
                'email_verified_at' => now(),
            ]);

            $profileData = $userData['profile'];
            $profileData['user_id'] = $user->id;
            $profileData['is_profile_completed'] = true;
            Profile::create($profileData);

            $deviceData = $userData['device'];
            $deviceData['user_id'] = $user->id;
            Device::create($deviceData);

            $this->command->info("✅ Kullanıcı oluşturuldu: {$userData['email']}");
        }

        $this->command->info('✅ Toplam ' . (count($users) + 1) . ' kullanıcı oluşturuldu!');
        $this->command->info('📧 Tüm kullanıcılar için şifre: password');
    }
}
