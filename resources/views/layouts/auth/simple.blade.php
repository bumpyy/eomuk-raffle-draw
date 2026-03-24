<div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="max-w-lg">
        <picture class="mx-auto w-11/12">
            <source srcset="{{ asset('img/final/auth_header.png') }}" media="(min-width: 1024px)" />
            <img src="{{ asset('img/final/headline-header2x.png') }}" alt="">

        </picture>
    </div>
    <div class="flex w-full max-w-lg flex-col gap-2">
        <a class="flex flex-col items-center gap-2 font-medium" href="{{ route('home') }}">
            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
        </a>
        <div class="relative">
            <div class="-left-11/12 absolute bottom-0 max-md:hidden md:h-[70dvh]">
                <img class="h-full w-auto" src="{{ asset('img/eomuk-cheese-mascot-2.png') }}" alt="">
            </div>
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
            <div class="-right-4/5 absolute bottom-0 max-md:hidden md:h-[70dvh]">
                <img class="h-full w-auto" src="{{ asset('img/eomuk-chili-mascot-2.png') }}" alt="">
            </div>
        </div>
    </div>
</div>
