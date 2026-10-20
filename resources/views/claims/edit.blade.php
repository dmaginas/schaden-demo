@extends('layouts.app')

@section('title', 'Meldung bearbeiten')

@section('content')
    <h1 class="text-2xl font-semibold">Meldung #{{ $claim->id }} bearbeiten</h1>

    @error('claim')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
    @else
        @if ($claim->status === App\Enums\ClaimStatus::Abgeschlossen)
            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="status">
                Diese Meldung ist abgeschlossen. Änderungen können nicht mehr gespeichert werden.
            </div>
        @endif
    @enderror

    <form method="POST" action="{{ route('claims.update', $claim) }}" class="flex flex-col gap-6 rounded-lg border border-gray-200 bg-white p-6">
        @csrf
        @method('PUT')

        @include('claims._form')

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">Änderungen speichern</button>
            <a href="{{ route('claims.show', $claim) }}" class="text-sm text-gray-500 hover:text-gray-900">Abbrechen</a>
        </div>
    </form>
@endsection
