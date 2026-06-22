<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sessao extends Model
{
    protected $table = 'sessoes';

    protected $fillable = [
        'user_id',
        'ip',
        'user_agent',
        'logged_in_at',
        'last_activity_at',
        'logged_out_at',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'logged_in_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
