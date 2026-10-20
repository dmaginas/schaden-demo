<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Schadenmeldungen') – {{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
        <header class="border-b border-gray-200 bg-white">
            <nav class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4">
                <a href="{{ route('claims.index') }}" class="text-lg font-semibold">Schadenmeldungen</a>

                <div class="flex items-center gap-4 text-sm font-medium">
                    <a href="{{ route('claims.index') }}" @class([
                        'hover:text-gray-900',
                        'text-gray-900' => request()->routeIs('claims.index'),
                        'text-gray-500' => ! request()->routeIs('claims.index'),
                    ])>Meldungen</a>
                    <a href="{{ route('claims.create') }}" class="rounded-md bg-gray-900 px-3 py-2 text-white hover:bg-gray-700">
                        Neue Meldung
                    </a>
                </div>
            </nav>
        </header>

        <main class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-8">
            @session('success')
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    {{ $value }}
                </div>
            @endsession

            @yield('content')
        </main>
    </body>
</html>
