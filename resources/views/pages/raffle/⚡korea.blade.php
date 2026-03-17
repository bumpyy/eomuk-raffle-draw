<?php

use App\Models\Submission;
use Livewire\Component;

new class extends Component {
    public ?Submission $winner = null;
    public string $buttonMessage = 'Raffle!';

    public function raffle(): void
    {
        $applicants = Submission::query()->inRandomOrder()->get();

        $applicants->each(function (Submission $applicant) {
            $this->stream('winner', $applicant->nickname, true);
            usleep(10000);
        });

        $this->winner = $applicants->last();
        $this->buttonMessage = 'Try again!';
    }
};
?>

<div class="flex h-screen flex-col items-center justify-center">

    <div class="m-10 h-20 rounded" wire:stream="winner">
        {{ $winner?->nickname ?? 'Click in raffle button' }}
    </div>

    <button class="rounded border-2 border-blue-400 p-2 hover:bg-blue-400" wire:click="raffle">
        {{ $buttonMessage }}
    </button>

</div>
