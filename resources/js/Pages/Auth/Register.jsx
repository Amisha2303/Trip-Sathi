import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
        terms: true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <AppLayout title="Create Account">
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
                                        <h2 className="fw-bold mb-1">Create an Account</h2>
                                        <p className="text-muted small">Join Trip Sathi to plan and book your journeys</p>
                                    </div>

                                    <form onSubmit={handleSubmit}>
                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Full Name *</label>
                                            <input
                                                type="text"
                                                className={`form-control form-control-lg ${errors.name ? 'is-invalid' : ''}`}
                                                value={data.name}
                                                onChange={(e) => setData('name', e.target.value)}
                                                placeholder="e.g. Rahul Sharma"
                                                required
                                                autoFocus
                                            />
                                            {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Email Address *</label>
                                            <input
                                                type="email"
                                                className={`form-control form-control-lg ${errors.email ? 'is-invalid' : ''}`}
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="name@example.com"
                                                required
                                            />
                                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Phone Number</label>
                                            <input
                                                type="tel"
                                                className={`form-control form-control-lg ${errors.phone ? 'is-invalid' : ''}`}
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                placeholder="+91"
                                            />
                                            {errors.phone && <div className="invalid-feedback">{errors.phone}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Password *</label>
                                            <input
                                                type="password"
                                                className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`}
                                                value={data.password}
                                                onChange={(e) => setData('password', e.target.value)}
                                                placeholder="At least 10 characters"
                                                required
                                            />
                                            {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label fw-semibold">Confirm Password *</label>
                                            <input
                                                type="password"
                                                className="form-control form-control-lg"
                                                value={data.password_confirmation}
                                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                                placeholder="Re-enter your password"
                                                required
                                            />
                                        </div>

                                        <div className="mb-4 form-check">
                                            <input
                                                type="checkbox"
                                                className="form-check-input"
                                                id="termsCheck"
                                                checked={data.terms}
                                                onChange={(e) => setData('terms', e.target.checked)}
                                                required
                                            />
                                            <label className="form-check-label text-muted small" htmlFor="termsCheck">
                                                I agree to the <Link href="/contact" className="text-primary text-decoration-none">Terms of Service</Link> & <Link href="/contact" className="text-primary text-decoration-none">Privacy Policy</Link>
                                            </label>
                                        </div>

                                        <div className="d-grid">
                                            <button
                                                type="submit"
                                                className="btn btn-primary btn-lg"
                                                disabled={processing}
                                            >
                                                {processing ? 'Creating Account...' : 'Create Account'}
                                            </button>
                                        </div>
                                    </form>

                                    <div className="text-center mt-4 pt-2 border-top">
                                        <p className="text-muted small mb-0">
                                            Already have an account?{' '}
                                            <Link href="/login" className="text-primary text-decoration-none fw-semibold">
                                                Log in
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
