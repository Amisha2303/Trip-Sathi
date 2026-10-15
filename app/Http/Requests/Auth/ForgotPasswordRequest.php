<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

/**
 * Reset-link request.
 *
 * Always "passes" for a well-formed address: revealing whether an email is
 * registered is exactly the user-enumeration leak this is designed to avoid.
 * The controller reports the same success message either way.
 */
class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter the email address on your account.',
            'email.email' => 'That does not look like an email address.',
        ];
    }

    /**
     * Send the link and return whether it was sent to a known account — used
     * only to rate-limit, never to choose the response body.
     */
    public function sendLink(): string
    {
        return Password::sendResetLink($this->only('email'));
    }
}
