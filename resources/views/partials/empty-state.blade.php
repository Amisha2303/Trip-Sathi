{{-- Shared empty state for listings, dashboards and tables. --}}
<div class="ts-empty">
    <div class="ts-empty-icon">
        <i class="bi {{ $icon ?? 'bi-search' }}"></i>
    </div>
    <h3>{{ $title ?? 'Nothing here yet' }}</h3>
    <p class="mb-3">{{ $text ?? 'Try adjusting your filters — new listings are added every week.' }}</p>
    @isset($actionUrl)
        <a class="btn btn-soft" href="{{ $actionUrl }}">{{ $actionLabel ?? 'Browse everything' }}</a>
    @endisset
</div>
