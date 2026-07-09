<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmailConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'admin' || $this->user()->hasRole('admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider_name' => ['required', 'string', 'max:255'],
            'host' => ['nullable', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'encryption' => ['nullable', 'string', 'in:ssl,tls,null'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'reply_to' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'port.required' => 'Please provide a valid SMTP port (e.g., 587 or 465).',
            'port.min' => 'The SMTP port must be at least 1.',
            'port.max' => 'The SMTP port cannot exceed 65535.',
            'from_name.required' => 'The Sender Name is required.',
            'from_email.email' => 'Please enter a valid From Email address.',
            'reply_to.email' => 'Please enter a valid Reply-To Email address.',
        ];
    }
}
