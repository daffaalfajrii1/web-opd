<?php

namespace App\Support;

use App\Models\Pengaduan;
use RuntimeException;

/**
 * Nomor pengaduan dibuat acak dan tidak berurutan.
 *
 * Nomor ini dipakai publik sebagai "kunci" untuk membuka pengaduan, sehingga
 * tidak boleh bisa ditebak. Angka acak 8 digit memberi ~100 juta kemungkinan
 * per tahun, jauh di luar jangkauan enumerasi manual.
 */
class PengaduanNomor
{
    /** Panjang bagian acak (digit). */
    public const RANDOM_DIGITS = 8;

    /** Batas percobaan bila terjadi tabrakan nomor. */
    private const MAX_ATTEMPTS = 10;

    public static function generate(): string
    {
        $year = now()->year;
        $prefix = "PGD-{$year}-";

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $nomor = $prefix.self::randomDigits();

            if (! Pengaduan::query()->where('nomor', $nomor)->exists()) {
                return $nomor;
            }
        }

        throw new RuntimeException('Gagal membuat nomor pengaduan unik. Silakan coba lagi.');
    }

    /**
     * Rapikan input pengguna sebelum dicari. Toleran terhadap spasi sisa
     * hasil salin-tempel dan perbedaan huruf besar/kecil.
     */
    public static function normalize(?string $value): string
    {
        return strtoupper(trim((string) $value));
    }

    /**
     * Angka acak kriptografis. Digit pertama tidak boleh 0 agar panjang
     * nomor konsisten saat disalin pengguna.
     */
    private static function randomDigits(): string
    {
        $first = (string) random_int(1, 9);
        $rest = '';

        for ($i = 1; $i < self::RANDOM_DIGITS; $i++) {
            $rest .= (string) random_int(0, 9);
        }

        return $first.$rest;
    }
}
