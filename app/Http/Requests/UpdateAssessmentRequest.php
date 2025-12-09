<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssessmentRequest extends FormRequest
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
        $assessmentId = $this->route('assessment');

        return [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('assessments', 'code')->ignore($assessmentId)
            ],
            'title' => 'sometimes|string|max:255|min:5',
            'description' => 'nullable|string|max:5000',
            'company_id' => 'sometimes|exists:companies,id',
            'assessment_type' => [
                'sometimes',
                Rule::in(['initial', 'periodic', 'specific'])
            ],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'in_progress', 'completed', 'reviewed', 'approved', 'archived'])
            ],
            'assessment_period_start' => 'sometimes|date',
            'assessment_period_end' => 'nullable|date|after:assessment_period_start',
            'progress_percentage' => 'sometimes|integer|min:0|max:100',
        ];
    }
}
