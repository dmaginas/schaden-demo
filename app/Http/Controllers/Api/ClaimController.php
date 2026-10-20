<?php

namespace App\Http\Controllers\Api;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClaimController extends Controller
{
    /**
     * List claims, optionally filtered by status and type.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $claims = Claim::query()
            ->with(['property', 'contractor'])
            ->filter(
                $request->enum('status', ClaimStatus::class),
                $request->enum('type', ClaimType::class),
            )
            ->latest('occurred_on')
            ->paginate(15)
            ->withQueryString();

        return ClaimResource::collection($claims);
    }

    /**
     * Show a single claim with its property and contractor.
     */
    public function show(Claim $claim): ClaimResource
    {
        return new ClaimResource($claim->load(['property', 'contractor']));
    }
}
