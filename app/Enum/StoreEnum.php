<?php

namespace App\Enum;

enum StoreEnum: string
{
    case INDOMARET = 'indomaret';
    case ALFAMART = 'alfamart';
    case ALFAMIDI = 'alfamidi';
    case FAMILYMART = 'familymart';
    case LAWSON = 'lawson';
    case INDOGROSIR = 'indogrosir';
    case LOTTEMART = 'lotte-mart';
    case LOTTESHOPPING = 'lotte-shopping';
    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INDOMARET => 'Indomaret',
            self::ALFAMART => 'Alfamart',
            self::FAMILYMART => 'FamilyMart',
            self::ALFAMIDI => 'Alfamidi',
            self::LAWSON => 'Lawson',
            self::INDOGROSIR => 'Indogrosir',
            self::LOTTEMART => 'Lotte Mart',
            self::LOTTESHOPPING => 'Lotte Shopping',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::INDOMARET => '#FFD700',
            self::ALFAMART => '#DA251D',
            self::ALFAMIDI => '#4a0300',
            self::FAMILYMART => '#009b3f',
            self::LAWSON => '#006CB7',
            self::INDOGROSIR => '#FFC107',
            self::LOTTEMART => '#007bff',
            self::LOTTESHOPPING => '#00b5fd',
            self::OTHER => '#808080',
        };
    }
}
