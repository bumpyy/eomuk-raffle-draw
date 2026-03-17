<?php

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;
use Livewire\Component;

new class extends Component
{
    public array $winners = [];

    public array $animationPool = [];

    public WinnerPrizeEnum $prize;

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

    public function pickAllRemainingWinners(): array
    {
        $target = $this->prize->targetWinners();
        $needed = $target - count($this->winners);

        if ($needed <= 0) {
            return [];
        }

        $newWinners    = $this->getService()->drawWinners($this->prize, $needed);
        $this->winners = array_merge($this->winners, $newWinners);

        $this->dispatch('winners-ready');

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
};
?>

<div class="flex min-h-screen relative flex-col items-center mt-20 py-12 font-sans" x-cloak x-data="{
    isStreaming: false,
    intervalId: null,
    displayRaffle: 'Ready to draw!',
    pool: @entangle('animationPool'),

    init() {
        window.addEventListener('load', () => {
            if (window.gsap) {
                window.gsap.set([this.$refs.header, this.$refs.displayArea], { autoAlpha: 1 });

                window.gsap.from(this.$refs.header, { y: -20, opacity: 0, duration: 0.5, ease: 'back.out(1.5)' });
                window.gsap.from(this.$refs.displayArea, { scale: 0.85, opacity: 0, duration: 0.5, delay: 0.1, ease: 'back.out(1.7)' });
            }
        });
    },

    startAnimation() {
        if (this.pool.length === 0) return;

        this.isStreaming = true;
        this.displayRaffle = 'Shuffling...';

        window.gsap.fromTo(this.$refs.startBtn, { scale: 0.95 }, { scale: 1, duration: 0.2, ease: 'back.out(2)' });

        window.gsap.to(this.$refs.displayArea, {
            scale: 1.02,
            boxShadow: '0px 0px 20px rgba(59, 130, 246, 0.7)',
            borderColor: '#3b82f6',
            repeat: -1,
            yoyo: true,
            duration: 0.15,
            ease: 'sine.inOut'
        });

        this.intervalId = setInterval(() => {
            let randomIndex = Math.floor(Math.random() * this.pool.length);
            this.displayRaffle = this.pool[randomIndex];
        }, 40);
    },

    async stopAnimation() {
        window.gsap.killTweensOf(this.$refs.displayArea);
        window.gsap.to(this.$refs.displayArea, { scale: 1, boxShadow: 'none', borderColor: '#e5e7eb', duration: 0.15 });

        window.gsap.fromTo(this.$refs.stopBtn, { scale: 0.95 }, { scale: 1, duration: 0.2, ease: 'back.out(2)' });

        this.displayRaffle = 'Verifying winners...';

        await $wire.pickAllRemainingWinners();

        clearInterval(this.intervalId);
        this.isStreaming = false;
        this.displayRaffle = 'Draw complete! 🎉';

        if (window.confetti) {
            window.confetti({
                particleCount: 200,
                spread: 120,
                origin: { y: 0.6 },
                colors: ['#3b82f6', '#10b981', '#fbbf24', '#ef4444'],
                zIndex: 100
            });
        }

        this.$nextTick(() => {

            let cards = document.querySelectorAll('.winner-card');

            if (cards.length > 0) {
                var tl = window.gsap.timeline();

                tl.set(cards, { scale: 0, autoAlpha: 1 })
                    .fromTo(cards, {
                        opacity: 0,
                        bottom: 20,
                        scale: 0,
                        rotationX: 0,
                    }, {
                        // immediateRender: false
                        opacity: 1,
                        bottom: 0,
                        scale: 1,
                        // ease: 'back.out(1.7)',
                        stagger: 0.05,
                    });
            }
        });
    }
}">

    <div class="gsap-init-hide mb-10 text-center" x-ref="header">
    </div>

    <div x-ref="displayArea" class="mb-8 relative flex h-28 w-full max-w-xl items-center justify-center rounded-2xl border-2 border-gray-200 bg-white text-4xl font-black text-gray-800 shadow-lg transition-colors" :class="displayRaffle === 'Draw complete! 🎉' ? 'border-green-400 bg-green-50 text-green-600' : ''">
        @if(count($winners) < $prize->targetWinners())
            <div>
                <span class="tracking-wider" x-text="displayRaffle">Ready to draw!</span>
            </div>
            @else
            <div class="text-center">
                <span class="tracking-wider ">Congratulation to the winners!</span>
            </div>
            @endif


            @switch($this->prize)
            @case(WinnerPrizeEnum::TRIP)
            <div class="absolute -top-[110%] right-[20%] w-[55%]">
                <img src="{{ asset('img/plane-prize.png') }}" alt="">
            </div>
            @break

            @case(WinnerPrizeEnum::MONEY)
            <div class="absolute -top-[130%] right-[30%] w-[35%]">
                <img src="{{ asset('img/money-prize.png') }}" alt="">
            </div>
            @break

            @default
            <span class=""></span>
            @endswitch
    </div>

    <div class="mb-12 h-16">
        @if (count($winners) < $prize->targetWinners())
            <button
                class="rounded-xl bg-blue-600 px-10 py-4 text-lg font-bold text-white shadow-md transition-all hover:bg-blue-700"
                x-ref="startBtn" x-show="!isStreaming" @click="startAnimation">
                Draw Raffle ({{ $prize->targetWinners() - count($winners) }})
            </button>

            <button
                class="rounded-xl bg-red-600 px-10 py-4 text-lg font-bold text-white shadow-md transition-all hover:bg-red-700"
                x-cloak x-ref="stopBtn" x-show="isStreaming" @click="stopAnimation">
                Stop & Reveal
            </button>
            @else
            <div class="text-2xl font-black uppercase tracking-widest text-green-500 drop-shadow-sm">
                All {{ $prize->targetWinners() }} Winners Selected!
            </div>
            @endif
    </div>

    <div class="w-full max-w-7xl px-6">

        <div class="mb-6 flex items-end justify-between border-b-2 border-gray-200 pb-2">
            @if (count($winners) > 0)
            <button
                class="flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-bold text-green-700 transition-colors hover:scale-105 hover:bg-green-200 active:scale-95"
                wire:click="exportCsv">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </button>

            <button
                class="flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-600 transition-colors hover:scale-105 hover:bg-red-200 active:scale-95"
                wire:click="resetWinners"
                wire:confirm="Are you sure you want to delete all winners for the {{ $prize->label() }} prize? This action cannot be undone.">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Reset Draw
            </button>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($winners as $index => $winner)
            <x-winner-card :winner="$winner" :index="$index" />
            @endforeach
        </div>
    </div>
</div>
