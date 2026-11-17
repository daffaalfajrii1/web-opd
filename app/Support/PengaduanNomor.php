<?php

namespace App\Support;

use App\Models\Pengaduan;

class PengaduanNomor
{
    public static function generate(): string
    {
        $year = now()->year;
        $prefix = "PGD-{$year}-";

        $last = Pengaduan::query()
            ->where('nomor', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('nomor');

        $next = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return sprintf('PGD-%d-%06d', $year, $next);
    }
}
