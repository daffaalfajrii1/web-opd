<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengaduan extends Model
{
    public const STATUS_BARU = 'baru';

    public const STATUS_DIPROSES = 'diproses';

    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_BARU => 'Baru',
        self::STATUS_DIPROSES => 'Diproses',
        self::STATUS_SELESAI => 'Selesai',
    ];

    protected $fillable = [
        'pengaduan_kategori_id',
        'nomor',
        'nama',
        'no_hp',
        'isi',
        'status',
        'submitted_at',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(PengaduanKategori::class, 'pengaduan_kategori_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(PengaduanFeedback::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    /**
     * Feedback terakhir thread. Dipakai untuk menahan pelapor agar tidak
     * mengirim pesan baru sebelum admin menjawab pesan sebelumnya.
     *
     * Query bersih tanpa orderBy bawaan relasi feedbacks(), karena
     * orderBy relasi menimpa latest('id') dan mengembalikan pesan tertua.
     */
    public function lastFeedback(): ?PengaduanFeedback
    {
        return $this->hasOne(PengaduanFeedback::class)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * True bila pesan terakhir berasal dari pelapor dan belum dijawab admin.
     * Pelapor harus menunggu jawaban admin dulu sebelum mengirim lagi.
     */
    public function awaitingAdminReply(): bool
    {
        $last = $this->lastFeedback();

        return $last !== null && $last->isUser();
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Samakan format nomor HP Indonesia: hanya digit, awalan 62 diubah ke 0.
     * Dipakai untuk verifikasi pelapor tanpa bergantung pada format penulisan.
     */
    public static function normalizePhone(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public function matchesNoHp(?string $value): bool
    {
        $expected = self::normalizePhone($this->no_hp);
        $given = self::normalizePhone($value);

        return $expected !== '' && hash_equals($expected, $given);
    }

    /**
     * Tampilkan nomor HP dalam bentuk tersamar agar tidak terekspos penuh
     * di layar publik (mis. perangkat bersama).
     */
    public function maskedNoHp(): string
    {
        $digits = self::normalizePhone($this->no_hp);
        $length = strlen($digits);

        if ($length === 0) {
            return '—';
        }

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        $visible = $length >= 8 ? 4 : 2;
        $suffix = 2;
        $masked = $length - $visible - $suffix;

        if ($masked < 1) {
            $visible = $length - $suffix - 1;
            $masked = 1;
        }

        return substr($digits, 0, $visible)
            .str_repeat('*', $masked)
            .substr($digits, -$suffix);
    }

    public function getRouteKeyName(): string
    {
        return 'nomor';
    }
}
