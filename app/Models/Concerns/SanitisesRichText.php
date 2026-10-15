<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Mews\Purifier\Facades\Purifier;

/**
 * All admin-authored rich text (descriptions, policies, instructions) is run
 * through HTMLPurifier on write.
 *
 * Storing sanitised HTML rather than escaping on output means the same record
 * can be rendered in a Blade `{!! !!}` block (rich text formatting preserved)
 * without ever having stored executable markup.
 */
trait SanitisesRichText
{
    /**
     * Fields treated as rich text. Only these are purified — a price or a slug
     * must never be run through the purifier's entity encoding.
     *
     * @return array<int,string>
     */
    public static function richTextFields(): array
    {
        return ['description', 'short_description', 'long_description'];
    }

    /** Purifier profile applied to admin rich text. */
    protected function purifierProfile(): string
    {
        return 'admin';
    }

    /**
     * Purify on set, so an unsafe value can never reach the database.
     * `null` is preserved: an absent description is not the same as an empty one.
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, static::richTextFields(), true) && is_string($value) && $value !== '') {
            $value = Purifier::clean($value, $this->purifierProfile());
        }

        return parent::setAttribute($key, $value);
    }
}
