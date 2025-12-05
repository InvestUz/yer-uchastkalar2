<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * YerSotuvQueryService
 *
 * Handles query building and filter application
 * Optimized for performance by separating query concerns
 */
class YerSotuvQueryService
{
    /**
     * Apply base filters (exclude "Бекор қилинган" lots)
     */
    public function applyBaseFilters($query)
    {
        // ✅ ONLY exclude "Бекор қилинган" lots (don't exclude NULL holat)
        return $query->where(function($q) {
            $q->where('holat', '!=', 'Бекор қилинган')
              ->orWhereNull('holat');
        });
    }

    /**
     * Get grafik cutoff date (last month's end)
     */
    public function getGrafikCutoffDate(): string
    {
        return Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Get tuman pattern variations for filtering
     */
    public function getTumanPatterns(string $tumanName): array
    {
        $base = str_replace([' т.', ' тумани'], '', $tumanName);

        $patterns = [
            $base,
            $base . ' т.',
            $base . ' тумани',
        ];

        // Handle о/ҳ variants
        if (mb_strpos($base, 'о') !== false) {
            $altBase = str_replace('о', 'ҳ', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        if (mb_strpos($base, 'ҳ') !== false) {
            $altBase = str_replace('ҳ', 'о', $base);
            $patterns[] = $altBase;
            $patterns[] = $altBase . ' т.';
            $patterns[] = $altBase . ' тумани';
        }

        return array_unique($patterns);
    }

    /**
     * Apply tuman filter to query
     * AUTOMATIC DISTRICT FILTERING: District users only see their own data
     */
    public function applyTumanFilter($query, ?array $tumanPatterns)
    {
        // CRITICAL: Apply automatic district filtering for district users
        if (Auth::check() && Auth::user()->isDistrict()) {
            $userDistrict = Auth::user()->tuman;
            if ($userDistrict) {
                $tumanPatterns = $this->getTumanPatterns($userDistrict);
            }
        }

        if ($tumanPatterns !== null && !empty($tumanPatterns)) {
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
        return $query;
    }

    /**
     * Apply date filters to query
     */
    public function applyDateFilters($query, array $dateFilters)
    {
        if (!empty($dateFilters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $dateFilters['auksion_sana_from']);
        }

        if (!empty($dateFilters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $dateFilters['auksion_sana_to']);
        }

        return $query;
    }
}
