<?php

namespace App\Config;

enum EventStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    
    public static function getStatuses(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    public static function isValid(string $status): bool
    {
        return in_array($status, self::getStatuses(), true);
    }
    
}