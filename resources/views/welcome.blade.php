@use('App\Enum\WinnerPrizeEnum')
<x-layouts::app>
    <div
        class="flex min-h-screen flex-col items-center justify-center p-8 font-['Spline_Sans',sans-serif] leading-relaxed">

        <h1 class="mb-12 text-4xl font-bold text-white">
            Selamat Datang Admin
        </h1>

        <div
            class="max-w-450 min-w-200 mx-auto my-[10vh] grid w-fit grid-cols-2 flex-wrap items-start justify-center gap-10">

            <article
                class="max-w-95 flex h-full w-full justify-center rounded-2xl bg-white p-2.5 text-center shadow-[0_30px_30px_-25px_rgba(65,51,183,0.25)]">
                <a class="inline-flex items-center justify-between gap-2 rounded-md bg-white"
                    href="{{ route('raffle.show', [WinnerPrizeEnum::TRIP->value]) }}">
                    <img class="w-full" src="{{ asset('img/plane-prize.png') }}" alt="">
                </a>
            </article>

            <article
                class="max-w-95 w-full rounded-2xl bg-white p-2.5 text-center shadow-[0_30px_30px_-25px_rgba(65,51,183,0.25)]">
                <a class="inline-flex items-center justify-between gap-2 rounded-md bg-white px-14 py-4"
                    href="{{ route('raffle.show', [WinnerPrizeEnum::MONEY->value]) }}">
                    <img src="{{ asset('img/money-prize.png') }}" alt="">
                </a>
            </article>
        </div>
    </div>
</x-layouts::app>

<a href="{{ route('raffle.show', ['prize' => \App\Enum\WinnerPrizeEnum::MONEY->value]) }}">
    Go to Trip Raffle
</a>
