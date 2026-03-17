<?php

use App\Enum\WinnerPrizeEnum;
use App\Services\RaffleService;
use Livewire\Component;

new class extends Component
{
    public array $winners = [];
    public array $animationPool = [];
    public WinnerPrizeEnum $prize;

    // REMOVED: public int $targetWinners = 100;

    private function getService(): RaffleService
    {
        return app(RaffleService::class);
    }

    public function mount(WinnerPrizeEnum $prize): void
    {
        $this->prize = $prize;
        $this->winners = $this->getService()->getExistingWinners($this->prize);
        $this->refreshAnimationPool();
    }

    public function refreshAnimationPool(): void
    {
        $this->animationPool = $this->getService()->getAnimationPool();
    }

    public function pickAllRemainingWinners(): array
    {
        // 1. Get the dynamic limit from the Enum
        $target = $this->prize->targetWinners();

        $needed = $target - count($this->winners);

        if ($needed <= 0) {
            return [];
        }

        $newWinners = $this->getService()->drawWinners($this->prize, $needed);
        $this->winners = array_merge($this->winners, $newWinners);

        return $newWinners;
    }

    public function resetWinners(): void
    {
        $this->getService()->resetDraw($this->prize);
        $this->winners = [];
        $this->refreshAnimationPool();
    }
};
?>

<div
    x-data="{
        isStreaming: false,
        intervalId: null,
        displayRaffle: 'Ready to draw!',
        pool: @entangle('animationPool'),

        startAnimation() {
            if (this.pool.length === 0) return;

            this.isStreaming = true;
            this.displayRaffle = 'Shuffling...';

            gsap.fromTo(this.$refs.startBtn,
                { scale: 0.9 },
                { scale: 1, duration: 0.4, ease: 'elastic.out(1, 0.5)' }
            );

            gsap.to(this.$refs.displayArea, {
                scale: 1.02,
                boxShadow: '0px 0px 20px rgba(59, 130, 246, 0.5)',
                repeat: -1,
                yoyo: true,
                duration: 0.4,
                ease: 'sine.inOut'
            });

            this.intervalId = setInterval(() => {
                let randomIndex = Math.floor(Math.random() * this.pool.length);
                this.displayRaffle = this.pool[randomIndex];
            }, 50);
        },

        async stopAnimation() {
            gsap.killTweensOf(this.$refs.displayArea);
            gsap.to(this.$refs.displayArea, { scale: 1, boxShadow: 'none', duration: 0.2 });

            gsap.fromTo(this.$refs.stopBtn,
                { scale: 0.9 },
                { scale: 1, duration: 0.4, ease: 'back.out(1.5)' }
            );

            this.displayRaffle = 'Verifying winners...';

            await $wire.pickAllRemainingWinners();

            clearInterval(this.intervalId);
            this.isStreaming = false;
            this.displayRaffle = 'Draw complete! 🎉';

            this.$nextTick(() => {
                let cards = document.querySelectorAll('.winner-card:not(.gsap-animated)');

                if (cards.length > 0) {
                    gsap.set(cards, { opacity: 0 });

                    gsap.fromTo(cards,
                        { opacity: 0, y: 40, scale: 0.9 },
                        {
                            opacity: 1,
                            y: 0,
                            scale: 1,
                            stagger: 0.05,
                            duration: 0.5,
                            ease: 'back.out(1.2)'
                        }
                    );

                    cards.forEach(c => c.classList.add('gsap-animated'));
                }
            });
        }
    }"
    class="flex min-h-screen flex-col items-center bg-gray-50 py-12 font-sans"
>

<div class="mb-10 text-center">
        <h1 class="text-5xl font-black text-gray-900 drop-shadow-sm uppercase tracking-tight">
            {{ $prize->label() }} Draw
        </h1>
        <p class="mt-2 text-lg font-bold text-gray-500">
            Targeting {{ $prize->targetWinners() }} Winners
        </p>
    </div>

    <div class="mb-12 h-16">
        @if(count($winners) < $prize->targetWinners())
            <button
                x-ref="startBtn"
                x-show="!isStreaming"
                @click="startAnimation"
                class="rounded-xl bg-blue-600 px-10 py-4 text-lg font-bold text-white shadow-md transition-all hover:bg-blue-700">
                Draw All Remaining ({{ $prize->targetWinners() - count($winners) }})
            </button>
            @else
            <div class="drop-shadow-sm text-2xl font-black uppercase tracking-widest text-green-500">
                All {{ $prize->targetWinners() }} Winners Selected!
            </div>
        @endif
    </div>

    <h3 class="text-2xl font-black text-gray-800">
        Official Winners
        <span class="text-lg font-bold text-gray-400">
            ({{ count($winners) }}/{{ $prize->targetWinners() }})
        </span>
    </h3>
</div>
