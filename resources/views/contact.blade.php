@extends('layouts.app')

@section('title', 'Contact Us — Trip Sathi')

@section('content')
    @include('partials.page-hero', [
        'image' => asset('photos/banners/banner-contact.jpg'),
        'eyebrow' => 'We reply within a day',
        'title' => 'Talk to a Trip Sathi',
        'lede' => 'Booking help, payment questions, partnership enquiries or feedback — send a note and a real person will get back to you.',
        'crumbs' => [['label' => 'Home', 'url' => route('home')], ['label' => 'Contact']],
        'stats' => [
            ['24h', 'Typical reply time'],
            ['7 days', 'Support coverage'],
            ['4.8', 'Customer rating'],
        ],
    ])

    <section class="ts-section">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <div class="ts-contact-side" style="--ts-contact-img: url('{{ asset('photos/banners/banner-contact.jpg') }}');">
                        <span class="ts-eyebrow">Reach us directly</span>
                        <h3>Support, sales & partnerships</h3>

                        <div class="ts-contact-item">
                            <i class="bi bi-person-badge-fill"></i>
                            <div>
                                <strong>Owner</strong>
                                <span>{{ $settings['owner'] }}</span>
                            </div>
                        </div>

                        <div class="ts-contact-item">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <strong>Email</strong>
                                <span>{{ $settings['email'] }}</span>
                            </div>
                        </div>

                        <div class="ts-contact-item">
                            <i class="bi bi-telephone-fill"></i>
                            <div>
                                <strong>Phone</strong>
                                <span>{{ $settings['phone'] }}</span>
                            </div>
                        </div>

                        @if ($settings['whatsapp'])
                            <div class="ts-contact-item">
                                <i class="bi bi-whatsapp"></i>
                                <div>
                                    <strong>WhatsApp</strong>
                                    <span>{{ $settings['whatsapp'] }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="ts-contact-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <strong>Office</strong>
                                <span>{{ $settings['address'] }}</span>
                            </div>
                        </div>

                        <div class="ts-contact-item">
                            <i class="bi bi-clock-fill"></i>
                            <div>
                                <strong>Hours</strong>
                                <span>Mon – Sat, 9:30 AM – 6:30 PM IST</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                            <p class="mb-2 small text-white-50">Prefer self-service?</p>
                            <a class="btn btn-accent btn-sm" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Open your dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="ts-card" style="box-shadow: none;">
                        <div class="ts-card-body" style="padding: 1.75rem;">
                            <h2 class="ts-title mb-1">Send us a message</h2>
                            <p class="ts-subtitle mb-4">Fill in the form and we will reply to your email.</p>

                            @if (session('success'))
                                <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
                                    <i class="bi bi-check-circle-fill mt-1"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                            @endif

                            <form method="post" action="{{ route('contact.store') }}">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-name">Full name</label>
                                        <input class="form-control @error('name') is-invalid @enderror" id="contact-name"
                                            name="name" value="{{ old('name', $currentUser?->name) }}" required
                                            placeholder="Your name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-email">Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email"
                                            id="contact-email" name="email"
                                            value="{{ old('email', $currentUser?->email) }}" required
                                            placeholder="name@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="contact-phone">Phone (optional)</label>
                                        <input class="form-control @error('phone') is-invalid @enderror" id="contact-phone"
                                            name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        @php
                                            $requestedSubject = old('subject', request('subject'));
                                            $knownSubject = collect($subjects)->keys()->contains($requestedSubject);
                                        @endphp
                                        <label class="form-label" for="contact-subject">Subject</label>
                                        <select class="form-select @error('subject') is-invalid @enderror" id="contact-subject"
                                            name="subject">
                                            @if ($requestedSubject && ! $knownSubject)
                                                <option value="{{ $requestedSubject }}" selected>{{ $requestedSubject }}</option>
                                            @endif
                                            @foreach ($subjects as $value => $label)
                                                <option value="{{ $value }}" @selected($requestedSubject === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="contact-message">Message</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" id="contact-message"
                                            name="message" rows="6" required
                                            placeholder="Tell us how we can help…">{{ old('message', request('message')) }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-shield-check me-1"></i> We never share your details.
                                        </p>
                                        <button class="btn btn-accent px-4" type="submit">
                                            Send message <i class="bi bi-send"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
