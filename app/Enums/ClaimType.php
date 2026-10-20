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
