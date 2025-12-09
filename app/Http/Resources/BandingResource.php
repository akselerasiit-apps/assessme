<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BandingResource extends JsonResource
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
            
            // Assessment details when loaded
            'assessment' => $this->whenLoaded('assessment', function() {
                return [
                    'id' => $this->assessment->id,
                    'code' => $this->assessment->code,
                    'title' => $this->assessment->title,
                    'status' => $this->assessment->status,
                ];
            }),
            
            'gamo_objective_id' => $this->gamo_objective_id,
            
            // GAMO Objective details when loaded
            'gamo_objective' => $this->whenLoaded('gamoObjective', function() {
                return [
                    'id' => $this->gamoObjective->id,
                    'code' => $this->gamoObjective->code,
                    'name' => $this->gamoObjective->name,
                    'category' => $this->gamoObjective->category,
                ];
            }),
            
            'round' => $this->round,
            'status' => $this->status,
            
            // Old and new values (JSON decoded)
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            
            // Score changes
            'old_maturity_level' => $this->when(isset($this->old_values['maturity_level']), function() {
                return $this->old_values['maturity_level'];
            }),
            'new_maturity_level' => $this->when(isset($this->new_values['maturity_level']), function() {
                return $this->new_values['maturity_level'];
            }),
            'maturity_level_change' => $this->when(
                isset($this->old_values['maturity_level']) && isset($this->new_values['maturity_level']),
                function() {
                    return round($this->new_values['maturity_level'] - $this->old_values['maturity_level'], 2);
                }
            ),
            
            'banding_reason' => $this->banding_reason,
            'banding_notes' => $this->banding_notes,
            'banded_score' => $this->banded_score ? round($this->banded_score, 2) : null,
            
            // Requestor information
            'requested_by' => $this->whenLoaded('requestedBy', function() {
                return [
                    'id' => $this->requestedBy->id,
                    'name' => $this->requestedBy->name,
                    'email' => $this->requestedBy->email,
                ];
            }),
            'requested_at' => $this->requested_at?->format('Y-m-d H:i:s'),
            
            // Handler information (for approved/rejected bandings)
            'handled_by' => $this->when($this->handled_by, function() {
                return [
                    'id' => $this->handledBy->id,
                    'name' => $this->handledBy->name,
                    'email' => $this->handledBy->email,
                ];
            }),
            'handled_at' => $this->handled_at?->format('Y-m-d H:i:s'),
            'handler_notes' => $this->handler_notes,
            
            // Reviewer information (if reviewed before handling)
            'reviewed_by' => $this->when($this->reviewed_by, function() {
                return [
                    'id' => $this->reviewedBy->id,
                    'name' => $this->reviewedBy->name,
                    'email' => $this->reviewedBy->email,
                ];
            }),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'reviewer_notes' => $this->reviewer_notes,
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
