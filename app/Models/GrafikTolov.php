<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrafikTolov extends Model
{
    protected $table = 'grafik_tolovlar';

    protected $fillable = [
        'yer_sotuv_id', 'lot_raqami', 'yil', 'oy', 'oy_nomi', 'grafik_summa'
    ];

    protected $casts = [
        'grafik_summa' => 'decimal:2',
        'yil' => 'integer',
        'oy' => 'integer'
    ];

    public function yerSotuv(): BelongsTo
    {
        return $this->belongsTo(YerSotuv::class, 'lot_raqami', 'lot_raqami');
    }
}
