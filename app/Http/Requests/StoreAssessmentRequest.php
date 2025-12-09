<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Super Admin', 'Admin', 'Manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('assessments', 'code')
            ],
            'title' => 'required|string|max:255|min:5',
            'description' => 'nullable|string|max:5000',
            'company_id' => 'required|exists:companies,id',
            'assessment_type' => [
                'required',
                Rule::in(['initial', 'periodic', 'specific'])
            ],
            'scope_type' => [
                'nullable',
                Rule::in(['full', 'tailored'])
            ],
            'assessment_period_start' => 'required|date|after_or_equal:today',
            'assessment_period_end' => 'nullable|date|after:assessment_period_start',
            
            'design_factors' => 'nullable|array|max:10',
            'design_factors.*.id' => 'required|exists:design_factors,id',
            'design_factors.*.selected_value' => 'nullable|string|max:500',
            'design_factors.*.description' => 'nullable|string|max:1000',
            
            'gamo_objectives' => 'nullable|array|max:24',
            'gamo_objectives.*' => 'exists:gamo_objectives,id',
            
            'target_maturity' => 'nullable|array',
            'target_maturity.*.gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'target_maturity.*.level' => 'required|numeric|min:0|max:5',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode assessment wajib diisi',
            'code.unique' => 'Kode assessment sudah digunakan',
            'title.required' => 'Judul assessment wajib diisi',
            'company_id.required' => 'Perusahaan wajib dipilih',
            'start_date.required' => 'Tanggal mulai wajib diisi',
        ];
    }
}
