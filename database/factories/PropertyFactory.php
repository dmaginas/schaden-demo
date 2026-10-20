<?php

namespace Database\Factories;

use App\Enums\ManagementType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $managementType = fake()->randomElement(ManagementType::cases());
        $street = fake()->streetAddress();
        $hasPolicy = fake()->boolean(80);

        return [
            'name' => match ($managementType) {
                ManagementType::Weg => 'WEG '.$street,
                ManagementType::Miet => 'Mietshaus '.$street,
            },
            'street' => $street,
            'postal_code' => fake()->numerify('#####'),
            'city' => fake()->city(),
            'management_type' => $managementType,
            'insurer' => $hasPolicy ? fake()->randomElement([
                'Allianz Versicherung',
                'R+V Versicherung',
                'HUK-COBURG',
                'AXA Versicherung',
                'Provinzial',
                'Gothaer',
            ]) : null,
            'policy_number' => $hasPolicy ? fake()->bothify('WG-####-####-??') : null,
        ];
    }

    /**
     * Indicate that the property is managed as a condominium (WEG).
     */
    public function weg(): static
    {
        return $this->state(fn (array $attributes) => [
            'management_type' => ManagementType::Weg,
        ]);
    }

    /**
     * Indicate that the property is managed as a rental property.
     */
    public function miet(): static
    {
        return $this->state(fn (array $attributes) => [
            'management_type' => ManagementType::Miet,
        ]);
    }
}
