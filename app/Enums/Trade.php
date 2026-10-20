<?php

namespace App\Enums;

enum Trade: string
{
    case Sanitaer = 'sanitaer';
    case Elektro = 'elektro';
    case Dach = 'dach';
    case Trocknung = 'trocknung';
    case Glaser = 'glaser';
    case Allgemein = 'allgemein';

    /**
     * Get the German display name.
     */
    public function label(): string
    {
        return match ($this) {
            self::Sanitaer => 'Sanitär',
            self::Elektro => 'Elektro',
            self::Dach => 'Dachdecker',
            self::Trocknung => 'Trocknung',
            self::Glaser => 'Glaserei',
            self::Allgemein => 'Allgemein',
        };
    }
}
