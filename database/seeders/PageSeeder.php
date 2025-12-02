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
                'content' => '<h2>PUBG Mobile Topluluk Platformu Hakkında</h2>
<p>PUBG Mobile Topluluk Platformu, Türkiye\'nin en büyük PUBG Mobile oyuncu topluluğunu bir araya getiren sosyal platformudur.</p>

<h3>Misyonumuz</h3>
<p>Oyuncuların birbirlerini bulmasını, takım kurmasını, klan oluşturmasını ve turnuvalara katılmasını kolaylaştırmak.</p>

<h3>Vizyonumuz</h3>
<p>Türkiye\'nin en aktif ve en büyük mobil oyun topluluğu olmak.</p>

<h3>Özelliklerimiz</h3>
<ul>
    <li>Takım arama ilanları</li>
    <li>Klan sistemi</li>
    <li>Turnuva organizasyonu</li>
    <li>Rehber ve ipuçları</li>
    <li>Cihaz ve hassasiyet paylaşımı</li>
    <li>XP ve rozet sistemi</li>
</ul>

<p>Topluluğumuza katılın ve PUBG Mobile deneyiminizi bir üst seviyeye taşıyın!</p>',
                'is_published' => true,
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'content' => '<h2>Gizlilik Politikası</h2>
<p><strong>Son güncelleme:</strong> ' . now()->format('d.m.Y') . '</p>

<h3>1. Toplanan Bilgiler</h3>
<p>Platformumuzu kullanırken aşağıdaki bilgileri topluyoruz:</p>
<ul>
    <li>Ad, e-posta adresi</li>
    <li>PUBG Mobile oyuncu bilgileri (nickname, rank, server)</li>
    <li>Profil bilgileri (şehir, yaş aralığı, oyun stili)</li>
    <li>Cihaz ve hassasiyet ayarları</li>
    <li>Platform kullanım verileri</li>
</ul>

<h3>2. Bilgilerin Kullanımı</h3>
<p>Topladığımız bilgileri şu amaçlarla kullanıyoruz:</p>
<ul>
    <li>Hesap oluşturma ve yönetimi</li>
    <li>Oyuncu eşleştirme ve takım kurma</li>
    <li>Platform özelliklerinin sağlanması</li>
    <li>İletişim ve bildirimler</li>
    <li>Platform iyileştirmeleri</li>
</ul>

<h3>3. Bilgi Güvenliği</h3>
<p>Verilerinizi korumak için endüstri standardı güvenlik önlemleri kullanıyoruz.</p>

<h3>4. Çerezler</h3>
<p>Platformumuz, kullanıcı deneyimini iyileştirmek için çerezler kullanır.</p>

<h3>5. Üçüncü Taraf Paylaşımı</h3>
<p>Kişisel bilgilerinizi üçüncü taraflarla paylaşmıyoruz.</p>

<h3>6. Haklarınız</h3>
<p>Verilerinize erişme, düzeltme veya silme hakkına sahipsiniz.</p>

