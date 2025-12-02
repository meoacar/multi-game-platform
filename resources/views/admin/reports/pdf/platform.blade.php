<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Analitiği Raporu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f59e0b;
        }
        .header h1 {
            color: #d97706;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header .meta {
            color: #6b7280;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f59e0b;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-item {
            display: table-row;
        }
        .stat-label {
            display: table-cell;
            padding: 8px;
            background-color: #f3f4f6;
            font-weight: bold;
            width: 50%;
        }
        .stat-value {
            display: table-cell;
            padding: 8px;
            background-color: #ffffff;
            border-left: 1px solid #e5e7eb;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #e5e7eb;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Platform Analitiği Raporu</h1>
        <div class="meta">
            <strong>Tarih Aralığı:</strong> {{ $dateRange }}<br>
            <strong>Oluşturulma Tarihi:</strong> {{ $generatedAt }}
        </div>
    </div>

    <!-- Genel İstatistikler -->
    <div class="section">
        <div class="section-title">Genel İstatistikler</div>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Toplam Sayfa Görüntüleme</div>
                <div class="stat-value">{{ number_format($analytics['total_page_views'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Ortalama Oturum Süresi</div>
                <div class="stat-value">{{ number_format($analytics['avg_session_duration'] ?? 0) }} dakika</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Bounce Rate</div>
                <div class="stat-value">{{ number_format($analytics['bounce_rate'] ?? 0, 2) }}%</div>
            </div>
        </div>
    </div>

    <!-- Popüler Sayfalar -->
    @if(isset($analytics['popular_pages']) && count($analytics['popular_pages']) > 0)
    <div class="section">
        <div class="section-title">Popüler Sayfalar</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sayfa</th>
                    <th>Görüntülenme</th>
                    <th>Yüzde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['popular_pages'] as $item)
                <tr>
                    <td>{{ $item['page'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['views'] ?? 0) }}</td>
                    <td>{{ number_format($item['percentage'] ?? 0, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Cihaz Dağılımı -->
    @if(isset($analytics['device_distribution']) && count($analytics['device_distribution']) > 0)
    <div class="section">
        <div class="section-title">Cihaz Dağılımı</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cihaz</th>
                    <th>Kullanıcı Sayısı</th>
                    <th>Yüzde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['device_distribution'] as $item)
                <tr>
                    <td>{{ $item['device'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['count'] ?? 0) }}</td>
                    <td>{{ number_format($item['percentage'] ?? 0, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Tarayıcı Dağılımı -->
    @if(isset($analytics['browser_distribution']) && count($analytics['browser_distribution']) > 0)
    <div class="section">
        <div class="section-title">Tarayıcı Dağılımı</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tarayıcı</th>
                    <th>Kullanıcı Sayısı</th>
                    <th>Yüzde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['browser_distribution'] as $item)
                <tr>
                    <td>{{ $item['browser'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['count'] ?? 0) }}</td>
                    <td>{{ number_format($item['percentage'] ?? 0, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>PUBG Mobile Topluluk Platformu - Admin Panel</p>
        <p>Bu rapor otomatik olarak oluşturulmuştur.</p>
    </div>
</body>
</html>
