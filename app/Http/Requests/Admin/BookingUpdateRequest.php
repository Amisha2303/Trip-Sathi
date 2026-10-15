<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(BookingStatus::values())],
            'payment_status' => ['required', 'string', Rule::in(PaymentStatus::values())],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, mixed> */
    public function messages(): array
    {
        return [
            'status.in' => 'Pick a valid booking status.',
            'payment_status.in' => 'Pick a valid payment status.',
            'cancellation_reason.required_if' => 'A reason is required when cancelling a booking.',
        ];
    }
}