<h3>7. İletişim</h3>
<p>Gizlilik politikamız hakkında sorularınız için bizimle iletişime geçebilirsiniz.</p>',
                'is_published' => true,
            ],
            [
                'title' => 'Kullanım Şartları',
                'slug' => 'kullanim-sartlari',
                'content' => '<h2>Kullanım Şartları</h2>
<p><strong>Son güncelleme:</strong> ' . now()->format('d.m.Y') . '</p>

<h3>1. Kabul</h3>
<p>Bu platformu kullanarak, aşağıdaki kullanım şartlarını kabul etmiş olursunuz.</p>

<h3>2. Hesap Sorumluluğu</h3>
<ul>
    <li>Hesap bilgilerinizin güvenliğinden siz sorumlusunuz</li>
    <li>Hesabınızı başkalarıyla paylaşmayın</li>
    <li>Şüpheli aktiviteleri hemen bildirin</li>
</ul>

<h3>3. Kabul Edilebilir Kullanım</h3>
<p>Platformu kullanırken şunları YAPAMAZSINIZ:</p>
<ul>
    <li>Spam veya yanıltıcı içerik paylaşmak</li>
    <li>Diğer kullanıcılara hakaret etmek</li>
    <li>Telif hakkı ihlali yapmak</li>
    <li>Platformu kötüye kullanmak</li>
    <li>Yasadışı aktivitelerde bulunmak</li>
</ul>

<h3>4. İçerik Sorumluluğu</h3>
<p>Paylaştığınız içeriklerden siz sorumlusunuz. Platform yönetimi uygunsuz içerikleri kaldırma hakkını saklı tutar.</p>

<h3>5. Hesap Askıya Alma</h3>
<p>Kurallara uymayan hesaplar uyarı almadan askıya alınabilir veya silinebilir.</p>

<h3>6. Değişiklikler</h3>
<p>Bu şartları önceden haber vermeksizin değiştirme hakkımız saklıdır.</p>

<h3>7. İletişim</h3>
<p>Kullanım şartları hakkında sorularınız için bizimle iletişime geçebilirsiniz.</p>',
                'is_published' => true,
            ],
            [
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => '<h2>İletişim</h2>
<p>Bizimle iletişime geçmek için aşağıdaki kanalları kullanabilirsiniz:</p>

<h3>📧 E-posta</h3>
<p><strong>Genel Sorular:</strong> info@pubgtopluluk.com</p>
<p><strong>Destek:</strong> destek@pubgtopluluk.com</p>
<p><strong>İş Birliği:</strong> isbirligi@pubgtopluluk.com</p>

<h3>💬 Sosyal Medya</h3>
<ul>
    <li><strong>Discord:</strong> discord.gg/pubgtopluluk</li>
    <li><strong>Twitter:</strong> @pubgtopluluk</li>
    <li><strong>Instagram:</strong> @pubgtopluluk</li>
    <li><strong>Facebook:</strong> /pubgtopluluk</li>
</ul>

<h3>🕐 Çalışma Saatleri</h3>
<p>Pazartesi - Cuma: 09:00 - 18:00</p>
<p>Cumartesi - Pazar: 10:00 - 16:00</p>

<h3>📍 Adres</h3>
<p>İstanbul, Türkiye</p>

<h3>⚡ Hızlı Destek</h3>
<p>Acil durumlar için Discord sunucumuza katılın ve destek kanalından yardım alın.</p>

<h3>🐛 Hata Bildirimi</h3>
<p>Platform ile ilgili hata veya sorun bildirmek için destek e-postamıza yazabilirsiniz.</p>',
                'is_published' => true,
            ],
            [
                'title' => 'SSS - Sık Sorulan Sorular',
                'slug' => 'sss',
                'content' => '<h2>Sık Sorulan Sorular</h2>

<h3>❓ Platform nasıl kullanılır?</h3>
<p>Kayıt olduktan sonra profilinizi doldurun, takım arama ilanı oluşturun veya mevcut ilanlara başvurun.</p>

<h3>❓ Üyelik ücretsiz mi?</h3>
<p>Evet, platformumuz tamamen ücretsizdir.</p>

<h3>❓ Klan nasıl kurulur?</h3>
<p>Klanlar menüsünden "Yeni Klan Oluştur" butonuna tıklayın ve formu doldurun.</p>

<h3>❓ XP nasıl kazanılır?</h3>
<p>Profil doldurma, ilan oluşturma, rehber yazma, günlük giriş gibi aktivitelerle XP kazanabilirsiniz.</p>

<h3>❓ Rozetler ne işe yarar?</h3>
<p>Rozetler, topluluktaki başarılarınızı gösterir ve profilinizi öne çıkarır.</p>

<h3>❓ Hesabımı nasıl silerim?</h3>
<p>Ayarlar > Hesap > Hesabı Sil seçeneğinden hesabınızı silebilirsiniz.</p>

<h3>❓ Şifremi unuttum, ne yapmalıyım?</h3>
<p>Giriş sayfasında "Şifremi Unuttum" linkine tıklayın ve e-posta adresinizi girin.</p>

<h3>❓ Uygunsuz içerik nasıl bildirilir?</h3>
<p>Her içeriğin yanındaki "Bildir" butonunu kullanarak uygunsuz içerikleri bildirebilirsiniz.</p>

<h3>❓ Turnuvalara nasıl katılırım?</h3>
<p>Turnuvalar sayfasından aktif turnuvaları görüntüleyip kayıt olabilirsiniz.</p>

<h3>❓ Mobil uygulama var mı?</h3>
<p>Şu anda web platformu olarak hizmet veriyoruz. Mobil uygulama yakında yayınlanacak.</p>',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
