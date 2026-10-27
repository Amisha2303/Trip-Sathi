import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <AppLayout title="Forgot Password">
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
                                        <h2 className="fw-bold mb-1">Reset Password</h2>
                                        <p className="text-muted small">
                                            Enter your email address and we'll send you a password reset link.
                                        </p>
                                    </div>

                                    {status && (
                                        <div className="alert alert-success d-flex align-items-center gap-2" role="alert">
                                            <i className="bi bi-check-circle-fill"></i>
                                            <div className="small">{status}</div>
                                        </div>
                                    )}

                                    <form onSubmit={handleSubmit}>
                                        <div className="mb-4">
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

                                        <div className="d-grid mb-3">
                                            <button
                                                type="submit"
                                                className="btn btn-primary btn-lg"
                                                disabled={processing}
                                            >
                                                {processing ? 'Sending...' : 'Send Password Reset Link'}
                                            </button>
                                        </div>

                                        <div className="text-center">
                                            <Link href="/login" className="text-primary text-decoration-none small fw-semibold">
                                                <i className="bi bi-arrow-left me-1"></i> Back to login
                                            </Link>
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
