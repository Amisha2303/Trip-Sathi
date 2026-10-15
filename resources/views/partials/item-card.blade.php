{{--
    Catalogue card shared by every listing page.

    @include('partials.item-card', [
        'image'    => $hotel->image_url,
        'title'    => $hotel->name,
        'subtitle' => $hotel->destination->name,
        'meta'     => ['bi-geo-alt' => 'Gujarat'],
        'text'     => $hotel->short_description,
        'price'    => $hotel->effective_price,   // float rupees, rendered by @money
        'stars'    => $hotel->star_rating,        // optional 1-5
        'rating'   => (float) $hotel->rating,     // optional 0-5 with reviews
        'badge'    => 'Featured',
        'wishType' => 'hotel',
        'wishId'   => $hotel->id,
        'wished'   => $ids->contains($hotel->id),
        'cta'      => 'Enquire',
        'ctaUrl'   => route('contact', ['subject' => 'Hotel: ' . $hotel->name]),
    ])
--}}
@php
    $meta = $meta ?? [];
    $text = $text ?? null;
    $badge = $badge ?? null;
    $wishType = $wishType ?? null;
    $wished = $wished ?? false;
    $cta = $cta ?? 'Enquire';
    $ctaUrl = $ctaUrl ?? route('contact');
    $extra = $extra ?? null;

    // Type-aware placeholder so a 404'd image swaps to the right artwork.
    $placeholder = $placeholder ?? ($wishType
        ? asset('images/' . $wishType . '-placeholder.svg')
        : asset('images/placeholder.svg'));
@endphp

<article class="ts-card h-100">
    <div class="ts-card-media">
        @if (! empty($image))
            <img src="{{ $image }}" alt="{{ $title }}" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ $placeholder }}';">
        @else
            <img src="{{ $placeholder }}" alt="{{ $title }}" loading="lazy">
        @endif

        @if ($badge)
            <span class="ts-card-media-tag">{{ $badge }}</span>
        @endif

        @if ($wishType)
            @auth
                <button type="button" class="ts-wish {{ $wished ? 'is-active' : '' }}"
                    data-ts-wish data-type="{{ $wishType }}" data-id="{{ $wishId }}"
                    aria-label="{{ $wished ? 'Remove from' : 'Add to' }} wishlist"
                    aria-pressed="{{ $wished ? 'true' : 'false' }}">
                    <i class="bi {{ $wished ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            @else
                <a href="{{ route('login') }}" class="ts-wish" aria-label="Sign in to save">
                    <i class="bi bi-heart"></i>
                </a>
            @endauth
        @endif
    </div>

    <div class="ts-card-body">
        @if (count($meta))
            <div class="ts-card-meta mb-1">
                @foreach ($meta as $icon => $label)
                    <span><i class="bi {{ $icon }}"></i> {{ $label }}</span>
                @endforeach
            </div>
        @endif

        <h3 class="ts-card-title">{{ $title }}</h3>

        @isset($subtitle)
            @if ($subtitle)
                <p class="ts-card-meta mb-1"><i class="bi bi-geo-alt"></i> {{ $subtitle }}</p>
            @endif
        @endisset

        @if ($text)
            <p class="ts-card-text">{{ $text }}</p>
        @endif

        @isset($stars)
            <div class="ts-stars mb-1" aria-label="{{ $stars }} star">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $stars ? 'bi-star-fill' : 'bi-star-fill ts-stars-empty' }}"></i>
                @endfor
            </div>
        @endisset

        <div class="ts-card-foot">
            <div>
                @if (isset($price) && $price !== null)
                    <div class="ts-price">{{ \App\Support\Money::decimal($price) }}
                        @isset($priceNote)
                            <small>{{ $priceNote }}</small>
                        @endisset
                    </div>
                @endif

                @isset($rating)
                    <div class="ts-card-meta">
                        <i class="bi bi-star-fill" style="color: var(--bs-warning, #ef8f1c);"></i>
                        <span>{{ number_format((float) $rating, 1) }}
                            @isset($ratingNote)
                                <span class="text-muted">({{ $ratingNote }})</span>
                            @endisset
                        </span>
                    </div>
                @endisset
            </div>

            <div class="ts-card-actions">
                @isset($extra)
                    {!! $extra !!}
                @endisset
                <a class="btn btn-sm btn-soft" href="{{ $ctaUrl }}">{{ $cta }}</a>
            </div>
        </div>
    </div>
</article>
