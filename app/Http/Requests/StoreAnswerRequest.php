<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'assessment_id' => 'required|exists:assessments,id',
            'question_id' => 'required|exists:gamo_questions,id',
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'answer_text' => 'required|string|min:10|max:10000',
            'answer_json' => 'nullable|json',
            'maturity_level' => 'required|integer|min:0|max:5',
            'capability_score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            'evidence_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'answer_text.required' => 'Jawaban wajib diisi',
            'answer_text.min' => 'Jawaban minimal 10 karakter',
            'maturity_level.required' => 'Maturity level wajib dipilih',
        ];
    }
}
