@extends('admin.layout')

@section('title', 'Rapor Yönetimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">⚠️ Rapor Yönetimi</h1>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tümü</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Çözüldü</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Türü</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Tümü</option>
                    <option value="App\Models\LfgPost" {{ request('type') == 'App\Models\LfgPost' ? 'selected' : '' }}>LFG İlanı</option>
                    <option value="App\Models\Clan" {{ request('type') == 'App\Models\Clan' ? 'selected' : '' }}>Klan</option>
                    <option value="App\Models\GuidePost" {{ request('type') == 'App\Models\GuidePost' ? 'selected' : '' }}>Rehber</option>
                    <option value="App\Models\CommunityPost" {{ request('type') == 'App\Models\CommunityPost' ? 'selected' : '' }}>Topluluk</option>
                    <option value="App\Models\User" {{ request('type') == 'App\Models\User' ? 'selected' : '' }}>Kullanıcı</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Rapor Listesi -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($reports->isEmpty())
            <div class="p-12 text-center text-gray-500">
                Rapor bulunamadı
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rapor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şikayetçi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İçerik Türü</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($reports as $report)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-red-600">{{ $report->reason }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $report->reporter->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($report->reportable_type === 'App\Models\LfgPost')
                                LFG İlanı
                            @elseif($report->reportable_type === 'App\Models\Clan')
                                Klan
                            @elseif($report->reportable_type === 'App\Models\GuidePost')
                                Rehber
                            @elseif($report->reportable_type === 'App\Models\CommunityPost')
                                Topluluk
                            @elseif($report->reportable_type === 'App\Models\User')
                                Kullanıcı
                            @else
                                {{ class_basename($report->reportable_type) }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $report->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $report->status === 'pending' ? 'Bekliyor' : '' }}
                                {{ $report->status === 'resolved' ? 'Çözüldü' : '' }}
                                {{ $report->status === 'rejected' ? 'Reddedildi' : '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $report->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.reports.show', $report->id) }}" 
                                class="text-blue-600 hover:underline">
                                İncele
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</div>
@endsection
