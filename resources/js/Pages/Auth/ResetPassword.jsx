import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token: token || '',
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <AppLayout title="Set New Password">
            <div
                className="ts-auth py-5 min-vh-75 d-flex align-items-center"
                style={{ '--ts-auth-img': `url('/photos/banners/banner-auth.jpg')` }}
            >
                <div className="container py-4">
                    <div className="row justify-content-center">
                        <div className="col-md-6 col-lg-5">
                            <div className="card border-0 shadow rounded-4">
                                <div className="card-body p-4 p-md-5">
                                    <div className="text-center mb-4">
                                        <h2 className="fw-bold mb-1">Set New Password</h2>
                                        <p className="text-muted small">Choose a secure password with at least 10 characters</p>
                                    </div>

                                    <form onSubmit={handleSubmit}>
                                        <input type="hidden" name="token" value={data.token} />

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Email Address</label>
                                            <input
                                                type="email"
                                                className={`form-control form-control-lg ${errors.email ? 'is-invalid' : ''}`}
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                required
                                            />
                                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">New Password</label>
                                            <input
                                                type="password"
                                                className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`}
                                                value={data.password}
                                                onChange={(e) => setData('password', e.target.value)}
                                                placeholder="At least 10 characters"
                                                required
                                                autoFocus
                                            />
                                            {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                                        </div>

                                        <div className="mb-4">
                                            <label className="form-label fw-semibold">Confirm New Password</label>
                                            <input
                                                type="password"
                                                className="form-control form-control-lg"
                                                value={data.password_confirmation}
                                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                                placeholder="Re-enter new password"
                                                required
                                            />
                                        </div>

                                        <div className="d-grid mb-3">
                                            <button
                                                type="submit"
                                                className="btn btn-primary btn-lg"
                                                disabled={processing}
                                            >
                                                {processing ? 'Updating...' : 'Update Password'}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
