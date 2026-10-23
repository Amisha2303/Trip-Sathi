@extends('layouts.app')

@section('title', 'Reset Password - Trip Sathi')

@section('content')
<div class="ts-auth" style="--ts-auth-img: url('{{ asset('photos/banners/banner-auth.jpg') }}');">
    <div class="container py-5 w-100">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-2 text-center">Choose a new password</h2>
                    <p class="text-muted text-center mb-4">
                        Use at least 10 characters with letters, numbers and a symbol.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $email) }}" required autofocus
                                placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input type="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                id="password" name="password" required autocomplete="new-password"
                                placeholder="New password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" class="form-control form-control-lg" id="password_confirmation"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Repeat the new password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Reset password</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Back to sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
