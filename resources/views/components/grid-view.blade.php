<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

    @foreach ($this->winnersPaginated as $index => $winner)
        <div class="winner-item" data-raffle="{{ $winner->submission->raffle_number }}"
            wire:key="grid-{{ $winner->submission->raffle_number }}">

            <x-winner-card :winner="$winner" :index="$index" />

        </div>
    @endforeach

    <template x-if="isStreaming && scrambleInList">
        <template x-for="i in batchSize" :key="'grid-placeholder-' + i">
            <div
                class="flex min-h-[140px] animate-pulse flex-col items-center justify-center rounded-xl border-2 border-dashed border-blue-400 bg-blue-50 p-5">
                <span class="mb-2 text-xs font-bold uppercase text-blue-400">Shuffling...</span>
                <span class="font-mono text-xl font-bold text-blue-600" x-text="displayRaffle"></span>
            </div>
        </template>
    </template>

</div>
