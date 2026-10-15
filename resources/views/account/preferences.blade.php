@extends('layouts.account')

@section('title', 'Preferences — Trip Sathi')

@section('account-content')
    <h1 class="ts-title" style="font-size: 1.4rem;">Preferences</h1>
    <p class="text-muted mb-4">Choose how prices, distances and updates are shown for your account.</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.preferences.update') }}">
        @csrf
        @method('PUT')

        {{-- Display ------------------------------------------------------ --}}
        <div class="ts-card mb-4">
            <div class="ts-card-body" style="padding: 1.5rem;">
                <h2 class="ts-title mb-1" style="font-size: 1.1rem;">Display</h2>
                <p class="text-muted small mb-4">Applied across listings, search results and booking summaries.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="pref-currency">Currency</label>
                        <select class="form-select" id="pref-currency" name="currency">
                            @foreach (['INR' => 'Indian Rupee (₹)', 'USD' => 'US Dollar ($)', 'EUR' => 'Euro (€)', 'GBP' => 'British Pound (£)', 'AED' => 'UAE Dirham (د.إ)'] as $code => $label)
                                <option value="{{ $code }}" @selected(old('currency', $prefs['currency']) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('currency')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="pref-language">Language</label>
                        <select class="form-select" id="pref-language" name="language">
                            @foreach (['en' => 'English', 'hi' => 'हिन्दी (Hindi)', 'gu' => 'ગુજરાતી (Gujarati)'] as $code => $label)
                                <option value="{{ $code }}" @selected(old('language', $prefs['language']) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('language')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="pref-temperature">Temperature</label>
                        <select class="form-select" id="pref-temperature" name="temperature_unit">
                            <option value="c" @selected(old('temperature_unit', $prefs['temperature_unit']) === 'c')>Celsius (°C)</option>
                            <option value="f" @selected(old('temperature_unit', $prefs['temperature_unit']) === 'f')>Fahrenheit (°F)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="pref-distance">Distance</label>
                        <select class="form-select" id="pref-distance" name="distance_unit">
                            <option value="km" @selected(old('distance_unit', $prefs['distance_unit']) === 'km')>Kilometres (km)</option>
                            <option value="mi" @selected(old('distance_unit', $prefs['distance_unit']) === 'mi')>Miles (mi)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications ----------------------------------------------- --}}
        <div class="ts-card mb-4">
            <div class="ts-card-body" style="padding: 1.5rem;">
                <h2 class="ts-title mb-1" style="font-size: 1.1rem;">Notifications</h2>
                <p class="text-muted small mb-4">Pick the channels you want us to reach you on.</p>

                <div class="ts-pref-toggles">
                    @php
                        $toggles = [
                            'email_notifications' => ['bi-envelope-check', 'Email updates', 'Booking confirmations, receipts and account alerts.'],
                            'sms_notifications' => ['bi-phone', 'SMS alerts', 'Time-sensitive reminders sent to your mobile number.'],
                            'whatsapp_notifications' => ['bi-whatsapp', 'WhatsApp', 'Quick updates on the number linked to your account.'],
                            'deal_alerts' => ['bi-tags', 'Deal & festival alerts', 'Price drops and seasonal offers — about twice a month.'],
                            'trip_reminders' => ['bi-bell', 'Trip reminders', 'Check-in, darshan and itinerary nudges before you travel.'],
                        ];
                    @endphp

                    @foreach ($toggles as $key => [$icon, $label, $hint])
                        <label class="ts-pref-toggle" for="pref-{{ $key }}">
                            <input type="checkbox" id="pref-{{ $key }}" name="{{ $key }}" value="1"
                                @checked(old($key, $prefs[$key]))>
                            <span class="ts-pref-toggle-body">
                                <span class="ts-pref-toggle-head">
                                    <i class="bi {{ $icon }}"></i>
                                    <strong>{{ $label }}</strong>
                                </span>
                                <span class="ts-pref-toggle-hint">{{ $hint }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary px-4" type="submit">
                <i class="bi bi-check2"></i> Save preferences
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('profile.edit') }}">Back to profile</a>
        </div>
    </form>
@endsection
