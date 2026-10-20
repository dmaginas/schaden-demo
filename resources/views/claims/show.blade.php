@extends('layouts.app')

@section('title', 'Meldung #'.$claim->id)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-2">
            <a href="{{ route('claims.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Zurück zur Liste</a>
            <h1 class="text-2xl font-semibold">Meldung #{{ $claim->id }}</h1>
            <div class="flex gap-2">
                <x-badge :class="$claim->type->badgeClasses()">{{ $claim->type->label() }}</x-badge>
                <x-badge :class="$claim->status->badgeClasses()">{{ $claim->status->label() }}</x-badge>
            </div>
        </div>

        <a href="{{ route('claims.edit', $claim) }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">Bearbeiten</a>
    </div>

    <section class="rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold">Schaden</h2>
        <dl class="grid gap-4 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-gray-500">Schadendatum</dt>
                <dd class="font-medium">{{ $claim->occurred_on->format('d.m.Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Gemeldet von</dt>
                <dd class="font-medium">{{ $claim->reported_by }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Geschätzte Kosten</dt>
                <dd class="font-medium">
                    {{ $claim->estimated_cost !== null ? number_format((float) $claim->estimated_cost, 2, ',', '.').' €' : '–' }}
                </dd>
            </div>
            <div class="sm:col-span-3">
                <dt class="text-gray-500">Beschreibung</dt>
                <dd class="whitespace-pre-line">{{ $claim->description }}</dd>
            </div>
        </dl>
    </section>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold">Objekt</h2>
            <dl class="flex flex-col gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">Name</dt>
                    <dd class="font-medium">{{ $claim->property->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Adresse</dt>
                    <dd>{{ $claim->property->street }}, {{ $claim->property->postal_code }} {{ $claim->property->city }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Verwaltungsart</dt>
                    <dd>{{ $claim->property->management_type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Versicherung</dt>
                    <dd>
                        @if ($claim->property->insurer)
                            {{ $claim->property->insurer }} (Police {{ $claim->property->policy_number ?? '–' }})
                        @else
                            Keine Angabe
                        @endif
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold">Handwerker</h2>
            @if ($claim->contractor)
                <dl class="flex flex-col gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Firma</dt>
                        <dd class="font-medium">{{ $claim->contractor->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Gewerk</dt>
                        <dd>{{ $claim->contractor->trade->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">E-Mail</dt>
                        <dd><a href="mailto:{{ $claim->contractor->email }}" class="underline-offset-4 hover:underline">{{ $claim->contractor->email }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Telefon</dt>
                        <dd>{{ $claim->contractor->phone ?? '–' }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-500">
                    Noch kein Handwerker beauftragt. Vorgeschlagenes Gewerk: {{ $claim->type->suggestedTrade()->label() }}.
                </p>
            @endif
        </section>
    </div>
@endsection
