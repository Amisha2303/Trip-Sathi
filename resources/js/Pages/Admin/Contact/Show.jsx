import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Show({ message, statuses = {} }) {
    const [selectedStatus, setSelectedStatus] = useState(message.status);

    const replyForm = useForm({
        reply: '',
    });

    const handleStatusChange = (e) => {
        const nextStatus = e.target.value;
        setSelectedStatus(nextStatus);
        router.put(`/admin/contact-messages/${message.id}`, { status: nextStatus }, {
            preserveScroll: true,
        });
    };

    const handleReply = (e) => {
        e.preventDefault();
        replyForm.post(`/admin/contact-messages/${message.id}/reply`, {
            preserveScroll: true,
            onSuccess: () => replyForm.reset(),
        });
    };

    const handleDelete = () => {
        if (confirm('Delete this message?')) {
            router.delete(`/admin/contact-messages/${message.id}`);
        }
    };

    return (
        <AdminLayout
            title={message.subject || 'Message'}
            heading={`Message · ${message.name}`}
        >
            <Head title={`${message.subject || 'Message'} — Admin — Trip Sathi`} />

            <div className="row g-4">
                <div className="col-lg-8">
                    {/* Message Content */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h2 className="h5 mb-0 fw-bold">{message.subject || 'General Enquiry'}</h2>
                                <span className="small text-muted">{new Date(message.created_at).toLocaleString()}</span>
                            </div>
                            <span className={`badge ${message.status === 'replied' ? 'bg-success' : message.status === 'new' ? 'bg-warning text-dark' : 'bg-secondary'}`}>
                                {statuses[message.status] || message.status}
                            </span>
                        </div>

                        <div className="card-body p-4">
                            <div className="p-3 bg-light rounded mb-4" style={{ whiteSpace: 'pre-line', fontSize: '1rem', lineHeight: '1.6' }}>
                                {message.message}
                            </div>

                            {/* Reply History if exists */}
                            {message.reply && (
                                <div className="border-top pt-4 mt-4">
                                    <h3 className="h6 fw-bold mb-2">Previous Staff Reply</h3>
                                    <div className="p-3 bg-white border rounded">
                                        <div className="small text-muted mb-2">
                                            Replied on {message.replied_at ? new Date(message.replied_at).toLocaleString() : ''}
                                            {message.replied_by ? ` by ${message.replied_by.name}` : ''}
                                        </div>
                                        <div style={{ whiteSpace: 'pre-line' }}>{message.reply}</div>
                                    </div>
                                </div>
                            )}

                            {/* Send / Record Reply Form */}
                            <form onSubmit={handleReply} className="border-top pt-4 mt-4">
                                <h3 className="h6 fw-bold mb-2">Reply to {message.name}</h3>
                                <div className="mb-3">
                                    <textarea
                                        rows="4"
                                        className={`form-control ${replyForm.errors.reply ? 'is-invalid' : ''}`}
                                        placeholder={`Write your response to ${message.email}…`}
                                        value={replyForm.data.reply}
                                        onChange={(e) => replyForm.setData('reply', e.target.value)}
                                        required
                                    ></textarea>
                                    {replyForm.errors.reply && <div className="invalid-feedback">{replyForm.errors.reply}</div>}
                                </div>

                                <div className="d-flex justify-content-end">
                                    <button className="btn btn-primary" type="submit" disabled={replyForm.processing}>
                                        <i className="bi bi-reply-fill me-1"></i> {replyForm.processing ? 'Sending…' : 'Record Reply'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    {/* Status card */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Message Status</h3>
                            <select
                                className="form-select mb-3"
                                value={selectedStatus}
                                onChange={handleStatusChange}
                            >
                                {Object.entries(statuses).map(([k, label]) => (
                                    <option key={k} value={k}>{label}</option>
                                ))}
                            </select>

                            <button className="btn btn-outline-danger btn-sm w-100" type="button" onClick={handleDelete}>
                                <i className="bi bi-trash me-1"></i> Delete Message
                            </button>
                        </div>
                    </div>

                    {/* Sender Profile card */}
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Sender Profile</h3>
                            <p className="fw-bold mb-1">{message.name}</p>
                            <p className="small text-muted mb-1">
                                <i className="bi bi-envelope me-1"></i>
                                <a href={`mailto:${message.email}`}>{message.email}</a>
                            </p>
                            {message.phone && (
                                <p className="small text-muted mb-3">
                                    <i className="bi bi-telephone me-1"></i>
                                    <a href={`tel:${message.phone}`}>{message.phone}</a>
                                </p>
                            )}

                            {message.user && (
                                <Link className="btn btn-sm btn-outline-secondary w-100 mt-2" href={`/admin/users/${message.user.id}`}>
                                    View Linked Account
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
