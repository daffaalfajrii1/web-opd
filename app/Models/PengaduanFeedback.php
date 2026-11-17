<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanFeedback extends Model
{
    public const SENDER_USER = 'user';

    public const SENDER_ADMIN = 'admin';

    protected $table = 'pengaduan_feedbacks';

    protected $fillable = [
        'pengaduan_id',
        'sender',
        'user_id',
        'message',
    ];

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->sender === self::SENDER_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->sender === self::SENDER_USER;
    }
}
