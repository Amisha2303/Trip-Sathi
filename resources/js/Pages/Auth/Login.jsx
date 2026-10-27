import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function Login({ canResetPassword = true }) {
    const searchParams = new URLSearchParams(window.location.search);
    const next = searchParams.get('next') || '';

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
        next: next,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <AppLayout title="Sign In">
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
                                        <h2 className="fw-bold mb-1">Welcome Back</h2>
                                        <p className="text-muted small">Sign in to manage your bookings and trips</p>
                                    </div>

                                    <form onSubmit={handleSubmit}>
                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Email Address</label>
                                            <input
                                                type="email"
                                                className={`form-control form-control-lg ${errors.email ? 'is-invalid' : ''}`}
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="name@example.com"
                                                required
                                                autoFocus
                                            />
                                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <div className="d-flex justify-content-between align-items-center mb-1">
                                                <label className="form-label fw-semibold mb-0">Password</label>
                                                {canResetPassword && (
                                                    <Link href="/forgot-password" className="text-primary text-decoration-none small">
                                                        Forgot password?
                                                    </Link>
                                                )}
                                            </div>
                                            <input
                                                type="password"
                                                className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`}
                                                value={data.password}
                                                onChange={(e) => setData('password', e.target.value)}
                                                placeholder="Enter your password"
                                                required
                                            />
                                            {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                                        </div>

                                        <div className="mb-4 form-check">
                                            <input
                                                type="checkbox"
                                                className="form-check-input"
                                                id="rememberMeCheck"
                                                checked={data.remember}
                                                onChange={(e) => setData('remember', e.target.checked)}
                                            />
                                            <label className="form-check-label text-muted small" htmlFor="rememberMeCheck">
                                                Keep me signed in
                                            </label>
                                        </div>

                                        <div className="d-grid">
                                            <button
                                                type="submit"
                                                className="btn btn-primary btn-lg"
                                                disabled={processing}
                                            >
                                                {processing ? 'Signing In...' : 'Log In'}
                                            </button>
                                        </div>
                                    </form>

                                    <div className="text-center mt-4 pt-2 border-top">
                                        <p className="text-muted small mb-0">
                                            Don't have an account?{' '}
                                            <Link href="/register" className="text-primary text-decoration-none fw-semibold">
                                                Sign up
                                            </Link>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
