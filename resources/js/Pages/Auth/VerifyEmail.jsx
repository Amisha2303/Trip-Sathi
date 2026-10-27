import React from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';

export default function VerifyEmail({ email, status }) {
    const { post, processing } = useForm({});

    const handleResend = (e) => {
        e.preventDefault();
        post('/email/verify/resend', {
            data: { email },
        });
    };

    return (
        <AppLayout title="Verify Your Email">
            <div
                className="ts-auth py-5 min-vh-75 d-flex align-items-center"
                style={{ '--ts-auth-img': `url('/photos/banners/banner-auth.jpg')` }}
            >
                <div className="container py-4">
                    <div className="row justify-content-center">
                        <div className="col-md-6 col-lg-5">
                            <div className="card border-0 shadow rounded-4">
                                <div className="card-body p-4 p-md-5 text-center">
                                    <div className="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex p-3 mb-3 fs-2">
                                        <i className="bi bi-envelope-check"></i>
                                    </div>

                                    <h2 className="fw-bold mb-2">Verify Your Email</h2>
                                    <p className="text-muted small mb-4">
                                        We sent a verification link to <strong>{email}</strong>. Please check your inbox and click the link to confirm your account.
                                    </p>

                                    {status && (
                                        <div className="alert alert-success d-flex align-items-center gap-2 mb-4 text-start" role="alert">
                                            <i className="bi bi-check-circle-fill"></i>
                                            <div className="small">{status}</div>
                                        </div>
                                    )}

                                    <form onSubmit={handleResend} className="mb-3">
                                        <button
                                            type="submit"
                                            className="btn btn-primary btn-lg w-100"
                                            disabled={processing}
                                        >
                                            {processing ? 'Sending...' : 'Resend Verification Email'}
                                        </button>
                                    </form>

                                    <div className="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <Link href="/dashboard" className="text-muted small text-decoration-none">
                                            Go to dashboard
                                        </Link>
                                        <Link href="/logout" method="post" as="button" className="btn btn-link text-danger small text-decoration-none p-0">
                                            Sign out
                                        </Link>
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
