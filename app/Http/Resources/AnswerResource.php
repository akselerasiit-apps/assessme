<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'question_id' => $this->question_id,
            'gamo_objective_id' => $this->gamo_objective_id,
            
            // Question details when loaded
            'question' => $this->whenLoaded('question', function() {
                return [
                    'id' => $this->question->id,
                    'code' => $this->question->code,
                    'question_text' => $this->question->question_text,
                    'guidance' => $this->question->guidance,
                    'question_type' => $this->question->question_type,
                    'maturity_level' => $this->question->maturity_level,
                ];
            }),
            
            // GAMO Objective details when loaded
            'gamo_objective' => $this->whenLoaded('gamoObjective', function() {
                return [
                    'id' => $this->gamoObjective->id,
                    'code' => $this->gamoObjective->code,
                    'name' => $this->gamoObjective->name,
                    'category' => $this->gamoObjective->category,
                ];
            }),
            
            // Answer data (decrypt if needed)
            'answer_text' => $this->answer_text,
            'answer_json' => $this->answer_json,
            'maturity_level' => $this->maturity_level,
            'capability_score' => $this->capability_score ? round($this->capability_score, 2) : null,
            
            // Evidence information
            'evidence_file' => $this->evidence_file,
            'evidence_url' => $this->when($this->evidence_file, function() {
                return route('api.assessments.evidence', [
                    'assessment' => $this->assessment_id,
                    'answer' => $this->id
                ]);
            }),
            'has_evidence' => !empty($this->evidence_file),
            'evidence_encrypted' => $this->evidence_encrypted,
            
            'notes' => $this->notes,
            
            // Capability scores per level when loaded
            'capability_scores' => $this->whenLoaded('capabilityScores', function() {
                return $this->capabilityScores->map(function($score) {
                    return [
                        'id' => $score->id,
                        'level' => $score->level,
                        'score' => round($score->score, 2),
                        'compliance_percentage' => $score->compliance_percentage,
                        'notes' => $score->notes,
                    ];
                });
            }),
            
            // User information
            'answered_by' => $this->whenLoaded('answerer', function() {
                return [
                    'id' => $this->answerer->id,
                    'name' => $this->answerer->name,
                    'email' => $this->answerer->email,
                ];
            }),
            'answered_at' => $this->answered_at?->format('Y-m-d H:i:s'),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
