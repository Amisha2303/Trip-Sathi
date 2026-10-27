import React from 'react';
import { Head, usePage } from '@inertiajs/react';

export default function Invoice({ booking, payment }) {
    const { appSettings } = usePage().props;
    const isSettled = booking.payment_status === 'paid' || booking.payment_status?.value === 'paid';

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: booking.currency || 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    return (
        <div style={{ background: '#f4f6f7', minHeight: '100vh', padding: '32px 16px', color: '#22303c', fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif' }}>
            <Head title={`Receipt ${booking.booking_number} — Trip Sathi`} />

            <div className="d-print-none" style={{ maxWidth: '820px', margin: '0 auto 14px', textAlign: 'right' }}>
                <button
                    type="button"
                    onClick={() => window.print()}
                    style={{
                        border: 0,
                        borderRadius: '7px',
                        padding: '9px 16px',
                        background: '#0b6e75',
                        color: '#fff',
                        fontWeight: 700,
                        cursor: 'pointer',
                    }}
                >
                    Print / Save as PDF
                </button>
            </div>

            <main
                style={{
                    maxWidth: '820px',
                    margin: '0 auto',
                    background: '#fff',
                    border: '1px solid #dfe6e8',
                    borderRadius: '10px',
                    padding: '40px',
                    boxShadow: '0 10px 30px rgba(20, 50, 60, .08)',
                }}
            >
                <div style={{ display: 'flex', justifyContent: 'space-between', gap: '24px', flexWrap: 'wrap' }}>
                    <div>
                        <div style={{ fontSize: '22px', fontWeight: 800, letterSpacing: '-.02em', color: '#0b6e75' }}>
                            Trip Sathi
                        </div>
                        <div style={{ color: '#6b7c86', fontSize: '13px' }}>Trips, stays and experiences across India</div>
                        <div style={{ color: '#6b7c86', fontSize: '13px' }}>{appSettings?.contact_email || 'hello@tripsathi.com'}</div>
                        <div style={{ color: '#6b7c86', fontSize: '13px' }}>{appSettings?.contact_phone || '+91 9904530641'}</div>
                    </div>
                    <div style={{ textAlign: 'right' }}>
                        <h1 style={{ fontSize: '20px', margin: '0 0 4px', fontWeight: 700 }}>
                            {isSettled ? 'Payment receipt' : 'Booking summary'}
                        </h1>
                        <div style={{ color: '#6b7c86' }}>{booking.booking_number}</div>
                        <div style={{ marginTop: '8px' }}>
                            <span
                                style={{
                                    display: 'inline-block',
                                    padding: '3px 10px',
                                    borderRadius: '999px',
                                    fontSize: '12px',
                                    fontWeight: 700,
                                    background: isSettled ? '#e4f6ee' : '#fdf1dd',
                                    color: isSettled ? '#18794e' : '#9a6206',
                                }}
                            >
                                {booking.payment_status?.label || booking.payment_status}
                            </span>
                        </div>
                        <div style={{ color: '#6b7c86', marginTop: '8px', fontSize: '13px' }}>
                            Issued {new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })}
                        </div>
                    </div>
                </div>

                <div
                    style={{
                        display: 'grid',
                        gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))',
                        gap: '16px',
                        marginTop: '26px',
                        padding: '16px',
                        background: '#f8fafb',
                        borderRadius: '8px',
                    }}
                >
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Customer</span>
                        <strong>{booking.customer_name}</strong>
                    </div>
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Email</span>
                        <div>{booking.customer_email}</div>
                    </div>
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Phone</span>
                        <div>{booking.customer_phone || '—'}</div>
                    </div>
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Booking date</span>
                        <div>{booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}</div>
                    </div>
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Payment method</span>
                        <div>{payment?.method_label || '—'}</div>
                    </div>
                    <div>
                        <span style={{ display: 'block', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Reference</span>
                        <div>{payment?.transaction_reference || '—'}</div>
                    </div>
                </div>

                <table style={{ width: '100%', borderCollapse: 'collapse', marginTop: '28px' }}>
                    <thead>
                        <tr>
                            <th style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'left', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Description</th>
                            <th style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'left', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Date</th>
                            <th style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Qty</th>
                            <th style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Unit</th>
                            <th style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right', fontSize: '12px', textTransform: 'uppercase', letterSpacing: '.06em', color: '#6b7c86' }}>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {(booking.items || []).map((item) => (
                            <tr key={item.id}>
                                <td style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef' }}>
                                    <strong>{item.name}</strong>
                                    <div style={{ color: '#6b7c86', fontSize: '12px' }}>{booking.type_label}</div>
                                </td>
                                <td style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef' }}>
                                    {item.service_date ? new Date(item.service_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
                                </td>
                                <td style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right' }}>{item.quantity}</td>
                                <td style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right' }}>{formatMoney(item.unit_price)}</td>
                                <td style={{ padding: '11px 8px', borderBottom: '1px solid #e7edef', textAlign: 'right', fontWeight: 600 }}>{formatMoney(item.total_amount)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <div style={{ marginLeft: 'auto', maxWidth: '320px', marginTop: '22px' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0' }}>
                        <span>Subtotal</span>
                        <span>{formatMoney(booking.subtotal)}</span>
                    </div>
                    {parseInt(booking.discount, 10) > 0 && (
                        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', color: '#18794e' }}>
                            <span>Discount</span>
                            <span>-{formatMoney(booking.discount)}</span>
                        </div>
                    )}
                    <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0' }}>
                        <span>Taxes &amp; fees</span>
                        <span>{formatMoney(booking.tax)}</span>
                    </div>
                    {parseInt(booking.refunded_amount, 10) > 0 && (
                        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', color: '#d9534f' }}>
                            <span>Refunded</span>
                            <span>-{formatMoney(booking.refunded_amount)}</span>
                        </div>
                    )}
                    <div style={{ display: 'flex', justifyContent: 'space-between', borderTop: '2px solid #22303c', marginTop: '6px', paddingTop: '10px', fontSize: '17px', fontWeight: 800 }}>
                        <span>Total</span>
                        <span>{formatMoney(booking.final_amount)}</span>
                    </div>
                </div>

                <div style={{ marginTop: '34px', paddingTop: '18px', borderTop: '1px solid #e7edef', fontSize: '12px', color: '#6b7c86' }}>
                    This document is generated from the Trip Sathi booking record. For changes or refunds, contact support with
                    the booking number above. Amounts are shown in {booking.currency || 'INR'}.
                </div>
            </main>
        </div>
    );
}
