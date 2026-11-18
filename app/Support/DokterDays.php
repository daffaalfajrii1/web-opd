<?php

namespace App\Support;

class DokterDays
{
    public const SENIN = 1;

    public const SELASA = 2;

    public const RABU = 3;

    public const KAMIS = 4;

    public const JUMAT = 5;

    public const SABTU = 6;

    public const MINGGU = 7;

    /** @var array<int, string> */
    public const DAYS = [
        self::SENIN => 'Senin',
        self::SELASA => 'Selasa',
        self::RABU => 'Rabu',
        self::KAMIS => 'Kamis',
        self::JUMAT => 'Jumat',
        self::SABTU => 'Sabtu',
        self::MINGGU => 'Minggu',
    ];

    public static function label(int $dayOfWeek): string
    {
        return self::DAYS[$dayOfWeek] ?? '—';
    }

    /** @return list<int> */
    public static function dayNumbers(): array
    {
        return array_keys(self::DAYS);
    }
}