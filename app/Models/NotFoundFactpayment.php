<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotFoundFactpayment extends Model
{
    use HasFactory;

    protected $table = 'not_found_factpayments';

    protected $fillable = [
        'row_number',
        'source_file',
        'lot_raqami',
        'raw_lot_value',
        'tolov_sana',
        'hujjat_raqam',
        'tolash_nom',
        'tolash_hisob',
        'tolash_inn',
        'tolov_summa',
        'detali',
        'reason',
        'raw_row',
    ];

    protected $casts = [
        'tolov_sana' => 'date',
        'tolov_summa' => 'float',
    ];
}
