<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'event')],
            'name' => ['required', 'string', 'max:150'],
            'venue' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'organiser' => ['nullable', 'string', 'max:150'],
            'event_date' => ['required', 'date'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'terms' => ['nullable', 'string', 'max:5000'],
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
            'venue.required' => 'A venue name is required.',
            'ends_at.after_or_equal' => 'The end time cannot be before the start time.',
        ];
    }
}
