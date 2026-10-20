@php
    $inputClasses = 'w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <label class="flex flex-col gap-1 sm:col-span-2">
        <span class="text-sm font-medium text-gray-700">Objekt</span>
        <select name="property_id" class="{{ $inputClasses }}">
            <option value="">Bitte wählen</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}" @selected((int) old('property_id', $claim->property_id) === $property->id)>
                    {{ $property->name }} ({{ $property->postal_code }} {{ $property->city }})
                </option>
            @endforeach
        </select>
        @error('property_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium text-gray-700">Gemeldet von</span>
        <input type="text" name="reported_by" value="{{ old('reported_by', $claim->reported_by) }}" class="{{ $inputClasses }}">
        @error('reported_by') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium text-gray-700">Schadenart</span>
        <select name="type" class="{{ $inputClasses }}">
            <option value="">Bitte wählen</option>
            @foreach (App\Enums\ClaimType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $claim->type?->value) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        @error('type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium text-gray-700">Schadendatum</span>
        <input type="date" name="occurred_on" max="{{ today()->toDateString() }}" value="{{ old('occurred_on', $claim->occurred_on?->format('Y-m-d')) }}" class="{{ $inputClasses }}">
        @error('occurred_on') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    <label class="flex flex-col gap-1">
        <span class="text-sm font-medium text-gray-700">Geschätzte Kosten (€)</span>
        <input type="number" name="estimated_cost" step="0.01" min="0" value="{{ old('estimated_cost', $claim->estimated_cost) }}" class="{{ $inputClasses }}">
        @error('estimated_cost') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    <label class="flex flex-col gap-1 sm:col-span-2">
        <span class="text-sm font-medium text-gray-700">Beschreibung</span>
        <textarea name="description" rows="4" class="{{ $inputClasses }}">{{ old('description', $claim->description) }}</textarea>
        @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </label>

    @if ($claim->exists)
        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-700">Status</span>
            <select name="status" class="{{ $inputClasses }}">
                @foreach (App\Enums\ClaimStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $claim->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            @error('status') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-sm font-medium text-gray-700">Handwerker</span>
            <select name="contractor_id" class="{{ $inputClasses }}">
                <option value="">– kein Handwerker –</option>
                @foreach (['Passendes Gewerk ('.$suggestedTrade->label().')' => $suggestedContractors, 'Weitere Handwerker' => $otherContractors] as $groupLabel => $contractors)
                    @if ($contractors->isNotEmpty())
                        <optgroup label="{{ $groupLabel }}">
                            @foreach ($contractors as $contractor)
                                <option value="{{ $contractor->id }}" @selected((int) old('contractor_id', $claim->contractor_id) === $contractor->id)>
                                    {{ $contractor->name }} – {{ $contractor->trade->label() }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
            @error('contractor_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </label>
    @endif
</div>
