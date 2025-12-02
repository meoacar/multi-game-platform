<div class="menu-item" data-id="{{ $item->id }}" style="margin-left: {{ $level * 2 }}rem;">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center flex-grow-1">
            <i class="fas fa-grip-vertical menu-item-handle me-3"></i>
            
            @if($item->icon)
                <i class="{{ $item->icon }} me-2"></i>
            @endif
            
            <div>
                <strong>{{ $item->title }}</strong>
                <br>
                <small class="text-muted">
                    @if($item->type === 'page' && $item->target)
                        <i class="fas fa-file-alt"></i> Sayfa: {{ $item->target->title }}
                    @elseif($item->type === 'url' || $item->type === 'custom')
                        <i class="fas fa-link"></i> {{ $item->url }}
                    @elseif($item->type === 'route')
                        <i class="fas fa-route"></i> Route: {{ $item->route }}
                    @else
                        <i class="fas fa-question-circle"></i> {{ $item->type }}
                    @endif
                </small>
            </div>
        </div>

        <div class="menu-item-actions">
            <!-- Durum Toggle -->
            <button type="button" 
                    class="btn btn-sm {{ $item->is_active ? 'btn-success' : 'btn-secondary' }}"
                    @click="toggleStatus({{ $item->id }})"
                    title="{{ $item->is_active ? 'Aktif' : 'Pasif' }}">
                <i class="fas fa-{{ $item->is_active ? 'check' : 'times' }}"></i>
            </button>

            <!-- Sil -->
            <button type="button" 
                    class="btn btn-sm btn-danger"
                    @click="deleteItem({{ $item->id }})"
                    title="Sil">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <!-- Alt Öğeler -->
    @if($item->children->count() > 0)
        <div class="menu-item-children">
            @foreach($item->children as $child)
                @include('admin.menus.partials.menu-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
