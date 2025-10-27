@extends('layouts.app')

@section('title', 'Филтрланган маълумотлар')

@section('content')
<div class="container-fluid py-4">
    
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-funnel-fill me-2"></i>
                    Филтрланган маълумотлар
                </h5>
                <a href="{{ route('yer-sotuvlar.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Статистикага қайтиш
                </a>
            </div>
        </div>
        
        <div class="card-body bg-light">
            <div class="row g-3">
                @if(!empty($filters['tuman']))
                <div class="col-auto">
                    <div class="badge bg-primary fs-6 px-3 py-2">
                        <i class="bi bi-geo-alt-fill me-1"></i>
                        Туман: <strong>{{ $filters['tuman'] }}</strong>
                    </div>
                </div>
                @endif
                
                @if(!empty($filters['tolov_turi']))
                <div class="col-auto">
                    <div class="badge bg-success fs-6 px-3 py-2">
                        <i class="bi bi-cash-coin me-1"></i>
                        Тўлов: <strong>{{ $filters['tolov_turi'] }}</strong>
                    </div>
                </div>
                @endif
                
                @if(!empty($filters['holat']))
                <div class="col-auto">
                    <div class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Ҳолат: <strong>{{ Str::limit($filters['holat'], 50) }}</strong>
                    </div>
                </div>
                @endif
                
                @if(!empty($filters['asos']))
                <div class="col-auto">
                    <div class="badge bg-info fs-6 px-3 py-2">
                        <i class="bi bi-file-text-fill me-1"></i>
                        Асос: <strong>{{ $filters['asos'] }}</strong>
                    </div>
                </div>
                @endif
                
                @if(!empty($filters['yil']))
                <div class="col-auto">
                    <div class="badge bg-secondary fs-6 px-3 py-2">
                        <i class="bi bi-calendar-fill me-1"></i>
                        Йил: <strong>{{ $filters['yil'] }}</strong>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Summary -->
            <div class="mt-3 pt-3 border-top">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-2">
                            <h4 class="text-primary mb-0">{{ $yerlar->total() }}</h4>
                            <small class="text-muted">Жами лотлар</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <h4 class="text-success mb-0">{{ number_format($yerlar->sum('maydoni'), 2) }}</h4>
                            <small class="text-muted">Жами майдон (га)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <h4 class="text-warning mb-0">{{ number_format($yerlar->sum('sotilgan_narx') / 1000000000, 1) }}</h4>
                            <small class="text-muted">Жами сумма (млрд)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2">
                            <h4 class="text-info mb-0">{{ $yerlar->currentPage() }}/{{ $yerlar->lastPage() }}</h4>
                            <small class="text-muted">Саҳифа</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                    <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th class="text-center" style="width: 50px;">№</th>
                            <th style="width: 120px;">Лот рақами</th>
                            <th style="width: 180px;">Туман</th>
                            <th style="width: 150px;">МФЙ</th>
                            <th class="text-end" style="width: 100px;">Майдон (га)</th>
                            <th class="text-end" style="width: 120px;">Бошл. нарх</th>
                            <th class="text-end" style="width: 120px;">Сотилган нарх</th>
                            <th class="text-center" style="width: 120px;">Тўлов тури</th>
                            <th class="text-end" style="width: 130px;">Аукционда турган<br>(млрд сўм)</th>
                            <th style="width: 250px;">Ҳолат</th>
                            <th class="text-center" style="width: 100px;">Аукцион сана</th>
                            <th class="text-center" style="width: 100px;">Амаллар</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($yerlar as $index => $yer)
                        <tr>
                            <td class="text-center align-middle">
                                <strong>{{ $yerlar->firstItem() + $index }}</strong>
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}" class="text-decoration-none fw-bold">
                                    {{ $yer->lot_raqami }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                {{ $yer->tuman }}
                            </td>
                            <td class="align-middle">
                                <small>{{ $yer->mfy }}</small>
                            </td>
                            <td class="text-end align-middle">
                                <span class="badge bg-light text-dark">{{ number_format($yer->maydoni, 2) }}</span>
                            </td>
                            <td class="text-end align-middle">
                                <small class="text-muted">{{ number_format($yer->boshlangich_narx / 1000000, 1) }} млн</small>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-success">{{ number_format($yer->sotilgan_narx / 1000000, 1) }} млн</strong>
                            </td>
                            <td class="text-center align-middle">
                                @if($yer->tolov_turi == 'муддатли эмас')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Бир йўла
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock-fill me-1"></i>
                                        Бўлиб
                                    </span>
                                @endif
                            </td>
                            <td class="text-end align-middle">
                                @if($yer->davaktivda_turgan && $yer->davaktivda_turgan > 0)
                                    <span class="badge bg-info text-dark">
                                        {{ number_format($yer->davaktivda_turgan / 1000000000, 3) }}
                                    </span>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="align-middle">
                                <small class="text-muted">{{ Str::limit($yer->holat, 50) }}</small>
                            </td>
                            <td class="text-center align-middle">
                                @if($yer->auksion_sana)
                                    <small>{{ $yer->auksion_sana->format('d.m.Y') }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <a href="{{ route('yer-sotuvlar.show', $yer->lot_raqami) }}" 
                                   class="btn btn-sm btn-primary" 
                                   data-bs-toggle="tooltip" 
                                   title="Батафсил кўриш">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    <h5>Маълумот топилмади</h5>
                                    <p class="mb-0">Филтр шартларига мос келадиган маълумотлар йўқ</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($yerlar->hasPages())
            <div class="p-3 border-top bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Кўрсатилмоқда: <strong>{{ $yerlar->firstItem() }}</strong> - <strong>{{ $yerlar->lastItem() }}</strong> 
                            / <strong>{{ $yerlar->total() }}</strong>
                        </small>
                    </div>
                    <div>
                        {{ $yerlar->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    a {
        transition: all 0.2s ease;
    }
    
    a:hover {
        opacity: 0.8;
    }
    
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 12px;
    }
</style>

<script>
    // Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection