<?php

namespace App\Enum;

enum WinnerPrizeEnum: string
{
    case TRIP = 'trip';
    case MONEY = 'money';

    /**
     * Define the maximum number of winners for each prize
     */
    public function targetWinners(): int
    {
        return match ($this) {
            self::TRIP => 5,
            self::MONEY => 140,
        };
    }

    /**
     * Define the batch of winners for draw
     */
    public function batchSize(): int
    {
        return match ($this) {
            self::TRIP => 1,
            self::MONEY => 10,
        };
    }

    /**
     * Clean display names for the UI
     */
    public function label(): string
    {
        return match ($this) {
            self::TRIP => 'Trip Ke Korea',
            self::MONEY => 'Hadiah Uang Tunai',
        };
    }
}
