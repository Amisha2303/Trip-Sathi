<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'destination_id' => ['required', 'integer', Rule::exists('destinations', 'id')],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'hotel')],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:12'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'star_rating' => ['nullable', 'integer', 'between:0,5'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'cancellation_policy' => ['nullable', 'string', 'max:5000'],
            'main_image' => ['nullable', function ($attribute, $value, $fail) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $ext = strtolower($value->getClientOriginalExtension() ?: (string) $value->extension());
                    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
                    if (! in_array($ext, $allowed, true)) {
                        $fail('Image must be a JPG, PNG, WEBP or SVG.');
                    }
                    if ($value->getSize() > 4096 * 1024) {
                        $fail('Image size must not exceed 4MB.');
                    }
                } elseif (! is_string($value) && ! is_null($value)) {
                    $fail('Invalid image provided.');
                }
            }],
            'remove_image' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function messages(): array
    {
        return [
            'destination_id.exists' => 'Choose a valid destination.',
            'address.required' => 'A street address is required.',
            'check_in_time.date_format' => 'Check-in time must be in HH:MM (24-hour) format.',
            'check_out_time.date_format' => 'Check-out time must be in HH:MM (24-hour) format.',
        ];
    }
}
