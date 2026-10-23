@extends('layouts.app')

@section('title', 'Forgot Password - Trip Sathi')

@section('content')
<div class="ts-auth" style="--ts-auth-img: url('{{ asset('photos/banners/banner-auth.jpg') }}');">
    <div class="container py-5 w-100">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-2 text-center">Forgot password?</h2>
                    <p class="text-muted text-center mb-4">
                        Enter the email address on your account and we will send you a reset link.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-info" role="alert">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Email me a reset link</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Remembered it?
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
