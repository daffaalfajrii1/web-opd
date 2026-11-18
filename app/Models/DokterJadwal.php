<?php

namespace App\Models;

use App\Support\DokterDays;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokterJadwal extends Model
{
    public const DAYS = DokterDays::DAYS;

    protected $fillable = [
        'dokter_id',
        'day_of_week',
        'start_time',
        'end_time',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('day_of_week')
            ->orderBy('start_time');
    }

    public function dayLabel(): string
    {
        return DokterDays::label((int) $this->day_of_week);
    }

    public function formatStartTime(): string
    {
        return $this->formatTime($this->start_time);
    }

    public function formatEndTime(): string
    {
        return $this->formatTime($this->end_time);
    }

    public function formatTimeRange(): string
    {
        return $this->formatStartTime().' – '.$this->formatEndTime();
    }

    private function formatTime(mixed $time): string
    {
        if (! $time) {
            return '—';
        }

        return Carbon::parse($time, 'Asia/Jakarta')->format('H:i');
    }
}