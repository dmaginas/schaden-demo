<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Example damage descriptions per claim type.
     *
     * @var array<string, list<string>>
     */
    protected const DESCRIPTIONS = [
        'leitungswasser' => [
            'Wasserfleck an der Decke im Badezimmer, vermutlich undichte Leitung in der Wohnung darüber.',
            'Rohrbruch im Keller, Wasser steht ca. 5 cm hoch im Heizungsraum.',
            'Feuchte Wand im Treppenhaus neben dem Steigstrang, Putz löst sich.',
        ],
        'feuer' => [
            'Kabelbrand im Sicherungskasten, Rauchschaden im Flur.',
            'Brand in der Küche einer Wohnung im 2. OG, Ruß an Wänden und Decke.',
            'Schmorstelle an einer Steckdose im Gemeinschaftsraum.',
        ],
        'sturm_hagel' => [
            'Mehrere Dachziegel nach Sturm heruntergefallen, Dach teilweise offen.',
            'Hagelschaden an der Dachrinne und am Vordach des Eingangs.',
            'Umgestürzter Baum hat die Fassade und das Garagendach beschädigt.',
        ],
        'elementar' => [
            'Starkregen hat den Keller überflutet, Trocknung erforderlich.',
            'Rückstau aus der Kanalisation, Wasser in mehreren Kellerabteilen.',
            'Hochwasser im Erdgeschoss, Estrich durchnässt.',
        ],
        'glas' => [
            'Scheibe der Hauseingangstür gesprungen.',
            'Fensterscheibe im Treppenhaus durch Steinwurf zerbrochen.',
            'Glaselement des Balkongeländers im 3. OG gebrochen.',
        ],
        'sonstiges' => [
            'Graffiti an der Fassade neben dem Eingang.',
            'Briefkastenanlage aufgebrochen und beschädigt.',
            'Tür zum Fahrradkeller nach Einbruchsversuch verzogen.',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ClaimType::cases());

        return [
            'property_id' => Property::factory(),
            'contractor_id' => null,
            'reported_by' => fake()->name(),
            'type' => $type,
            'occurred_on' => fake()->dateTimeBetween('-6 months', 'now'),
            'description' => fake()->randomElement(self::DESCRIPTIONS[$type->value]),
            'status' => ClaimStatus::Gemeldet,
            'estimated_cost' => fake()->optional(0.7)->randomFloat(2, 150, 25000),
        ];
    }

    /**
     * Indicate the claim's status.
     */
    public function status(ClaimStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Indicate that a contractor has been commissioned for the claim.
     */
    public function withContractor(?Contractor $contractor = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClaimStatus::HandwerkerBeauftragt,
            'contractor_id' => $contractor ?? Contractor::factory()->trade($attributes['type']->suggestedTrade()),
        ]);
    }
}
