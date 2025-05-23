<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|regex:/^([^0-9]*)$/',
            'lastname' => 'required|regex:/^([^0-9]*)$/',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|string|min:6',
            'phone' => [
                'nullable',
                Rule::unique('users', 'phone')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'firstname.regex' => 'Numeric characters are not allowed.',
            'lastname.regex' => 'Numeric characters are not allowed.',
        ];
    }
}
