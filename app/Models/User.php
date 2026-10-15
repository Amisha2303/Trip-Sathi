<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityAction;
use App\Models\Concerns\LogsActivity;
use App\Notifications\AdminNewBookingNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingConfirmedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_admin
 * @property bool $is_active
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'preferences',
        'is_active',
        'email_verified_at',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'preferences' => 'array',
        ];
    }

    /**
     * Default display + notification preferences for every account.
     * Keys here are the full contract — the preferences form is generated
     * from this map so a new preference is one line, not a form rewrite.
     *
     * @return array<string, mixed>
     */
    public static function defaultPreferences(): array
    {
        return [
            'currency' => 'INR',
            'language' => 'en',
            'temperature_unit' => 'c',
            'distance_unit' => 'km',
            'email_notifications' => true,
            'sms_notifications' => false,
            'whatsapp_notifications' => true,
            'deal_alerts' => true,
            'trip_reminders' => true,
        ];
    }

    /** Merge stored preferences over the defaults and return the full map. */
    public function prefs(): array
    {
        return array_merge(self::defaultPreferences(), (array) ($this->attributes['preferences'] ?? []));
    }

    /** Read a single preference with a default fallback. */
    public function pref(string $key, mixed $default = null): mixed
    {
        return $this->prefs()[$key] ?? $default;
    }

    /**
     * Merge a partial preferences payload over the current values.
     *
     * @param  array<string, mixed>  $input
     */
    public function mergePreferences(array $input): array
    {
        $known = self::defaultPreferences();
        $current = $this->prefs();

        foreach ($input as $key => $value) {
            if (array_key_exists($key, $known)) {
                $current[$key] = $value;
            }
        }

        return $current;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class)->latest();
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Access control
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /** Deactivated accounts cannot sign in or keep a live session. */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isDeactivated(): bool
    {
        return ! $this->isActive();
    }

    /** Gate::before() consults this — super admins bypass every permission check. */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(\App\Enums\UserRole::SuperAdmin->value);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function isTwoFactorPending(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at === null;
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    /** Never store a plaintext password, even if something slips past validation. */
    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['password'] = Hash::make($value);
        $this->attributes['password_changed_at'] = now();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('is_admin', false);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('is_admin', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function markLogin(?string $ip = null): void
    {
        $this->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ])->saveQuietly();

        $this->logActivity(ActivityAction::Login, "{$this->name} signed in");
    }

    public function logLoginFailure(string $email, ?string $ip = null): void
    {
        ActivityLog::record(
            ActivityAction::LoginFailed,
            'auth',
            'Failed sign-in attempt',
            null,
            ['email' => $email],
            $ip,
        );
    }

    /** Full name used on invoices and in admin tables. */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? self::imageUrl($this->avatar, 'images/avatar-placeholder.svg')
            : 'https://ui-avatars.com/api/?name=' . rawurlencode($this->name) . '&background=0E7C7B&color=fff&size=128';
    }

    /** Initials used by the dashboard greeting and avatars. */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= Str::upper(Str::substr($part, 0, 1));
        }

        return $initials === '' ? 'TS' : $initials;
    }

    /** Notify this user about a booking state change through the shared notifier. */
    public function notifyBookingConfirmed(Booking $booking): void
    {
        $this->notify(new BookingConfirmedNotification($booking));
    }

    public function notifyBookingCancelled(Booking $booking, ?string $reason = null): void
    {
        $this->notify(new BookingCancelledNotification($booking, $reason));
    }

    public function notifyAdminNewBooking(Booking $booking): void
    {
        $this->notify(new AdminNewBookingNotification($booking));
    }

    /** Alias used by PaymentService when a booking settles. */
    public function notifyBookingAdmin(Booking $booking): void
    {
        $this->notify(new AdminNewBookingNotification($booking));
    }

    /**
     * Active staff accounts.
     *
     * Cached for a minute because notification fan-out hits this on every
     * payment and booking confirmation.
     *
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function activeAdmins(): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'users.active_admins',
            60,
            fn () => static::query()
                ->admins()
                ->active()
                ->orderBy('id')
                ->get(),
        );
    }

    /** Bust the admin list cache after a role or status change. */
    public static function flushAdminCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('users.active_admins');
    }

    /** Notify every active admin of a platform event. */
    public static function notifyAdmins(object|string $notification, mixed $data = null): void
    {
        foreach (self::activeAdmins() as $admin) {
            $notification instanceof \Illuminate\Notifications\Notification
                ? $admin->notify($notification)
                : $admin->notify(new $notification($data));
        }
    }

    /** Verification link honouring the current request host for local dev. */
    public function getEmailVerificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())],
        );
    }

    /**
     * Send the reset link through Trip Sathi's own template rather than
     * Laravel's stock notification, so the mail matches the rest of the brand
     * and lands in the EmailLog the admin can inspect.
     */
    public function sendPasswordResetNotification($token): void
    {
        \Illuminate\Support\Facades\Mail::to($this)
            ->queue(new \App\Mail\PasswordResetMail($this, $token));
    }

    /**
     * Same reasoning for the verification link.
     */
    public function sendEmailVerificationNotification(): void
    {
        \Illuminate\Support\Facades\Mail::to($this)
            ->queue(new \App\Mail\EmailVerificationMail($this));
    }

    /**
     * Deterministic booking-stat counters used by the dashboard stat cards.
     * Kept here so the controller stays a thin reader.
     *
     * @return array<string, int>
     */
    public function bookingStats(): array
    {
        $base = $this->bookings()->getQuery();

        return [
            'total' => (clone $base)->count(),
            'upcoming' => (clone $base)
                ->whereIn('status', ['pending', 'confirmed', 'processing'])
                ->whereDate('booking_date', '>=', today())
                ->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->whereIn('status', ['cancelled', 'refunded'])->count(),
        ];
    }
}
