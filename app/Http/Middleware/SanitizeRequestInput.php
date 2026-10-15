<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trim and normalise input before it reaches a validator.
 *
 * Registered on the `sanitize` alias and applied to request-accepting routes so
 * " Smith " never becomes a distinct person from "Smith" in search, or a coupon
 * code that differs only by whitespace.
 *
 * Purification of rich text is *not* done here — that happens at render time
 * via the `purify` Blade directive, because escaping on the way in loses edits
 * when the stored markup is rewritten later.
 */
class SanitizeRequestInput
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge($this->normalize($request->all()));

        return $next($request);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function normalize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = $this->normalizeString($value);
            } elseif (is_array($value)) {
                $input[$key] = $this->normalize($value);
            }
        }

        return $input;
    }

    private function normalizeString(string $value): string
    {
        // Strip control characters (keeps the payload ASCII-safe) but leave
        // newlines alone so textarea content survives.
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;

        return trim($value);
    }
}
