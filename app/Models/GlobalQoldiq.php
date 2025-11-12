<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalQoldiq extends Model
{
    protected $table = 'global_qoldiq';

    protected $fillable = [
        'sana',
        'summa',
        'tur',
        'izoh'
    ];

    protected $casts = [
        'sana' => 'date',
        'summa' => 'decimal:2'
    ];

    /**
     * Get the most recent qoldiq before or on a given date
     */
    public static function getQoldiqForDate($date)
    {
        return self::where('sana', '<=', $date)
            ->orderBy('sana', 'desc')
            ->first();
    }

    /**
     * Calculate adjusted fakt total for a date range
     * Applies qoldiq adjustment to fakt payments
     */
    public static function getAdjustedFaktTotal($faktSum, $startDate = null)
    {
        // Get the applicable qoldiq
        $qoldiq = self::getQoldiqForDate($startDate ?? now());

        if (!$qoldiq) {
            return $faktSum;
        }

        // Apply adjustment based on tur
        if ($qoldiq->tur === 'plus') {
            return $faktSum + $qoldiq->summa;
        } else {
            return $faktSum - $qoldiq->summa;
        }
    }
}
