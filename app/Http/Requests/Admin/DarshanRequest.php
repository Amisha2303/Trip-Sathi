<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DarshanRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'darshan')],
            'name' => ['required', 'string', 'max:150'],
            'temple_name' => ['required', 'string', 'max:150'],
            'deity' => ['nullable', 'string', 'max:100'],
            'darshan_type' => ['nullable', 'string', 'in:vip,regular,special,online'],
            'temple_address' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'max_visitors_per_booking' => ['nullable', 'integer', 'min:1', 'max:500'],
            'max_visitors_per_day' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            // Newline-separated textarea; exploded to a list in the controller.
            'documents_required' => ['nullable', 'string', 'max:2000'],
            'dress_code' => ['nullable', 'string', 'max:1000'],
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
            'temple_name.required' => 'The temple name is required.',
            'darshan_type.in' => 'Pick a valid darshan type.',
        ];
    }
}
