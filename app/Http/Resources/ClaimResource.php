<?php

namespace App\Http\Resources;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Claim
 */
class ClaimResource extends JsonResource
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
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'reported_by' => $this->reported_by,
            'occurred_on' => $this->occurred_on->toDateString(),
            'description' => $this->description,
            'estimated_cost' => $this->estimated_cost,
            'property' => PropertyResource::make($this->whenLoaded('property')),
            'contractor' => ContractorResource::make($this->whenLoaded('contractor')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
