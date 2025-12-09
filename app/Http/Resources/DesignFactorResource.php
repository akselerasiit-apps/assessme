<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignFactorResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'factor_order' => $this->factor_order,
            'is_active' => $this->is_active,
            
            // Pivot data when accessed from assessment relationship
            'selected_value' => $this->whenPivotLoaded('assessment_design_factors', function() {
                return $this->pivot->selected_value;
            }),
            'pivot_description' => $this->whenPivotLoaded('assessment_design_factors', function() {
                return $this->pivot->description;
            }),
            'selected_at' => $this->whenPivotLoaded('assessment_design_factors', function() {
                return $this->pivot->created_at?->format('Y-m-d H:i:s');
            }),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
