<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Navigation helper for the `activeNav` Blade directive.
 */
final class Nav
{
    /** Return `active` when the current route matches the given pattern. */
    public static function active(string $prefix): string
    {
        $path = '/' . ltrim(request()->path(), '/');

        if ($prefix === '/') {
            return request()->routeIs('home') ? 'active' : '';
        }

        return str_starts_with($path, $prefix) ? 'active' : '';
    }

    /** Same as active() but returns true/false for `aria-current`. */
    public static function isCurrent(string $prefix): bool
    {
        return self::active($prefix) !== '';
    }
}
