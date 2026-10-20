@extends('layouts.app')

@section('title', 'Meldung bearbeiten')

@section('content')
    <h1 class="text-2xl font-semibold">Meldung #{{ $claim->id }} bearbeiten</h1>

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
