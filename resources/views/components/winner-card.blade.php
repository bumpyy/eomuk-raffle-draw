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
                {{ $winner->submission->user->phone }}
            </div>
            <div class="flex items-center">
                <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="truncate" title="{{ $winner->submission->user->email }}">
                    {{ $winner->submission->user->email }}</span>
            </div>
        </div>
    </div>
</div>
