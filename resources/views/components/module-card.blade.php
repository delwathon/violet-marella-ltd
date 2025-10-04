<div class="module-card {{ $type }}">
    <div class="module-header">
        <div class="module-icon">
            <i class="fas {{ $icon }}"></i>
        </div>
        <h3 class="module-title">{{ $title }}</h3>
        <p class="module-description">{{ $description }}</p>
    </div>
    <div class="module-body">
        {{ $slot }}
    </div>
</div>
