<?php

namespace App\Support;

use App\Models\Pengaduan;

/**
 * Pelapor membuktikan kepemilikan pengaduan dengan nomor + nomor HP,
 * lalu akses disimpan di session agar tidak perlu mengulang verifikasi.
 */
class PengaduanAccess
{
    public const SESSION_KEY = 'pengaduan.verified';

    private const MAX_ENTRIES = 10;

    public static function markVerified(string $nomor): void
    {
        $verified = session()->get(self::SESSION_KEY, []);
        $verified[$nomor] = now()->getTimestamp();

        // Batasi jumlah entri agar session tidak tumbuh tanpa batas.
        if (count($verified) > self::MAX_ENTRIES) {
            asort($verified);
            $verified = array_slice($verified, -self::MAX_ENTRIES, null, true);
        }

        session()->put(self::SESSION_KEY, $verified);
    }

    public static function isVerified(?string $nomor): bool
    {
        if ($nomor === null || $nomor === '') {
            return false;
        }

        return array_key_exists($nomor, session()->get(self::SESSION_KEY, []));
    }

    public static function phoneMatches(Pengaduan $pengaduan, ?string $input): bool
    {
        return $pengaduan->matchesNoHp($input);
    }
}
