<?php

namespace App\Config;

enum ParticipantStatus: string
{
    case EN_RECHERCHE = 'en_recherche';
    case EN_FORMATION = 'en_formation';
    case EN_STAGE = 'en_stage';
    case EN_EMPLOI = 'en_emploi';
    case NON_DEFINI = 'non_defini';

    public static function getStatuses(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::getStatuses(), true);
    }
}
