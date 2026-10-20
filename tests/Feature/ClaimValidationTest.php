<?php

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * Build a valid claim payload, optionally overriding fields.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function validClaimPayload(array $overrides = []): array
{
    return [
        'property_id' => Property::factory()->create()->id,
        'reported_by' => 'Erika Mustermann',
        'type' => 'leitungswasser',
        'occurred_on' => now()->subDay()->toDateString(),
        'description' => 'Wasser tropft von der Decke im Bad.',
        'estimated_cost' => '850.50',
        ...$overrides,
    ];
}

it('rejects invalid claim data', function (string $field, mixed $value) {
    $this->from(route('claims.create'))
        ->post(route('claims.store'), validClaimPayload([$field => $value]))
        ->assertRedirect(route('claims.create'))
        ->assertSessionHasErrors($field);

    expect(Claim::count())->toBe(0);
})->with([
    'missing property' => ['property_id', null],
    'unknown property' => ['property_id', 999],
    'unknown type' => ['type', 'erdbeben'],
    'missing date' => ['occurred_on', null],
    'invalid date' => ['occurred_on', 'gestern'],
    'future date' => ['occurred_on', '2099-01-01'],
    'missing reporter' => ['reported_by', null],
    'reporter too long' => ['reported_by', str_repeat('a', 256)],
    'description too short' => ['description', 'Zu kurz'],
    'description too long' => ['description', str_repeat('a', 5001)],
    'cost not numeric' => ['estimated_cost', 'viel'],
    'negative cost' => ['estimated_cost', '-1'],
    'cost too high' => ['estimated_cost', '1000000.01'],
    'cost with three decimals' => ['estimated_cost', '10.555'],
]);

it('accepts a claim without estimated cost and with today as date', function () {
    $this->post(route('claims.store'), validClaimPayload([
        'estimated_cost' => null,
        'occurred_on' => today()->toDateString(),
    ]))->assertSessionHasNoErrors();

    expect(Claim::sole()->estimated_cost)->toBeNull();
});

it('shows german validation messages with german field names', function () {
    $this->post(route('claims.store'), validClaimPayload([
        'occurred_on' => now()->addDay()->toDateString(),
        'description' => 'Zu kurz',
        'estimated_cost' => '10.555',
    ]))->assertSessionHasErrors([
        'occurred_on' => 'Das Feld Schadendatum muss ein Datum vor oder gleich heute sein.',
        'description' => 'Das Feld Beschreibung muss mindestens 10 Zeichen haben.',
        'estimated_cost' => 'Die geschätzten Kosten dürfen höchstens 2 Nachkommastellen haben.',
    ]);
});

it('keeps the old input after a failed validation', function () {
    $this->from(route('claims.create'))
        ->post(route('claims.store'), validClaimPayload(['reported_by' => 'Max Muster', 'description' => '']));

    $this->get(route('claims.create'))
        ->assertSee('value="Max Muster"', false)
        ->assertSee('Das Feld Beschreibung ist erforderlich.');
});

it('requires a contractor from status handwerker_beauftragt on', function (ClaimStatus $status) {
    $claim = Claim::factory()->create();

    $this->put(route('claims.update', $claim), validClaimPayload([
        'status' => $status->value,
        'contractor_id' => null,
    ]))->assertSessionHasErrors([
        'contractor_id' => 'Ab dem Status „Handwerker beauftragt“ muss ein Handwerker ausgewählt sein.',
    ]);

    expect($claim->fresh()->status)->toBe(ClaimStatus::Gemeldet);
})->with([ClaimStatus::HandwerkerBeauftragt, ClaimStatus::Reguliert, ClaimStatus::Abgeschlossen]);

it('allows no contractor before a contractor is commissioned', function () {
    $claim = Claim::factory()->create();

    $this->put(route('claims.update', $claim), validClaimPayload([
        'status' => ClaimStatus::InPruefung->value,
        'contractor_id' => null,
    ]))->assertSessionHasNoErrors();

    expect($claim->fresh()->status)->toBe(ClaimStatus::InPruefung);
});

it('rejects an unknown status or contractor on update', function () {
    $claim = Claim::factory()->create();

    $this->put(route('claims.update', $claim), validClaimPayload([
        'status' => 'erledigt',
        'contractor_id' => 999,
    ]))->assertSessionHasErrors(['status', 'contractor_id']);
});

it('does not allow changing a closed claim', function () {
    $contractor = Contractor::factory()->create();
    $claim = Claim::factory()->status(ClaimStatus::Abgeschlossen)->create([
        'contractor_id' => $contractor->id,
        'description' => 'Ursprüngliche Beschreibung',
    ]);

    $this->from(route('claims.edit', $claim))
        ->put(route('claims.update', $claim), validClaimPayload([
            'status' => ClaimStatus::InPruefung->value,
            'contractor_id' => $contractor->id,
            'description' => 'Nachträglich geänderte Beschreibung',
        ]))
        ->assertRedirect(route('claims.edit', $claim))
        ->assertSessionHasErrors([
            'claim' => 'Diese Meldung ist abgeschlossen und kann nicht mehr geändert werden.',
        ]);

    $claim->refresh();

    expect($claim->status)->toBe(ClaimStatus::Abgeschlossen)
        ->and($claim->description)->toBe('Ursprüngliche Beschreibung');
});
