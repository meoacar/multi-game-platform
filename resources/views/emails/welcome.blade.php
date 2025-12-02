<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎮 Hoş Geldiniz!</h1>
        </div>
        <div class="content">
            <p>Merhaba <strong>{{ $userName }}</strong>,</p>
            
            <p>PUBG Mobile Topluluk Platformu'na hoş geldiniz! Artık binlerce oyuncuyla tanışabilir, takım arkadaşları bulabilir ve klanlar oluşturabilirsiniz.</p>
            
            <h3>🚀 Hemen Başlayın:</h3>
            <ul>
                <li>✅ Profilinizi tamamlayın (PUBG ID, rank, şehir)</li>
                <li>🎯 LFG ilanlarına göz atın</li>
                <li>👥 Klan arayın veya kendi klanınızı oluşturun</li>
                <li>📱 Cihaz ve hassasiyet ayarlarınızı paylaşın</li>
                <li>⭐ XP kazanın ve rozetler toplayın</li>
            </ul>
            
            <div style="text-align: center;">
                <a href="{{ $profileUrl }}" class="button">Profilimi Tamamla</a>
            </div>
            
            <p><strong>İpucu:</strong> Profilinizi tamamlayarak 50 XP kazanabilirsiniz! 🎁</p>
        </div>
        <div class="footer">
            <p>Bu e-posta PUBG Mobile Topluluk Platformu tarafından gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} PUBG Mobile Topluluğu. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
