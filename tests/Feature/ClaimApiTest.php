<?php

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Models\Claim;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Expected JSON structure of a single claim.
 *
 * @return array<int|string, mixed>
 */
function claimJsonStructure(): array
{
    return [
        'id',
        'type' => ['value', 'label'],
        'status' => ['value', 'label'],
        'reported_by',
        'occurred_on',
        'description',
        'estimated_cost',
        'property' => [
            'id', 'name', 'street', 'postal_code', 'city',
            'management_type' => ['value', 'label'],
            'insurer', 'policy_number',
        ],
        'contractor',
        'created_at',
        'updated_at',
    ];
}

it('lists claims as paginated json', function () {
    Claim::factory()->count(16)->create();

    $this->getJson('/api/claims')
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['*' => claimJsonStructure()],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('meta.total', 16)
        ->assertJsonPath('meta.last_page', 2);
});

it('returns enums with german labels and dates in iso 8601', function () {
    $claim = Claim::factory()->create([
        'type' => ClaimType::SturmHagel,
        'occurred_on' => '2026-03-15',
        'estimated_cost' => 850.5,
    ]);

    $this->getJson('/api/claims')
        ->assertOk()
        ->assertJsonPath('data.0.type', ['value' => 'sturm_hagel', 'label' => 'Sturm/Hagel'])
        ->assertJsonPath('data.0.status', ['value' => 'gemeldet', 'label' => 'Gemeldet'])
        ->assertJsonPath('data.0.occurred_on', '2026-03-15')
        ->assertJsonPath('data.0.estimated_cost', '850.50')
        ->assertJsonPath('data.0.created_at', $claim->created_at->toIso8601String());
});

it('filters claims by status', function () {
    $closed = Claim::factory()->withContractor()->status(ClaimStatus::Abgeschlossen)->create();
    Claim::factory()->create();

    $this->getJson('/api/claims?status=abgeschlossen')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $closed->id);
});

it('filters claims by type', function () {
    $glass = Claim::factory()->create(['type' => ClaimType::Glas]);
    Claim::factory()->create(['type' => ClaimType::Feuer]);

    $this->getJson('/api/claims?type=glas')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $glass->id);
});

it('ignores unknown filter values', function () {
    Claim::factory()->count(2)->create();

    $this->getJson('/api/claims?status=unbekannt')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('shows a single claim with property and contractor', function () {
    $claim = Claim::factory()->withContractor()->create();

    $this->getJson("/api/claims/{$claim->id}")
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                ...claimJsonStructure(),
                'contractor' => ['id', 'name', 'trade' => ['value', 'label'], 'email', 'phone'],
            ],
        ])
        ->assertJsonPath('data.id', $claim->id)
        ->assertJsonPath('data.property.id', $claim->property_id)
        ->assertJsonPath('data.contractor.id', $claim->contractor_id);
});

it('only exposes selected fields', function () {
    $claim = Claim::factory()->create();

    $this->getJson("/api/claims/{$claim->id}")
        ->assertOk()
        ->assertJsonPath('data.contractor', null)
        ->assertJsonMissingPath('data.property_id')
        ->assertJsonMissingPath('data.contractor_id')
        ->assertJsonMissingPath('data.property.created_at');
});

it('returns a json 404 for an unknown claim', function () {
    $this->getJson('/api/claims/999')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});

it('is read only', function () {
    $claim = Claim::factory()->create();

    $this->postJson('/api/claims', [])->assertMethodNotAllowed();
    $this->putJson("/api/claims/{$claim->id}", [])->assertMethodNotAllowed();
    $this->deleteJson("/api/claims/{$claim->id}")->assertMethodNotAllowed();
});
