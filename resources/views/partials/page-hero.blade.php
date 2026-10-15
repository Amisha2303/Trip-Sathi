{{--
    Inner-page hero.

    Usage:
    @include('partials.page-hero', [
        'eyebrow'   => 'Explore India',
        'title'     => 'Destinations',
        'lede'      => 'Handpicked places…',
        'image'     => asset('photos/banners/banner-destinations.jpg'),
        'stats'     => [['12', 'Places'], ['4.8', 'Avg rating']],
        'crumbs'    => [['label' => 'Home', 'url' => route('home')], ['label' => 'Destinations']],
    ])
--}}
<section class="ts-hero ts-hero-compact {{ isset($image) && $image ? 'ts-hero-photo' : '' }}"
    @isset($image)
        @if ($image) style="--ts-hero-img: url('{{ $image }}');" @endif
    @endisset>
    <div class="container">
        @isset($crumbs)
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
        @endisset

        @isset($eyebrow)
            <span class="ts-eyebrow" style="color: {{ $accent ?? '#ef8f1c' }};">{{ $eyebrow }}</span>
        @endisset

        <h1 class="ts-hero-title">{{ $title }}</h1>

        @isset($lede)
            <p class="ts-hero-lede">{{ $lede }}</p>
        @endisset

        @isset($stats)
            @if (count($stats))
                <div class="ts-hero-stats">
                    @foreach ($stats as $stat)
                        <div>
                            <strong>{{ $stat[0] }}</strong>
                            <span>{{ $stat[1] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        @endisset
    </div>
</section>
