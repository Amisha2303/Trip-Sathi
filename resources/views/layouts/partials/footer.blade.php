<footer class="ts-footer" style="--ts-footer-img: url('{{ asset('photos/banners/banner-footer.jpg') }}');">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="footer-brand-logo">
                    <img src="{{ asset('images/logo.png') }}" width="104" height="46"
                        alt="{{ $appSettings['site_name'] ?? config('app.name', 'Trip Sathi') }}">
                </div>
                <p style="max-width: 42ch;">
                    {{ $appSettings['footer_about'] ?? 'Plan your journey and create beautiful memories — stays, food, events, adventures, darshan and rituals, all in one place.' }}
                </p>
                <div class="d-flex gap-3 mt-3 fs-5">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <div class="footer-title">Explore</div>
                <ul class="footer-links">
                    <li><a href="{{ route('destinations.index') }}">Destinations</a></li>
                    <li><a href="{{ route('hotels.index') }}">Hotels</a></li>
                    <li><a href="{{ route('events.index') }}">Events</a></li>
                    <li><a href="{{ route('adventures.index') }}">Adventures</a></li>
                    <li><a href="{{ route('trips.index') }}">Trip planner</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <div class="footer-title">Spiritual</div>
                <ul class="footer-links">
                    <li><a href="{{ route('darshan.index') }}">VIP Darshan</a></li>
                    <li><a href="{{ route('pooja.index') }}">Pooja Vidhi</a></li>
                    <li><a href="{{ route('contact') }}">Temple partnerships</a></li>
                    <li><a href="{{ route('contact') }}">Group enquiries</a></li>
                </ul>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="footer-title">Contact</div>
                <ul class="footer-links">
                    <li><i class="bi bi-person-badge me-2"></i>{{ $appSettings['owner_name'] ?? 'Amisha Chauhan' }}
                        <span class="text-muted">(Owner)</span></li>
                    <li><i class="bi bi-geo-alt me-2"></i>{{ $appSettings['contact_address'] ?? 'Bhavnagar, Gujarat, India' }}</li>
                    <li><i class="bi bi-envelope me-2"></i>{{ $appSettings['contact_email'] ?? 'Amisha@tripsathi.com' }}</li>
                    <li><i class="bi bi-telephone me-2"></i>{{ $appSettings['contact_phone'] ?? '+91 9904530641' }}</li>
                    <li><i class="bi bi-clock me-2"></i>Mon – Sat, 9:30 AM – 6:30 PM IST</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <p class="mb-0">&copy; {{ date('Y') }} <strong>{{ $appSettings['site_name'] ?? 'Trip Sathi' }}</strong>. All
                rights reserved.</p>
            <p class="mb-0">
                <a href="{{ route('contact') }}">Privacy Policy</a>
                <span class="mx-2">·</span>
                <a href="{{ route('contact') }}">Terms of Service</a>
            </p>
        </div>
    </div>
</footer>
