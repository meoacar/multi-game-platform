@extends('admin.layout')

@section('title', 'Rol Detayı: ' . $role->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.roles.index') }}" class="hover:text-blue-600">Rol Yönetimi</a>
            <span>/</span>
            <span>{{ $role->name }}</span>
        </div>
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">{{ $role->name }}</h1>
            <div class="flex gap-2">
                @if(!$role->is_system)
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Düzenle
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Kolon - Rol Bilgileri -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Temel Bilgiler -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Rol Bilgileri</h2>
                
                <div class="space-y-4">
                    <!-- Rol İkonu ve Rengi -->
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                        <div class="flex-shrink-0 h-16 w-16 rounded-full flex items-center justify-center" style="background-color: {{ $role->color }}20;">
                            <svg class="w-8 h-8" style="color: {{ $role->color }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Renk</div>
                            <div class="font-medium">{{ $role->color }}</div>
                        </div>
                    </div>

                    <!-- Slug -->
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Slug</div>
                        <div class="font-mono text-sm bg-gray-100 px-3 py-2 rounded">{{ $role->slug }}</div>
                    </div>

                    <!-- Açıklama -->
                    @if($role->description)
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Açıklama</div>
                        <div class="text-sm text-gray-900">{{ $role->description }}</div>
                    </div>
                    @endif

                    <!-- Tip -->
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Tip</div>
                        @if($role->is_system)
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Sistem Rolü
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Özel Rol
                            </span>
                        @endif
                    </div>

                    <!-- Tarihler -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Oluşturulma:</span>
                                <span class="font-medium">{{ $role->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Son Güncelleme:</span>
                                <span class="font-medium">{{ $role->updated_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İstatistikler -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">İstatistikler</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <div class="text-sm text-gray-600">Toplam Yetki</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $role->permissions_count }}</div>
                        </div>
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <div class="text-sm text-gray-600">Kullanıcı Sayısı</div>
                            <div class="text-2xl font-bold text-green-600">{{ $role->users_count }}</div>
                        </div>
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon - Yetkiler ve Kullanıcılar -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Yetkiler -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Yetkiler ({{ $role->permissions_count }})</h2>
                
                @if($role->permissions->isEmpty())
                    <p class="text-gray-500 text-center py-8">Bu role henüz yetki atanmamış</p>
                @else
                    @php
                        $groupedPermissions = $role->permissions->groupBy('group');
                    @endphp
                    
                    <div class="space-y-4">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-900 capitalize mb-3">{{ $group }} ({{ $permissions->count() }})</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($permissions as $permission)
                                        <div class="flex items-start gap-2 p-2 bg-gray-50 rounded">
                                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $permission->slug }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Kullanıcılar -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bu Role Sahip Kullanıcılar ({{ $role->users_count }})</h2>
                
                @if($role->users->isEmpty())
                    <p class="text-gray-500 text-center py-8">Bu role sahip kullanıcı bulunmuyor</p>
                @else
                    <div class="space-y-3">
                        @foreach($role->users->take(10) as $user)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    @if($user->profile && $user->profile->avatar)
                                        <img src="{{ $user->profile->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endforeach

                        @if($role->users_count > 10)
                            <div class="text-center pt-2">
                                <span class="text-sm text-gray-500">ve {{ $role->users_count - 10 }} kullanıcı daha...</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
