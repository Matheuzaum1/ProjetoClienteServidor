<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'imagem',
        'legenda',
        'curtidas',
    ];

    protected function casts(): array
    {
        return [
            'curtidas' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function curtidasRel(): HasMany
    {
        return $this->hasMany(Curtida::class);
    }
}
