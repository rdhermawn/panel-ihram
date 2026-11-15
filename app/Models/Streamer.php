<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streamer extends Model
{
    protected $table = 'pwp_streamers';

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'bonus_percent',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'bonus_percent' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'ID');
    }

    public static function findByCode(?string $code): ?self
    {
        $code = strtolower(trim((string) $code));

        if ($code === '') {
            return null;
        }

        return static::whereRaw('LOWER(code) = ?', [$code])->first();
    }
}
