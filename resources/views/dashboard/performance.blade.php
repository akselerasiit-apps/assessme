@extends('layouts.app')

@section('title', 'Performance Dashboard - Maturity Analysis')

@php
use Illuminate\Support\Str;

// Color mapping for maturity levels (0-5)
$maturityColors = [
    0 => '#e3e6eb', // Level 0 - Light gray
    1 => '#ffc3ba', // Level 1 - Light red
    2 => '#ffe9b6', // Level 2 - Light orange
    3 => '#fff4b3', // Level 3 - Light yellow
    4 => '#d4f4b3', // Level 4 - Light green
    5 => '#b3f4b3', // Level 5 - Darker green
];

$maturityLabels = [
    0 => 'Level 0',
    1 => 'Level 1',
    2 => 'Level 2',
    3 => 'Level 3',
    4 => 'Level 4',
    5 => 'Level 5',
];
@endphp

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none sticky-top bg-white">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle">Analytics</div>
                    <h2 class="page-title">
                        <i class="ti ti-chart-line me-2"></i>Performance & Maturity
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- GAMO Category Maturity Overview -->
            <div class="row row-deck row-cards mb-3">
                @foreach($categoryMaturity as $category => $data)
                <div class="col-sm-6 col-lg-2.4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $data['average'] }}</div>
                                <div class="ms-auto">
                                    @php
                                        $categoryColor = match($category) {
                                            'EDM' => 'purple',
                                            'APO' => 'blue',
                                            'BAI' => 'green',
                                            'DSS' => 'orange',
                                            'MEA' => 'pink',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $categoryColor }}-lt">{{ $category }}</span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">{{ $category }} Avg Maturity</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $categoryColor }}" style="width: {{ ($data['average'] / 5) * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="text-muted small mt-2">{{ $data['count'] }}/{{ $data['total'] }} assessed</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Charts Row -->
            <div class="row row-deck row-cards mb-3">
                <!-- Category Maturity Comparison -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-bar me-2"></i>GAMO Category Maturity Comparison
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="categoryMaturityChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Company Capability Ranking -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-building me-2"></i>Company Capability Ranking
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="companyCapabilityChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Maturity Trend -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-line me-2"></i>Maturity Trend (Last 30 Days)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="maturityTrendChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Heatmap -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-filter me-2"></i>Maturity Heatmap
                            </h3>
                        </div>

                        <!-- Filters -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('dashboard.performance') }}" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company</label>
                                    <select name="company" class="form-select">
                                        <option value="">All Companies</option>
                                        @foreach($allCompanies as $company)
                                            <option value="{{ $company->id }}" {{ $companyFilter == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Period/Status</label>
                                    <select name="period" class="form-select">
                                        @foreach($periods as $value => $label)
                                            <option value="{{ $value }}" {{ $periodFilter === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-search me-1"></i>Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Heatmap Legend -->
                        <div class="card-body border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Maturity Level Color Guide:</small>
                                </div>
                                @for($i = 0; $i <= 5; $i++)
                                <div class="col-auto">
                                    <div class="d-flex align-items-center">
                                        <div class="color-box me-2" style="width: 24px; height: 24px; background-color: {{ $maturityColors[$i] }}; border: 1px solid #ddd; border-radius: 3px;"></div>
                                        <small class="text-muted">{{ $maturityLabels[$i] }}</small>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Heatmap Table -->
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background-color: #f5f5f5;">
                                        <th style="width: 200px; min-width: 200px;">Company</th>
                                        @foreach($gamoObjectives as $gamo)
                                            <th style="width: 60px; text-align: center; padding: 8px 4px;" title="{{ $gamo->code }}: {{ $gamo->name }}">
                                                <small>{{ $gamo->code }}</small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                    <tr>
                                        <td style="font-weight: 600; vertical-align: middle;">{{ $company->name }}</td>
                                        @foreach($gamoObjectives as $gamo)
                                            @php
                                                $maturityLevel = $heatmapData[$company->id][$gamo->id] ?? null;
                                                $bgColor = $maturityLevel !== null ? $maturityColors[$maturityLevel] : '#ffffff';
                                                $textColor = $maturityLevel !== null && $maturityLevel >= 3 ? '#000' : '#666';
                                            @endphp
                                            <td style="background-color: {{ $bgColor }}; text-align: center; padding: 8px 4px; vertical-align: middle; cursor: pointer;" 
                                                title="@if($maturityLevel !== null){{ $gamo->code }}: Level {{ $maturityLevel }}@else{{ $gamo->code }}: Not assessed@endif">
                                                @if($maturityLevel !== null)
                                                    <strong style="color: {{ $textColor }};">{{ $maturityLevel }}</strong>
                                                @else
                                                    <span style="color: #999;">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Details Table -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-building me-2"></i>Company Capability Summary
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">Rank</th>
                                        <th>Company</th>
                                        <th>Assessments</th>
                                        <th style="width: 200px;">Avg Maturity</th>
                                        <th style="width: 200px;">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companyCapability as $rank => $company)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $rank + 1 }}</span>
                                        </td>
                                        <td class="fw-semibold">{{ $company['name'] }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $company['count'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="h5 mb-0 me-2">{{ $company['avg_maturity'] }}/5</span>
                                                <div class="progress" style="width: 100px; height: 6px;">
                                                    <div class="progress-bar" style="width: {{ ($company['avg_maturity'] / 5) * 100 }}%; background-color: #0d6efd;"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ ($company['avg_maturity'] / 5) * 100 }}%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>

@push('scripts')
<script>
    // GAMO Category Maturity Comparison Chart
    const categoryMaturityChart = new ApexCharts(document.getElementById('categoryMaturityChart'), {
        series: [{
            name: 'Average Maturity Level',
            data: [
                {{ $categoryMaturity['EDM']['average'] ?? 0 }},
                {{ $categoryMaturity['APO']['average'] ?? 0 }},
                {{ $categoryMaturity['BAI']['average'] ?? 0 }},
                {{ $categoryMaturity['DSS']['average'] ?? 0 }},
                {{ $categoryMaturity['MEA']['average'] ?? 0 }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: ['#0d6efd'],
        xaxis: {
            categories: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Maturity Level (0-5)',
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            },
            min: 0,
            max: 5,
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(2);
            }
        }
    });
    categoryMaturityChart.render();

    // Company Capability Ranking Chart (Horizontal Bar)
    const companyData = {!! json_encode($companyCapability) !!};
    const companyNames = companyData.map(c => c.name).slice(0, 8);
    const companyMaturity = companyData.map(c => c.avg_maturity).slice(0, 8);

    const companyCapabilityChart = new ApexCharts(document.getElementById('companyCapabilityChart'), {
        series: [{
            name: 'Average Maturity',
            data: companyMaturity
        }],
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '55%',
                borderRadius: 4,
                dataLabels: {
                    position: 'right'
                }
            }
        },
        colors: ['#2fb344'],
        xaxis: {
            title: {
                text: 'Maturity Level',
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            },
            min: 0,
            max: 5,
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        yaxis: {
            categories: companyNames,
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(2);
            }
        }
    });
    companyCapabilityChart.render();

    // Maturity Trend Chart (Area)
    const trendChart = new ApexCharts(document.getElementById('maturityTrendChart'), {
        series: [{
            name: 'Average Maturity Level',
            data: [{{ implode(',', $maturityTrend) }}]
        }],
        chart: {
            type: 'area',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        colors: ['#0d6efd'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: [{{ implode(',', array_map(fn($d) => "'{$d}'", array_keys($maturityTrend))) }}],
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Maturity Level',
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            },
            min: 0,
            max: 5,
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        dataLabels: {
            enabled: false
        }
    });
    trendChart.render();
</script>
@endpush
