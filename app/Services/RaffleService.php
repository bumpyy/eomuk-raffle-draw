<?php

namespace App\Services;

use App\Enum\SubmissionStatusEnum;
use App\Enum\WinnerPrizeEnum;
use App\Models\Submission;
use App\Models\Winner;

class RaffleService
{
    /**
     * Get users who are excluded from winning anymore.
     */
    public function getDisqualifiedUserIds(): array
    {
        return Winner::with('submission:id,user_id')
            ->get()
            ->pluck('submission.user_id')
            ->filter()
            ->unique()
            ->toArray();
    }

    /**
     * Fetch existing winners for a specific prize.
     */
    public function getExistingWinners(WinnerPrizeEnum $prize): array
    {
        return Winner::with(['submission.user'])
            ->where('prize', $prize->value)
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn ($winner) => $this->formatWinnerData($winner->submission))
            ->toArray();
    }

    public function getExistingWinnersPaginated(WinnerPrizeEnum $prize)
    {
        return Winner::with(['submission.user'])
            ->where('prize', $prize->value)
            ->orderBy('id', 'asc')
            ->paginate(10);
    }

    /**
     * Get fake pool for frontend animation.
     */
    public function getAnimationPool(): array
    {
        return Submission::query()
            ->where('status', SubmissionStatusEnum::ACCEPTED)

            ->whereNotNull('raffle_number')
            ->whereHas('user', function ($query) {
                $query->where('disqualified', false);
            })
            ->whereNotIn('user_id', $this->getDisqualifiedUserIds())
            ->inRandomOrder()
            ->limit(500)
            ->pluck('raffle_number')
            ->toArray();
    }

    /**
     * The core logic to pick new winners and save them.
     */
    public function drawWinners(WinnerPrizeEnum $prize, int $neededAmount): array
    {
        if ($neededAmount <= 0) {
            return [];
        }

        $winningSubmissions = Submission::query()
            ->with('user')
            ->where('status', SubmissionStatusEnum::ACCEPTED)
            ->whereNotNull('raffle_number')
            ->whereHas('user', function ($query) {
                $query->where('disqualified', false);
            })
            ->whereNotIn('user_id', $this->getDisqualifiedUserIds())
            ->inRandomOrder()
            ->limit($neededAmount)
            ->get();

        if ($winningSubmissions->isEmpty()) {
            return [];
        }

        $insertData = [];
        $newWinnersData = [];
        $timestamp = now();

        foreach ($winningSubmissions as $submission) {
            $insertData[] = [
                'submission_id' => $submission->id,
                'prize' => $prize->value,
                'status' => 'pending',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $newWinnersData[] = $this->formatWinnerData($submission);
        }

        Winner::insert($insertData);

        return $newWinnersData;
    }

    /**
     * Delete all winners for a specific prize.
     */
    public function resetDraw(WinnerPrizeEnum $prize): void
    {
        Winner::where('prize', $prize->value)->delete();
    }

    /**
     * Helper to consistently format the output array.
     */
    private function formatWinnerData(Submission $submission): array
    {
        return [
            'raffle_number' => $submission->raffle_number,
            'name' => $submission->user?->name ?? 'Unknown User',
            'email' => $submission->user?->email ?? 'N/A',
            'phone' => $submission->user?->phone ?? 'N/A',
        ];
    }
}
