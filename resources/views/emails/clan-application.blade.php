<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .message-box { background: white; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 5px; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Klana Yeni Başvuru!</h1>
        </div>
        <div class="content">
            <p>Merhaba <strong>{{ $leaderName }}</strong>,</p>
            
            <p><strong>{{ $applicantName }}</strong> kullanıcısı "<strong>{{ $clanName }}</strong>" klanınıza üye olmak istiyor!</p>
            
            <div class="message-box">
                <h4>📝 Başvuru Mesajı:</h4>
                <p>{{ $message ?? 'Mesaj yok' }}</p>
            </div>
            
            <p>Başvuruyu incelemek ve kabul/red etmek için aşağıdaki butona tıklayın:</p>
            
            <div style="text-align: center;">
                <a href="{{ $applicationUrl }}" class="button">Başvuruyu İncele</a>
            </div>
            
            <p><small>💡 <strong>İpucu:</strong> Klan üyelerinizi dikkatli seçmek, güçlü bir topluluk oluşturmanıza yardımcı olur!</small></p>
        </div>
        <div class="footer">
            <p>Bu e-posta PUBG Mobile Topluluk Platformu tarafından gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} PUBG Mobile Topluluğu. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
