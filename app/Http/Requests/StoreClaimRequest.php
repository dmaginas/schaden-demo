<?php

namespace App\Http\Requests;

use App\Enums\ClaimType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'type' => ['required', Rule::enum(ClaimType::class)],
            'occurred_on' => ['required', 'date', 'before_or_equal:today'],
            'reported_by' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'estimated_cost.decimal' => 'Die geschätzten Kosten dürfen höchstens 2 Nachkommastellen haben.',
        ];
    }

    /**
     * Get the German display names for the validated fields.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'Objekt',
            'type' => 'Schadenart',
            'occurred_on' => 'Schadendatum',
            'reported_by' => 'Gemeldet von',
            'description' => 'Beschreibung',
            'estimated_cost' => 'Geschätzte Kosten',
        ];
    }
}
