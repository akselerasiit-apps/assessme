<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only owner or Admin+ can update
        return $this->user()->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answer_text' => 'sometimes|string|min:10|max:10000',
            'answer_json' => 'nullable|json',
            'maturity_level' => 'sometimes|integer|min:0|max:5',
            'capability_score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            
            // Evidence file validation
            'evidence_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240', // 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'answer_text.min' => 'Jawaban minimal 10 karakter',
            'answer_text.max' => 'Jawaban maksimal 10000 karakter',
            'maturity_level.min' => 'Maturity level minimal 0',
            'maturity_level.max' => 'Maturity level maksimal 5',
            'evidence_file.mimes' => 'Format file tidak didukung',
            'evidence_file.max' => 'Ukuran file maksimal 10MB',
        ];
    }
}
