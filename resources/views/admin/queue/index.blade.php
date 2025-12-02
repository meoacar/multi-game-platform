@extends('admin.layout')

@section('title', 'Queue Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📬 Queue Yönetimi</h1>
            <p class="text-gray-600 mt-1">E-posta, bildirim ve XP işlemlerini yönetin</p>
        </div>
        <div class="flex gap-3">
            <button onclick="checkWorkerStatus()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                🔄 Worker Durumu
            </button>
            <button onclick="document.getElementById('testJobModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                🧪 Test Job Gönder
            </button>
        </div>
    </div>

    <!-- Worker Status Alert -->
    <div id="workerStatus" class="hidden"></div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Toplam Bekleyen</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_pending'] }}</p>
                </div>
                <div class="text-4xl">⏳</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">E-postalar</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['emails_pending'] }}</p>
                </div>
                <div class="text-4xl">📧</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bildirimler</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['notifications_pending'] }}</p>
                </div>
                <div class="text-4xl">🔔</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Genel</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['default_pending'] }}</p>
                </div>
                <div class="text-4xl">⚙️</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Başarısız</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['total_failed'] }}</p>
                </div>
                <div class="text-4xl">❌</div>
            </div>
        </div>
    </div>

    <!-- Queue'lar -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">📊 Queue Durumu</h2>
        </div>
        <div class="p-6">
            @if($pendingJobs->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">✅</div>
                    <p class="text-gray-600">Bekleyen job yok! Tüm işler tamamlandı.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingJobs as $queue)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl">
                                    @if($queue->queue === 'emails')
                                        📧
                                    @elseif($queue->queue === 'notifications')
                                        🔔
                                    @else
                                        ⚙️
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">{{ ucfirst($queue->queue) }} Queue</h3>
                                    <p class="text-sm text-gray-600">{{ $queue->count }} bekleyen job</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $queue->count }} job
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Failed Jobs -->
    @if($stats['total_failed'] > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-red-600">❌ Başarısız Job'lar</h2>
                <div class="flex gap-2">
                    <form action="{{ route('admin.queue.retry-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            🔄 Tümünü Tekrar Dene
                        </button>
                    </form>
                    <form action="{{ route('admin.queue.flush') }}" method="POST" class="inline" onsubmit="return confirm('Tüm başarısız job\'ları silmek istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                            🗑️ Tümünü Temizle
                        </button>
                    </form>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exception</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başarısız Olma</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($failedJobs as $job)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">
                                            {{ $job->queue }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 truncate max-w-md">
                                            {{ Str::limit($job->exception, 100) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.queue.show-failed', $job->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detay</a>
                                        <form action="{{ route('admin.queue.retry', $job->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Tekrar Dene</button>
                                        </form>
                                        <form action="{{ route('admin.queue.delete-failed', $job->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu job\'ı silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $failedJobs->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- Kullanım Kılavuzu -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">📚 Queue Worker Nasıl Çalıştırılır?</h3>
        <div class="space-y-2 text-sm text-blue-800">
            <p><strong>1. Tüm queue'ları çalıştır:</strong></p>
            <code class="block bg-blue-100 p-2 rounded">php artisan queue:work</code>
            
            <p class="mt-3"><strong>2. Sadece emails queue'sunu çalıştır:</strong></p>
            <code class="block bg-blue-100 p-2 rounded">php artisan queue:work --queue=emails</code>
            
            <p class="mt-3"><strong>3. Sadece notifications queue'sunu çalıştır:</strong></p>
            <code class="block bg-blue-100 p-2 rounded">php artisan queue:work --queue=notifications</code>
            
            <p class="mt-3"><strong>4. Arka planda çalıştır (production):</strong></p>
            <code class="block bg-blue-100 p-2 rounded">php artisan queue:work --daemon</code>
        </div>
    </div>
</div>

<!-- Test Job Modal -->
<div id="testJobModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🧪 Test Job Gönder</h3>
            <form action="{{ route('admin.queue.test') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job Tipi</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg">
                        <option value="notification">📬 Bildirim Job</option>
                        <option value="email">📧 E-posta Job</option>
                        <option value="xp">⭐ XP İşleme Job</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Gönder
                    </button>
                    <button type="button" onclick="document.getElementById('testJobModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkWorkerStatus() {
    fetch('{{ route('admin.queue.check-worker') }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('workerStatus');
            statusDiv.classList.remove('hidden');
            
            if (data.is_running) {
                statusDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="text-2xl">✅</div>
                            <div>
                                <p class="font-semibold text-green-900">${data.message}</p>
                                <p class="text-sm text-green-700">Son 5 dakikada ${data.recent_jobs} job işlendi.</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="text-2xl">⚠️</div>
                            <div>
                                <p class="font-semibold text-red-900">${data.message}</p>
                                <p class="text-sm text-red-700">Komut: <code class="bg-red-100 px-2 py-1 rounded">php artisan queue:work</code></p>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
}

// Sayfa yüklendiğinde worker durumunu kontrol et
document.addEventListener('DOMContentLoaded', function() {
    checkWorkerStatus();
});
</script>
@endsection
