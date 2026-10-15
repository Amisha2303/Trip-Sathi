<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

/**
 * IP allow-listing for the admin panel.
 *
 * Optional hardening: set ADMIN_ALLOWED_IPS in .env to restrict /admin to
 * office/VPN ranges. Blank configuration means "no restriction", which keeps
 * local development friction-free.
 */
final class AdminIpGuard
{
    /** @return array<int, string> */
    public static function allowed(): array
    {
        return (array) config('tripsathi.admin.allowed_ips', []);
    }

    public static function isEmpty(): bool
    {
        return self::allowed() === [];
    }

    /** True when the request IP is inside one of the configured CIDRs/IPs. */
    public static function allows(?string $ip): bool
    {
        if (self::isEmpty()) {
            return true;
        }

        if ($ip === null || $ip === '') {
            return false;
        }

        foreach (self::allowed() as $range) {
            if (self::matches($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    public static function allowsRequest(Request $request): bool
    {
        return self::allows($request->ip());
    }

    /** Single IP or CIDR membership test. */
    private static function matches(string $ip, string $range): bool
    {
        $range = trim($range);

        if ($range === '') {
            return false;
        }

        if (! str_contains($range, '/')) {
            return hash_equals($range, $ip);
        }

        [$subnet, $bits] = explode('/', $range, 2);

        if (! is_numeric($bits)) {
            return false;
        }

        $ipBinary = @inet_pton($ip);
        $subnetBinary = @inet_pton($subnet);

        if ($ipBinary === false || $subnetBinary === false || strlen($ipBinary) !== strlen($subnetBinary)) {
            return false;
        }

        $bits = (int) $bits;
        $wholeBytes = intdiv($bits, 8);
        $remainingBits = $bits % 8;

        if ($wholeBytes > 0 && substr($ipBinary, 0, $wholeBytes) !== substr($subnetBinary, 0, $wholeBytes)) {
            return false;
        }

        if ($remainingBits === 0) {
            return true;
        }

        $mask = ~((1 << (8 - $remainingBits)) - 1) & 0xFF;

        return (ord($ipBinary[$wholeBytes]) & $mask) === (ord($subnetBinary[$wholeBytes]) & $mask);
    }
}
