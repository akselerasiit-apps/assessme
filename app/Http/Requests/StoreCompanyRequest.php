<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Super Admin and Admin can create companies
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
            'name' => 'required|string|max:255|min:3|unique:companies,name',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/',
            'email' => 'nullable|email|max:255',
            'industry' => 'nullable|string|max:100',
            'size' => [
                'nullable',
                Rule::in(['startup', 'sme', 'enterprise'])
            ],
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama perusahaan wajib diisi',
            'name.min' => 'Nama perusahaan minimal 3 karakter',
            'name.unique' => 'Nama perusahaan sudah terdaftar',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'email.email' => 'Format email tidak valid',
            'size.in' => 'Ukuran perusahaan tidak valid',
            'established_year.min' => 'Tahun berdiri tidak valid',
            'established_year.max' => 'Tahun berdiri tidak boleh melebihi tahun saat ini',
        ];
    }
}
