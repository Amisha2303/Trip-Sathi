@extends('layouts.app')

@section('title', 'Register - Trip Sathi')

@section('content')
<div class="ts-auth" style="--ts-auth-img: url('{{ asset('photos/banners/banner-auth.jpg') }}');">
    <div class="container py-5 w-100">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h2 class="fw-bold mb-4 text-center">Create an Account</h2>
                    
                    <form method="POST" action="{{ route('register.attempt') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Create a password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" value="1" required>
                            <label class="form-check-label text-muted" for="terms">I agree to the <a href="#" class="text-primary text-decoration-none">Terms and Conditions</a></label>
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Log in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
