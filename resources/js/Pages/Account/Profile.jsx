import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AccountLayout from '@/Layouts/AccountLayout';

export default function Profile({ user, isAdmin }) {
    const profileForm = useForm({
        name: user?.name || '',
        email: user?.email || '',
        phone: user?.phone || '',
        date_of_birth: user?.date_of_birth ? user.date_of_birth.substring(0, 10) : '',
        gender: user?.gender || '',
        address: user?.address || '',
        city: user?.city || '',
        state: user?.state || '',
        country: user?.country || 'India',
        emergency_contact_name: user?.emergency_contact_name || '',
        emergency_contact_phone: user?.emergency_contact_phone || '',
    });

    const passwordForm = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    const submitProfile = (e) => {
        e.preventDefault();
        profileForm.put('/account/profile', {
            preserveScroll: true,
        });
    };

    const submitPassword = (e) => {
        e.preventDefault();
        passwordForm.put('/account/password', {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    };

    const memberSince = user?.created_at
        ? new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
        : 'Recently';

    return (
        <AccountLayout>
            <Head title="Profile & Security — Trip Sathi" />

            <div className="mb-3">
                <h1 className="ts-title" style={{ fontSize: '1.4rem' }}>Profile & security</h1>
                <p className="ts-subtitle">Your details are used on bookings, invoices and trip reminders.</p>
            </div>

            <div className="row g-4">
                <div className="col-lg-7">
                    <div className="ts-card">
                        <div className="ts-card-body p-4">
                            <h2 className="ts-title mb-3" style={{ fontSize: '1.1rem' }}>Personal details</h2>

                            <form onSubmit={submitProfile}>
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label" htmlFor="profile-name">Full name</label>
                                        <input
                                            className={`form-control ${profileForm.errors.name ? 'is-invalid' : ''}`}
                                            id="profile-name"
                                            value={profileForm.data.name}
                                            onChange={(e) => profileForm.setData('name', e.target.value)}
                                            required
                                        />
                                        {profileForm.errors.name && (
                                            <div className="invalid-feedback">{profileForm.errors.name}</div>
                                        )}
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label" htmlFor="profile-email">Email</label>
                                        <input
                                            className={`form-control ${profileForm.errors.email ? 'is-invalid' : ''}`}
                                            type="email"
                                            id="profile-email"
                                            value={profileForm.data.email}
                                            onChange={(e) => profileForm.setData('email', e.target.value)}
                                            required
                                        />
                                        {profileForm.errors.email && (
                                            <div className="invalid-feedback">{profileForm.errors.email}</div>
                                        )}
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label" htmlFor="profile-phone">Phone</label>
                                        <input
                                            className={`form-control ${profileForm.errors.phone ? 'is-invalid' : ''}`}
                                            id="profile-phone"
                                            value={profileForm.data.phone}
                                            onChange={(e) => profileForm.setData('phone', e.target.value)}
                                            placeholder="+91 98765 43210"
                                        />
                                        {profileForm.errors.phone && (
                                            <div className="invalid-feedback">{profileForm.errors.phone}</div>
                                        )}
                                    </div>

                                    <div className="col-md-3">
                                        <label className="form-label" htmlFor="profile-dob">Date of birth</label>
                                        <input
                                            className={`form-control ${profileForm.errors.date_of_birth ? 'is-invalid' : ''}`}
                                            type="date"
                                            id="profile-dob"
                                            value={profileForm.data.date_of_birth}
                                            onChange={(e) => profileForm.setData('date_of_birth', e.target.value)}
                                        />
                                        {profileForm.errors.date_of_birth && (
                                            <div className="invalid-feedback">{profileForm.errors.date_of_birth}</div>
                                        )}
                                    </div>

                                    <div className="col-md-3">
                                        <label className="form-label" htmlFor="profile-gender">Gender</label>
                                        <select
                                            className={`form-select ${profileForm.errors.gender ? 'is-invalid' : ''}`}
                                            id="profile-gender"
                                            value={profileForm.data.gender}
                                            onChange={(e) => profileForm.setData('gender', e.target.value)}
                                        >
                                            <option value="">Prefer not to say</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        {profileForm.errors.gender && (
                                            <div className="invalid-feedback">{profileForm.errors.gender}</div>
                                        )}
                                    </div>

                                    <div className="col-12">
                                        <label className="form-label" htmlFor="profile-address">Address</label>
                                        <textarea
                                            className={`form-control ${profileForm.errors.address ? 'is-invalid' : ''}`}
                                            id="profile-address"
                                            rows="2"
                                            value={profileForm.data.address}
                                            onChange={(e) => profileForm.setData('address', e.target.value)}
                                        ></textarea>
                                        {profileForm.errors.address && (
                                            <div className="invalid-feedback">{profileForm.errors.address}</div>
                                        )}
                                    </div>

                                    <div className="col-md-4">
                                        <label className="form-label" htmlFor="profile-city">City</label>
                                        <input
                                            className="form-control"
                                            id="profile-city"
                                            value={profileForm.data.city}
                                            onChange={(e) => profileForm.setData('city', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label className="form-label" htmlFor="profile-state">State</label>
                                        <input
                                            className="form-control"
                                            id="profile-state"
                                            value={profileForm.data.state}
                                            onChange={(e) => profileForm.setData('state', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label className="form-label" htmlFor="profile-country">Country</label>
                                        <input
                                            className="form-control"
                                            id="profile-country"
                                            value={profileForm.data.country}
                                            onChange={(e) => profileForm.setData('country', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label" htmlFor="profile-emergency-name">Emergency contact name</label>
                                        <input
                                            className="form-control"
                                            id="profile-emergency-name"
                                            value={profileForm.data.emergency_contact_name}
                                            onChange={(e) => profileForm.setData('emergency_contact_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label" htmlFor="profile-emergency-phone">Emergency contact phone</label>
                                        <input
                                            className="form-control"
                                            id="profile-emergency-phone"
                                            value={profileForm.data.emergency_contact_phone}
                                            onChange={(e) => profileForm.setData('emergency_contact_phone', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12 d-flex justify-content-end gap-2 mt-4">
                                        <Link className="btn btn-outline-secondary" href="/account">Cancel</Link>
                                        <button className="btn btn-primary" type="submit" disabled={profileForm.processing}>
                                            {profileForm.processing ? 'Saving…' : 'Save changes'}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div className="col-lg-5">
                    <div className="ts-card mb-4">
                        <div className="ts-card-body p-4">
                            <h2 className="ts-title mb-1" style={{ fontSize: '1.1rem' }}>Change password</h2>
                            <p className="text-muted small mb-3">
                                Confirm your current password, then choose a new one — minimum 10 characters.
                            </p>

                            <form onSubmit={submitPassword}>
                                <div className="mb-3">
                                    <label className="form-label" htmlFor="current-password">Current password</label>
                                    <input
                                        className={`form-control ${passwordForm.errors.current_password ? 'is-invalid' : ''}`}
                                        type="password"
                                        id="current-password"
                                        autoComplete="current-password"
                                        value={passwordForm.data.current_password}
                                        onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                        required
                                    />
                                    {passwordForm.errors.current_password && (
                                        <div className="invalid-feedback">{passwordForm.errors.current_password}</div>
                                    )}
                                </div>

                                <div className="mb-3">
                                    <label className="form-label" htmlFor="new-password">New password</label>
                                    <input
                                        className={`form-control ${passwordForm.errors.new_password ? 'is-invalid' : ''}`}
                                        type="password"
                                        id="new-password"
                                        autoComplete="new-password"
                                        value={passwordForm.data.new_password}
                                        onChange={(e) => passwordForm.setData('new_password', e.target.value)}
                                        required
                                    />
                                    {passwordForm.errors.new_password && (
                                        <div className="invalid-feedback">{passwordForm.errors.new_password}</div>
                                    )}
                                </div>

                                <div className="mb-3">
                                    <label className="form-label" htmlFor="new-password-confirmation">Confirm new password</label>
                                    <input
                                        className="form-control"
                                        type="password"
                                        id="new-password-confirmation"
                                        autoComplete="new-password"
                                        value={passwordForm.data.new_password_confirmation}
                                        onChange={(e) => passwordForm.setData('new_password_confirmation', e.target.value)}
                                        required
                                    />
                                </div>

                                <div className="d-flex justify-content-end">
                                    <button className="btn btn-soft" type="submit" disabled={passwordForm.processing}>
                                        <i className="bi bi-key me-1"></i> {passwordForm.processing ? 'Updating…' : 'Update password'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div className="ts-card">
                        <div className="ts-card-body p-4">
                            <h2 className="ts-title mb-3" style={{ fontSize: '1.1rem' }}>Account status</h2>

                            <ul className="list-unstyled mb-0 d-flex flex-column gap-3">
                                <li className="d-flex align-items-center gap-3">
                                    <i className="bi bi-envelope-check text-success fs-4"></i>
                                    <div>
                                        <p className="mb-0 fw-semibold">
                                            Email {user?.email_verified_at ? 'verified' : 'not verified'}
                                        </p>
                                        <p className="mb-0 text-muted small">{user?.email}</p>
                                    </div>
                                </li>
                                <li className="d-flex align-items-center gap-3">
                                    <i className="bi bi-person-badge text-primary fs-4"></i>
                                    <div>
                                        <p className="mb-0 fw-semibold">{isAdmin ? 'Administrator' : 'Traveller'}</p>
                                        <p className="mb-0 text-muted small">Member since {memberSince}</p>
                                    </div>
                                </li>
                                <li className="d-flex align-items-center gap-3">
                                    <i className="bi bi-shield-lock text-primary fs-4"></i>
                                    <div>
                                        <p className="mb-0 fw-semibold">Two-factor authentication</p>
                                        <p className="mb-0 text-muted small">
                                            {user?.two_factor_confirmed_at ? 'Enabled' : 'Not enabled yet'}
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </AccountLayout>
    );
}
