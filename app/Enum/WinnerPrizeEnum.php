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
            self::MONEY => 100,
        };
    }

    /**
     * Clean display names for the UI
     */
    public function label(): string
    {
        return match ($this) {
            self::TRIP => 'Grand Trip to Korea',
            self::MONEY => 'Cash Prize Pool',
        };
    }
}
