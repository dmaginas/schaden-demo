<?php

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Property
 */
class PropertyResource extends JsonResource
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
            'name' => $this->name,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'management_type' => [
                'value' => $this->management_type->value,
                'label' => $this->management_type->label(),
            ],
            'insurer' => $this->insurer,
            'policy_number' => $this->policy_number,
        ];
    }
}
