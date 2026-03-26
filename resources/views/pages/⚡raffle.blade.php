<?php

    use App\Enum\WinnerPrizeEnum;
    use App\Services\RaffleService;
    use Livewire\Attributes\Computed;
    use Livewire\Component;
    use Livewire\WithPagination;
    use Spatie\LaravelPdf\Facades\Pdf;

    new class extends Component
    {
    use WithPagination;

    public array $winners       = [];
    public array $animationPool = [];
    public WinnerPrizeEnum $prize;

    public string $viewMode = 'grid';
    public bool $scrambleInList;
    public bool $hideTopScramble;

    private function getService(): RaffleService
    {
        return app(RaffleService::class);
    }

    public function mount(): void
    {
        $this->winners = $this->getService()->getExistingWinners($this->prize);
        $this->refreshAnimationPool();

        $this->scrambleInList  = env('RAFFLE_SCRAMBLE_IN_LIST', true);
        $this->hideTopScramble = env('RAFFLE_HIDE_TOP_SCRAMBLE', false);
    }

    #[Computed]
    public function winnersPaginated()
    {
        return $this->getService()->getExistingWinnersPaginated($this->prize);
    }

    public function refreshAnimationPool(): void
    {
        $this->animationPool = $this->getService()->getAnimationPool();
    }

    public function pickWinners(): array
    {
        $target    = $this->prize->targetWinners();
        $batch     = $this->prize->batchSize();
        $remaining = $target - count($this->winners);
        $toDraw    = min($batch, $remaining);

        if ($toDraw <= 0) {
            return [];
        }

        $newWinners    = $this->getService()->drawWinners($this->prize, $toDraw);
        $this->winners = $this->getService()->getExistingWinners($this->prize);

        $this->gotoPage($this->winnersPaginated()->lastPage());

        $lastWinner = array_slice($newWinners, -1)[0];
        $this->dispatch('winners-ready', firstRaffleNumber: $lastWinner['raffle_number']);

        return $newWinners;
    }

    public function resetWinners(): void
    {
        $this->getService()->resetDraw($this->prize);
        $this->winners = [];
        $this->resetPage(); // Reset pagination
        $this->refreshAnimationPool();
    }

    public function exportCsv()
    {
        if (empty($this->winners)) {
            return;
        }

        $prizeName = $this->prize->value;
        $filename  = "raffle_winners_{$prizeName}_" . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Raffle Number', 'Name', 'Email', 'Phone']);

            foreach ($this->winners as $index => $winner) {
                fputcsv($file, [$index + 1, $winner['raffle_number'], $winner['name'], $winner['email'], $winner['phone']]);
            }

            fclose($file);
        }, $filename);
    }

    public function exportPdf()
    {
        if (empty($this->winners)) {
            return;
        }

        $prizeName = $this->prize->label();
        $filename  = "raffle_winners_{$this->prize->value}_" . now()->format('Ymd_His') . '.pdf';

        return Pdf::view('pdf.winners', [
            'winners'   => $this->winners,
            'prizeName' => $prizeName,
        ])
            ->format('a4')
            ->download($filename);
    }

    public function exportExcel()
    {
        if (empty($this->winners)) {
            return;
        }

        $prizeName = $this->prize->value;
        $filename  = "raffle_winners_{$prizeName}_" . now()->format('Ymd_His') . '.xlsx';

        // Wrap Spatie inside Livewire's trusted streamDownload response
        return response()->streamDownload(function () {

            // Create the Spatie writer and point it directly to the output buffer
            $writer = \Spatie\SimpleExcel\SimpleExcelWriter::create('php://output', 'xlsx');

            foreach ($this->winners as $index => $winner) {
                $writer->addRow([
                    'No'            => $index + 1,
                    'Raffle Number' => $winner['raffle_number'],
                    'Name'          => $winner['name'],
                    'Email'         => $winner['email'],
                    'Phone'         => $winner['phone'],
                ]);
            }

        }, $filename);
    }
    };
?>

