@use('App\Enum\WinnerPrizeEnum')
<x-layouts::app>
    <div class="min-h-screen bg-[#eaeef6] p-2.5 font-['Spline_Sans',sans-serif] leading-relaxed">
        <div class="max-w-450 mx-auto my-[10vh] flex w-[90%] flex-wrap items-start justify-center gap-10">

            <article
                class="max-w-95 w-full rounded-2xl bg-white p-2.5 text-center shadow-[0_30px_30px_-25px_rgba(65,51,183,0.25)]">
                <div class="relative overflow-hidden rounded-xl bg-[#ffedec] p-5 pt-10 text-[#917072]">
                    <span
                        class="absolute right-0 top-0 flex items-center rounded-l-full bg-[#fbc8be] px-3 py-1 text-xl font-bold text-[#754d42]">
                        <small class="mr-1 text-xs text-[#917072]">Grand Prize</small>
                    </span>
                    <h2 class="mt-3 text-2xl font-extrabold text-[#754d42]">
                        Korea trip Prize
                    </h2>
                </div>
                <div class="mt-4">
                    <a class="inline-flex items-center justify-between gap-2 rounded-md border-2 border-[#6558d3] bg-white px-4 py-2 font-medium text-[#6558d3] transition-colors hover:bg-[#6558d3] hover:text-white focus:bg-[#6558d3] focus:text-white"
                        href="{{ route('raffle.show', [WinnerPrizeEnum::TRIP->value]) }}">
                        Go to Raffle
                    </a>
                </div>
            </article>

            <article
                class="max-w-95 w-full rounded-2xl bg-white p-2.5 text-center shadow-[0_30px_30px_-25px_rgba(65,51,183,0.25)]">
                <div class="relative overflow-hidden rounded-xl bg-[#ffedec] p-5 pt-10 text-[#917072]">
                    {{-- <span
                        class="absolute right-0 top-0 flex items-center rounded-l-full bg-[#fbc8be] px-3 py-1 text-xl font-bold text-[#754d42]">
                        <small class="mr-1 text-xs text-[#917072]">Champion</small>
                    </span> --}}
                    <h2 class="mt-3 text-2xl font-extrabold text-[#754d42]">
                        Money Prize
                    </h2>
                </div>
                <div class="mt-4">
                    <a class="inline-flex items-center justify-between gap-2 rounded-md border-2 border-[#6558d3] bg-white px-4 py-2 font-medium text-[#6558d3] transition-colors hover:bg-[#6558d3] hover:text-white focus:bg-[#6558d3] focus:text-white"
                        href="{{ route('raffle.show', [WinnerPrizeEnum::MONEY->value]) }}">
                        Go to Raffle
                    </a>
                </div>
            </article>
        </div>
    </div>
</x-layouts::app>

<a href="{{ route('raffle.show', ['prize' => \App\Enum\WinnerPrizeEnum::MONEY->value]) }}">
    Go to Trip Raffle
</a>
