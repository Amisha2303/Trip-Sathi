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
abstract class BaseMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

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
        $this->onQueue('mail');
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

        return array_merge([
            'siteName' => $settings->siteName(),
            'tagline' => $settings->string('site_tagline'),
            'supportEmail' => $settings->supportEmail(),
            'supportPhone' => $settings->string('contact_phone'),
            'year' => now()->year,
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
     * Build a URL, falling back to a page that always exists when the
     * preferred route is not registered in this deployment.
     */
    protected function urlFor(string $name, mixed $parameters = [], string $fallback = 'home'): string
    {
        return Route::has($name) ? route($name, $parameters) : route($fallback);
    }

    protected function fromAddress(): Address
    {
        return new Address(
            config('mail.from.address', 'no-reply@tripsathi.invalid'),
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
                $recipients[] = ['address' => $to instanceof \Symfony\Component\Mime\Address ? $to->getAddress() : (string) $to];
            }

            EmailLog::create([
                'mailable' => static::class,
                'subject' => $this->mailSubject,
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
