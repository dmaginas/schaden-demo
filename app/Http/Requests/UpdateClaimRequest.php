<?php

namespace App\Http\Requests;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateClaimRequest extends StoreClaimRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'status' => ['required', Rule::enum(ClaimStatus::class)],
            'contractor_id' => [
                'nullable',
                Rule::requiredIf(fn (): bool => $this->enum('status', ClaimStatus::class)?->hasContractor() ?? false),
                'exists:contractors,id',
            ],
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
            ...parent::messages(),
            'contractor_id.required' => 'Ab dem Status „Handwerker beauftragt“ muss ein Handwerker ausgewählt sein.',
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
            ...parent::attributes(),
            'status' => 'Status',
            'contractor_id' => 'Handwerker',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Claim $claim */
                $claim = $this->route('claim');

                if ($claim->status === ClaimStatus::Abgeschlossen) {
                    $validator->errors()->add('claim', 'Diese Meldung ist abgeschlossen und kann nicht mehr geändert werden.');
                }
            },
        ];
    }
}
