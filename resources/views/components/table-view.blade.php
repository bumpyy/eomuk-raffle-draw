<table class="w-full border-collapse text-left">
    <thead class="bg-gray-50 text-xs font-bold uppercase text-gray-600">
        <tr>
            <th class="px-6 py-4">No</th>
            <th class="px-6 py-4">Raffle Number</th>
            <th class="px-6 py-4">Name</th>
            <th class="px-6 py-4">Contact</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 text-black">
        @foreach ($this->winnersPaginated as $index => $winner)
            <tr class="winner-item transition-colors hover:bg-gray-50"
                data-raffle="{{ $winner->submission->raffle_number }}" x-show="!(isStreaming && scrambleInList)"
                wire:key="table-{{ $winner->submission->raffle_number }}">

                <td class="px-6 py-4 font-bold text-blue-600">
                    #{{ ($this->winnersPaginated->currentpage() - 1) * $this->winnersPaginated->perpage() + $index + 1 }}
                </td>
                <td class="px-6 py-4 font-mono font-black">{{ $winner->submission->raffle_number }}</td>
                <td class="px-6 py-4 font-semibold">{{ $winner->submission->user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $winner->submission->user->email }}</td>
            </tr>
        @endforeach

        <template x-if="isStreaming && scrambleInList">
            <template x-for="i in batchSize" :key="'table-placeholder-' + i">
                <tr class="animate-pulse bg-blue-50">
                    <td class="px-6 py-4 text-blue-400">#...</td>
                    <td class="px-6 py-4 font-mono font-bold text-blue-600" x-text="displayRaffle"></td>
                    <td class="px-6 py-4 italic text-blue-400" colspan="2">Selecting winner...</td>
                </tr>
            </template>
        </template>

    </tbody>
</table>
