<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('type', 'destination')],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'best_time_to_visit' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'language' => ['nullable', 'string', 'max:100'],
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
            'main_image.image' => 'Upload a valid image file.',
            'main_image.mimes' => 'Image must be a JPG, PNG, WEBP or SVG.',
        ];
    }
}
