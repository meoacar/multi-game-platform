@extends('admin.layout')

@section('title', 'Yorum Detayı')

@section('content')
<div class="container-fluid px-4">
    <!-- Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Yorum Detayı</h1>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="row">
        <!-- Yorum Bilgileri -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Yorum İçeriği</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-0">{{ $comment->content }}</p>
                    </div>
                    
                    @if($comment->parent)
                    <div class="alert alert-info">
                        <strong>Cevap Verilen Yorum:</strong>
                        <p class="mb-0 mt-2">{{ $comment->parent->content }}</p>
                        <small class="text-muted">
                            {{ $comment->parent->user->name ?? 'Silinmiş Kullanıcı' }} - 
                            {{ $comment->parent->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>
                    @endif

                    @if($comment->replies->count() > 0)
                    <div class="mt-4">
                        <h6>Cevaplar ({{ $comment->replies->count() }})</h6>
                        <div class="list-group">
                            @foreach($comment->replies as $reply)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $reply->user->name ?? 'Silinmiş Kullanıcı' }}</strong>
                                        <p class="mb-1 mt-2">{{ $reply->content }}</p>
                                        <small class="text-muted">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <a href="{{ route('admin.comments.show', $reply->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- İçerik Bilgisi -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">İçerik Bilgisi</h5>
                </div>
                <div class="card-body">
                    @php
                        $type = class_basename($comment->commentable_type);
                        $typeLabels = [
                            'LfgPost' => 'LFG İlanı',
                            'Clan' => 'Klan',
                            'GuidePost' => 'Rehber',
                            'CommunityPost' => 'Topluluk Gönderisi',
                        ];
                        $typeLabel = $typeLabels[$type] ?? $type;
                    @endphp
                    
                    <div class="mb-3">
                        <strong>İçerik Türü:</strong>
                        <span class="badge bg-secondary">{{ $typeLabel }}</span>
                    </div>

                    @if($comment->commentable)
                    <div class="mb-3">
                        <strong>İçerik:</strong>
                        <p class="mb-0">
                            @if($type === 'LfgPost' || $type === 'GuidePost')
                                {{ $comment->commentable->title ?? 'Silinmiş' }}
                            @elseif($type === 'Clan')
                                {{ $comment->commentable->name ?? 'Silinmiş' }}
                            @elseif($type === 'CommunityPost')
                                {{ Str::limit($comment->commentable->content ?? 'Silinmiş', 100) }}
                            @endif
                        </p>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        İçerik silinmiş veya bulunamadı.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-md-4">
            <!-- Kullanıcı Bilgileri -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    @if($comment->user)
                    <div class="text-center mb-3">
                        @if($comment->user->profile && $comment->user->profile->avatar)
                            <img src="{{ asset('storage/' . $comment->user->profile->avatar) }}" 
                                 class="rounded-circle mb-2" width="80" height="80" alt="">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-2" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        @endif
                        <h6 class="mb-0">{{ $comment->user->name }}</h6>
                        <small class="text-muted">{{ $comment->user->email }}</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.show', $comment->user->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user"></i> Kullanıcı Profiline Git
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        Kullanıcı silinmiş veya bulunamadı.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tarih Bilgileri -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tarih Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Oluşturulma:</strong><br>
                        <small>{{ $comment->created_at->format('d.m.Y H:i:s') }}</small>
                    </div>
                    <div>
                        <strong>Güncellenme:</strong><br>
                        <small>{{ $comment->updated_at->format('d.m.Y H:i:s') }}</small>
                    </div>
                </div>
            </div>

            <!-- İşlemler -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="markSpam()">
                            <i class="fas fa-flag"></i> Spam İşaretle
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteComment()">
                            <i class="fas fa-trash"></i> Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Spam Form -->
<form id="spamForm" method="POST" action="{{ route('admin.comments.mark-spam', $comment->id) }}" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
function deleteComment() {
    if (confirm('Bu yorumu silmek istediğinizden emin misiniz?')) {
        document.getElementById('deleteForm').submit();
    }
}

function markSpam() {
    if (confirm('Bu yorumu spam olarak işaretlemek istediğinizden emin misiniz?')) {
        document.getElementById('spamForm').submit();
    }
}
</script>
@endpush
@endsection
