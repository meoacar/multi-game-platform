@extends('admin.layout')

@section('title', 'Email Şablonu Düzenle')

@section('content')
<div x-data="templateEditor()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">✏️ Email Şablonu Düzenle</h1>
                <p class="text-gray-600 mt-1">{{ $template->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.notifications.templates.preview', $template->id) }}" 
                   class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition"
                   target="_blank">
                    👁️ Önizle
                </a>
                <a href="{{ route('admin.notifications.templates') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Geri Dön
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.notifications.templates.update', $template->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Taraf: Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Temel Bilgiler</h2>
                    
                    <!-- Şablon Adı -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Şablon Adı <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="form.name"
                            @input="generateSlug()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Örn: Hoş Geldin Emaili"
                            required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Slug
                            <span class="text-xs text-gray-500">(Otomatik oluşturulur)</span>
                        </label>
                        <input 
                            type="text" 
                            name="slug" 
                            x-model="form.slug"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                            placeholder="hosgeldin-emaili"
                            readonly>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Konusu -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Konusu <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="subject" 
                            x-model="form.subject"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Örn: {{site_name}} Platformuna Hoş Geldiniz!"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            💡 Değişken kullanabilirsiniz: {{name}}, {{email}}, {{site_name}} vb.
                        </p>
                        @error('subject')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durum -->
                    <div>
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                x-model="form.is_active"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">
                                Şablonu aktif et
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-8">
                            Aktif şablonlar bildirim gönderiminde kullanılabilir
                        </p>
                    </div>
                </div>

                <!-- Email İçeriği -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">✉️ Email İçeriği</h2>
                    
                    <!-- Editör Toolbar -->
                    <div class="mb-3 flex items-center space-x-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                        <button type="button" @click="insertVariable('name')" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            {{name}}
                        </button>
                        <button type="button" @click="insertVariable('email')" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            {{email}}
                        </button>
                        <button type="button" @click="insertVariable('site_name')" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            {{site_name}}
                        </button>
                        <button type="button" @click="insertVariable('site_url')" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            {{site_url}}
                        </button>
                        <button type="button" @click="showVariableModal = true" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            + Özel Değişken
                        </button>
                    </div>

                    <!-- Editör -->
                    <textarea 
                        name="body" 
                        x-model="form.body"
                        x-ref="bodyEditor"
                        rows="15"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                        placeholder="Email içeriğini buraya yazın... HTML kullanabilirsiniz."
                        required></textarea>
                    
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-xs text-gray-500">
                            💡 HTML etiketleri kullanabilirsiniz: &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;, &lt;br&gt; vb.
                        </p>
                        <p class="text-xs text-gray-500">
                            Karakter: <span x-text="form.body.length"></span>
                        </p>
                    </div>
                    
                    @error('body')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Değişkenler -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">🔧 Değişkenler</h2>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        Bu şablonda kullanılabilecek değişkenleri tanımlayın. Her değişken için bir açıklama ekleyin.
                    </p>

                    <!-- Değişken Listesi -->
                    <div class="space-y-3">
                        <template x-for="(variable, index) in form.variables" :key="index">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <input 
                                    type="text" 
                                    :name="'variables[' + index + ']'"
                                    x-model="form.variables[index]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                    placeholder="Değişken adı (örn: user_name)">
                                <button 
                                    type="button"
                                    @click="removeVariable(index)"
                                    class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                    🗑️
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Değişken Ekle Butonu -->
                    <button 
                        type="button"
                        @click="addVariable()"
                        class="mt-3 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center justify-center space-x-2">
                        <span>➕</span>
                        <span>Değişken Ekle</span>
                    </button>
                </div>
            </div>

            <!-- Sağ Taraf: Önizleme ve Bilgi -->
            <div class="space-y-6">
                <!-- Şablon Bilgileri -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">ℹ️ Şablon Bilgileri</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Oluşturulma:</span>
                            <span class="font-medium text-gray-900">{{ $template->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Son Güncelleme:</span>
                            <span class="font-medium text-gray-900">{{ $template->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-600">Durum:</span>
                            @if($template->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    ⏸️ Pasif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Önizleme -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center justify-between">
                        <span>👁️ Önizleme</span>
                        <button 
                            type="button"
                            @click="showPreview = !showPreview"
                            class="text-sm text-blue-600 hover:text-blue-800">
                            <span x-show="!showPreview">Göster</span>
                            <span x-show="showPreview">Gizle</span>
                        </button>
                    </h3>
                    
                    <div x-show="showPreview" class="space-y-4">
                        <!-- Konu Önizleme -->
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Konu:</div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-medium text-gray-900" x-text="renderPreview(form.subject)"></p>
                            </div>
                        </div>

                        <!-- İçerik Önizleme -->
                        <div>
                            <div class="text-xs text-gray-500 mb-1">İçerik:</div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 max-h-96 overflow-y-auto">
                                <div class="text-sm text-gray-700 prose prose-sm max-w-none" x-html="renderPreview(form.body)"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yardım -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                    <h3 class="font-semibold text-blue-900 mb-3">💡 Yardım</h3>
                    
                    <div class="space-y-3 text-sm text-blue-800">
                        <div>
                            <strong>Değişken Kullanımı:</strong>
                            <p class="text-xs mt-1">Değişkenleri {{variable_name}} formatında kullanın</p>
                        </div>

                        <div>
                            <strong>Varsayılan Değişkenler:</strong>
                            <ul class="text-xs mt-1 space-y-1 ml-4 list-disc">
                                <li>{{name}} - Kullanıcı adı</li>
                                <li>{{email}} - Email adresi</li>
                                <li>{{site_name}} - Site adı</li>
                                <li>{{site_url}} - Site URL'i</li>
                            </ul>
                        </div>

                        <div>
                            <strong>HTML Kullanımı:</strong>
                            <p class="text-xs mt-1">Temel HTML etiketlerini kullanabilirsiniz</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Butonları -->
        <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.notifications.templates.destroy', $template->id) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Bu şablonu silmek istediğinize emin misiniz?')">
                @csrf
                @method('DELETE')
                <button 
                    type="submit"
                    class="px-6 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition flex items-center space-x-2">
                    <span>🗑️</span>
                    <span>Şablonu Sil</span>
                </button>
            </form>

            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.notifications.templates') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    İptal
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                    <span>💾</span>
                    <span>Değişiklikleri Kaydet</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Özel Değişken Modal -->
    <div x-show="showVariableModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showVariableModal = false">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Özel Değişken Ekle</h3>
            
            <input 
                type="text" 
                x-model="customVariable"
                @keydown.enter="insertCustomVariable()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4"
                placeholder="Değişken adı (örn: activation_link)">
            
            <div class="flex items-center justify-end space-x-3">
                <button 
                    type="button"
                    @click="showVariableModal = false"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    İptal
                </button>
                <button 
                    type="button"
                    @click="insertCustomVariable()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ekle
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function templateEditor() {
    return {
        form: {
            name: @json($template->name),
            slug: @json($template->slug),
            subject: @json($template->subject),
            body: @json($template->body),
            is_active: @json($template->is_active),
            variables: @json($template->variables ?? [])
        },
        showPreview: true,
        showVariableModal: false,
        customVariable: '',

        generateSlug() {
            // Türkçe karakterleri değiştir ve slug oluştur
            const turkishMap = {
                'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
                'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u'
            };
            
            this.form.slug = this.form.name
                .split('')
                .map(char => turkishMap[char] || char)
                .join('')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },

        insertVariable(variable) {
            const textarea = this.$refs.bodyEditor;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = this.form.body;
            const before = text.substring(0, start);
            const after = text.substring(end);
            
            this.form.body = before + '{{' + variable + '}}' + after;
            
            // Cursor pozisyonunu ayarla
            this.$nextTick(() => {
                textarea.focus();
                const newPos = start + variable.length + 4;
                textarea.setSelectionRange(newPos, newPos);
            });
        },

        insertCustomVariable() {
            if (this.customVariable.trim()) {
                this.insertVariable(this.customVariable.trim());
                this.customVariable = '';
                this.showVariableModal = false;
            }
        },

        addVariable() {
            this.form.variables.push('');
        },

        removeVariable(index) {
            this.form.variables.splice(index, 1);
        },

        renderPreview(text) {
            if (!text) return 'İçerik girilmedi';
            
            // Örnek verilerle değişkenleri değiştir
            const sampleData = {
                name: 'Ahmet Yılmaz',
                email: 'ahmet@example.com',
                site_name: '{{ config("app.name") }}',
                site_url: '{{ url("/") }}'
            };
            
            let preview = text;
            for (const [key, value] of Object.entries(sampleData)) {
                preview = preview.replace(new RegExp('{{' + key + '}}', 'g'), value);
            }
            
            return preview;
        }
    }
}
</script>
@endpush
@endsection
