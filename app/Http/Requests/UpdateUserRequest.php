<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Super Admin, Admin, or own profile
        $user = $this->user();
        $targetUser = $this->route('user');
        
        return $user->hasAnyRole(['Super Admin', 'Admin']) || $user->id == $targetUser;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|string|max:255|min:3',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'company_id' => 'sometimes|exists:companies,id',
            'role' => [
                'sometimes',
                Rule::in(['Super Admin', 'Admin', 'Manager', 'Assessor', 'Viewer'])
            ],
            'status' => [
                'sometimes',
                Rule::in(['active', 'inactive'])
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Nama minimal 3 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'company_id.exists' => 'Perusahaan tidak ditemukan',
            'role.in' => 'Role tidak valid',
        ];
    }
}
