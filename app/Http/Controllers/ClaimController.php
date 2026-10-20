<?php

namespace App\Http\Controllers;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Models\Contractor;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaimController extends Controller
{
    /**
     * Display a listing of the claims.
     */
    public function index(Request $request): View
    {
        $status = $request->enum('status', ClaimStatus::class);
        $type = $request->enum('type', ClaimType::class);

        $claims = Claim::query()
            ->with(['property', 'contractor'])
            ->filter($status, $type)
            ->latest('occurred_on')
            ->paginate(15)
            ->withQueryString();

        return view('claims.index', [
            'claims' => $claims,
            'selectedStatus' => $status,
            'selectedType' => $type,
        ]);
    }

    /**
     * Show the form for creating a new claim.
     */
    public function create(): View
    {
        return view('claims.create', [
            'claim' => new Claim,
            'properties' => Property::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created claim.
     */
    public function store(StoreClaimRequest $request): RedirectResponse
    {
        $claim = new Claim($request->validated());
        $claim->status = ClaimStatus::Gemeldet;
        $claim->save();

        return to_route('claims.show', $claim)
            ->with('success', 'Schadenmeldung wurde angelegt.');
    }

    /**
     * Display the specified claim.
     */
    public function show(Claim $claim): View
    {
        $claim->load(['property', 'contractor']);

        return view('claims.show', ['claim' => $claim]);
    }

    /**
     * Show the form for editing the specified claim.
     */
    public function edit(Claim $claim): View
    {
        $suggestedTrade = $claim->type->suggestedTrade();

        [$suggestedContractors, $otherContractors] = Contractor::orderBy('name')
            ->get()
            ->partition(fn (Contractor $contractor) => $contractor->trade === $suggestedTrade);

        return view('claims.edit', [
            'claim' => $claim,
            'properties' => Property::orderBy('name')->get(),
            'suggestedTrade' => $suggestedTrade,
            'suggestedContractors' => $suggestedContractors,
            'otherContractors' => $otherContractors,
        ]);
    }

    /**
     * Update the specified claim.
     */
    public function update(UpdateClaimRequest $request, Claim $claim): RedirectResponse
    {
        $claim->fill($request->validated());
        $claim->status = ClaimStatus::from($request->validated('status'));
        $claim->save();

        return to_route('claims.show', $claim)
            ->with('success', 'Schadenmeldung wurde aktualisiert.');
    }
}
