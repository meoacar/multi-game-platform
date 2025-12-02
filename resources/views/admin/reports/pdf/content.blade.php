<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İçerik Analitiği Raporu</title>
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
            border-bottom: 2px solid #10b981;
        }
        .header h1 {
            color: #047857;
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
            background-color: #10b981;
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
        <h1>İçerik Analitiği Raporu</h1>
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
                <div class="stat-label">Toplam İçerik</div>
                <div class="stat-value">{{ number_format($analytics['total_content'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">LFG İlanları</div>
                <div class="stat-value">{{ number_format($analytics['lfg_posts'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Klanlar</div>
                <div class="stat-value">{{ number_format($analytics['clans'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Rehberler</div>
                <div class="stat-value">{{ number_format($analytics['guides'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Topluluk Gönderileri</div>
                <div class="stat-value">{{ number_format($analytics['community_posts'] ?? 0) }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Yorumlar</div>
                <div class="stat-value">{{ number_format($analytics['comments'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- İçerik Türü Dağılımı -->
    @if(isset($analytics['content_type_distribution']) && count($analytics['content_type_distribution']) > 0)
    <div class="section">
        <div class="section-title">İçerik Türü Dağılımı</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>İçerik Türü</th>
                    <th>Sayı</th>
                    <th>Yüzde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['content_type_distribution'] as $item)
                <tr>
                    <td>{{ $item['type'] }}</td>
                    <td>{{ number_format($item['count']) }}</td>
                    <td>{{ number_format($item['percentage'], 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Popüler İçerikler -->
    @if(isset($analytics['popular_content']) && count($analytics['popular_content']) > 0)
    <div class="section">
        <div class="section-title">Popüler İçerikler (Top 10)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Tür</th>
                    <th>Görüntülenme</th>
                    <th>Beğeni</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($analytics['popular_content'], 0, 10) as $item)
                <tr>
                    <td>{{ Str::limit($item['title'] ?? 'N/A', 50) }}</td>
                    <td>{{ $item['type'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['views'] ?? 0) }}</td>
                    <td>{{ number_format($item['likes'] ?? 0) }}</td>
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
