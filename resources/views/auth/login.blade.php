@extends('layouts.app')

@section('title', 'Log In - Trip Sathi')

@section('content')
<div class="ts-auth" style="--ts-auth-img: url('{{ asset('photos/banners/banner-auth.jpg') }}');">
    <div class="container py-5 w-100">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4 text-center">Welcome Back</h2>
                    
                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf

                        {{-- Send the visitor back to the page that bounced them here. --}}
                        @if (request()->query('next'))
                            <input type="hidden" name="next" value="{{ request()->query('next') }}">
                        @endif

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label fw-semibold mb-0">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small">Forgot password?</a>
                                @endif
                            </div>
                            <input type="password" class="form-control form-control-lg mt-2 @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Enter your password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Log In</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
