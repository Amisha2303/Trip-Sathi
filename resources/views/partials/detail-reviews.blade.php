{{--
    Reviews block for catalogue detail pages.

    @include('partials.detail-reviews', [
        'reviews' => $reviews,          // collection of approved reviews (user loaded)
        'rating' => 4.6, 'reviewCount' => 128, 'name' => 'Hotel name',
    ])
--}}
@php
    $reviews = $reviews ?? collect();
    $rating = $rating ?? null;
    $reviewCount = (int) ($reviewCount ?? 0);
@endphp

<div class="ts-detail-block" id="reviews">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h3 class="ts-title mb-0">Traveller reviews</h3>
        @if ($rating !== null)
            <span class="ts-review-summary">
                <i class="bi bi-star-fill"></i> {{ number_format($rating, 1) }}
                <span class="text-muted">· {{ $reviewCount }} reviews</span>
            </span>
        @endif
    </div>

    @if ($rating !== null && $reviewCount > 0)
        @php
            // Distribution across 5→1 stars, derived from the loaded reviews.
            $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            foreach ($reviews as $r) {
                $s = (int) $r->rating;
                if (isset($counts[$s])) {
                    $counts[$s]++;
                }
            }
            $total = max(1, $reviews->count());
        @endphp
        <div class="ts-rating-bars mb-4">
            @foreach ([5, 4, 3, 2, 1] as $star)
                @php $pct = (int) round($counts[$star] / $total * 100); @endphp
                <div class="ts-rating-bar">
                    <span class="ts-rating-bar-star">{{ $star }} <i class="bi bi-star-fill"></i></span>
                    <span class="ts-rating-bar-track"><span class="ts-rating-bar-fill" style="width: {{ $pct }}%"></span></span>
                    <span class="ts-rating-bar-count">{{ $counts[$star] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if ($reviews->isEmpty())
        <div class="ts-review-empty">
            <i class="bi bi-chat-square-text"></i>
            <p class="mb-0">No reviews yet for <strong>{{ $name }}</strong>. Book it and be the first to share
                your experience.</p>
        </div>
    @else
        <div class="ts-review-list">
            @foreach ($reviews as $review)
                <article class="ts-review">
                    <div class="ts-review-avatar" aria-hidden="true">{{ $review->author_initials }}</div>
                    <div class="ts-review-body">
                        <div class="ts-review-head">
                            <div>
                                <strong class="ts-review-name">{{ $review->author_name }}</strong>
                                @if ($review->is_verified)
                                    <span class="ts-review-verified"><i class="bi bi-patch-check-fill"></i> Verified
                                        booking</span>
                                @endif
                            </div>
                            <span class="ts-review-date">{{ $review->created_at?->diffForHumans() }}</span>
                        </div>
                        <div class="ts-stars mb-1" aria-label="{{ $review->rating }} out of 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star-fill ts-stars-empty' }}"></i>
                            @endfor
                        </div>
                        @if ($review->title)
                            <h4 class="ts-review-title">{{ $review->title }}</h4>
                        @endif
                        <p class="ts-review-text">{{ $review->comment }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
