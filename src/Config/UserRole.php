<?php

namespace App\Config;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_PARTICIPANT = 'ROLE_PARTICIPANT';
    case ROLE_PROFESSIONAL = 'ROLE_PROFESSIONAL';

    /**
     * Retourne la liste des rôles disponibles
     */
    public static function getRoles(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Vérifie si un rôle est valide
     */
    public static function isValid(string $role): bool
    {
        return in_array($role, self::getRoles(), true);
    }
}
