import React, { useState, useMemo } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Create({
    type,
    model,
    detail,
    rooms = [],
    tickets = [],
    slots = [],
    today,
    tomorrow,
    maxDate,
    requiresIdProof,
    maxParticipants = 10,
    maxVisitors = 10,
    maxGuests = 30,
    taxPercent = 18,
    currencySymbol = '₹',
    gatewayLabel = 'Razorpay',
}) {
    // Initial form state based on booking type
    const initialData = {
        coupon_code: '',
        notes: '',
        // Hotel
        room_id: rooms.length > 0 ? rooms[0].id : '',
        check_in: tomorrow,
        check_out: new Date(new Date(tomorrow).getTime() + 86400000).toISOString().substring(0, 10),
        adults: 2,
        children: 0,
        rooms: 1,
        // Event
        ticket_type_id: tickets.length > 0 ? tickets[0].id : '',
        quantity: 1,
        attendees: [''],
        // Adventure
        slot_id: slots.length > 0 ? slots[0].id : '',
        participants: [{ name: '', age: '' }],
        emergency_contact: '',
        // Darshan
        visitors: [{ name: '', id_number: '' }],
        // Pooja
        devotee_name: '',
        // Restaurant / Food
        date: tomorrow,
        guests: 2,
        occasion: '',
        seating: '',
        dietary: '',
    };

    const { data, setData, post, processing, errors } = useForm(initialData);

    // Calculate subtotal
    const subtotal = useMemo(() => {
        if (type === 'hotel') {
            const selectedRoom = rooms.find((r) => String(r.id) === String(data.room_id)) || rooms[0];
            const price = selectedRoom ? (selectedRoom.effective_price || 0) : 0;
            const inDate = new Date(data.check_in);
            const outDate = new Date(data.check_out);
            const nights = Math.max(1, Math.round((outDate - inDate) / (1000 * 60 * 60 * 24)));
            return price * nights * (parseInt(data.rooms, 10) || 1);
        }
        if (type === 'event') {
            const selectedTicket = tickets.find((t) => String(t.id) === String(data.ticket_type_id)) || tickets[0];
            const price = selectedTicket ? (selectedTicket.effective_price || 0) : 0;
            return price * (parseInt(data.quantity, 10) || 1);
        }
        if (type === 'adventure') {
            const unitPrice = parseFloat(detail?.price || 0);
            return unitPrice * (data.participants?.length || 1);
        }
        if (type === 'darshan') {
            const unitPrice = parseFloat(detail?.price || 0);
            return unitPrice * (data.visitors?.length || 1);
        }
        if (type === 'pooja') {
            const unitPrice = parseFloat(detail?.price || 0);
            return unitPrice;
        }
        if (type === 'restaurant' || type === 'food') {
            const unitPrice = parseFloat(detail?.price || 0);
            return unitPrice * (parseInt(data.guests, 10) || 1);
        }
        return parseFloat(detail?.price || 0);
    }, [type, data, rooms, tickets, detail]);

    const tax = Math.round(subtotal * (taxPercent / 100));
    const total = subtotal + tax;

    const formatMoney = (amount) => {
        return `${currencySymbol}${new Intl.NumberFormat('en-IN').format(amount)}`;
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/book/${type}/${model.slug}`);
    };

    // Helper functions for dynamic array rows
    const addParticipant = () => {
        if (data.participants.length < maxParticipants) {
            setData('participants', [...data.participants, { name: '', age: '' }]);
        }
    };

    const removeParticipant = (index) => {
        if (data.participants.length > 1) {
            setData('participants', data.participants.filter((_, i) => i !== index));
        }
    };

    const updateParticipant = (index, field, value) => {
        const next = [...data.participants];
        next[index][field] = value;
        setData('participants', next);
    };

    const addVisitor = () => {
        if (data.visitors.length < maxVisitors) {
            setData('visitors', [...data.visitors, { name: '', id_number: '' }]);
        }
    };

    const removeVisitor = (index) => {
        if (data.visitors.length > 1) {
            setData('visitors', data.visitors.filter((_, i) => i !== index));
        }
    };

    const updateVisitor = (index, field, value) => {
        const next = [...data.visitors];
        next[index][field] = value;
        setData('visitors', next);
    };

    return (
        <AppLayout>
            <Head title={`Book ${detail?.name || 'Experience'} — Trip Sathi`} />

            <section className="ts-booking-head py-4 bg-light border-bottom">
                <div className="container">
                    <nav aria-label="breadcrumb" className="mb-2">
                        <ol className="breadcrumb mb-0">
                            <li className="breadcrumb-item"><Link href={`/${type === 'hotel' ? 'hotels' : type}`}>{detail?.eyebrow || 'Catalogue'}</Link></li>
                            <li className="breadcrumb-item"><Link href={`/${type === 'hotel' ? 'hotels' : type}/${model.slug}`}>{detail?.name}</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">Book</li>
                        </ol>
                    </nav>

                    <div className="d-flex flex-wrap align-items-end justify-content-between gap-3">
                        <div>
                            <span className="ts-eyebrow text-primary text-uppercase fw-bold small">Secure Booking</span>
                            <h1 className="ts-title h3 mb-1">{detail?.name}</h1>
                            <div className="ts-card-meta small text-muted d-flex gap-3">
                                {detail?.location && (
                                    <span><i className="bi bi-geo-alt me-1"></i> {detail.location}</span>
                                )}
                                {detail?.rating != null && (
                                    <span><i className="bi bi-star-fill text-warning me-1"></i> {detail.rating} ({detail.reviewCount})</span>
                                )}
                            </div>
                        </div>
                        {detail?.price != null && (
                            <div className="ts-price fs-4 fw-bold text-end">
                                {formatMoney(detail.price)}
                                <small className="d-block text-muted fs-6 fw-normal">{detail.priceNote}</small>
                            </div>
                        )}
                    </div>
                </div>
            </section>

            <section className="ts-section py-5">
                <div className="container">
                    {Object.keys(errors).length > 0 && (
                        <div className="alert alert-danger mb-4" role="alert">
                            <strong className="d-block mb-1">Please fix the following errors:</strong>
                            <ul className="mb-0 ps-3">
                                {Object.entries(errors).map(([k, err]) => (
                                    <li key={k}>{err}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        <div className="row g-4">
                            <div className="col-lg-7">
                                <div className="card shadow-sm border-0 mb-4">
                                    <div className="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
                                        <span className="badge bg-primary rounded-circle p-2 px-3 fs-6">1</span>
                                        <div>
                                            <h2 className="h5 mb-0 fw-bold">Booking Details</h2>
                                            <p className="text-muted small mb-0">Availability is held for you once submitted.</p>
                                        </div>
                                    </div>

                                    <div className="card-body p-4">
                                        {/* HOTEL SPECIFIC */}
                                        {type === 'hotel' && (
                                            <>
                                                <div className="mb-4">
                                                    <label className="form-label fw-semibold">Choose Room</label>
                                                    <div className="d-flex flex-column gap-2">
                                                        {rooms.map((room) => (
                                                            <label
                                                                key={room.id}
                                                                className={`p-3 border rounded d-flex justify-content-between align-items-center ${
                                                                    String(data.room_id) === String(room.id) ? 'border-primary bg-light' : ''
                                                                }`}
                                                                style={{ cursor: 'pointer' }}
                                                            >
                                                                <div className="d-flex align-items-center gap-3">
                                                                    <input
                                                                        type="radio"
                                                                        name="room_id"
                                                                        className="form-check-input"
                                                                        checked={String(data.room_id) === String(room.id)}
                                                                        onChange={() => setData('room_id', room.id)}
                                                                    />
                                                                    <div>
                                                                        <strong>{room.name}</strong>
                                                                        <div className="small text-muted">
                                                                            Sleeps {room.max_occupancy}
                                                                            {room.breakfast_included ? ' · Breakfast included' : ''}
                                                                            {` · ${room.total_rooms} room(s) available`}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="text-end">
                                                                    <div className="fw-bold">{formatMoney(room.effective_price)}</div>
                                                                    <small className="text-muted">per night</small>
                                                                </div>
                                                            </label>
                                                        ))}
                                                    </div>
                                                </div>

                                                <div className="row g-3">
                                                    <div className="col-sm-6">
                                                        <label className="form-label" htmlFor="check_in">Check-in</label>
                                                        <input
                                                            type="date"
                                                            id="check_in"
                                                            className="form-control"
                                                            min={today}
                                                            value={data.check_in}
                                                            onChange={(e) => setData('check_in', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    <div className="col-sm-6">
                                                        <label className="form-label" htmlFor="check_out">Check-out</label>
                                                        <input
                                                            type="date"
                                                            id="check_out"
                                                            className="form-control"
                                                            min={data.check_in || tomorrow}
                                                            value={data.check_out}
                                                            onChange={(e) => setData('check_out', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    <div className="col-sm-4">
                                                        <label className="form-label" htmlFor="adults">Adults</label>
                                                        <input
                                                            type="number"
                                                            id="adults"
                                                            className="form-control"
                                                            min="1"
                                                            max="10"
                                                            value={data.adults}
                                                            onChange={(e) => setData('adults', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    <div className="col-sm-4">
                                                        <label className="form-label" htmlFor="children">Children</label>
                                                        <input
                                                            type="number"
                                                            id="children"
                                                            className="form-control"
                                                            min="0"
                                                            max="8"
                                                            value={data.children}
                                                            onChange={(e) => setData('children', e.target.value)}
                                                        />
                                                    </div>
                                                    <div className="col-sm-4">
                                                        <label className="form-label" htmlFor="rooms">Rooms</label>
                                                        <input
                                                            type="number"
                                                            id="rooms"
                                                            className="form-control"
                                                            min="1"
                                                            max="10"
                                                            value={data.rooms}
                                                            onChange={(e) => setData('rooms', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                </div>
                                            </>
                                        )}

                                        {/* EVENT SPECIFIC */}
                                        {type === 'event' && (
                                            <>
                                                <div className="mb-4">
                                                    <label className="form-label fw-semibold">Select Ticket</label>
                                                    <div className="d-flex flex-column gap-2">
                                                        {tickets.map((t) => (
                                                            <label
                                                                key={t.id}
                                                                className={`p-3 border rounded d-flex justify-content-between align-items-center ${
                                                                    String(data.ticket_type_id) === String(t.id) ? 'border-primary bg-light' : ''
                                                                }`}
                                                                style={{ cursor: 'pointer' }}
                                                            >
                                                                <div className="d-flex align-items-center gap-3">
                                                                    <input
                                                                        type="radio"
                                                                        name="ticket_type_id"
                                                                        className="form-check-input"
                                                                        checked={String(data.ticket_type_id) === String(t.id)}
                                                                        onChange={() => setData('ticket_type_id', t.id)}
                                                                    />
                                                                    <div>
                                                                        <strong>{t.name}</strong>
                                                                        <div className="small text-muted">{t.description || 'General entry'}</div>
                                                                    </div>
                                                                </div>
                                                                <div className="text-end">
                                                                    <div className="fw-bold">{formatMoney(t.effective_price)}</div>
                                                                    <small className="text-muted">per ticket</small>
                                                                </div>
                                                            </label>
                                                        ))}
                                                    </div>
                                                </div>

                                                <div className="row g-3">
                                                    <div className="col-sm-6">
                                                        <label className="form-label" htmlFor="quantity">Tickets</label>
                                                        <input
                                                            type="number"
                                                            id="quantity"
                                                            className="form-control"
                                                            min="1"
                                                            max="20"
                                                            value={data.quantity}
                                                            onChange={(e) => setData('quantity', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                </div>
                                            </>
                                        )}

                                        {/* ADVENTURE SPECIFIC */}
                                        {type === 'adventure' && (
                                            <>
                                                <div className="mb-4">
                                                    <label className="form-label fw-semibold" htmlFor="slot_id">Departure Slot</label>
                                                    <select
                                                        id="slot_id"
                                                        className="form-select"
                                                        value={data.slot_id}
                                                        onChange={(e) => setData('slot_id', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">Choose a date &amp; time</option>
                                                        {slots.map((s) => (
                                                            <option key={s.id} value={s.id}>
                                                                {s.label || `${s.start_date} ${s.start_time || ''}`}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div className="d-flex justify-content-between align-items-center mb-2">
                                                    <label className="form-label fw-semibold mb-0">Participants</label>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-outline-primary"
                                                        onClick={addParticipant}
                                                        disabled={data.participants.length >= maxParticipants}
                                                    >
                                                        <i className="bi bi-plus-lg me-1"></i> Add person
                                                    </button>
                                                </div>

                                                {data.participants.map((p, i) => (
                                                    <div key={i} className="row g-2 align-items-end mb-2">
                                                        <div className="col-7">
                                                            <label className="form-label small">Name</label>
                                                            <input
                                                                type="text"
                                                                className="form-control"
                                                                value={p.name}
                                                                onChange={(e) => updateParticipant(i, 'name', e.target.value)}
                                                                required
                                                            />
                                                        </div>
                                                        <div className="col-3">
                                                            <label className="form-label small">Age</label>
                                                            <input
                                                                type="number"
                                                                className="form-control"
                                                                min="1"
                                                                max="99"
                                                                value={p.age}
                                                                onChange={(e) => updateParticipant(i, 'age', e.target.value)}
                                                                required
                                                            />
                                                        </div>
                                                        <div className="col-2">
                                                            <button
                                                                type="button"
                                                                className="btn btn-outline-danger w-100"
                                                                onClick={() => removeParticipant(i)}
                                                                disabled={data.participants.length === 1}
                                                            >
                                                                <i className="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                ))}

                                                <div className="mt-3">
                                                    <label className="form-label" htmlFor="emergency_contact">Emergency Contact</label>
                                                    <input
                                                        type="text"
                                                        id="emergency_contact"
                                                        className="form-control"
                                                        placeholder="Name &amp; phone number"
                                                        value={data.emergency_contact}
                                                        onChange={(e) => setData('emergency_contact', e.target.value)}
                                                    />
                                                </div>
                                            </>
                                        )}

                                        {/* DARSHAN SPECIFIC */}
                                        {type === 'darshan' && (
                                            <>
                                                <div className="mb-4">
                                                    <label className="form-label fw-semibold" htmlFor="slot_id">Darshan Slot</label>
                                                    <select
                                                        id="slot_id"
                                                        className="form-select"
                                                        value={data.slot_id}
                                                        onChange={(e) => setData('slot_id', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">Choose a date &amp; time</option>
                                                        {slots.map((s) => (
                                                            <option key={s.id} value={s.id}>
                                                                {s.label || `${s.start_date} ${s.start_time || ''}`}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div className="d-flex justify-content-between align-items-center mb-2">
                                                    <label className="form-label fw-semibold mb-0">Visitors</label>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-outline-primary"
                                                        onClick={addVisitor}
                                                        disabled={data.visitors.length >= maxVisitors}
                                                    >
                                                        <i className="bi bi-plus-lg me-1"></i> Add visitor
                                                    </button>
                                                </div>

                                                {data.visitors.map((v, i) => (
                                                    <div key={i} className="row g-2 align-items-end mb-2">
                                                        <div className="col-6">
                                                            <label className="form-label small">Visitor Name</label>
                                                            <input
                                                                type="text"
                                                                className="form-control"
                                                                value={v.name}
                                                                onChange={(e) => updateVisitor(i, 'name', e.target.value)}
                                                                required
                                                            />
                                                        </div>
                                                        <div className="col-4">
                                                            <label className="form-label small">ID Number (Aadhaar/Passport)</label>
                                                            <input
                                                                type="text"
                                                                className="form-control"
                                                                value={v.id_number}
                                                                onChange={(e) => updateVisitor(i, 'id_number', e.target.value)}
                                                                required={Boolean(requiresIdProof)}
                                                            />
                                                        </div>
                                                        <div className="col-2">
                                                            <button
                                                                type="button"
                                                                className="btn btn-outline-danger w-100"
                                                                onClick={() => removeVisitor(i)}
                                                                disabled={data.visitors.length === 1}
                                                            >
                                                                <i className="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                ))}
                                            </>
                                        )}

                                        {/* POOJA SPECIFIC */}
                                        {type === 'pooja' && (
                                            <>
                                                <div className="mb-3">
                                                    <label className="form-label fw-semibold" htmlFor="slot_id">Pooja Slot</label>
                                                    <select
                                                        id="slot_id"
                                                        className="form-select"
                                                        value={data.slot_id}
                                                        onChange={(e) => setData('slot_id', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">Choose a slot</option>
                                                        {slots.map((s) => (
                                                            <option key={s.id} value={s.id}>
                                                                {s.label || `${s.start_date} ${s.start_time || ''}`}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div className="row g-3">
                                                    <div className="col-sm-8">
                                                        <label className="form-label" htmlFor="devotee_name">Devotee / Sankalp Name</label>
                                                        <input
                                                            type="text"
                                                            id="devotee_name"
                                                            className="form-control"
                                                            placeholder="Main devotee name"
                                                            value={data.devotee_name}
                                                            onChange={(e) => setData('devotee_name', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    <div className="col-sm-4">
                                                        <label className="form-label" htmlFor="participants">Participants</label>
                                                        <input
                                                            type="number"
                                                            id="participants"
                                                            className="form-control"
                                                            min="1"
                                                            max={maxParticipants}
                                                            value={data.participants?.length || 1}
                                                            onChange={(e) => setData('participants', parseInt(e.target.value, 10))}
                                                            required
                                                        />
                                                    </div>
                                                </div>
                                            </>
                                        )}

                                        {/* RESTAURANT & FOOD SPECIFIC */}
                                        {(type === 'restaurant' || type === 'food') && (
                                            <>
                                                <div className="mb-3">
                                                    <label className="form-label fw-semibold" htmlFor="slot_id">Time Slot</label>
                                                    <select
                                                        id="slot_id"
                                                        className="form-select"
                                                        value={data.slot_id}
                                                        onChange={(e) => setData('slot_id', e.target.value)}
                                                        required
                                                    >
                                                        <option value="">Choose time slot</option>
                                                        {slots.map((s) => (
                                                            <option key={s.id} value={s.id}>
                                                                {s.label || `${s.name || ''} (${s.start_time || ''} - ${s.end_time || ''})`}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </div>

                                                <div className="row g-3">
                                                    <div className="col-sm-6">
                                                        <label className="form-label" htmlFor="date">Date</label>
                                                        <input
                                                            type="date"
                                                            id="date"
                                                            className="form-control"
                                                            min={today}
                                                            value={data.date}
                                                            onChange={(e) => setData('date', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    <div className="col-sm-6">
                                                        <label className="form-label" htmlFor="guests">Number of Guests</label>
                                                        <input
                                                            type="number"
                                                            id="guests"
                                                            className="form-control"
                                                            min="1"
                                                            max={maxGuests}
                                                            value={data.guests}
                                                            onChange={(e) => setData('guests', e.target.value)}
                                                            required
                                                        />
                                                    </div>
                                                    {type === 'restaurant' && (
                                                        <>
                                                            <div className="col-sm-6">
                                                                <label className="form-label" htmlFor="occasion">Occasion</label>
                                                                <input
                                                                    type="text"
                                                                    id="occasion"
                                                                    className="form-control"
                                                                    placeholder="e.g. Birthday, Anniversary"
                                                                    value={data.occasion}
                                                                    onChange={(e) => setData('occasion', e.target.value)}
                                                                />
                                                            </div>
                                                            <div className="col-sm-6">
                                                                <label className="form-label" htmlFor="seating">Seating Preference</label>
                                                                <select
                                                                    id="seating"
                                                                    className="form-select"
                                                                    value={data.seating}
                                                                    onChange={(e) => setData('seating', e.target.value)}
                                                                >
                                                                    <option value="">Any</option>
                                                                    <option value="indoor">Indoor</option>
                                                                    <option value="outdoor">Outdoor / Rooftop</option>
                                                                    <option value="window">Window Side</option>
                                                                </select>
                                                            </div>
                                                        </>
                                                    )}
                                                    {type === 'food' && (
                                                        <div className="col-12">
                                                            <label className="form-label" htmlFor="dietary">Dietary Requirements</label>
                                                            <input
                                                                type="text"
                                                                id="dietary"
                                                                className="form-control"
                                                                placeholder="e.g. Pure Vegetarian, Jain, Vegan, Allergies"
                                                                value={data.dietary}
                                                                onChange={(e) => setData('dietary', e.target.value)}
                                                            />
                                                        </div>
                                                    )}
                                                </div>
                                            </>
                                        )}

                                        {/* COUPON & NOTES */}
                                        <hr className="my-4" />

                                        <div className="row g-3">
                                            <div className="col-sm-6">
                                                <label className="form-label" htmlFor="coupon_code">Coupon Code</label>
                                                <input
                                                    type="text"
                                                    id="coupon_code"
                                                    className="form-control text-uppercase"
                                                    placeholder="e.g. FESTIVE20"
                                                    value={data.coupon_code}
                                                    onChange={(e) => setData('coupon_code', e.target.value.toUpperCase())}
                                                />
                                            </div>
                                            <div className="col-12">
                                                <label className="form-label" htmlFor="notes">Special Requests / Notes</label>
                                                <textarea
                                                    id="notes"
                                                    className="form-control"
                                                    rows="2"
                                                    placeholder="Any special instructions for the host or guide"
                                                    value={data.notes}
                                                    onChange={(e) => setData('notes', e.target.value)}
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* PRICE SUMMARY ASIDE */}
                            <div className="col-lg-5">
                                <aside className="card shadow-sm border-0 sticky-top" style={{ top: '2rem' }}>
                                    <div className="card-body p-4">
                                        <h3 className="h5 fw-bold mb-3 border-bottom pb-2">Price Breakdown</h3>

                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="text-muted">Subtotal</span>
                                            <span className="fw-semibold">{formatMoney(subtotal)}</span>
                                        </div>

                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="text-muted">Estimated Tax ({taxPercent}%)</span>
                                            <span className="fw-semibold">{formatMoney(tax)}</span>
                                        </div>

                                        <div className="d-flex justify-content-between py-3 border-top border-bottom my-3">
                                            <strong className="fs-5">Estimated Total</strong>
                                            <strong className="fs-5 text-primary">{formatMoney(total)}</strong>
                                        </div>

                                        <button
                                            type="submit"
                                            className="btn btn-accent btn-lg w-100 shadow-sm mb-3"
                                            disabled={processing}
                                        >
                                            {processing ? 'Holding your booking…' : 'Proceed to Payment'}
                                        </button>

                                        <ul className="list-unstyled text-muted small d-flex flex-column gap-2 mb-0">
                                            <li className="d-flex align-items-center gap-2">
                                                <i className="bi bi-shield-check text-success"></i>
                                                <span>Your booking is held while you pay</span>
                                            </li>
                                            <li className="d-flex align-items-center gap-2">
                                                <i className="bi bi-credit-card text-primary"></i>
                                                <span>Pay safely via {gatewayLabel}</span>
                                            </li>
                                            <li className="d-flex align-items-center gap-2">
                                                <i className="bi bi-envelope-check text-info"></i>
                                                <span>Confirmation email &amp; receipt provided</span>
                                            </li>
                                        </ul>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </AppLayout>
    );
}
