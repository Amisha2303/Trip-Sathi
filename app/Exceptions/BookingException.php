<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * A business-rule failure during a booking.
 *
 * Rendered to the customer as a friendly message (never a stack trace) and
 * recorded as a warning, so an expected "slot just sold out" is not treated as
 * an application error.
 */
class BookingException extends RuntimeException
{
    /** @var array<string, mixed> */
    private array $context = [];

    public function __construct(string $message, array $context = [], int $code = 422)
    {
        parent::__construct($message, $code);

        $this->context = $context;
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return $this->context;
    }

    public static function soldOut(string $what = 'This slot'): self
    {
        return new self($what . ' has just sold out. Please choose another option.');
    }

    public static function notBookable(string $what = 'This item'): self
    {
        return new self($what . ' is not available for booking.');
    }

    public static function invalidDate(string $field): self
    {
        return new self('The ' . $field . ' you selected is not valid.');
    }
}
