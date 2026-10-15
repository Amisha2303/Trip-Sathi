@extends('layouts.app')

@section('title', ($detail['seoTitle'] ?? $detail['name'] . ' — ' . $detail['eyebrow']) . ' — Trip Sathi')
@section('meta-description', $detail['lede'] ?? \Illuminate\Support\Str::limit(strip_tags($detail['description'] ?? ''), 155))

@section('content')
    {{-- Hero ------------------------------------------------------------- --}}
    <section class="ts-hero ts-hero-photo ts-detail-hero"
        style="--ts-hero-img: url('{{ $detail['mainImage'] }}');">
        <div class="container">
            @if (! empty($crumbs))
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb ts-breadcrumb-ondark mb-0">
                        @foreach ($crumbs as $crumb)
                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                @if ($loop->last)
                                    {{ $crumb['label'] }}
                                @else
                                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="ts-detail-hero-top">
                <div>
                    <span class="ts-eyebrow" style="color: #ef8f1c;">{{ $detail['eyebrow'] }}</span>
                    <h1 class="ts-hero-title mb-2">{{ $detail['name'] }}</h1>

                    <div class="ts-detail-hero-meta">
                        @if ($detail['location'])
                            <span><i class="bi bi-geo-alt"></i> {{ $detail['location'] }}</span>
                        @endif
                        @if ($detail['stars'])
                            <span class="ts-stars" aria-label="{{ $detail['stars'] }} star">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $detail['stars'] ? 'bi-star-fill' : 'bi-star-fill ts-stars-empty' }}"></i>
                                @endfor
                            </span>
                        @endif
                        @if ($detail['rating'] !== null)
                            <span><i class="bi bi-star-fill" style="color:#ef8f1c;"></i>
                                {{ number_format($detail['rating'], 1) }}
                                <span class="opacity-75">({{ $detail['reviewCount'] }} reviews)</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="ts-detail-hero-actions">
                    @include('partials.wishlist-toggle', [
                        'type' => $detail['wishType'],
                        'id' => $detail['wishId'],
                        'wished' => $wished ?? false,
                        'variant' => 'hero',
                    ])
                    @if ($detail['price'] !== null)
                        <div class="ts-detail-price">
                            <span class="ts-price">
                                {{ \App\Support\Money::decimal($detail['price']) }}
                                @if ($detail['priceNote'])
                                    <small>{{ $detail['priceNote'] }}</small>
                                @endif
                            </span>
                        </div>
                    @endif
                    <a class="btn btn-accent" href="{{ $detail['cta']['url'] }}">
                        {{ $detail['cta']['label'] }}
                        <i class="bi {{ $detail['cta']['icon'] }}"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery ---------------------------------------------------------- --}}
    @if (count($detail['gallery']) > 1)
        <section class="ts-section ts-section-tight">
            <div class="container">
                <div class="ts-gallery">
                    @foreach ($detail['gallery'] as $image)
                        <figure class="ts-gallery-item">
                            <img src="{{ $image['url'] }}" alt="{{ $detail['name'] }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ $detail['mainImage'] }}';">
                            @if ($image['caption'])
                                <figcaption>{{ $image['caption'] }}</figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Body ------------------------------------------------------------- --}}
    <section class="ts-section">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-lg-8">
                    @if ($detail['description'])
                        <div class="ts-detail-copy">
                            <h2 class="ts-title mb-3">About this {{ strtolower($detail['eyebrow']) }}</h2>
                            {!! \App\Support\Catalogue::paragraphs($detail['description']) !!}
                        </div>
                    @endif

                    @if (count($detail['facts']))
                        <div class="ts-detail-block">
                            <h3 class="ts-title mb-3">Good to know</h3>
                            <div class="ts-facts">
                                @foreach ($detail['facts'] as $fact)
                                    <div class="ts-fact">
                                        <i class="bi {{ $fact['icon'] }}"></i>
                                        <div>
                                            <span class="ts-fact-label">{{ $fact['label'] }}</span>
                                            <span class="ts-fact-value">{{ $fact['value'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @foreach ($detail['sections'] as $section)
                        <div class="ts-detail-block">
                            <h3 class="ts-title mb-3">{{ $section['title'] }}</h3>
                            @isset($section['list'])
                                <ul class="ts-check-list">
                                    @foreach ($section['list'] as $li)
                                        <li><i class="bi bi-check-circle-fill"></i> {{ $li }}</li>
                                    @endforeach
                                </ul>
                            @endisset
                            @isset($section['body'])
                                <div class="ts-detail-copy">{!! $section['body'] !!}</div>
                            @endisset
                        </div>
                    @endforeach

                    @include('partials.detail-reviews', [
                        'reviews' => $reviews ?? collect(),
                        'rating' => $detail['rating'],
                        'reviewCount' => $detail['reviewCount'],
                        'name' => $detail['name'],
                    ])
                </div>

                <div class="col-lg-4">
                    <aside class="ts-detail-aside">
                        <div class="ts-detail-card">
                            @if ($detail['price'] !== null)
                                <div class="ts-detail-card-price">
                                    <span class="ts-price">
                                        {{ \App\Support\Money::decimal($detail['price']) }}
                                        @if ($detail['priceNote'])
                                            <small>{{ $detail['priceNote'] }}</small>
                                        @endif
                                    </span>
                                    @if ($detail['rating'] !== null)
                                        <span class="ts-detail-card-rating">
                                            <i class="bi bi-star-fill"></i> {{ number_format($detail['rating'], 1) }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <a class="btn btn-accent w-100 mb-2" href="{{ $detail['cta']['url'] }}">
                                {{ $detail['cta']['label'] }}
                                <i class="bi {{ $detail['cta']['icon'] }}"></i>
                            </a>

                            @include('partials.wishlist-toggle', [
                                'type' => $detail['wishType'],
                                'id' => $detail['wishId'],
                                'wished' => $wished ?? false,
                                'variant' => 'block',
                            ])

                            <ul class="ts-detail-trust">
                                <li><i class="bi bi-shield-check"></i> Confirmed availability</li>
                                <li><i class="bi bi-headset"></i> Real-person support, 7 days</li>
                                <li><i class="bi bi-arrow-counterclockwise"></i> Clear cancellation terms</li>
                            </ul>

                            <div class="ts-detail-help">
                                <p class="mb-1"><strong>Not sure yet?</strong></p>
                                <p class="mb-2 small">Talk it through with a Trip Sathi before you commit.</p>
                                <a class="btn btn-soft btn-sm w-100" href="{{ route('contact', ['subject' => $detail['eyebrow'] . ': ' . $detail['name']]) }}">
                                    <i class="bi bi-chat-dots"></i> Ask a question
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    {{-- Related ---------------------------------------------------------- --}}
    @if ($related->isNotEmpty())
        <section class="ts-section ts-section-alt">
            <div class="container">
                <div class="ts-section-head">
                    <div>
                        <span class="ts-eyebrow">Keep exploring</span>
                        <h2 class="ts-title mb-0">More {{ $relatedLabel ?? 'like this' }}</h2>
                    </div>
                    @if ($relatedIndexUrl ?? null)
                        <a class="btn btn-soft" href="{{ $relatedIndexUrl }}">
                            View all <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="row g-4">
                    @foreach ($related as $card)
                        <div class="col-md-6 col-lg-4">
                            @include('partials.item-card', $card)
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
