<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpaymuLog extends Model
{
    /**
     * Nama tabel.
     */
    protected $table = 'pwp_ipaymu';

    /**
     * Primary key sebenarnya adalah kolom id.
     */
    protected $primaryKey = 'id';

    /**
     * ID menggunakan integer.
     */
    protected $keyType = 'int';

    /**
     * ID auto increment.
     */
    public $incrementing = true;

    /**
     * Gunakan created_at dan updated_at.
     */
    public $timestamps = true;

    /**
     * Kolom yang boleh diisi.
     */
    protected $fillable = [
        'trx_id',
        'user_id',
        'streamer_id',
        'streamer_bonus',
        'amount',
        'money',
        'status',
        'status_code',
        'sid',
        'reference_id',
    ];

    /**
     * Relasi ke user.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'ID'
        );
    }
}