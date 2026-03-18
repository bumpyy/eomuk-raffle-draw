            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($this->winnersPaginated as $index => $winner)
                    <div class="winner-item winner-card" wire:key="grid-{{ $winner->submission->raffle_number }}">
                        <x-winner-card :winner="$winner" :index="$index" />
                    </div>
                @endforeach
            </div>
