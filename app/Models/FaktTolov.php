<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaktTolov extends Model
{
    protected $table = 'fakt_tolovlar';

    protected $fillable = [
        'lot_raqami', 'tolov_sana', 'hujjat_raqam', 'tolash_nom',
        'tolash_inn', 'tolash_hisob', 'tolov_summa', 'detali'
    ];

    protected $casts = [
        'tolov_summa' => 'decimal:2',
        'tolov_sana' => 'date'
    ];

    public function yerSotuv(): BelongsTo
    {
        return $this->belongsTo(YerSotuv::class, 'lot_raqami', 'lot_raqami');
    }
}
