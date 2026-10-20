<?php

namespace App\Enums;

enum ManagementType: string
{
    case Weg = 'weg';
    case Miet = 'miet';

    /**
     * Get the German display name.
     */
    public function label(): string
    {
        return match ($this) {
            self::Weg => 'WEG-Verwaltung',
            self::Miet => 'Mietverwaltung',
        };
    }
}
