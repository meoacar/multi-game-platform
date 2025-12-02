@extends('admin.layout')

@section('title', 'Yedekleme Yönetimi')

@section('content')
<div x-data="backupManager()" x-init="init()">
    <!-- Sayfa Başlığı -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">💾 Yedekleme Yönetimi</h1>
            <p class="text-gray-600 mt-1">Veritabanı ve dosya yedekleme işlemleri</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="showCreateModal = true" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Yeni Yedek Oluştur</span>
            </button>
            <button @click="refreshBackups()" 
                    :disabled="loading"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                <svg class="w-5 h-5" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Yenile</span>
            </button>
        </div>
    </div>

    <!-- Yapılandırma Uyarısı -->
    @if(!$configuration['is_configured'])
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-yellow-800 font-semibold mb-2">⚠️ Yedekleme Yapılandırması Eksik</h3>
                <ul class="text-yellow-700 text-sm space-y-1 mb-3">
                    @foreach($configuration['issues'] as $issue)
                    <li>• {{ $issue }}</li>
                    @endforeach
                </ul>
                <div class="bg-yellow-100 border border-yellow-300 rounded p-3 text-sm text-yellow-800">
                    <p class="font-semibold mb-2">💡 Çözüm:</p>
                    <p class="mb-2">MySQL komutlarını PATH'e eklemek için:</p>
                    <ol class="list-decimal ml-5 space-y-1">
                        <li>MySQL kurulum dizinini bulun (örn: <code class="bg-yellow-200 px-1 rounded">C:\xampp\mysql\bin</code>)</li>
                        <li>Windows Sistem Ayarları → Gelişmiş Sistem Ayarları → Ortam Değişkenleri</li>
                        <li>Path değişkenine MySQL bin klasörünü ekleyin</li>
                        <li>Terminali yeniden başlatın</li>
                    </ol>
                    <p class="mt-3 text-xs">
                        <strong>Not:</strong> MySQL komutları olmadan sadece <strong>dosya yedekleme</strong> yapabilirsiniz. 
                        Veritabanı yedekleme için MySQL komutları gereklidir.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Toplam Yedek -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Toplam Yedek</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_backups'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">💾</span>
                </div>
            </div>
        </div>

        <!-- Başarılı Yedekler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Başarılı</p>
                    <p class="text-3xl font-bold text-green-600">{{ $statistics['completed_backups'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <!-- Başarısız Yedekler -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Başarısız</p>
                    <p class="text-3xl font-bold text-red-600">{{ $statistics['failed_backups'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">❌</span>
                </div>
            </div>
        </div>

        <!-- Toplam Boyut -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Toplam Boyut</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $statistics['total_size'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📦</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk Kullanımı -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Disk Kullanımı</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-2">Yedekleme Dizini</p>
                <p class="text-lg font-semibold text-gray-900">{{ $directoryInfo['total_size_human'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $directoryInfo['file_count'] }} dosya</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-2">Son Yedek</p>
                <p class="text-lg font-semibold text-gray-900">
                    @if($statistics['last_backup'])
                        {{ $statistics['last_backup']->created_at->diffForHumans() }}
                    @else
                        Henüz yedek yok
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-2">Başarı Oranı (30 gün)</p>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($statistics['success_rate'], 1) }}%</p>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Yedek Tipi</label>
                <select x-model="filters.type" @change="filterBackups()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="database">Veritabanı</option>
                    <option value="files">Dosyalar</option>
                    <option value="full">Tam Yedek</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600 mb-1 block">Durum</label>
                <select x-model="filters.status" @change="filterBackups()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="failed">Başarısız</option>
                    <option value="running">Devam Ediyor</option>
                </select>
            </div>
            <div class="ml-auto">
                <label class="text-sm text-gray-600 mb-1 block">&nbsp;</label>
                <button @click="cleanupOldBackups()" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Eski Yedekleri Temizle</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Yedek Listesi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Yedek Listesi</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boyut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oluşturan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="backup in filteredBackups" :key="backup.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span x-text="getTypeIcon(backup.type)" class="text-2xl mr-2"></span>
                                    <span x-text="getTypeLabel(backup.type)" class="text-sm font-medium text-gray-900"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                      :class="getStatusClass(backup.status)"
                                      x-text="getStatusLabel(backup.status)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatBytes(backup.file_size)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="backup.creator?.name || 'Sistem'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(backup.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button @click="downloadBackup(backup.id)" 
                                            x-show="backup.status === 'completed'"
                                            class="text-blue-600 hover:text-blue-900" title="İndir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </button>
                                    <button @click="confirmRestore(backup)" 
                                            x-show="backup.status === 'completed'"
                                            class="text-green-600 hover:text-green-900" title="Geri Yükle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(backup.id)" 
                                            class="text-red-600 hover:text-red-900" title="Sil">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredBackups.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Henüz yedek bulunmuyor
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yedek Oluşturma Modal -->
    <div x-show="showCreateModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showCreateModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Yeni Yedek Oluştur</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Yedek Tipi</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg {{ $configuration['is_configured'] ? 'cursor-pointer hover:bg-gray-50' : 'opacity-50 cursor-not-allowed' }}" :class="{ 'border-blue-500 bg-blue-50': createForm.type === 'database' }">
                                <input type="radio" x-model="createForm.type" value="database" class="mr-3" {{ $configuration['is_configured'] ? '' : 'disabled' }}>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">🗄️</span>
                                        <span class="font-medium">Veritabanı</span>
                                        @if(!$configuration['is_configured'])
                                        <span class="ml-2 text-xs bg-red-100 text-red-600 px-2 py-1 rounded">MySQL Gerekli</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Sadece veritabanını yedekle</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-blue-500 bg-blue-50': createForm.type === 'files' }">
                                <input type="radio" x-model="createForm.type" value="files" class="mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">📁</span>
                                        <span class="font-medium">Dosyalar</span>
                                        <span class="ml-2 text-xs bg-green-100 text-green-600 px-2 py-1 rounded">✓ Kullanılabilir</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Sadece dosyaları yedekle</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg {{ $configuration['is_configured'] ? 'cursor-pointer hover:bg-gray-50' : 'opacity-50 cursor-not-allowed' }}" :class="{ 'border-blue-500 bg-blue-50': createForm.type === 'full' }">
                                <input type="radio" x-model="createForm.type" value="full" class="mr-3" {{ $configuration['is_configured'] ? '' : 'disabled' }}>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">💾</span>
                                        <span class="font-medium">Tam Yedek</span>
                                        @if(!$configuration['is_configured'])
                                        <span class="ml-2 text-xs bg-red-100 text-red-600 px-2 py-1 rounded">MySQL Gerekli</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Veritabanı + Dosyalar</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button @click="showCreateModal = false" 
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    İptal
                </button>
                <button @click="createBackup()" 
                        :disabled="creating || !createForm.type"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!creating">Oluştur</span>
                    <span x-show="creating">Oluşturuluyor...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function backupManager() {
    return {
        loading: false,
        creating: false,
        showCreateModal: false,
        backups: @json($recentBackups),
        filteredBackups: [],
        filters: {
            type: '',
            status: ''
        },
        createForm: {
            type: '{{ $configuration['is_configured'] ? 'database' : 'files' }}'
        },

        init() {
            this.filteredBackups = this.backups;
        },

        async refreshBackups() {
            this.loading = true;
            try {
                const response = await fetch('/admin/backup/list');
                const data = await response.json();
                if (data.success) {
                    this.backups = data.data;
                    this.filterBackups();
                }
            } catch (error) {
                console.error('Yedekler yüklenirken hata:', error);
                alert('Yedekler yüklenirken bir hata oluştu');
            } finally {
                this.loading = false;
            }
        },

        filterBackups() {
            this.filteredBackups = this.backups.filter(backup => {
                if (this.filters.type && backup.type !== this.filters.type) return false;
                if (this.filters.status && backup.status !== this.filters.status) return false;
                return true;
            });
        },

        async createBackup() {
            if (!this.createForm.type) {
                alert('Lütfen yedek tipi seçin');
                return;
            }

            if (!confirm('Yedek oluşturmak istediğinize emin misiniz? Bu işlem biraz zaman alabilir.')) {
                return;
            }

            this.creating = true;
            try {
                const response = await fetch('/admin/backup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(this.createForm)
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.showCreateModal = false;
                    this.createForm.type = '{{ $configuration['is_configured'] ? 'database' : 'files' }}';
                    await this.refreshBackups();
                    window.location.reload();
                } else {
                    alert('Hata: ' + data.message);
                }
            } catch (error) {
                console.error('Yedek oluşturulurken hata:', error);
                alert('Yedek oluşturulurken bir hata oluştu');
            } finally {
                this.creating = false;
            }
        },

        downloadBackup(id) {
            window.location.href = `/admin/backup/${id}/download`;
        },

        async confirmRestore(backup) {
            if (!confirm(`${this.getTypeLabel(backup.type)} yedeğini geri yüklemek istediğinize emin misiniz?\n\nUYARI: Bu işlem mevcut verilerin üzerine yazacaktır!`)) {
                return;
            }

            if (!confirm('SON UYARI: Bu işlem geri alınamaz! Devam etmek istediğinize emin misiniz?')) {
                return;
            }

            try {
                const response = await fetch(`/admin/backup/${backup.id}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Hata: ' + data.message);
                }
            } catch (error) {
                console.error('Yedek geri yüklenirken hata:', error);
                alert('Yedek geri yüklenirken bir hata oluştu');
            }
        },

        async confirmDelete(id) {
            if (!confirm('Bu yedeği silmek istediğinize emin misiniz?')) {
                return;
            }

            try {
                const response = await fetch(`/admin/backup/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await this.refreshBackups();
                } else {
                    alert('Hata: ' + data.message);
                }
            } catch (error) {
                console.error('Yedek silinirken hata:', error);
                alert('Yedek silinirken bir hata oluştu');
            }
        },

        async cleanupOldBackups() {
            if (!confirm('30 günden eski yedekleri temizlemek istediğinize emin misiniz?')) {
                return;
            }

            try {
                const response = await fetch('/admin/backup/cleanup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ type: this.filters.type || null })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await this.refreshBackups();
                    window.location.reload();
                } else {
                    alert('Hata: ' + data.message);
                }
            } catch (error) {
                console.error('Yedekler temizlenirken hata:', error);
                alert('Yedekler temizlenirken bir hata oluştu');
            }
        },

        getTypeIcon(type) {
            const icons = {
                'database': '🗄️',
                'files': '📁',
                'full': '💾'
            };
            return icons[type] || '📦';
        },

        getTypeLabel(type) {
            const labels = {
                'database': 'Veritabanı',
                'files': 'Dosyalar',
                'full': 'Tam Yedek'
            };
            return labels[type] || type;
        },

        getStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800',
                'failed': 'bg-red-100 text-red-800',
                'running': 'bg-blue-100 text-blue-800',
                'pending': 'bg-yellow-100 text-yellow-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusLabel(status) {
            const labels = {
                'completed': 'Tamamlandı',
                'failed': 'Başarısız',
                'running': 'Devam Ediyor',
                'pending': 'Bekliyor'
            };
            return labels[status] || status;
        },

        formatBytes(bytes) {
            if (!bytes) return 'N/A';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let size = bytes;
            let unitIndex = 0;
            
            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }
            
            return `${size.toFixed(2)} ${units[unitIndex]}`;
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleString('tr-TR');
        }
    }
}
</script>
@endpush
@endsection
