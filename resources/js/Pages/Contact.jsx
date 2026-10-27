import React, { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import PageHero from '../Components/PageHero';

export default function Contact({ settings = {}, subjects = {} }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        subject: Object.keys(subjects)[0] || 'General enquiry',
        message: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/contact', {
            preserveScroll: true,
            onSuccess: () => reset('message'),
        });
    };

    return (
        <AppLayout
            title="Contact Us"
            description="Booking help, payment questions, partnership enquiries or feedback — send a note to Trip Sathi."
        >
            <PageHero
                image="/photos/banners/banner-contact.jpg"
                eyebrow="We reply within a day"
                title="Talk to a Trip Sathi"
                lede="Booking help, payment questions, partnership enquiries or feedback — send a note and a real person will get back to you."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Contact' }]}
                stats={[
                    ['24h', 'Typical reply time'],
                    ['7 days', 'Support coverage'],
                    ['4.8', 'Customer rating'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4 align-items-stretch">
                        <div className="col-lg-5">
                            <div
                                className="ts-contact-side h-100 p-4 text-white rounded-4"
                                style={{
                                    background: 'linear-gradient(135deg, #1b2a4a 0%, #0d172a 100%)',
                                }}
                            >
                                <span className="ts-eyebrow text-warning">Reach us directly</span>
                                <h3 className="h4 fw-bold mb-4">Support, sales & partnerships</h3>

                                <div className="ts-contact-item d-flex gap-3 mb-3">
                                    <i className="bi bi-person-badge-fill fs-4 text-warning"></i>
                                    <div>
                                        <strong className="d-block">Owner</strong>
                                        <span className="text-white-50">{settings.owner || 'Amisha Chauhan'}</span>
                                    </div>
                                </div>

                                <div className="ts-contact-item d-flex gap-3 mb-3">
                                    <i className="bi bi-envelope-fill fs-4 text-warning"></i>
                                    <div>
                                        <strong className="d-block">Email</strong>
                                        <span className="text-white-50">{settings.email || 'Amisha@tripsathi.com'}</span>
                                    </div>
                                </div>

                                <div className="ts-contact-item d-flex gap-3 mb-3">
                                    <i className="bi bi-telephone-fill fs-4 text-warning"></i>
                                    <div>
                                        <strong className="d-block">Phone</strong>
                                        <span className="text-white-50">{settings.phone || '+91 9904530641'}</span>
                                    </div>
                                </div>

                                {settings.whatsapp && (
                                    <div className="ts-contact-item d-flex gap-3 mb-3">
                                        <i className="bi bi-whatsapp fs-4 text-success"></i>
                                        <div>
                                            <strong className="d-block">WhatsApp</strong>
                                            <span className="text-white-50">{settings.whatsapp}</span>
                                        </div>
                                    </div>
                                )}

                                <div className="ts-contact-item d-flex gap-3 mb-3">
                                    <i className="bi bi-geo-alt-fill fs-4 text-warning"></i>
                                    <div>
                                        <strong className="d-block">Office</strong>
                                        <span className="text-white-50">{settings.address || 'Bhavnagar, Gujarat, India'}</span>
                                    </div>
                                </div>

                                <div className="ts-contact-item d-flex gap-3 mb-4">
                                    <i className="bi bi-clock-fill fs-4 text-warning"></i>
                                    <div>
                                        <strong className="d-block">Hours</strong>
                                        <span className="text-white-50">Mon – Sat, 9:30 AM – 6:30 PM IST</span>
                                    </div>
                                </div>

                                <div className="mt-auto pt-3 border-top border-white border-opacity-25">
                                    <p className="mb-2 small text-white-50">Prefer self-service?</p>
                                    <Link className="btn btn-accent btn-sm" href="/dashboard">
                                        <i className="bi bi-speedometer2 me-1"></i> Open your dashboard
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-7">
                            <div className="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                                <h2 className="ts-title mb-1">Send us a message</h2>
                                <p className="ts-subtitle text-muted mb-4">Fill in the form and we will reply to your email.</p>

                                <form onSubmit={handleSubmit}>
                                    <div className="row g-3">
                                        <div className="col-md-6">
                                            <label className="form-label fw-semibold">Your Name *</label>
                                            <input
                                                type="text"
                                                className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                                value={data.name}
                                                onChange={(e) => setData('name', e.target.value)}
                                                required
                                            />
                                            {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                        </div>

                                        <div className="col-md-6">
                                            <label className="form-label fw-semibold">Email Address *</label>
                                            <input
                                                type="email"
                                                className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                required
                                            />
                                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                        </div>

                                        <div className="col-md-6">
                                            <label className="form-label fw-semibold">Phone Number</label>
                                            <input
                                                type="tel"
                                                className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                placeholder="+91"
                                            />
                                            {errors.phone && <div className="invalid-feedback">{errors.phone}</div>}
                                        </div>

                                        <div className="col-md-6">
                                            <label className="form-label fw-semibold">Subject *</label>
                                            <select
                                                className="form-select"
                                                value={data.subject}
                                                onChange={(e) => setData('subject', e.target.value)}
                                            >
                                                {Object.entries(subjects).map(([k, v]) => (
                                                    <option key={k} value={k}>{v}</option>
                                                ))}
                                            </select>
                                        </div>

                                        <div className="col-12">
                                            <label className="form-label fw-semibold">Message *</label>
                                            <textarea
                                                className={`form-control ${errors.message ? 'is-invalid' : ''}`}
                                                rows="5"
                                                value={data.message}
                                                onChange={(e) => setData('message', e.target.value)}
                                                placeholder="Tell us about your trip dates, preferred destination, questions, or requirements..."
                                                required
                                            ></textarea>
                                            {errors.message && <div className="invalid-feedback">{errors.message}</div>}
                                        </div>

                                        <div className="col-12">
                                            <button
                                                type="submit"
                                                className="btn btn-accent btn-lg px-4"
                                                disabled={processing}
                                            >
                                                {processing ? 'Sending...' : 'Send Message'}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
