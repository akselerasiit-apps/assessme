<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
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
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'company_id' => $this->company_id,
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'industry' => $this->company->industry,
                'size' => $this->company->size,
            ],
            'assessment_type' => $this->assessment_type,
            'scope_type' => $this->scope_type,
            'status' => $this->status,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'progress_percentage' => $this->progress_percentage,
            'overall_maturity_level' => $this->overall_maturity_level ? round($this->overall_maturity_level, 2) : null,
            
            // Relationships
            'design_factors' => DesignFactorResource::collection($this->whenLoaded('designFactors')),
            'gamo_objectives' => GamoObjectiveResource::collection($this->whenLoaded('gamoObjectives')),
            'gamo_scores' => $this->whenLoaded('gamoScores', function() {
                return $this->gamoScores->map(function($score) {
                    return [
                        'id' => $score->id,
                        'gamo_objective_id' => $score->gamo_objective_id,
                        'gamo_code' => $score->gamoObjective->code,
                        'gamo_name' => $score->gamoObjective->name,
                        'current_maturity_level' => round($score->current_maturity_level, 2),
                        'target_maturity_level' => round($score->target_maturity_level, 2),
                        'capability_score' => $score->capability_score ? round($score->capability_score, 2) : null,
                        'capability_level' => $score->capability_level ? round($score->capability_level, 2) : null,
                        'percentage_complete' => $score->percentage_complete,
                        'status' => $score->status,
                        'gap' => round($score->target_maturity_level - $score->current_maturity_level, 2),
                    ];
                });
            }),
            'bandings' => $this->whenLoaded('bandings', function() {
                return $this->bandings->map(function($banding) {
                    return [
                        'id' => $banding->id,
                        'gamo_objective_id' => $banding->gamo_objective_id,
                        'gamo_code' => $banding->gamoObjective->code,
                        'round' => $banding->round,
                        'status' => $banding->status,
                        'created_at' => $banding->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            
            // User information
            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ],
            'reviewed_by' => $this->when($this->reviewed_by, function() {
                return [
                    'id' => $this->reviewer->id,
                    'name' => $this->reviewer->name,
                    'email' => $this->reviewer->email,
                ];
            }),
            'approved_by' => $this->when($this->approved_by, function() {
                return [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'email' => $this->approver->email,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
