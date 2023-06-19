<?php

namespace App\Enums;

enum LabelLanguage: string
{
    case IT = 'it';
    case EN = 'en';
    case FR = 'fr';

    static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
