<div class="widget widget-popular-content">
    <h5 class="widget-title">{{ $title }}</h5>
    <div class="widget-body">
        @if($items->count() > 0)
            <ul class="list-unstyled">
                @foreach($items as $item)
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <strong>{{ $item['title'] ?? 'Başlıksız' }}</strong>
                        </a>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-eye"></i> {{ $item['views_count'] ?? 0 }} görüntülenme
                        </small>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">Henüz içerik yok.</p>
        @endif
    </div>
</div>
