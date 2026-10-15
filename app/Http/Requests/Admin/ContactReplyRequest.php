<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reply' => ['required', 'string', 'min:10', 'max:10000'],
            'status' => ['nullable', 'string', Rule::in(['replied', 'closed'])],
        ];
    }

    /** @return array<string, mixed> */
    public function messages(): array
    {
        return [
            'reply.required' => 'Write a reply before sending.',
            'reply.min' => 'The reply must be at least 10 characters.',
        ];
    }
}
