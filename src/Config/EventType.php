<?php

namespace App\Config;

enum EventType: string
{
    case JOB_DATING = 'job_dating';
    case FORMATION = 'formation';
    case ATELIER = 'atelier';
    case REUNION = 'reunion';
    case NON_DEFINI = 'non_defini';
    
    public static function getTypes(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function isValid(string $type): bool
    {
        return in_array($type, self::getTypes(), true);
    }
}