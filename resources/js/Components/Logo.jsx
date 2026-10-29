import React from 'react';

/**
 * Official Trip Sathi Logo Component
 * Meets all Brand & Visual Identity guidelines:
 * - Brand spelling: Trip Sathi
 * - Primary Navy (#16324F) & Secondary Teal (#0F9D91), Accent Coral (#FF795D)
 * - Concept: Location pin + Horizon + Compass
 * - Variants: 'horizontal' (wordmark + icon), 'icon' (compact squircle)
 * - Themes: 'light' (navy text), 'dark' (white text)
 */
export default function Logo({
    variant = 'horizontal',
    theme = 'light',
    height = 40,
    className = '',
    showTagline = true,
}) {
    const isDark = theme === 'dark';
    const textColorPrimary = isDark ? '#FFFFFF' : '#16324F';
    const textColorSecondary = isDark ? '#16B8AB' : '#0F9D91';
    const taglineColor = isDark ? '#94A3B8' : '#64748B';

    if (variant === 'icon') {
        return (
            <svg
                width={height}
                height={height}
                viewBox="0 0 48 48"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                className={`ts-logo-icon ${className}`}
                aria-label="Trip Sathi"
            >
                <defs>
                    <linearGradient id="tsIconGradCompact" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stopColor="#16324F" />
                        <stop offset="100%" stopColor="#0F9D91" />
                    </linearGradient>
                </defs>
                <rect width="48" height="48" rx="13" fill="url(#tsIconGradCompact)" />
                <rect x="1.5" y="1.5" width="45" height="45" rx="11.5" stroke="rgba(255,255,255,0.22)" strokeWidth="1.2" />
                <path d="M8 31 C 17 26, 31 26, 40 31" stroke="#FFFFFF" strokeWidth="2" strokeLinecap="round" fill="none" opacity="0.45" />
                <path d="M24 10 C 17.5 10, 12 15.5, 12 22 C 12 29.5, 24 38, 24 38 C 24 38, 36 29.5, 36 22 C 36 15.5, 30.5 10, 24 10 Z" fill="#FFFFFF" fillOpacity="0.96" />
                <circle cx="24" cy="21" r="5" fill="#0F9D91" />
                <circle cx="24" cy="21" r="2" fill="#FFFFFF" />
                <circle cx="37" cy="11" r="2.8" fill="#FF795D" />
            </svg>
        );
    }

    // Default horizontal wordmark
    const width = Math.round((height * 200) / 48);

    return (
        <svg
            width={width}
            height={height}
            viewBox="0 0 200 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className={`ts-logo-full ${className}`}
            aria-label="Trip Sathi - Discover & Plan"
        >
            <defs>
                <linearGradient id={`tsIconGrad_${theme}`} x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stopColor={isDark ? '#0F9D91' : '#16324F'} />
                    <stop offset="100%" stopColor={isDark ? '#16324F' : '#0F9D91'} />
                </linearGradient>
            </defs>
            <g transform="translate(4, 4)">
                <rect width="40" height="40" rx="11" fill={`url(#tsIconGrad_${theme})`} />
                <rect x="1.5" y="1.5" width="37" height="37" rx="9.5" stroke="rgba(255,255,255,0.22)" strokeWidth="1" />
                <path d="M7 26 C 14 22, 26 22, 33 26" stroke="#FFFFFF" strokeWidth="1.8" strokeLinecap="round" fill="none" opacity="0.45" />
                <path d="M20 9 C 14.5 9, 10 13.5, 10 19 C 10 25.5, 20 33, 20 33 C 20 33, 30 25.5, 30 19 C 30 13.5, 25.5 9, 20 9 Z" fill="#FFFFFF" fillOpacity="0.95" />
                <circle cx="20" cy="18" r="4.2" fill="#0F9D91" />
                <circle cx="20" cy="18" r="1.8" fill="#FFFFFF" />
                <circle cx="31" cy="9" r="2.2" fill="#FF795D" />
            </g>
            <text x="54" y="29" fontFamily="'Manrope', 'Inter', system-ui, sans-serif" fontSize="21" fontWeight="800" letterSpacing="-0.5px">
                <tspan fill={textColorPrimary}>Trip </tspan>
                <tspan fill={textColorSecondary}>Sathi</tspan>
            </text>
            {showTagline && (
                <text x="55" y="40" fontFamily="'Inter', system-ui, sans-serif" fontSize="8" fontWeight="600" fill={taglineColor} letterSpacing="1.2px">
                    DISCOVER &bull; PLAN &bull; EXPLORE
                </text>
            )}
        </svg>
    );
}
