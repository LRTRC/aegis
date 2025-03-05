<?php

namespace App\Config;

enum ProfessionalPosition: string
{
    case CONSEILLER = 'conseiller';
    case FORMATEUR = 'formateur';
    case COORDINATEUR = 'coordinateur';
    case RESPONSABLE = 'responsable';
    case NON_DEFINI = 'non_defini';

    public static function getPositions(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $position): bool
    {
        return in_array($position, self::getPositions(), true);
    }
}
