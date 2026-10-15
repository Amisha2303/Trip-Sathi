@extends('layouts.account')

@section('title', 'Profile — Trip Sathi')

@section('account-content')
    <div class="mb-3">
        <h1 class="ts-title" style="font-size: 1.4rem;">Profile & security</h1>
        <p class="ts-subtitle">Your details are used on bookings, invoices and trip reminders.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ts-card">
                <div class="ts-card-body" style="padding: 1.5rem;">
                    <h2 class="ts-title mb-3" style="font-size: 1.1rem;">Personal details</h2>

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="profile-name">Full name</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="profile-name"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                    id="profile-email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-phone">Phone</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="profile-phone"
                                    name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+91 98765 43210">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="profile-dob">Date of birth</label>
                                <input class="form-control @error('date_of_birth') is-invalid @enderror" type="date"
                                    id="profile-dob" name="date_of_birth"
                                    value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="profile-gender">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="profile-gender"
                                    name="gender">
                                    <option value="">Prefer not to say</option>
                                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('gender', $user->gender) === $value)>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="profile-address">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="profile-address"
                                    name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="profile-city">City</label>
                                <input class="form-control" id="profile-city" name="city"
                                    value="{{ old('city', $user->city) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="profile-state">State</label>
                                <input class="form-control" id="profile-state" name="state"
                                    value="{{ old('state', $user->state) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="profile-country">Country</label>
                                <input class="form-control" id="profile-country" name="country"
                                    value="{{ old('country', $user->country ?? 'India') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-emergency-name">Emergency contact name</label>
                                <input class="form-control" id="profile-emergency-name" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-emergency-phone">Emergency contact phone</label>
                                <input class="form-control" id="profile-emergency-phone" name="emergency_contact_phone"
                                    value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}">
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ts-card mb-4">
                <div class="ts-card-body" style="padding: 1.5rem;">
                    <h2 class="ts-title mb-1" style="font-size: 1.1rem;">Change password</h2>
                    <p class="text-muted small mb-3">Confirm your current password, then choose a new one — minimum 10
                        characters with letters, numbers and symbols.</p>

                    <form method="post" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="current-password">Current password</label>
                            <input class="form-control @error('current_password') is-invalid @enderror" type="password"
                                id="current-password" name="current_password" autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="new-password">New password</label>
                            <input class="form-control @error('new_password') is-invalid @enderror" type="password"
                                id="new-password" name="new_password" autocomplete="new-password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="new-password-confirmation">Confirm new password</label>
                            <input class="form-control" type="password" id="new-password-confirmation"
                                name="new_password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-soft" type="submit">
                                <i class="bi bi-key"></i> Update password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="ts-card">
                <div class="ts-card-body" style="padding: 1.5rem;">
                    <h2 class="ts-title mb-3" style="font-size: 1.1rem;">Account status</h2>

                    <ul class="ts-list">
                        <li>
                            <i class="bi bi-envelope-check text-success"></i>
                            <div class="flex-grow-1">
                                <p class="ts-list-title">Email
                                    {{ $user->email_verified_at ? 'verified' : 'not verified' }}</p>
                                <p class="ts-list-meta">{{ $user->email }}</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-person-badge text-primary"></i>
                            <div class="flex-grow-1">
                                <p class="ts-list-title">{{ $isAdmin ? 'Administrator' : 'Traveller' }}</p>
                                <p class="ts-list-meta">Member since {{ $user->created_at->format('M Y') }}</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-shield-lock text-primary"></i>
                            <div class="flex-grow-1">
                                <p class="ts-list-title">Two-factor authentication</p>
                                <p class="ts-list-meta">
                                    {{ $user->two_factor_confirmed_at ? 'Enabled' : 'Not enabled yet' }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
