@extends('admin.layout')

@section('title', 'Yorum Düzenle')

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Yorum Düzenle</h1>
        <div>
            <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-secondary me-2">
                <i class="fas fa-eye"></i> Detay
            </a>
            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Form -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Yorum Bilgileri</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.comments.update', $comment->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- İçerik -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Yorum İçeriği <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="6" required>{{ old('content', $comment->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimum 1000 karakter</small>
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                                İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-md-4">
            <!-- Kullanıcı Bilgileri -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Kullanıcı</h5>
                </div>
                <div class="card-body">
                    @if($comment->user)
                    <div class="d-flex align-items-center mb-3">
                        @if($comment->user->profile && $comment->user->profile->avatar)
                            <img src="{{ asset('storage/' . $comment->user->profile->avatar) }}" 
                                 class="rounded-circle me-2" width="50" height="50" alt="">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold">{{ $comment->user->name }}</div>
                            <small class="text-muted">{{ $comment->user->email }}</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $comment->user->id) }}" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-user"></i> Profili Görüntüle
                    </a>
                    @else
                    <div class="alert alert-warning mb-0">
                        Kullanıcı silinmiş
                    </div>
                    @endif
                </div>
            </div>

            <!-- İçerik Bilgisi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">İçerik</h5>
                </div>
                <div class="card-body">
                    @php
                        $type = class_basename($comment->commentable_type);
                        $typeLabels = [
                            'LfgPost' => 'LFG İlanı',
                            'Clan' => 'Klan',
                            'GuidePost' => 'Rehber',
                            'CommunityPost' => 'Topluluk',
                        ];
                        $typeLabel = $typeLabels[$type] ?? $type;
                    @endphp
                    
                    <div class="mb-2">
                        <strong>Tür:</strong>
                        <span class="badge bg-secondary">{{ $typeLabel }}</span>
                    </div>

                    @if($comment->commentable)
                    <div>
                        <strong>Başlık:</strong>
                        <p class="mb-0">
                            @if($type === 'LfgPost' || $type === 'GuidePost')
                                {{ $comment->commentable->title ?? 'Silinmiş' }}
                            @elseif($type === 'Clan')
                                {{ $comment->commentable->name ?? 'Silinmiş' }}
                            @elseif($type === 'CommunityPost')
                                {{ Str::limit($comment->commentable->content ?? 'Silinmiş', 50) }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tarih Bilgileri -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tarih Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Oluşturulma:</strong><br>
                        <small>{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <div>
                        <strong>Güncellenme:</strong><br>
                        <small>{{ $comment->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
