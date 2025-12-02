<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamanlanmış Analitik Raporu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .info-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }
        .info-box h2 {
            margin-top: 0;
            color: #1e40af;
            font-size: 18px;
        }
        .info-item {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
        }
        .info-value {
            color: #111827;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6b7280;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Zamanlanmış Analitik Raporu</h1>
    </div>
    
    <div class="content">
        <div class="info-box">
            <h2>Rapor Bilgileri</h2>
            <div class="info-item">
                <span class="info-label">Rapor Adı:</span>
                <span class="info-value">{{ $reportName }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Rapor Türü:</span>
                <span class="info-value">{{ $reportType }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Gönderim Sıklığı:</span>
                <span class="info-value">{{ $frequency }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Oluşturulma Tarihi:</span>
                <span class="info-value">{{ now()->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        <p>Merhaba,</p>
        <p>
            Zamanlanmış analitik raporunuz hazır. Rapor dosyaları bu email'e eklenmiştir.
            Detaylı analitik verilerini incelemek için ekteki dosyaları açabilirsiniz.
        </p>

        <p>
            <strong>Not:</strong> Bu rapor otomatik olarak oluşturulmuş ve gönderilmiştir.
            Zamanlanmış raporlarınızı admin panelinden yönetebilirsiniz.
        </p>
    </div>
    
    <div class="footer">
        <p><strong>PUBG Mobile Topluluk Platformu</strong></p>
        <p>Admin Panel - Analitik Sistemi</p>
        <p style="margin-top: 10px; font-size: 11px;">
            Bu email otomatik olarak gönderilmiştir. Lütfen yanıtlamayın.
        </p>
    </div>
</body>
</html>
