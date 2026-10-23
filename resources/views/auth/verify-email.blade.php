@extends('layouts.app')

@section('title', 'Verify Your Email - Trip Sathi')

@section('content')
<div class="ts-auth" style="--ts-auth-img: url('{{ asset('photos/banners/banner-auth.jpg') }}');">
    <div class="container py-5 w-100">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary"
                            style="width: 3.5rem; height: 3.5rem;">
                            <i class="bi bi-envelope-check fs-3"></i>
                        </span>
                    </div>

                    <h2 class="fw-bold mb-2 text-center">Verify your email</h2>
                    <p class="text-muted text-center mb-4">
                        We sent a verification link to
                        <strong>{{ $email }}</strong>. Open it to finish setting up your account.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-info" role="alert">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Resend verification email</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted p-0">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
