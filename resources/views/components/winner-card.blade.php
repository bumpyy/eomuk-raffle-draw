@props(['winner', 'index'])

<div
    class="flex h-full flex-col justify-between rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
    <div class="mb-4 flex items-center justify-between">
        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
            {{ ($this->winnersPaginated->currentpage() - 1) * $this->winnersPaginated->perpage() + $index + 1 }}
        </span>
        <span class="rounded bg-gray-100 px-2 py-1 font-mono text-lg font-black tracking-widest text-gray-800">
            {{ $winner->submission->raffle_number }}
        </span>
    </div>

    <div class="border-t border-gray-100 pt-3">
        <h4 class="truncate text-lg font-bold text-gray-800" title="{{ $winner->submission->user->name }}">
            {{ $winner->submission->user->name }}
        </h4>

        <div class="mt-2 flex flex-col space-y-1 text-sm text-gray-500">
            <div class="flex items-center">
                <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ $winner->submission->user->maskedPhone }}
            </div>
            {{-- <div class="flex items-center">
                <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="truncate" title="{{ $winner->submission->user->email }}">
                    {{ $winner->submission->user->maskedEmail }}</span>
            </div> --}}
            @if ($winner->submission->store_area)
                <div class="flex items-center">
                    <svg class="mr-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M15 22a1 1 0 0 1-1-1v-4a1 1 0 0 1 .445-.832l3-2a1 1 0 0 1 1.11 0l3 2A1 1 0 0 1 22 17v4a1 1 0 0 1-1 1z" />
                        <path d="M18 10a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 .601.2" />
                        <path d="M18 22v-3" />
                        <circle cx="10" cy="10" r="3" />
                    </svg>
                    <span class="truncate" title="{{ $winner->submission->store_area }}">
                        {{ $winner->submission->store_area }}</span>
                </div>
            @endif

        </div>
    </div>
</div>
