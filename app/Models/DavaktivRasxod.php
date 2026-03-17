<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DavaktivRasxod extends Model
{
    use HasFactory;

    protected $table = 'davaktiv_rasxods';

    protected $fillable = [
        'doc_date',
        'month',
        'doc_number',
        'recipient_name',
        'article',
        'account_number',
        'bank_code',
        'amount',
        'details',
        'by_articles',
            'district',
    ];

    protected $casts = [
        'doc_date' => 'date',
        'amount' => 'float',
    ];
}
