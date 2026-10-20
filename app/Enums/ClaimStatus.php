<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case Gemeldet = 'gemeldet';
    case InPruefung = 'in_pruefung';
    case HandwerkerBeauftragt = 'handwerker_beauftragt';
    case Reguliert = 'reguliert';
    case Abgeschlossen = 'abgeschlossen';

    /**
     * Get the German display name.
     */
    public function label(): string
    {
        return match ($this) {
            self::Gemeldet => 'Gemeldet',
            self::InPruefung => 'In Prüfung',
            self::HandwerkerBeauftragt => 'Handwerker beauftragt',
            self::Reguliert => 'Reguliert',
            self::Abgeschlossen => 'Abgeschlossen',
        };
    }

    /**
     * Determine whether a contractor is assigned at this stage.
     */
    public function hasContractor(): bool
    {
        return match ($this) {
            self::Gemeldet, self::InPruefung => false,
            self::HandwerkerBeauftragt, self::Reguliert, self::Abgeschlossen => true,
        };
    }
}
