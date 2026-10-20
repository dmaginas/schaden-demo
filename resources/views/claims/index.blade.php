@extends('layouts.app')

@section('title', 'Meldungen')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-2xl font-semibold">Meldungen</h1>

        <form method="GET" action="{{ route('claims.index') }}" class="flex flex-wrap items-end gap-3 text-sm">
            <label class="flex flex-col gap-1">
                <span class="font-medium text-gray-700">Status</span>
                <select name="status" class="rounded-md border border-gray-300 bg-white px-3 py-2">
                    <option value="">Alle</option>
                    @foreach (App\Enums\ClaimStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($selectedStatus === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1">
                <span class="font-medium text-gray-700">Schadenart</span>
                <select name="type" class="rounded-md border border-gray-300 bg-white px-3 py-2">
                    <option value="">Alle</option>
                    @foreach (App\Enums\ClaimType::cases() as $type)
                        <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="rounded-md bg-gray-900 px-3 py-2 font-medium text-white hover:bg-gray-700">Filtern</button>

            @if ($selectedStatus || $selectedType)
                <a href="{{ route('claims.index') }}" class="px-1 py-2 text-gray-500 hover:text-gray-900">Zurücksetzen</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Datum</th>
                    <th class="px-4 py-3">Objekt</th>
                    <th class="px-4 py-3">Schadenart</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Handwerker</th>
                    <th class="px-4 py-3 text-right">Kosten (geschätzt)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($claims as $claim)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('claims.show', $claim) }}" class="font-medium text-gray-900 underline-offset-4 hover:underline">
                                {{ $claim->occurred_on->format('d.m.Y') }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $claim->property->name }}</td>
                        <td class="px-4 py-3">
                            <x-badge :class="$claim->type->badgeClasses()">{{ $claim->type->label() }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :class="$claim->status->badgeClasses()">{{ $claim->status->label() }}</x-badge>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $claim->contractor?->name ?? '–' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ $claim->estimated_cost !== null ? number_format((float) $claim->estimated_cost, 2, ',', '.').' €' : '–' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Keine Meldungen gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $claims->links() }}
@endsection
