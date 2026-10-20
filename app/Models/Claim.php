<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use Database\Factories\ClaimFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    /** @use HasFactory<ClaimFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * The status is intentionally excluded so it can only change through
     * deliberate workflow steps, never from submitted request data.
     *
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'contractor_id',
        'reported_by',
        'type',
        'occurred_on',
        'description',
        'estimated_cost',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ClaimStatus::Gemeldet->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ClaimType::class,
            'status' => ClaimStatus::class,
            'occurred_on' => 'date',
            'estimated_cost' => 'decimal:2',
        ];
    }

    /**
     * Filter claims by status and type, ignoring filters that are not set.
     *
     * @param  Builder<Claim>  $query
     */
    #[Scope]
    protected function filter(Builder $query, ?ClaimStatus $status, ?ClaimType $type): void
    {
        $query
            ->when($status, fn (Builder $query, ClaimStatus $status) => $query->where('status', $status))
            ->when($type, fn (Builder $query, ClaimType $type) => $query->where('type', $type));
    }

    /**
     * Get the property the claim belongs to.
     *
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the contractor assigned to the claim.
     *
     * @return BelongsTo<Contractor, $this>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }
}
