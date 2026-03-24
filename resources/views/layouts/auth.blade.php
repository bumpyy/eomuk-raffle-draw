<x-layouts::app>
    <x-layouts::auth.simple :title="$title ?? null">
        {{ $slot }}
    </x-layouts::auth.simple>
</x-layouts::app>
