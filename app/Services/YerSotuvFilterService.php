<?php

namespace App\Services;

use App\Models\YerSotuv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * YerSotuvFilterService
 *
 * Handles all filtering logic for land sales (yer-sotuvlar)
 * Optimized for performance by separating filter concerns
 */
class YerSotuvFilterService
{
    protected $yerSotuvService;
    protected $queryService;

    public function __construct(YerSotuvService $yerSotuvService, YerSotuvQueryService $queryService)
    {
        $this->yerSotuvService = $yerSotuvService;
        $this->queryService = $queryService;
    }

    /**
     * Apply all filters to query
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        // Apply base filters (exclude cancelled and auction lots)
        $this->applyBaseFilters($query);

        // Apply specific filters
        $this->applyLotFilter($query, $filters);
        $this->applySearchFilter($query, $filters);
        $this->applyTumanFilter($query, $filters);
        $this->applyYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters);
        $this->applyPriceRangeFilter($query, $filters);
        $this->applyAreaRangeFilter($query, $filters);
        $this->applySpecialStatusFilters($query, $filters);

        return $query;
    }

    /**
     * Apply base filters (exclude cancelled lots and auction lots)
     */
    private function applyBaseFilters(Builder $query): void
    {
        // ✅ Exclude "Бекор қилинган" lots
        $this->yerSotuvService->applyBaseFilters($query);

        // ✅ EXCLUDE "Аукционда турган" lots from list page
        $query->where(function($q) {
            $q->where('tolov_turi', 'муддатли')
              ->orWhere('tolov_turi', 'муддатли эмас');
        });
    }

