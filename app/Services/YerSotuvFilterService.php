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
        $this->applyBaseFilters($query, $filters);

        // Apply specific filters
        $this->applyLotFilter($query, $filters);
        $this->applySearchFilter($query, $filters);
        $this->applyTumanFilter($query, $filters);
        $this->applyYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters);
        $this->applyPriceRangeFilter($query, $filters);
        $this->applyAreaRangeFilter($query, $filters);
        $this->applyHolatFilter($query, $filters);
        $this->applyAsosFilter($query, $filters);
        $this->applySpecialStatusFilters($query, $filters);

        return $query;
    }

    /**
     * Apply base filters (exclude cancelled lots and auction lots)
     */
    private function applyBaseFilters(Builder $query, array $filters = []): void
    {
        // ✅ Check if include_all parameter is set to skip cancelled lot exclusion
        // OR if include_bekor is set to only skip cancelled exclusion (but keep auksonda exclusion)
        if ((empty($filters['include_all']) || $filters['include_all'] !== 'true') &&
            (empty($filters['include_bekor']) || $filters['include_bekor'] !== 'true')) {
            // ✅ Exclude "Бекор қилинган" lots ONLY if both include_all and include_bekor are not true
            $this->yerSotuvService->applyBaseFilters($query);
            \Log::info('FilterService: Applied applyBaseFilters (exclude Бекор қилинган)');
        } else {
            \Log::info('FilterService: Skipped excluding Бекор қилинган (include_bekor or include_all is true)');
        }

        // ✅ EXCLUDE "Аукционда турган" lots from list page ONLY if not explicitly filtering for them
        // This allows specific tolov_turi filters to work correctly
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
     * AUTOMATIC DISTRICT FILTERING: Uses QueryService which checks user role
     */
    private function applyTumanFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['tuman'])) {
            $tumanPatterns = $this->yerSotuvService->getTumanPatterns($filters['tuman']);
            // Use QueryService which has automatic district filtering
            $this->queryService->applyTumanFilter($query, $tumanPatterns);
        } else {
            // No tuman filter specified - apply automatic district filtering
            $this->queryService->applyTumanFilter($query, null);
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
     * Apply holat (status) filter
     */
    private function applyHolatFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['holat'])) {
            // Use LIKE for partial match
            $query->where('holat', 'like', '%' . $filters['holat'] . '%');
        }
    }

    /**
     * Apply asos (basis) filter
     */
    private function applyAsosFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['asos'])) {
            $query->where('asos', $filters['asos']);
        }
    }

    /**
     * Apply special status filters
     */
    private function applySpecialStatusFilters(Builder $query, array $filters): void
    {
        // Track if special filter is applied
        $hasSpecialFilter = false;

        if (!empty($filters['auksonda_turgan']) && $filters['auksonda_turgan'] === 'true') {
            $this->applyAuksondaTurganFilter($query);
            $hasSpecialFilter = true;
        } elseif (!empty($filters['toliq_tolangan']) && $filters['toliq_tolangan'] === 'true') {
            $this->applyToliqTolanganFilter($query);
            $hasSpecialFilter = true;
        } elseif (!empty($filters['nazoratda']) && $filters['nazoratda'] === 'true') {
            $this->applyNazoratdaFilter($query);
            $hasSpecialFilter = true;
        } elseif (!empty($filters['grafik_ortda']) && $filters['grafik_ortda'] === 'true') {
            // grafik_ortda can include BOTH муддатли (with overdue grafik) AND муддатли эмас (with qoldiq qarz)
            $this->applyGrafikOrtdaFilter($query, $filters);
            $hasSpecialFilter = true;
        } elseif (!empty($filters['qoldiq_qarz']) && $filters['qoldiq_qarz'] === 'true') {
            $this->applyQoldiqQarzFilter($query);
            $hasSpecialFilter = true;
        }

        // Apply tolov_turi filter if specified and no special filter overrides it
        if (!empty($filters['tolov_turi']) && !$hasSpecialFilter) {
            $query->where('tolov_turi', $filters['tolov_turi']);
            \Log::info('FilterService: Applied tolov_turi filter', ['tolov_turi' => $filters['tolov_turi']]);
        } elseif (empty($filters['tolov_turi']) && !$hasSpecialFilter) {
            // ✅ Check if include_auksonda is true OR include_all is true
            $includeAuksonda = (!empty($filters['include_auksonda']) && $filters['include_auksonda'] === 'true')
                            || (!empty($filters['include_all']) && $filters['include_all'] === 'true');

            // ✅ Check if include_bekor is true - need special handling for cancelled lots
            $includeBekor = !empty($filters['include_bekor']) && $filters['include_bekor'] === 'true';

            if (!$includeAuksonda) {
                if ($includeBekor) {
                    // ✅ include_bekor=true: Include муддатли + муддатли эмас + ALL Бекор қилинган (regardless of tolov_turi)
                    // This matches how yigma calculates: biryola + bolib + all bekor
                    $query->where(function($q) {
                        $q->whereIn('tolov_turi', ['муддатли', 'муддатли эмас'])
                          ->orWhere('holat', 'Бекор қилинган');
                    });
                    \Log::info('FilterService: Applied include_bekor filter (муддатли + муддатли эмас + ALL Бекор қилинган)');
                } else {
                    // Default: exclude auksonda turgan lots if no tolov_turi specified
                    // AND neither include_auksonda nor include_all is set
                    $query->where(function($q) {
                        $q->where('tolov_turi', 'муддатли')
                          ->orWhere('tolov_turi', 'муддатли эмас');
                    });
                    \Log::info('FilterService: Applied DEFAULT filter (exclude Аукционда турган - only муддатли + муддатли эмас)');
                }
            } else {
                \Log::info('FilterService: Including Аукционда турган lots (include_auksonda or include_all is true)');
            }
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
     * If tolov_turi is specified, only filter that type
     * If no tolov_turi, include BOTH муддатли (overdue grafik) AND муддатли эмас (qoldiq qarz)
     */
    private function applyGrafikOrtdaFilter(Builder $query, array $filters): void
    {
        $bugun = $this->yerSotuvService->getGrafikCutoffDate();
        $tolovTuri = $filters['tolov_turi'] ?? null;

        if ($tolovTuri === 'муддатли') {
            // Only муддатли with overdue grafik
            $this->applyMuddatliGrafikOrtda($query, $bugun);
        } elseif ($tolovTuri === 'муддатли эмас') {
            // Only муддатли эмас with qoldiq qarz
            $this->applyQoldiqQarzFilter($query);
        } else {
            // BOTH types: муддатли (grafik ortda) + муддатли эмас (qoldiq qarz)
            $muddatliLots = $this->getMuddatliGrafikOrtdaLots($bugun);
            $muddatliEmasLots = $this->getQoldiqQarzLots();

            $allOverdueLots = array_merge($muddatliLots, $muddatliEmasLots);

            if (!empty($allOverdueLots)) {
                $query->whereIn('lot_raqami', $allOverdueLots);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
    }

    /**
     * Apply муддатли grafik ortda filter
     */
    private function applyMuddatliGrafikOrtda(Builder $query, string $bugun): void
    {
        $query->where('tolov_turi', 'муддатли');

        // Get ALL муддатли lots first
        $allMuddatliLots = (clone $query)->pluck('lot_raqami')->toArray();

        if (!empty($allMuddatliLots)) {
            $lotsWithDebt = $this->getMuddatliGrafikOrtdaLots($bugun, $allMuddatliLots);

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
     * Get муддатли lots with grafik ortda
     */
    private function getMuddatliGrafikOrtdaLots(string $bugun, ?array $lotRaqamlari = null): array
    {
        if ($lotRaqamlari === null) {
            // Get all муддатли lots
            $lotRaqamlari = DB::table('yer_sotuvlar')
                ->where('tolov_turi', 'муддатли')
                ->where('holat', '!=', 'Бекор қилинган')
                ->whereNotNull('holat')
                ->pluck('lot_raqami')
                ->toArray();
        }

        if (empty($lotRaqamlari)) {
            return [];
        }

        $lotsWithDebt = [];

        // LOT-BY-LOT: Calculate debt for each lot
        foreach ($lotRaqamlari as $lotRaqami) {
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

        return $lotsWithDebt;
    }

    /**
     * Get муддатли эмас lots with qoldiq qarz
     * ✅ Only includes lots where Қолдиқ маблағ > 0 (excluding fully paid lots)
     */
    private function getQoldiqQarzLots(): array
    {
        return DB::table('yer_sotuvlar')
            ->where('tolov_turi', 'муддатли эмас')
            ->whereNotNull('holat')
            ->where(function ($q) {
                $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                    ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                    ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                    ->orWhere('holat', 'like', '%Бекор қилинған%');
            })
            // ✅ Only include lots where Қолдиқ маблағ > 0
            // Formula: expected - paid > 0.01
            ->whereRaw('(
                (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
                - COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
                > 0.01
            )')
            ->pluck('lot_raqami')
            ->toArray();
    }

    /**
     * Filter: Qoldiq qarz (Auksonda turgan mablagh)
     * ✅ Only shows lots where Қолдиқ маблағ > 0 (excluding fully paid lots)
     */
    private function applyQoldiqQarzFilter(Builder $query): void
    {
        $query->where('tolov_turi', 'муддатли эмас');

        // ✅ Include only qoldiq_qarz statuses (no Лот якунланди)
        $query->where(function ($q) {
            $q->where('holat', 'like', '%Ishtirokchi roziligini kutish jarayonida%')
                ->orWhere('holat', 'like', '%G`olib shartnoma imzolashga rozilik bildirdi%')
                ->orWhere('holat', 'like', '%Ишл. кечикт. туф. мулкни қабул қил. тасдиқланмаған%')
                ->orWhere('holat', 'like', '%Бекор қилинған%');
        });

        // ✅ Only include lots where Қолдиқ маблағ > 0
        // Formula: expected - paid > 0.01
        $query->whereRaw('(
            (COALESCE(golib_tolagan, 0) + COALESCE(shartnoma_summasi, 0) - COALESCE(auksion_harajati, 0))
            - COALESCE((SELECT SUM(tolov_summa) FROM fakt_tolovlar WHERE fakt_tolovlar.lot_raqami = yer_sotuvlar.lot_raqami), 0)
            > 0.01
        )');
    }
}
