<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Employer = 'employer';
    case Candidate = 'candidate';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}