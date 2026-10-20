<?php

namespace App\Enums;

enum ClaimType: string
{
    case Leitungswasser = 'leitungswasser';
    case Feuer = 'feuer';
    case SturmHagel = 'sturm_hagel';
    case Elementar = 'elementar';
    case Glas = 'glas';
    case Sonstiges = 'sonstiges';

    /**
     * Get the German display name.
     */
    public function label(): string
    {
        return match ($this) {
            self::Leitungswasser => 'Leitungswasser',
            self::Feuer => 'Feuer',
            self::SturmHagel => 'Sturm/Hagel',
            self::Elementar => 'Elementar',
            self::Glas => 'Glas',
            self::Sonstiges => 'Sonstiges',
        };
    }

    /**
     * Get the Tailwind classes for the claim type badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Leitungswasser => 'bg-blue-100 text-blue-800',
            self::Feuer => 'bg-red-100 text-red-800',
            self::SturmHagel => 'bg-slate-200 text-slate-800',
            self::Elementar => 'bg-teal-100 text-teal-800',
            self::Glas => 'bg-cyan-100 text-cyan-800',
            self::Sonstiges => 'bg-stone-200 text-stone-700',
        };
    }

    /**
     * Get the trade that usually handles this type of claim.
     */
    public function suggestedTrade(): Trade
    {
        return match ($this) {
            self::Leitungswasser => Trade::Sanitaer,
            self::Feuer => Trade::Elektro,
            self::SturmHagel => Trade::Dach,
            self::Elementar => Trade::Trocknung,
            self::Glas => Trade::Glaser,
            self::Sonstiges => Trade::Allgemein,
        };
    }
}
