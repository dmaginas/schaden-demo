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
     * Get the Tailwind classes for the status badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Gemeldet => 'bg-sky-100 text-sky-800',
            self::InPruefung => 'bg-amber-100 text-amber-800',
            self::HandwerkerBeauftragt => 'bg-violet-100 text-violet-800',
            self::Reguliert => 'bg-emerald-100 text-emerald-800',
            self::Abgeschlossen => 'bg-gray-200 text-gray-700',
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
