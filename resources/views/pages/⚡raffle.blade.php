<?php

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;
use Livewire\Component;
use Spatie\LaravelPdf\Facades\Pdf;

new class extends Component
{
    public array $winners = [];
    public array $animationPool = [];
    public WinnerPrizeEnum $prize;

    public string $viewMode = 'grid';

    private function getService(): RaffleService
    {
        return app(RaffleService::class);
    }

    public function mount(): void
    {
        $this->winners = $this->getService()->getExistingWinners($this->prize);
        $this->refreshAnimationPool();
    }

    public function refreshAnimationPool(): void
    {
        $this->animationPool = $this->getService()->getAnimationPool();
    }

    public function pickWinners(): array
    {
        $target = $this->prize->targetWinners();
        $batch = $this->prize->batchSize();
        $remaining = $target - count($this->winners);
        $toDraw = min($batch, $remaining);

        if ($toDraw <= 0) return [];

        $newWinners = $this->getService()->drawWinners($this->prize, $toDraw);
        $this->winners = array_merge($this->winners, $newWinners);

        // Pass the first raffle number safely to Alpine
        $this->dispatch('winners-ready', firstRaffleNumber: $newWinners[0]['raffle_number']);

        return $newWinners;
    }

    public function resetWinners(): void
    {
        $this->getService()->resetDraw($this->prize);
        $this->winners = [];
        $this->refreshAnimationPool();
    }

    public function exportCsv()
    {
        if (empty($this->winners)) return;

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
        if (empty($this->winners)) return;

        $prizeName = $this->prize->label();
        $filename  = "raffle_winners_{$this->prize->value}_" . now()->format('Ymd_His') . '.pdf';

        return Pdf::view('pdf.winners', [
            'winners' => $this->winners,
            'prizeName' => $prizeName,
        ])
            ->format('a4')
            ->download($filename);
    }
};
?>

<div class="flex min-h-screen relative flex-col items-center mt-20 py-12 font-sans"
    x-cloak
    x-data="{
        isStreaming: false,
        intervalId: null,
        displayRaffle: 'Ready to draw!',
        pool: @entangle('animationPool'),
        viewMode: @entangle('viewMode'),

        init() {
            window.addEventListener('load', () => {
                gsap.set(this.$refs.displayArea, { autoAlpha: 1 });
                gsap.from(this.$refs.displayArea, { scale: 0.85, opacity: 0, duration: 0.5, ease: 'back.out(1.7)' });
            });
        },

        startAnimation() {
            if (this.pool.length === 0) return;
            this.isStreaming = true;
            this.displayRaffle = 'Shuffling...';

            gsap.to(this.$refs.displayArea, {
                scale: 1.02,
                boxShadow: '0px 0px 20px rgba(59, 130, 246, 0.7)',
                repeat: -1, yoyo: true, duration: 0.15, ease: 'sine.inOut'
            });

            this.intervalId = setInterval(() => {
                let randomIndex = Math.floor(Math.random() * this.pool.length);
                this.displayRaffle = this.pool[randomIndex];
            }, 40);
        },

        async stopAnimation() {
            gsap.killTweensOf(this.$refs.displayArea);
            gsap.to(this.$refs.displayArea, { scale: 1, boxShadow: 'none', borderColor: '#e5e7eb', duration: 0.15 });

            this.displayRaffle = 'Picking...';

            // Use an arrow function so 'this' remains bound to Alpine
            const handleResult = (event) => {
                clearInterval(this.intervalId);
                this.isStreaming = false;

                // Livewire 3 safely injects the named argument here. We fall back safely just in case.
                const winNum = event.detail.firstRaffleNumber || (event.detail[0] && event.detail[0].firstRaffleNumber);

                // Lock the main display to the first winner's number
                this.displayRaffle = winNum || 'Done!';

                if (window.confetti) {
                    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
                }

                window.removeEventListener('winners-ready', handleResult);

                // Trigger the stagger reveal
                this.$nextTick(() => this.playStagger());
            };

            // Attach listener BEFORE firing the Livewire request to prevent race conditions
            window.addEventListener('winners-ready', handleResult);

            await $wire.pickWinners();
        },

        playStagger() {
            let items = document.querySelectorAll('.winner-item:not(.gsap-animated)');

            if (items.length > 0) {
                gsap.set(items, { opacity: 0, y: 20, scale: 0.95 });

                gsap.to(items, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.4,
                    ease: 'back.out(1.7)',
                    stagger: 0.08,
                    onComplete: () => {
                        items.forEach(i => i.classList.add('gsap-animated'));
                    }
                });
            }
        }
    }">

    <div wire:ignore x-ref="displayArea" class="gsap-init-hide mb-8 relative flex h-28 w-full max-w-xl items-center justify-center rounded-2xl border-2 border-gray-200 bg-white text-4xl font-black text-gray-800 shadow-lg">

        <div class="flex flex-col items-center">
            <span class="tracking-wider font-mono text-blue-600" x-text="displayRaffle">Ready to draw!</span>
        </div>

    </div>

    <div class="mb-12 h-16 flex flex-col items-center">
        @if (count($winners) < $prize->targetWinners())
            <button class="rounded-xl bg-blue-600 px-10 py-4 text-lg font-bold text-white shadow-md transition-all hover:-translate-y-1"
                x-show="!isStreaming" @click="startAnimation">
                Draw {{ $prize->batchSize() }} Raffle
            </button>
            <button x-cloak class="rounded-xl bg-red-600 px-10 py-4 text-lg font-bold text-white shadow-md hover:-translate-y-1"
                x-show="isStreaming" @click="stopAnimation">
                Stop & Reveal
            </button>
            @else
            <div class="text-2xl font-black text-green-500 uppercase drop-shadow-sm">Draw Finished!</div>
            @endif
    </div>

    <div class="w-full max-w-7xl px-6">
        <div class="mb-6 flex items-center justify-between border-b-2 border-gray-200 pb-4">
            <div class="flex items-center space-x-2 bg-gray-200 p-1 rounded-lg">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="px-3 py-1 rounded-md text-sm font-bold transition-all">Grid</button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="px-3 py-1 rounded-md text-sm font-bold transition-all">Table</button>
            </div>

            <div class="flex space-x-3">
                @if(count($winners) > 0)
                <button wire:click="exportCsv" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-bold text-sm hover:bg-green-200 transition-colors">
                    Export CSV
                </button>

                <a href="{{ route('export.pdf', ['prize' => $prize->value]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-100 text-cedea-blue rounded-lg font-bold text-sm hover:bg-purple-200 transition-colors">
                    Export PDF
                </a>

                <button wire:click="resetWinners" wire:confirm="Reset all?" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg font-bold text-sm hover:bg-red-200 transition-colors">
                    Reset
                </button>
                @endif
            </div>
        </div>

        <div x-show="viewMode === 'grid'" x-transition>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($winners as $index => $winner)
                <div wire:key="grid-{{ $winner['raffle_number'] }}" class="winner-item winner-card">
                    <x-winner-card :winner="$winner" :index="$index" />
                </div>
                @endforeach
            </div>
        </div>

        <div x-show="viewMode === 'table'" x-transition x-cloak class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Raffle Number</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Contact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($winners as $index => $winner)
                    <tr wire:key="table-{{ $winner['raffle_number'] }}" class="winner-item hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-blue-600">#{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-mono font-black">{{ $winner['raffle_number'] }}</td>
                        <td class="px-6 py-4 font-semibold">{{ $winner['name'] }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $winner['email'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
