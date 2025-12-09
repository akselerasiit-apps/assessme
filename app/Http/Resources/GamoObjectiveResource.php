<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GamoObjectiveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');
        
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->getLocalizedName($locale),
            'name_en' => $this->name,
            'name_id' => $this->name_id,
            'description' => $this->getLocalizedDescription($locale),
            'description_en' => $this->description,
            'description_id' => $this->description_id,
            'category' => $this->category,
            'objective_order' => $this->objective_order,
            'is_active' => $this->is_active,
            
            // Questions count when loaded
            'questions_count' => $this->whenLoaded('questions', function() {
                return $this->questions->count();
            }),
            
            // Questions detail when loaded
            'questions' => $this->whenLoaded('questions', function() use ($locale) {
                return $this->questions->map(function($question) use ($locale) {
                    return [
                        'id' => $question->id,
                        'code' => $question->code,
                        'question_text' => $question->question_text,
                        'guidance' => $question->guidance,
                        'question_type' => $question->question_type,
                        'maturity_level' => $question->maturity_level,
                        'required' => $question->required,
                        'question_order' => $question->question_order,
                    ];
                });
            }),
            
            // Capability definitions when loaded
            'capability_definitions' => $this->whenLoaded('capabilityDefinitions', function() {
                return $this->capabilityDefinitions->map(function($definition) {
                    return [
                        'id' => $definition->id,
                        'level' => $definition->level,
                        'title' => $definition->title,
                        'description' => $definition->description,
                        'criteria' => $definition->criteria,
                    ];
                });
            }),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
