<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '<h1>Hakkımızda</h1>
<p>SquadBul, oyuncuları bir araya getiren Türkiye\'nin en büyük multi-game platformudur.</p>
<h2>Misyonumuz</h2>
<p>Oyuncuların takım arkadaşı bulmasını kolaylaştırmak, topluluk oluşturmalarını sağlamak ve oyun deneyimlerini geliştirmek.</p>
<h2>Vizyonumuz</h2>
<p>Türkiye\'nin en büyük oyuncu topluluğu olmak ve tüm popüler oyunlar için merkezi bir platform sunmak.</p>',
                'meta_description' => 'SquadBul hakkında bilgi edinin. Türkiye\'nin en büyük multi-game platformu.',
                'is_published' => true,
            ],
            [
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => '<h1>İletişim</h1>
<p>Bizimle iletişime geçmek için aşağıdaki kanalları kullanabilirsiniz:</p>
<h2>E-posta</h2>
<p>📧 <a href="mailto:info@squadbul.com">info@squadbul.com</a></p>
<h2>Sosyal Medya</h2>
<p>📱 Twitter: <a href="https://twitter.com/squadbul" target="_blank">@squadbul</a></p>
<p>📱 Instagram: <a href="https://instagram.com/squadbul" target="_blank">@squadbul</a></p>
<p>💬 Discord: <a href="https://discord.gg/squadbul" target="_blank">discord.gg/squadbul</a></p>',
                'meta_description' => 'SquadBul ile iletişime geçin. E-posta ve sosyal medya kanallarımız.',
                'is_published' => true,
            ],
            [
                'title' => 'Sıkça Sorulan Sorular',
                'slug' => 'sss',
                'content' => '<h1>Sıkça Sorulan Sorular</h1>

<h2>SquadBul nedir?</h2>
<p>SquadBul, oyuncuların takım arkadaşı bulmasını, klan kurmasını ve turnuvalara katılmasını sağlayan bir platformdur.</p>

<h2>Üyelik ücretsiz mi?</h2>
<p>Evet! SquadBul tamamen ücretsizdir.</p>

<h2>Hangi oyunları destekliyorsunuz?</h2>
<p>Şu anda PUBG Mobile, Valorant, Call of Duty, CS:GO ve League of Legends oyunlarını destekliyoruz. Yakında daha fazla oyun eklenecek!</p>

<h2>Nasıl takım arkadaşı bulabilirim?</h2>
<p>İlanlar bölümünden ilan oluşturabilir veya mevcut ilanlara başvurabilirsiniz. Ayrıca eşleşme sistemimizi kullanarak otomatik eşleşme yapabilirsiniz.</p>

<h2>Klan nasıl kurarım?</h2>
<p>Oyun subdomain\'ine girdikten sonra Klanlar bölümünden "Klan Kur" butonuna tıklayarak kendi klanınızı oluşturabilirsiniz.</p>

<h2>Hesabımı nasıl silerim?</h2>
<p>Profil ayarlarından hesabınızı silebilirsiniz. Bu işlem geri alınamaz!</p>',
                'meta_description' => 'SquadBul hakkında sıkça sorulan sorular ve cevapları.',
                'is_published' => true,
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '<h1>Gizlilik Politikası</h1>
<p>Son güncelleme: ' . date('d.m.Y') . '</p>

<h2>Toplanan Bilgiler</h2>
<p>SquadBul olarak kullanıcılarımızın gizliliğine önem veriyoruz. Topladığımız bilgiler:</p>
<ul>
<li>Ad, soyad ve e-posta adresi</li>
<li>Oyun içi kullanıcı adları</li>
<li>Profil bilgileri</li>
<li>Platform kullanım verileri</li>
</ul>

<h2>Bilgilerin Kullanımı</h2>
<p>Topladığımız bilgiler sadece platform hizmetlerini sunmak için kullanılır.</p>

<h2>Bilgi Güvenliği</h2>
<p>Verileriniz güvenli sunucularda saklanır ve üçüncü şahıslarla paylaşılmaz.</p>',
                'meta_description' => 'SquadBul gizlilik politikası ve kullanıcı verilerinin korunması.',
                'is_published' => true,
            ],
            [
                'title' => 'Kullanım Şartları',
                'slug' => 'kullanim-sartlari',
                'content' => '<h1>Kullanım Şartları</h1>
<p>Son güncelleme: ' . date('d.m.Y') . '</p>

<h2>Genel Kurallar</h2>
<ul>
<li>Platform kurallarına uygun davranmalısınız</li>
<li>Spam, hakaret ve uygunsuz içerik paylaşmak yasaktır</li>
<li>Hesabınızın güvenliğinden siz sorumlusunuz</li>
<li>Birden fazla hesap açmak yasaktır</li>
</ul>

<h2>İçerik Kuralları</h2>
<p>Paylaştığınız içerikler yasalara ve toplum kurallarına uygun olmalıdır.</p>

<h2>Yaptırımlar</h2>
<p>Kurallara uymayan kullanıcıların hesapları askıya alınabilir veya silinebilir.</p>',
                'meta_description' => 'SquadBul kullanım şartları ve platform kuralları.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('✅ Sayfalar oluşturuldu!');
    }
}
