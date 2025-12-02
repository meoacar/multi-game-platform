<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Analitiği Raporu</title>
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
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #1e40af;
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
            background-color: #3b82f6;
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
        <h1>Kullanıcı Analitiği Raporu</h1>
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
                <div class="stat-label">Toplam Kullanıcı</div>
                <div class="stat-value">{{ number_format($analytics['total_users'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Aktif Kullanıcı</div>
                <div class="stat-value">{{ number_format($analytics['active_users'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Yeni Kayıtlar</div>
                <div class="stat-value">{{ number_format($analytics['new_registrations'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Banlı Kullanıcı</div>
                <div class="stat-value">{{ number_format($analytics['banned_users'] ?? 0) }}</div>
            </div>
            @if(isset($analytics['churn_rate']))
            <div class="stat-item">
                <div class="stat-label">Churn Rate (Kayıp Oranı)</div>
                <div class="stat-value">{{ number_format($analytics['churn_rate'], 2) }}%</div>
            </div>
            @endif
            @if(isset($analytics['retention_rate']))
            <div class="stat-item">
                <div class="stat-label">Retention Rate (Elde Tutma)</div>
                <div class="stat-value">{{ number_format($analytics['retention_rate'], 2) }}%</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Kayıt Trendi -->
    @if(isset($analytics['registration_trend']) && count($analytics['registration_trend']) > 0)
    <div class="section">
        <div class="section-title">Kayıt Trendi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Kayıt Sayısı</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['registration_trend'] as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ number_format($item['count']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Kullanıcı Segmentasyonu -->
    @if(isset($analytics['segmentation']) && count($analytics['segmentation']) > 0)
    <div class="section">
        <div class="section-title">Kullanıcı Segmentasyonu</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Segment</th>
                    <th>Kullanıcı Sayısı</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['segmentation'] as $segment => $count)
                <tr>
                    <td>{{ ucfirst($segment) }}</td>
                    <td>{{ number_format($count) }}</td>
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
