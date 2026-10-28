import React, { useState, useEffect } from 'react';

export const PALETTES = [
    { id: 'teal', name: 'Emerald', hex: '#0d9488', lightHex: '#2dd4bf', darkHex: '#0f766e' },
    { id: 'blue', name: 'Sapphire', hex: '#2563eb', lightHex: '#60a5fa', darkHex: '#1d4ed8' },
    { id: 'purple', name: 'Amethyst', hex: '#7c3aed', lightHex: '#a78bfa', darkHex: '#6d28d9' },
    { id: 'amber', name: 'Coral', hex: '#ea580c', lightHex: '#fb923c', darkHex: '#c2410c' },
    { id: 'rose', name: 'Ruby', hex: '#e11d48', lightHex: '#fb7185', darkHex: '#be123c' },
    { id: 'gold', name: 'Imperial Gold', hex: '#d97706', lightHex: '#fbbf24', darkHex: '#b45309' },
];

export function applyTheme(mode, accent, customHex = null) {
    if (typeof window === 'undefined') return;

    const root = document.documentElement;
    let effectiveMode = mode;
    if (mode === 'auto') {
        effectiveMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    root.setAttribute('data-theme', effectiveMode);
    root.setAttribute('data-accent', accent);

    if (customHex) {
        root.style.setProperty('--ts-primary', customHex);
        root.style.setProperty('--ts-primary-dark', customHex);
        root.style.setProperty('--ts-primary-light', customHex);
    } else {
        root.style.removeProperty('--ts-primary');
        root.style.removeProperty('--ts-primary-dark');
        root.style.removeProperty('--ts-primary-light');
    }

    try {
        localStorage.setItem('ts-theme-mode', mode);
        localStorage.setItem('ts-theme-accent', accent);
        if (customHex) {
            localStorage.setItem('ts-theme-custom-hex', customHex);
        } else {
            localStorage.removeItem('ts-theme-custom-hex');
        }
    } catch (e) {}
}

export default function ThemeCustomizer({ isOpen, onClose }) {
    const [mode, setMode] = useState('light');
    const [accent, setAccent] = useState('teal');
    const [customHex, setCustomHex] = useState('');

    useEffect(() => {
        try {
            const savedMode = localStorage.getItem('ts-theme-mode') || 'light';
            const savedAccent = localStorage.getItem('ts-theme-accent') || 'teal';
            const savedCustomHex = localStorage.getItem('ts-theme-custom-hex') || '';
            setMode(savedMode);
            setAccent(savedAccent);
            setCustomHex(savedCustomHex);
            applyTheme(savedMode, savedAccent, savedCustomHex || null);
        } catch (e) {}
    }, []);

    const handleModeChange = (newMode) => {
        setMode(newMode);
        applyTheme(newMode, accent, accent === 'custom' ? customHex : null);
    };

    const handleAccentChange = (newAccent) => {
        setAccent(newAccent);
        applyTheme(mode, newAccent, null);
    };

    const handleCustomColorChange = (e) => {
        const hex = e.target.value;
        setCustomHex(hex);
        setAccent('custom');
        applyTheme(mode, 'custom', hex);
    };

    if (!isOpen) return null;

    return (
        <>
            {/* Backdrop */}
            <div
                className={`ts-theme-backdrop ${isOpen ? 'is-open' : ''}`}
                onClick={onClose}
                aria-hidden="true"
            />

            {/* Apple Frosted Glass Drawer */}
            <div className={`ts-theme-drawer ${isOpen ? 'is-open' : ''}`} role="dialog" aria-modal="true">
                <div className="ts-drawer-handle" />

                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 className="fw-bold mb-1 fs-5 text-white">Appearance & Colors</h4>
                        <p className="small text-white-50 mb-0">Personalize your Trip Sathi journey</p>
                    </div>
                    <button
                        type="button"
                        className="btn-close btn-close-white"
                        onClick={onClose}
                        aria-label="Close theme customizer"
                    />
                </div>

                {/* Light / Dark Mode Toggle */}
                <div className="mb-4">
                    <label className="form-label text-uppercase small fw-bold text-white-50 mb-2">Display Mode</label>
                    <div className="d-flex gap-2">
                        <button
                            type="button"
                            className={`ts-mode-btn ${mode === 'light' ? 'is-active' : ''}`}
                            onClick={() => handleModeChange('light')}
                        >
                            <i className="bi bi-sun-fill text-warning"></i>
                            <span>Light</span>
                        </button>
                        <button
                            type="button"
                            className={`ts-mode-btn ${mode === 'dark' ? 'is-active' : ''}`}
                            onClick={() => handleModeChange('dark')}
                        >
                            <i className="bi bi-moon-stars-fill text-info"></i>
                            <span>Dark</span>
                        </button>
                        <button
                            type="button"
                            className={`ts-mode-btn ${mode === 'auto' ? 'is-active' : ''}`}
                            onClick={() => handleModeChange('auto')}
                        >
                            <i className="bi bi-laptop text-secondary"></i>
                            <span>System</span>
                        </button>
                    </div>
                </div>

                {/* Accent Color Palette Selection */}
                <div className="mb-4">
                    <label className="form-label text-uppercase small fw-bold text-white-50 mb-2">Accent Color</label>
                    <div className="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        {PALETTES.map((p) => (
                            <button
                                key={p.id}
                                type="button"
                                className={`ts-color-swatch ${accent === p.id ? 'is-selected' : ''}`}
                                style={{ backgroundColor: p.hex }}
                                onClick={() => handleAccentChange(p.id)}
                                title={p.name}
                                aria-label={p.name}
                            />
                        ))}
                    </div>
                </div>

                {/* Custom Color Option */}
                <div className="d-flex align-items-center justify-content-between p-3 rounded-3" style={{ background: 'rgba(255, 255, 255, 0.06)' }}>
                    <div>
                        <div className="fw-semibold text-white small">Custom Color</div>
                        <div className="text-white-50 small">Pick any custom shade you love</div>
                    </div>
                    <div className="d-flex align-items-center gap-2">
                        <input
                            type="color"
                            className="form-control form-control-color border-0 p-0"
                            style={{ width: '38px', height: '38px', borderRadius: '50%', cursor: 'pointer' }}
                            value={customHex || '#0d9488'}
                            onChange={handleCustomColorChange}
                            title="Choose custom color"
                        />
                    </div>
                </div>

                <div className="mt-4 pt-2">
                    <button
                        type="button"
                        className="btn btn-primary w-100 rounded-pill py-2 fw-semibold"
                        onClick={onClose}
                    >
                        Done
                    </button>
                </div>
            </div>
        </>
    );
}
