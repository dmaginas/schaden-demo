<?php

namespace Database\Seeders;

use App\Enums\ClaimStatus;
use App\Enums\Trade;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $properties = Property::factory(5)->create();

        $contractorTrades = [...Trade::cases(), Trade::Sanitaer, Trade::Trocknung];
        $contractors = collect($contractorTrades)
            ->map(fn (Trade $trade) => Contractor::factory()->trade($trade)->create());

        foreach (range(0, 19) as $index) {
            $status = ClaimStatus::cases()[$index % count(ClaimStatus::cases())];

            $claim = Claim::factory()
                ->for($properties->random())
                ->status($status)
                ->make();

            if ($status->hasContractor()) {
                $claim->contractor()->associate(
                    $contractors->where('trade', $claim->type->suggestedTrade())->random()
                );
            }

            $claim->save();
        }
    }
}
