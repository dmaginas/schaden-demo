<?php

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Enums\Trade;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('links claims to their property and contractor', function () {
    $property = Property::factory()->create();
    $contractor = Contractor::factory()->create();
    $claim = Claim::factory()->for($property)->withContractor($contractor)->create();

    expect($claim->property->is($property))->toBeTrue()
        ->and($claim->contractor->is($contractor))->toBeTrue()
        ->and($property->claims->pluck('id')->all())->toBe([$claim->id])
        ->and($contractor->claims->pluck('id')->all())->toBe([$claim->id]);
});

it('casts enum, date and cost attributes', function () {
    $claim = Claim::factory()->create([
        'type' => 'glas',
        'occurred_on' => '2026-03-15',
        'estimated_cost' => 1234.5,
    ])->fresh();

    expect($claim->type)->toBe(ClaimType::Glas)
        ->and($claim->status)->toBe(ClaimStatus::Gemeldet)
        ->and($claim->occurred_on->toDateString())->toBe('2026-03-15')
        ->and($claim->estimated_cost)->toBe('1234.50');
});

it('does not mass assign the status', function () {
    $claim = Claim::create([
        ...Claim::factory()->raw(),
        'status' => ClaimStatus::Abgeschlossen,
    ]);

    expect($claim->fresh()->status)->toBe(ClaimStatus::Gemeldet);
});

it('suggests a trade for every claim type', function (ClaimType $type, Trade $trade) {
    expect($type->suggestedTrade())->toBe($trade);
})->with([
    [ClaimType::Leitungswasser, Trade::Sanitaer],
    [ClaimType::Feuer, Trade::Elektro],
    [ClaimType::SturmHagel, Trade::Dach],
    [ClaimType::Elementar, Trade::Trocknung],
    [ClaimType::Glas, Trade::Glaser],
    [ClaimType::Sonstiges, Trade::Allgemein],
]);

it('unassigns the contractor when the contractor is deleted', function () {
    $claim = Claim::factory()->withContractor()->create();

    $claim->contractor->delete();

    expect($claim->fresh()->contractor_id)->toBeNull();
});

it('prevents deleting a property that has claims', function () {
    $claim = Claim::factory()->create();

    $claim->property->delete();
})->throws(QueryException::class);
