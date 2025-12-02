@extends('admin.layout')

@section('title', 'Yeni Toplu Bildirim')

@section('content')
<div x-data="notificationForm()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📢 Yeni Toplu Bildirim</h1>
                <p class="text-gray-600 mt-1">Kullanıcılara toplu bildirim gönderin</p>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                ← Geri Dön
            </a>
        </div>
    </div>

    <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Taraf: Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Temel Bilgiler</h2>
                    
                    <!-- Başlık -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Başlık <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            x-model="form.title"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Bildirim başlığı"
                            required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mesaj -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mesaj <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="message" 
                            x-model="form.message"
                            rows="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Bildirim mesajı..."
                            required></textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Karakter sayısı: <span x-text="form.message.length"></span>
                        </p>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bildirim Türü -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bildirim Türü <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.type === 'email' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="email" x-model="form.type" class="sr-only" required>
                                <div class="text-center w-full">
                                    <div class="text-2xl mb-1">📧</div>
                                    <div class="text-sm font-medium">Email</div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.type === 'sms' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="sms" x-model="form.type" class="sr-only">
                                <div class="text-center w-full">
                                    <div class="text-2xl mb-1">📱</div>
                                    <div class="text-sm font-medium">SMS</div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.type === 'push' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="push" x-model="form.type" class="sr-only">
                                <div class="text-center w-full">
                                    <div class="text-2xl mb-1">🔔</div>
                                    <div class="text-sm font-medium">Push</div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.type === 'site' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="type" value="site" x-model="form.type" class="sr-only">
                                <div class="text-center w-full">
                                    <div class="text-2xl mb-1">🌐</div>
                                    <div class="text-sm font-medium">Site İçi</div>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hedef Kitle Seçimi -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🎯 Hedef Kitle</h2>
                    
                    <!-- Hedef Türü -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hedef Türü <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.target_type === 'all' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="target_type" value="all" x-model="form.target_type" @change="updateAudiencePreview()" class="mr-3" required>
                                <div>
                                    <div class="font-medium">Tüm Kullanıcılar</div>
                                    <div class="text-sm text-gray-500">Sistemdeki tüm aktif kullanıcılara gönder</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.target_type === 'segment' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="target_type" value="segment" x-model="form.target_type" @change="updateAudiencePreview()" class="mr-3">
                                <div>
                                    <div class="font-medium">Segment</div>
                                    <div class="text-sm text-gray-500">Belirli kriterlere uyan kullanıcılara gönder</div>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition"
                                   :class="form.target_type === 'custom' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="target_type" value="custom" x-model="form.target_type" @change="updateAudiencePreview()" class="mr-3">
                                <div>
                                    <div class="font-medium">Özel Seçim</div>
                                    <div class="text-sm text-gray-500">Manuel olarak kullanıcı seç</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Segment Kriterleri -->
                    <div x-show="form.target_type === 'segment'" class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Segment Kriterleri</h3>
                        
                        <div class="space-y-3">
                            <!-- Kullanıcı Tipi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kullanıcı Tipi</label>
                                <select name="target_criteria[user_type]" x-model="form.criteria.user_type" @change="updateAudiencePreview()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Tümü</option>
                                    <option value="new">Yeni Kullanıcılar (Son 7 gün)</option>
                                    <option value="active">Aktif Kullanıcılar (Son 30 gün)</option>
                                    <option value="inactive">Pasif Kullanıcılar (30+ gün)</option>
                                </select>
                            </div>

                            <!-- Email Doğrulama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Durumu</label>
                                <select name="target_criteria[email_verified]" x-model="form.criteria.email_verified" @change="updateAudiencePreview()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Tümü</option>
                                    <option value="1">Email Doğrulanmış</option>
                                    <option value="0">Email Doğrulanmamış</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Özel Kullanıcı Seçimi -->
                    <div x-show="form.target_type === 'custom'" class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Kullanıcı Seçimi</h3>
                        <p class="text-sm text-gray-600 mb-3">Kullanıcı ID'lerini virgülle ayırarak girin</p>
                        <textarea 
                            name="user_ids_text"
                            x-model="userIdsText"
                            @input="parseUserIds()"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Örnek: 1, 2, 3, 4, 5"></textarea>
                        <input type="hidden" name="user_ids" :value="JSON.stringify(form.user_ids)">
                    </div>
                </div>

                <!-- Zamanlama -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">⏰ Zamanlama</h2>
                    
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition"
                               :class="!form.scheduled ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" x-model="form.scheduled" :value="false" class="mr-3">
                            <div>
                                <div class="font-medium">Hemen Gönder</div>
                                <div class="text-sm text-gray-500">Bildirimi taslak olarak kaydet</div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition"
                               :class="form.scheduled ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" x-model="form.scheduled" :value="true" class="mr-3">
                            <div>
                                <div class="font-medium">Zamanla</div>
                                <div class="text-sm text-gray-500">Belirli bir tarih ve saatte gönder</div>
                            </div>
                        </label>
                    </div>

                    <div x-show="form.scheduled" class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gönderim Tarihi ve Saati</label>
                        <input 
                            type="datetime-local" 
                            name="scheduled_at"
                            x-model="form.scheduled_at"
                            :min="minDateTime"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Sağ Taraf: Önizleme ve Özet -->
            <div class="space-y-6">
                <!-- Hedef Kitle Özeti -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-900 mb-4">📊 Hedef Kitle Özeti</h3>
                    
                    <div class="space-y-4">
                        <!-- Alıcı Sayısı -->
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="text-sm text-blue-600 mb-1">Toplam Alıcı</div>
                            <div class="text-3xl font-bold text-blue-900" x-text="audienceCount"></div>
                            <div class="text-xs text-blue-600 mt-1">kullanıcı</div>
                        </div>

                        <!-- Bildirim Türü -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Bildirim Türü:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <span x-show="form.type === 'email'">📧 Email</span>
                                <span x-show="form.type === 'sms'">📱 SMS</span>
                                <span x-show="form.type === 'push'">🔔 Push</span>
                                <span x-show="form.type === 'site'">🌐 Site İçi</span>
                                <span x-show="!form.type" class="text-gray-400">Seçilmedi</span>
                            </span>
                        </div>

                        <!-- Hedef Türü -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Hedef Türü:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <span x-show="form.target_type === 'all'">Tüm Kullanıcılar</span>
                                <span x-show="form.target_type === 'segment'">Segment</span>
                                <span x-show="form.target_type === 'custom'">Özel Seçim</span>
                                <span x-show="!form.target_type" class="text-gray-400">Seçilmedi</span>
                            </span>
                        </div>

                        <!-- Zamanlama -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Zamanlama:</span>
                            <span class="text-sm font-medium text-gray-900">
                                <span x-show="!form.scheduled">Taslak</span>
                                <span x-show="form.scheduled">Zamanlanmış</span>
                            </span>
                        </div>

                        <!-- Önizleme Butonu -->
                        <button 
                            type="button"
                            @click="showPreview = !showPreview"
                            class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <span x-show="!showPreview">👁️ Önizleme Göster</span>
                            <span x-show="showPreview">❌ Önizlemeyi Kapat</span>
                        </button>
                    </div>
                </div>

                <!-- Önizleme -->
                <div x-show="showPreview" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">👁️ Önizleme</h3>
                    
                    <!-- Email Önizleme -->
                    <div x-show="form.type === 'email'" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="text-xs text-gray-500 mb-2">Konu:</div>
                        <div class="font-medium text-gray-900 mb-3" x-text="form.title || 'Başlık girilmedi'"></div>
                        <div class="text-xs text-gray-500 mb-2">Mesaj:</div>
                        <div class="text-sm text-gray-700 whitespace-pre-wrap" x-text="form.message || 'Mesaj girilmedi'"></div>
                    </div>

                    <!-- SMS Önizleme -->
                    <div x-show="form.type === 'sms'" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="text-xs text-gray-500 mb-2">SMS İçeriği:</div>
                        <div class="text-sm text-gray-700" x-text="form.message || 'Mesaj girilmedi'"></div>
                        <div class="text-xs text-gray-500 mt-2">
                            Karakter: <span x-text="form.message.length"></span>/160
                        </div>
                    </div>

                    <!-- Push Önizleme -->
                    <div x-show="form.type === 'push'" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                                🔔
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 mb-1" x-text="form.title || 'Başlık girilmedi'"></div>
                                <div class="text-sm text-gray-600" x-text="form.message || 'Mesaj girilmedi'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Site İçi Önizleme -->
                    <div x-show="form.type === 'site'" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 mb-1" x-text="form.title || 'Başlık girilmedi'"></div>
                                <div class="text-sm text-gray-600" x-text="form.message || 'Mesaj girilmedi'"></div>
                                <div class="text-xs text-gray-400 mt-1">Şimdi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Butonları -->
        <div class="flex items-center justify-end space-x-4 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                İptal
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <span>💾</span>
                <span>Bildirimi Kaydet</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function notificationForm() {
    return {
        form: {
            title: '',
            message: '',
            type: 'email',
            target_type: 'all',
            criteria: {
                user_type: '',
                email_verified: ''
            },
            user_ids: [],
            scheduled: false,
            scheduled_at: ''
        },
        userIdsText: '',
        audienceCount: 0,
        showPreview: false,
        minDateTime: '',

        init() {
            // Minimum tarih/saat ayarla (şu andan 5 dakika sonra)
            const now = new Date();
            now.setMinutes(now.getMinutes() + 5);
            this.minDateTime = now.toISOString().slice(0, 16);
            
            // İlk yüklemede hedef kitle sayısını al
            this.updateAudiencePreview();
        },

        parseUserIds() {
            // Virgülle ayrılmış ID'leri parse et
            const ids = this.userIdsText
                .split(',')
                .map(id => parseInt(id.trim()))
                .filter(id => !isNaN(id) && id > 0);
            
            this.form.user_ids = ids;
            this.updateAudiencePreview();
        },

        async updateAudiencePreview() {
            try {
                const response = await fetch('{{ route("admin.notifications.preview-audience") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        target_type: this.form.target_type,
                        target_criteria: this.form.criteria,
                        user_ids: this.form.user_ids
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.audienceCount = data.count;
                }
            } catch (error) {
                console.error('Hedef kitle önizleme hatası:', error);
            }
        }
    }
}
</script>
@endpush
@endsection
