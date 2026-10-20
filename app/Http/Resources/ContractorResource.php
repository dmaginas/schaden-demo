<?php

namespace App\Http\Resources;

use App\Models\Contractor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Contractor
 */
class ContractorResource extends JsonResource
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
            'trade' => [
                'value' => $this->trade->value,
                'label' => $this->trade->label(),
            ],
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
