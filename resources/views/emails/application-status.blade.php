<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, {{ $status === 'accepted' ? '#10b981 0%, #059669 100%' : '#ef4444 0%, #dc2626 100%' }}); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .status-box { background: white; padding: 20px; border-left: 4px solid {{ $status === 'accepted' ? '#10b981' : '#ef4444' }}; margin: 20px 0; border-radius: 5px; text-align: center; }
        .button { display: inline-block; background: {{ $status === 'accepted' ? '#10b981' : '#6b7280' }}; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $status === 'accepted' ? '✅ Başvurunuz Kabul Edildi!' : '❌ Başvuru Durumu' }}</h1>
        </div>
        <div class="content">
            <p>Merhaba <strong>{{ $applicantName }}</strong>,</p>
            
            @if($status === 'accepted')
                <div class="status-box">
                    <h2 style="color: #10b981; margin: 0;">🎉 Tebrikler!</h2>
                    <p style="font-size: 18px; margin: 10px 0;">Başvurunuz kabul edildi!</p>
                </div>
                
                <p>"<strong>{{ $title }}</strong>" {{ $type === 'lfg' ? 'ilanına' : 'klanına' }} başvurunuz onaylandı. Artık takımın bir parçasısınız!</p>
                
                <p><strong>Sırada ne var?</strong></p>
                <ul>
                    <li>{{ $type === 'lfg' ? 'İlan sahibiyle iletişime geçin' : 'Klan liderinizle tanışın' }}</li>
                    <li>Oyun bilgilerinizi paylaşın</li>
                    <li>İlk maçınızı planlayın</li>
                </ul>
            @else
                <div class="status-box">
                    <h2 style="color: #ef4444; margin: 0;">Başvuru Durumu</h2>
                    <p style="font-size: 18px; margin: 10px 0;">Başvurunuz değerlendirildi</p>
                </div>
                
                <p>"<strong>{{ $title }}</strong>" {{ $type === 'lfg' ? 'ilanına' : 'klanına' }} başvurunuz maalesef kabul edilmedi.</p>
                
                <p>Üzülmeyin! Platformumuzda size uygun birçok {{ $type === 'lfg' ? 'ilan' : 'klan' }} bulabilirsiniz.</p>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="button">{{ $status === 'accepted' ? 'Detayları Gör' : ($type === 'lfg' ? 'Diğer İlanlar' : 'Diğer Klanlar') }}</a>
            </div>
        </div>
        <div class="footer">
            <p>Bu e-posta PUBG Mobile Topluluk Platformu tarafından gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} PUBG Mobile Topluluğu. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
