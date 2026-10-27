import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AccountLayout from '@/Layouts/AccountLayout';

export default function Preferences({ user, prefs }) {
    const { data, setData, put, processing, errors } = useForm({
        currency: prefs?.currency || 'INR',
        language: prefs?.language || 'en',
        temperature_unit: prefs?.temperature_unit || 'c',
        distance_unit: prefs?.distance_unit || 'km',
        email_notifications: Boolean(prefs?.email_notifications),
        sms_notifications: Boolean(prefs?.sms_notifications),
        whatsapp_notifications: Boolean(prefs?.whatsapp_notifications),
        deal_alerts: Boolean(prefs?.deal_alerts),
        trip_reminders: Boolean(prefs?.trip_reminders),
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put('/account/preferences', {
            preserveScroll: true,
        });
    };

    const toggles = [
        {
            key: 'email_notifications',
            icon: 'bi-envelope-check',
            label: 'Email updates',
            hint: 'Booking confirmations, receipts and account alerts.',
        },
        {
            key: 'sms_notifications',
            icon: 'bi-phone',
            label: 'SMS alerts',
            hint: 'Time-sensitive reminders sent to your mobile number.',
        },
        {
            key: 'whatsapp_notifications',
            icon: 'bi-whatsapp',
            label: 'WhatsApp',
            hint: 'Quick updates on the number linked to your account.',
        },
        {
            key: 'deal_alerts',
            icon: 'bi-tags',
            label: 'Deal & festival alerts',
            hint: 'Price drops and seasonal offers — about twice a month.',
        },
        {
            key: 'trip_reminders',
            icon: 'bi-bell',
            label: 'Trip reminders',
            hint: 'Check-in, darshan and itinerary nudges before you travel.',
        },
    ];

    return (
        <AccountLayout>
            <Head title="Preferences — Trip Sathi" />

            <h1 className="ts-title" style={{ fontSize: '1.4rem' }}>Preferences</h1>
            <p className="text-muted mb-4">Choose how prices, distances and updates are shown for your account.</p>

            <form onSubmit={handleSubmit}>
                {/* Display */}
                <div className="ts-card mb-4">
                    <div className="ts-card-body p-4">
                        <h2 className="ts-title mb-1" style={{ fontSize: '1.1rem' }}>Display</h2>
                        <p className="text-muted small mb-4">Applied across listings, search results and booking summaries.</p>

                        <div className="row g-3">
                            <div className="col-md-6">
                                <label className="form-label fw-semibold" htmlFor="pref-currency">Currency</label>
                                <select
                                    className={`form-select ${errors.currency ? 'is-invalid' : ''}`}
                                    id="pref-currency"
                                    value={data.currency}
                                    onChange={(e) => setData('currency', e.target.value)}
                                >
                                    <option value="INR">Indian Rupee (₹)</option>
                                    <option value="USD">US Dollar ($)</option>
                                    <option value="EUR">Euro (€)</option>
                                    <option value="GBP">British Pound (£)</option>
                                    <option value="AED">UAE Dirham (د.إ)</option>
                                </select>
                                {errors.currency && (
                                    <div className="text-danger small mt-1">{errors.currency}</div>
                                )}
                            </div>

                            <div className="col-md-6">
                                <label className="form-label fw-semibold" htmlFor="pref-language">Language</label>
                                <select
                                    className={`form-select ${errors.language ? 'is-invalid' : ''}`}
                                    id="pref-language"
                                    value={data.language}
                                    onChange={(e) => setData('language', e.target.value)}
                                >
                                    <option value="en">English</option>
                                    <option value="hi">हिन्दी (Hindi)</option>
                                    <option value="gu">ગુજરાતી (Gujarati)</option>
                                </select>
                                {errors.language && (
                                    <div className="text-danger small mt-1">{errors.language}</div>
                                )}
                            </div>

                            <div className="col-md-6">
                                <label className="form-label fw-semibold" htmlFor="pref-temperature">Temperature</label>
                                <select
                                    className="form-select"
                                    id="pref-temperature"
                                    value={data.temperature_unit}
                                    onChange={(e) => setData('temperature_unit', e.target.value)}
                                >
                                    <option value="c">Celsius (°C)</option>
                                    <option value="f">Fahrenheit (°F)</option>
                                </select>
                            </div>

                            <div className="col-md-6">
                                <label className="form-label fw-semibold" htmlFor="pref-distance">Distance</label>
                                <select
                                    className="form-select"
                                    id="pref-distance"
                                    value={data.distance_unit}
                                    onChange={(e) => setData('distance_unit', e.target.value)}
                                >
                                    <option value="km">Kilometres (km)</option>
                                    <option value="mi">Miles (mi)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Notifications */}
                <div className="ts-card mb-4">
                    <div className="ts-card-body p-4">
                        <h2 className="ts-title mb-1" style={{ fontSize: '1.1rem' }}>Notifications</h2>
                        <p className="text-muted small mb-4">Pick the channels you want us to reach you on.</p>

                        <div className="d-flex flex-column gap-3">
                            {toggles.map(({ key, icon, label, hint }) => (
                                <label
                                    key={key}
                                    className="d-flex align-items-start gap-3 p-3 rounded border"
                                    style={{ cursor: 'pointer', background: 'var(--ts-bg, #fff)' }}
                                    htmlFor={`pref-${key}`}
                                >
                                    <input
                                        type="checkbox"
                                        id={`pref-${key}`}
                                        className="form-check-input mt-1"
                                        checked={data[key]}
                                        onChange={(e) => setData(key, e.target.checked)}
                                    />
                                    <div>
                                        <div className="d-flex align-items-center gap-2 fw-semibold">
                                            <i className={`bi ${icon} text-primary`}></i>
                                            <span>{label}</span>
                                        </div>
                                        <div className="text-muted small">{hint}</div>
                                    </div>
                                </label>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="d-flex gap-2">
                    <button className="btn btn-primary px-4" type="submit" disabled={processing}>
                        <i className="bi bi-check2 me-1"></i> {processing ? 'Saving…' : 'Save preferences'}
                    </button>
                    <Link className="btn btn-outline-secondary" href="/account/profile">Back to profile</Link>
                </div>
            </form>
        </AccountLayout>
    );
}
