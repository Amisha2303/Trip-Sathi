<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailLog;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Base class for every Trip Sathi email.
 *
 * Responsibilities kept here so individual mailables stay declarative:
 *  - queued by default, so a booking request never waits on SMTP
 *  - writes an EmailLog row (sent / failed) the admin can inspect
 *  - resolves the from address and site name from settings, not hardcoded
 */
abstract class BaseMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Discard queued job if serialized model was deleted. */
    public bool $deleteWhenMissingModels = true;

    /** Number of queue attempts before the job is abandoned. */
    public int $tries = 3;

    /** Exponential backoff so a flaky SMTP server is not hammered. */
    public array $backoff = [30, 120, 600];

    /** The subject line, filled in by the child class. */
    protected string $mailSubject = '';

    /** Blade view used as the body. Untyped to match Mailable::$view. */
    public $view = 'emails.blank';

    public function __construct()
    {
    }

    /**
     * Intercept send to automatically write EmailLog on success or failure.
     */
    public function send($mailer)
    {
        try {
            $result = parent::send($mailer);
            $this->logSuccess();
            return $result;
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
            from: $this->fromAddress(),
            replyTo: [$this->fromAddress()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: $this->viewData(),
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Every template receives the same base context, so a footer change is a
     * single edit rather than a hunt across thirteen classes. Each mailable
     * then layers its own `payload()` variables on top.
     *
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $settings = app(SettingsService::class);
        $appUrl = rtrim((string) config('app.url', 'https://the-trip-sathi.vercel.app'), '/');

        return array_merge([
            'siteName' => $settings->siteName(),
            'tagline' => $settings->string('site_tagline'),
            'supportEmail' => $settings->supportEmail(),
            'supportPhone' => $settings->string('contact_phone'),
            'year' => now()->year,
            'appUrl' => $appUrl,
            'homeUrl' => $appUrl,
            'destinationsUrl' => $this->urlFor('destinations.index'),
            'hotelsUrl' => $this->urlFor('hotels.index'),
            'darshanUrl' => $this->urlFor('darshan.index'),
            'tripsUrl' => $this->urlFor('trips.index'),
        ], $this->payload());
    }

    /**
     * Template-specific variables, overridden by each child.
     *
     * @return array<string, mixed>
     */
    protected function payload(): array
    {
        return [];
    }

    /**
     * Build an absolute URL forced to the production domain (https://the-trip-sathi.vercel.app).
     */
    protected function urlFor(string $name, mixed $parameters = [], string $fallback = 'home'): string
    {
        $base = rtrim((string) config('app.url', 'https://the-trip-sathi.vercel.app'), '/');
        if (empty($base) || str_contains($base, 'orpin')) {
            $base = 'https://the-trip-sathi.vercel.app';
        }

        $path = Route::has($name)
            ? route($name, $parameters, false)
            : (Route::has($fallback) ? route($fallback, [], false) : '/');

        return $base . (str_starts_with($path, '/') ? $path : '/' . $path);
    }

    /**
     * Ensure any relative or absolute URL is rooted on the production frontend domain.
     */
    public static function toFrontendUrl(string $url): string
    {
        $base = rtrim((string) config('app.url', 'https://the-trip-sathi.vercel.app'), '/');
        if (empty($base) || str_contains($base, 'orpin')) {
            $base = 'https://the-trip-sathi.vercel.app';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $base . (str_starts_with($path, '/') ? $path : '/' . $path) . $query . $fragment;
    }

    protected function fromAddress(): Address
    {
        return new Address(
            config('mail.from.address', 'noreply.tripsathi@gmail.com'),
            config('mail.from.name', app(SettingsService::class)->siteName()),
        );
    }

    /**
     * Record the outcome. Called after the message is handed to the transport,
     * which is where a transport-level failure surfaces.
     */
    public function logSuccess(): void
    {
        $this->writeLog('sent');
    }

    public function logFailure(?Throwable $exception = null): void
    {
        $this->writeLog('failed', $exception?->getMessage());
    }

    private function writeLog(string $status, ?string $error = null): void
    {
        try {
            $recipients = [];

            foreach ($this->to ?? [] as $to) {
                if ($to instanceof \Symfony\Component\Mime\Address) {
                    $recipients[] = ['address' => $to->getAddress()];
                } elseif (is_array($to)) {
                    $recipients[] = ['address' => $to['address'] ?? ($to['email'] ?? json_encode($to))];
                } elseif (is_object($to) && isset($to->email)) {
                    $recipients[] = ['address' => (string) $to->email];
                } else {
                    $recipients[] = ['address' => (string) $to];
                }
            }

            EmailLog::create([
                'mailable' => static::class,
                'subject' => $this->mailSubject ?: ($this->subject ?? 'Trip Sathi Notification'),
                'to' => $recipients,
                'status' => $status,
                'error' => $error === null ? null : mb_substr($error, 0, 2000),
                'sent_at' => $status === 'sent' ? now() : null,
            ]);
        } catch (Throwable $e) {
            // Never let logging break delivery.
            Log::warning('Unable to write email log', ['error' => $e->getMessage()]);
        }
    }
}
