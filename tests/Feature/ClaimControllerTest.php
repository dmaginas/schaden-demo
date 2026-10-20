<?php

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Enums\Trade;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

it('lists claims with property, contractor and german labels', function () {
    $claim = Claim::factory()->withContractor()->create(['type' => ClaimType::SturmHagel]);

    $this->get(route('claims.index'))
        ->assertOk()
        ->assertSee($claim->property->name)
        ->assertSee($claim->contractor->name)
        ->assertSee('Sturm/Hagel')
        ->assertSee('Handwerker beauftragt');
});

it('filters claims by status and type', function () {
    $matching = Claim::factory()->status(ClaimStatus::InPruefung)->create(['type' => ClaimType::Feuer]);
    $otherStatus = Claim::factory()->create(['type' => ClaimType::Feuer]);
    $otherType = Claim::factory()->status(ClaimStatus::InPruefung)->create(['type' => ClaimType::Glas]);

    $this->get(route('claims.index', ['status' => 'in_pruefung', 'type' => 'feuer']))
        ->assertOk()
        ->assertSee(route('claims.show', $matching))
        ->assertDontSee(route('claims.show', $otherStatus))
        ->assertDontSee(route('claims.show', $otherType));
});

it('ignores unknown filter values', function () {
    $claim = Claim::factory()->create();

    $this->get(route('claims.index', ['status' => 'unbekannt']))
        ->assertOk()
        ->assertSee(route('claims.show', $claim));
});

it('stores a new claim with status gemeldet regardless of the request', function () {
    $property = Property::factory()->create();

    $response = $this->post(route('claims.store'), [
        'property_id' => $property->id,
        'reported_by' => 'Erika Mustermann',
        'type' => 'leitungswasser',
        'occurred_on' => '2026-10-01',
        'description' => 'Wasser tropft von der Decke.',
        'estimated_cost' => '850.00',
        'status' => 'abgeschlossen',
    ]);

    $claim = Claim::sole();

    $response->assertRedirect(route('claims.show', $claim))
        ->assertSessionHas('success', 'Schadenmeldung wurde angelegt.');

    expect($claim->status)->toBe(ClaimStatus::Gemeldet)
        ->and($claim->property->is($property))->toBeTrue()
        ->and($claim->type)->toBe(ClaimType::Leitungswasser);
});

it('requires the claim fields when storing', function () {
    $this->post(route('claims.store'), [])
        ->assertSessionHasErrors(['property_id', 'reported_by', 'type', 'occurred_on', 'description']);

    expect(Claim::count())->toBe(0);
});

it('shows a claim with its property and contractor', function () {
    $claim = Claim::factory()->withContractor()->create();

    $this->get(route('claims.show', $claim))
        ->assertOk()
        ->assertSee($claim->property->street)
        ->assertSee($claim->contractor->email);
});

it('returns 404 for an unknown claim', function () {
    $this->get(route('claims.show', 999))->assertNotFound();
});

it('lists contractors with the suggested trade first when editing', function () {
    $claim = Claim::factory()->create(['type' => ClaimType::Glas]);
    $electrician = Contractor::factory()->trade(Trade::Elektro)->create(['name' => 'Aaa Elektro GmbH']);
    $glazier = Contractor::factory()->trade(Trade::Glaser)->create(['name' => 'Zzz Glaserei KG']);

    $this->get(route('claims.edit', $claim))
        ->assertOk()
        ->assertSeeInOrder(['Passendes Gewerk (Glaserei)', $glazier->name, 'Weitere Handwerker', $electrician->name]);
});

it('updates the claim status and contractor', function () {
    $claim = Claim::factory()->create();
    $contractor = Contractor::factory()->create();

    $this->put(route('claims.update', $claim), [
        'property_id' => $claim->property_id,
        'reported_by' => $claim->reported_by,
        'type' => $claim->type->value,
        'occurred_on' => $claim->occurred_on->toDateString(),
        'description' => 'Aktualisierte Beschreibung',
        'status' => 'handwerker_beauftragt',
        'contractor_id' => $contractor->id,
    ])
        ->assertRedirect(route('claims.show', $claim))
        ->assertSessionHas('success', 'Schadenmeldung wurde aktualisiert.');

    $claim->refresh();

    expect($claim->status)->toBe(ClaimStatus::HandwerkerBeauftragt)
        ->and($claim->contractor->is($contractor))->toBeTrue()
        ->and($claim->description)->toBe('Aktualisierte Beschreibung');
});

it('does not allow deleting claims', function () {
    $claim = Claim::factory()->create();

    $this->delete(route('claims.show', $claim))->assertMethodNotAllowed();

    expect(Claim::count())->toBe(1);
});
