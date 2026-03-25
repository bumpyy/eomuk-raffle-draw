<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body
    class="bg-cedea-red bg-size-[40%_auto] relative bg-[url('../assets/asia-pattern-1.png')] bg-fixed bg-center bg-no-repeat">

    {{-- <div class="fixed left-1/2 top-1/2 w-full -translate-x-1/2 -translate-y-1/2">
        <img class="relative -z-10 mx-auto w-1/2" src="{{ asset('img/asia-pattern-1.png') }}">
    </div> --}}

    {{ $slot }}

    @livewireScripts
</body>

</html>
