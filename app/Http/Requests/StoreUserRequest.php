<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'phone' => 'nullable|string|max:20',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'role' => [
                'required',
                Rule::in(['Super Admin', 'Admin', 'Manager', 'Assessor', 'Viewer'])
            ],
        ];
    }
}
