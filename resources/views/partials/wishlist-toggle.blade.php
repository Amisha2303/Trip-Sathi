{{--
    Reusable wishlist heart button.

    @include('partials.wishlist-toggle', ['type' => 'hotel', 'id' => 1, 'wished' => false, 'variant' => 'hero'])
    variants: 'hero' (outlined pill over the hero), 'block' (full-width under CTA), 'icon' (bare)
--}}
@php
    $variant = $variant ?? 'icon';
    $wished = $wished ?? false;
    $label = $wished ? 'Saved to wishlist' : 'Save to wishlist';
@endphp

@if ($variant === 'block')
    <button type="button"
        class="ts-wish-block {{ $wished ? 'is-active' : '' }}"
        data-ts-wish data-type="{{ $type }}" data-id="{{ $id }}"
        aria-label="{{ $label }}" aria-pressed="{{ $wished ? 'true' : 'false' }}">
        <i class="bi {{ $wished ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        <span>{{ $wished ? 'Saved' : 'Save to wishlist' }}</span>
    </button>
@elseif ($variant === 'hero')
    @auth
        <button type="button"
            class="ts-wish ts-wish-hero {{ $wished ? 'is-active' : '' }}"
            data-ts-wish data-type="{{ $type }}" data-id="{{ $id }}"
            aria-label="{{ $label }}" aria-pressed="{{ $wished ? 'true' : 'false' }}">
            <i class="bi {{ $wished ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        </button>
    @else
        <a href="{{ route('login') }}" class="ts-wish ts-wish-hero" aria-label="Sign in to save">
            <i class="bi bi-heart"></i>
        </a>
    @endauth
@else
    @auth
        <button type="button" class="ts-wish {{ $wished ? 'is-active' : '' }}"
            data-ts-wish data-type="{{ $type }}" data-id="{{ $id }}"
            aria-label="{{ $label }}" aria-pressed="{{ $wished ? 'true' : 'false' }}">
            <i class="bi {{ $wished ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        </button>
    @else
        <a href="{{ route('login') }}" class="ts-wish" aria-label="Sign in to save">
            <i class="bi bi-heart"></i>
        </a>
    @endauth
@endif