<div class="relative mt-20 flex min-h-screen flex-col items-center py-12 font-sans" x-cloak x-data="{
    isStreaming: false,
    intervalId: null,
    displayRaffle: 'Ready to draw!',
    pool: @entangle('animationPool'),
    viewMode: @entangle('viewMode'),

    scrambleInList: {{ $scrambleInList ? 'true' : 'false' }},
    hideTopScramble: {{ $hideTopScramble ? 'true' : 'false' }},
    batchSize: {{ $prize->batchSize() }},

    animatedSet: new Set(),

    init() {
        window.addEventListener('load', () => {
            if(!this.hideTopScramble && this.$refs.displayArea) {
                gsap.set(this.$refs.displayArea, { autoAlpha: 1 });
                gsap.from(this.$refs.displayArea, { scale: 0.85, opacity: 0, duration: 0.5, ease: 'back.out(1.7)' });
            }
        });
    },

    startAnimation() {
        if (this.pool.length === 0) return;
        this.isStreaming = true;
        this.displayRaffle = 'Shuffling...';

        if(!this.hideTopScramble && this.$refs.displayArea) {
            gsap.to(this.$refs.displayArea, {
                scale: 1.02,
                boxShadow: '0px 0px 20px rgba(59, 130, 246, 0.7)',
                repeat: -1,
                yoyo: true,
                duration: 0.15,
                ease: 'sine.inOut'
            });
        }

        this.intervalId = setInterval(() => {
            let randomIndex = Math.floor(Math.random() * this.pool.length);
            this.displayRaffle = this.pool[randomIndex];
        }, 40);
    },

    async stopAnimation() {
        if(!this.hideTopScramble && this.$refs.displayArea) {
            gsap.killTweensOf(this.$refs.displayArea);
            gsap.to(this.$refs.displayArea, { scale: 1, boxShadow: 'none', borderColor: '#e5e7eb', duration: 0.15 });
        }

        this.displayRaffle = 'Picking...';

        const handleResult = (event) => {
            clearInterval(this.intervalId);
            this.isStreaming = false;

            const winNum = event.detail.firstRaffleNumber || (event.detail[0] && event.detail[0].firstRaffleNumber);
            this.displayRaffle = winNum || 'Done!';

            if (window.confetti) {
                confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
            }

            window.removeEventListener('winners-ready', handleResult);

            this.$nextTick(() => {
                setTimeout(() => this.revealNewItems(), 50);
            });
        };

        window.addEventListener('winners-ready', handleResult);
        await $wire.pickWinners();
    },

    revealNewItems() {
        let items = document.querySelectorAll('.winner-item');
        let newItems = [];

        items.forEach(item => {
            let raffleNum = item.dataset.raffle;
            if (raffleNum && !this.animatedSet.has(raffleNum)) {
                newItems.push(item);
                this.animatedSet.add(raffleNum);
            }
        });

        if (newItems.length > 0) {
            gsap.set(newItems, { opacity: 0, y: 20, scale: 0.95 });

            gsap.to(newItems, {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.4,
                ease: 'back.out(1.5)'
            });
        }
    }
}">

    <template x-if="!hideTopScramble">
        <div class="gsap-init-hide relative mb-8 flex h-28 w-full max-w-xl items-center justify-center rounded-2xl border-2 border-gray-200 bg-white text-4xl font-black text-gray-800 shadow-lg"
            wire:ignore x-ref="displayArea">
            <div class="flex flex-col items-center">
                <span class="font-mono tracking-wider text-blue-600" x-text="displayRaffle"></span>
            </div>
        </div>
    </template>

    <div class="mb-12 flex h-16 flex-col items-center">
        @if (count($winners) < $prize->targetWinners())
            <button class="rounded-xl bg-blue-600 px-10 py-4 text-lg font-bold text-white shadow-md transition-all hover:-translate-y-1"
                x-show="!isStreaming" @click="startAnimation">
                Draw {{ $prize->batchSize() }} Raffle
            </button>
            <button class="rounded-xl bg-red-600 px-10 py-4 text-lg font-bold text-white shadow-md hover:-translate-y-1"
                x-cloak x-show="isStreaming" @click="stopAnimation">
                Stop & Reveal
            </button>
        @else
            <div class="text-2xl font-black uppercase text-green-500 drop-shadow-sm">Draw Finished!</div>
        @endif
    </div>

@if(count($this->winnersPaginated))
    <div class="w-full max-w-7xl px-6">
        <div class="mb-6 flex items-center justify-between border-b-2 border-gray-200 pb-4">
            <div class="flex items-center space-x-2 rounded-lg bg-gray-200 p-1">
                <button type="button" class="rounded-md px-3 py-1 text-sm font-bold transition-all" @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-white shadow text-blue-600' : 'text-gray-500 cursor-pointer'">Grid</button>
                <button type="button" class="rounded-md px-3 py-1 text-sm font-bold transition-all" @click="viewMode = 'table'"
                    :class="viewMode === 'table' ? 'bg-white shadow text-blue-600' : 'text-gray-500 cursor-pointer'">Table</button>
            </div>

            <div class="flex space-x-3">
                <a class="bg-cedea-blue inline-flex items-center rounded-lg px-4 py-2 text-sm font-bold text-white transition-colors" href="{{ route('welcome') }}" target="_self">
                    Back to Main Menu
                </a>

                @if (count($winners) > 0)
                <button class="cursor-pointer rounded-lg bg-green-100 px-4 py-2 text-sm font-bold text-green-700 transition-colors hover:bg-green-200" wire:click="exportCsv">
                    Export CSV
                </button>

                <button class="rounded-lg cursor-pointer bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 transition-colors hover:bg-emerald-200" wire:click="exportExcel">
            Export Excel
        </button>

                <a class="bg-cedea-blue inline-flex cursor-pointer items-center rounded-lg px-4 py-2 text-sm font-bold text-white transition-colors"
                    href="{{ route('export.pdf', ['prize' => $prize->value]) }}" target="_blank">
                    Export PDF
                </a>

                <button class="rounded-lg bg-red-100 px-4 py-2 cursor-pointer text-sm font-bold text-red-600 transition-colors hover:bg-red-200" wire:click="resetWinners" wire:confirm="Reset all?">
                    Reset
                </button>
                @endif
            </div>
        </div>

        <div x-show="viewMode === 'grid'" x-transition>
            @include('grid-view')
        </div>

        <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm" x-show="viewMode === 'table'" x-transition x-cloak>
            @include('table-view')
        </div>

        <div class="mt-4">
            {{ $this->winnersPaginated->links() }}
        </div>
    </div>
@endif

</div>
