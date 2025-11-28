<?php

namespace App\Services;

use App\Models\YerSotuv;

class YerSotuvService
{
    protected $queryService;
    protected $dataService;
    protected $calculationService;
    protected $statisticsService;

    public function __construct(
        YerSotuvQueryService $queryService,
        YerSotuvDataService $dataService,
        YerSotuvCalculationService $calculationService,
        YerSotuvStatisticsService $statisticsService
    ) {
        $this->queryService = $queryService;
        $this->dataService = $dataService;
        $this->calculationService = $calculationService;
        $this->statisticsService = $statisticsService;
    }

    // ==================== Query Service Delegations ====================

    public function applyBaseFilters($query)
    {
        return $this->queryService->applyBaseFilters($query);
    }

    public function getGrafikCutoffDate(): string
    {
        return $this->queryService->getGrafikCutoffDate();
    }

    public function getTumanPatterns(string $tumanName): array
    {
        return $this->queryService->getTumanPatterns($tumanName);
    }

    public function applyTumanFilter($query, ?array $tumanPatterns)
    {
        return $this->queryService->applyTumanFilter($query, $tumanPatterns);
    }

    public function applyDateFilters($query, array $dateFilters)
    {
        return $this->queryService->applyDateFilters($query, $dateFilters);
    }

    // ==================== Data Service Delegations ====================

    public function getTumanData(?array $tumanPatterns = null, ?string $tolovTuri = null, array $dateFilters = []): array
    {
        return $this->dataService->getTumanData($tumanPatterns, $tolovTuri, $dateFilters);
    }

    public function getAuksondaTurgan(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getAuksondaTurgan($tumanPatterns, $dateFilters);
    }

    public function getMulkQabulQilmagan(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getMulkQabulQilmagan($tumanPatterns, $dateFilters);
    }

    public function getBolibLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getBolibLotlar($tumanPatterns, $dateFilters);
    }

    public function getBiryolaLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getBiryolaLotlar($tumanPatterns, $dateFilters);
    }

    public function getQoldiqQarzLotlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getQoldiqQarzLotlar($tumanPatterns, $dateFilters);
    }

    public function getListStatistics($query): array
    {
        return $this->dataService->getListStatistics($query);
    }

    public function getNarhiniBolib(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getNarhiniBolib($tumanPatterns, $dateFilters);
    }

    public function getToliqTolanganlar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getToliqTolanganlar($tumanPatterns, $dateFilters);
    }

    public function getNazoratdagilar(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getNazoratdagilar($tumanPatterns, $dateFilters);
    }

    public function getGrafikOrtda(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getGrafikOrtda($tumanPatterns, $dateFilters);
    }

    public function getNazoratdagilarByPeriod(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getNazoratdagilarByPeriod($tumanPatterns, $dateFilters);
    }

    public function getGrafikOrtdaByPeriod(?array $tumanPatterns = null, array $dateFilters = []): array
    {
        return $this->dataService->getGrafikOrtdaByPeriod($tumanPatterns, $dateFilters);
    }

    public function getMonitoringCategoryData(string $category, array $dateFilters = []): array
    {
        return $this->dataService->getMonitoringCategoryData($category, $dateFilters);
    }

    // ==================== Calculation Service Delegations ====================

    public function calculateGrafikTushadigan(?array $tumanPatterns = null, array $dateFilters = [], string $tolovTuri = 'муддатли'): float
    {
        return $this->calculationService->calculateGrafikTushadigan($tumanPatterns, $dateFilters, $tolovTuri);
    }

    public function calculateBiryolaFakt(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        return $this->calculationService->calculateBiryolaFakt($tumanPatterns, $dateFilters, $this->dataService);
    }

    public function calculateBolibTushgan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        return $this->calculationService->calculateBolibTushgan($tumanPatterns, $dateFilters, $this->dataService);
    }

    public function calculateBolibTushadigan(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        return $this->calculationService->calculateBolibTushadigan($tumanPatterns, $dateFilters);
    }

    public function calculateBekorQilinganlarPayments(?array $tumanPatterns = null, array $dateFilters = []): float
    {
        return $this->calculationService->calculateBekorQilinganlarPayments($tumanPatterns, $dateFilters);
    }

    public function calculateFaktByPeriod(?array $tumanPatterns, array $dateFilters, string $tolovTuri): float
    {
        return $this->calculationService->calculateFaktByPeriod($tumanPatterns, $dateFilters, $tolovTuri);
    }

    public function calculateGrafikTushadiganByPeriod(array $dateFilters, string $tolovTuri): float
    {
        return $this->calculationService->calculateGrafikTushadiganByPeriod($dateFilters, $tolovTuri);
    }

    // ==================== Statistics Service Delegations ====================

    public function getDetailedStatistics(array $dateFilters = []): array
    {
        return $this->statisticsService->getDetailedStatistics($dateFilters);
    }

    public function getSvod3Statistics(array $dateFilters = []): array
    {
        return $this->statisticsService->getSvod3Statistics($dateFilters);
    }

    public function calculateTolovTaqqoslash(YerSotuv $yer): array
    {
        return $this->statisticsService->calculateTolovTaqqoslash($yer);
    }

    public function getMonthlyComparativeData(array $filters = []): array
    {
        return $this->statisticsService->getMonthlyComparativeData($filters);
    }

    public function getAvailablePeriods(): array
    {
        return $this->statisticsService->getAvailablePeriods();
    }

    public function logDetailedStatisticsToFile(array $statistics): void
    {
        $this->statisticsService->logDetailedStatisticsToFile($statistics);
    }
}