    /**
     * Apply lot raqamlari filter
     */
    private function applyLotFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['lot_raqamlari']) && is_array($filters['lot_raqamlari'])) {
            $query->whereIn('lot_raqami', $filters['lot_raqamlari']);
        }
    }

    /**
     * Apply search filter
     */
    private function applySearchFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('lot_raqami', 'like', '%' . $searchTerm . '%')
                    ->orWhere('tuman', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mfy', 'like', '%' . $searchTerm . '%')
                    ->orWhere('manzil', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unikal_raqam', 'like', '%' . $searchTerm . '%')
                    ->orWhere('zona', 'like', '%' . $searchTerm . '%')
                    ->orWhere('golib_nomi', 'like', '%' . $searchTerm . '%')
                    ->orWhere('auksion_golibi', 'like', '%' . $searchTerm . '%')
                    ->orWhere('telefon', 'like', '%' . $searchTerm . '%')
                    ->orWhere('holat', 'like', '%' . $searchTerm . '%')
                    ->orWhere('asos', 'like', '%' . $searchTerm . '%')
                    ->orWhere('shartnoma_raqam', 'like', '%' . $searchTerm . '%')
                    ->orWhere('tolov_turi', 'like', '%' . $searchTerm . '%');
            });
        }
    }

    /**
     * Apply tuman filter
     */
    private function applyTumanFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['tuman'])) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($filters['tuman']);
            $query->where(function ($q) use ($tumanPatterns) {
                foreach ($tumanPatterns as $pattern) {
                    $q->orWhere('tuman', 'like', '%' . $pattern . '%');
                }
            });
        }
    }

    /**
     * Apply year filter
     */
    private function applyYearFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['yil'])) {
            $query->where('yil', $filters['yil']);
        }
    }

    /**
     * Apply date filters
     */
    private function applyDateFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['auksion_sana_from'])) {
            $query->whereDate('auksion_sana', '>=', $filters['auksion_sana_from']);
        }
        if (!empty($filters['auksion_sana_to'])) {
            $query->whereDate('auksion_sana', '<=', $filters['auksion_sana_to']);
        }
        if (!empty($filters['shartnoma_sana_from'])) {
            $query->whereDate('shartnoma_sana', '>=', $filters['shartnoma_sana_from']);
        }
        if (!empty($filters['shartnoma_sana_to'])) {
            $query->whereDate('shartnoma_sana', '<=', $filters['shartnoma_sana_to']);
        }
    }

    /**
     * Apply price range filter
     */
    private function applyPriceRangeFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['narx_from'])) {
            $query->where('sotilgan_narx', '>=', $filters['narx_from']);
        }
        if (!empty($filters['narx_to'])) {
            $query->where('sotilgan_narx', '<=', $filters['narx_to']);
        }
    }

    /**
     * Apply area range filter
     */
    private function applyAreaRangeFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['maydoni_from'])) {
            $query->where('maydoni', '>=', $filters['maydoni_from']);
        }
        if (!empty($filters['maydoni_to'])) {
            $query->where('maydoni', '<=', $filters['maydoni_to']);
        }
    }

    /**
     * Apply special status filters
     */
    private function applySpecialStatusFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['auksonda_turgan']) && $filters['auksonda_turgan'] === 'true') {
            $this->applyAuksondaTurganFilter($query);
        } elseif (!empty($filters['toliq_tolangan']) && $filters['toliq_tolangan'] === 'true') {
            $this->applyToliqTolanganFilter($query);
        } elseif (!empty($filters['nazoratda']) && $filters['nazoratda'] === 'true') {
            $this->applyNazoratdaFilter($query);
        } elseif (!empty($filters['grafik_ortda']) && $filters['grafik_ortda'] === 'true') {
            $this->applyGrafikOrtdaFilter($query);
        } elseif (!empty($filters['qoldiq_qarz']) && $filters['qoldiq_qarz'] === 'true') {
            $this->applyQoldiqQarzFilter($query);
        }
    }

    /**
     * Filter: Auksonda turgan
     */
    private function applyAuksondaTurganFilter(Builder $query): void
    {
        $query->where(function ($q) {
            $q->where('tolov_turi', '!=', 'муддатли')
                ->where('tolov_turi', '!=', 'муддатли эмас')
                ->orWhereNull('tolov_turi');
        });
    }

    /**
     * Filter: Toliq tolangan
     */
    private function applyToliqTolanganFilter(Builder $query): void
    {
        $query->where('tolov_turi', 'муддатли');
        $query->whereRaw('(
            (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
            - (
                COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                + COALESCE(auksion_harajati, 0)
            )
        ) <= 0
        AND (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0)) > 0');
    }

    /**
     * Filter: Nazoratda
     */
    private function applyNazoratdaFilter(Builder $query): void
    {
        $query->where('tolov_turi', 'муддатли');
        $query->whereRaw('(
            (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0))
            - (
                COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                + COALESCE(auksion_harajati, 0)
            )
        ) > 0');
    }

    /**
     * Filter: Grafik ortda (LOT-BY-LOT calculation with auction org exclusions)
     */
    private function applyGrafikOrtdaFilter(Builder $query): void
    {
        $bugun = $this->yerSotuvService->getGrafikCutoffDate();
        $query->where('tolov_turi', 'муддатли');

        // Get ALL муддатли lots first
        $allMuddatliLots = (clone $query)->pluck('lot_raqami')->toArray();

        if (!empty($allMuddatliLots)) {
            $lotsWithDebt = [];

            // LOT-BY-LOT: Calculate debt for each lot
            foreach ($allMuddatliLots as $lotRaqami) {
                $lotGrafikTushadigan = DB::table('grafik_tolovlar')
                    ->where('lot_raqami', $lotRaqami)
                    ->whereRaw('CONCAT(yil, "-", LPAD(oy, 2, "0"), "-01") <= ?', [$bugun])
                    ->sum('grafik_summa');

                $lotGrafikTushgan = DB::table('fakt_tolovlar')
                    ->where('lot_raqami', $lotRaqami)
                    ->where(function($q) {
                        $q->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZ%')
                          ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH AJ%')
                          ->where('tolash_nom', 'NOT LIKE', '%ELEKTRON ONLAYN-AUKSIONLARNI TASHKIL ETISH MARKAZI%')
                          ->orWhereNull('tolash_nom');
                    })
                    ->sum('tolov_summa');

                $lotDebt = $lotGrafikTushadigan - $lotGrafikTushgan;

                if ($lotDebt > 0) {
                    $lotsWithDebt[] = $lotRaqami;
                }
            }

            if (!empty($lotsWithDebt)) {
                $query->whereIn('lot_raqami', $lotsWithDebt);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    /**
     * Filter: Qoldiq qarz (Auksonda turgan mablagh)
     */
    private function applyQoldiqQarzFilter(Builder $query): void
    {
        $query->where('tolov_turi', 'муддатли эмас');

        $query->where(function ($q) {
            $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаган%');
        });

        $query->whereRaw('(
        (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
        >= COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0) - 0.01
    )');
    }
}
