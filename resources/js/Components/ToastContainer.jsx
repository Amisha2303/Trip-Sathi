import React, { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function ToastContainer() {
    const { flash } = usePage().props;
    const [messages, setMessages] = useState([]);

    useEffect(() => {
        const newMsgs = [];
        if (flash?.success) newMsgs.push({ id: Date.now() + 1, type: 'success', text: flash.success });
        if (flash?.error) newMsgs.push({ id: Date.now() + 2, type: 'error', text: flash.error });
        if (flash?.info) newMsgs.push({ id: Date.now() + 3, type: 'info', text: flash.info });
        if (flash?.warning) newMsgs.push({ id: Date.now() + 4, type: 'warning', text: flash.warning });

        if (newMsgs.length > 0) {
            setMessages((prev) => [...prev, ...newMsgs]);
            // Auto-dismiss after 6 seconds
            const timer = setTimeout(() => {
                setMessages((prev) => prev.filter((m) => !newMsgs.some((nm) => nm.id === m.id)));
            }, 6000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const removeMessage = (id) => {
        setMessages((prev) => prev.filter((m) => m.id !== id));
    };

    if (messages.length === 0) return null;

    const config = {
        success: { icon: 'bi-check-circle-fill', tone: 'text-success', title: 'Success' },
        error: { icon: 'bi-x-circle-fill', tone: 'text-danger', title: 'Error' },
        warning: { icon: 'bi-exclamation-triangle-fill', tone: 'text-warning', title: 'Notice' },
        info: { icon: 'bi-info-circle-fill', tone: 'text-info', title: 'Info' },
    };

    return (
        <div className="toast-container position-fixed top-0 end-0 p-3" style={{ zIndex: 1090 }}>
            {messages.map((msg) => {
                const conf = config[msg.type] || config.info;
                return (
                    <div key={msg.id} className="toast show mb-2 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                        <div className="toast-header">
                            <span className={`me-2 ${conf.tone}`}>
                                <i className={`bi ${conf.icon}`}></i>
                            </span>
                            <strong className="me-auto">{conf.title}</strong>
                            <button
                                type="button"
                                className="btn-close"
                                onClick={() => removeMessage(msg.id)}
                                aria-label="Close"
                            ></button>
                        </div>
                        <div className="toast-body">{msg.text}</div>
                    </div>
                );
            })}
        </div>
    );
}
