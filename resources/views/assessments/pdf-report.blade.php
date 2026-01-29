<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assessment Report - {{ $assessment->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1e293b;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #206bc4;
        }
        
        .header h1 {
            color: #206bc4;
            font-size: 20pt;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #64748b;
            font-size: 11pt;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #206bc4;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 6px 10px;
            background-color: #f1f5f9;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-value {
            display: table-cell;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 600;
        }
        
        .badge-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-info {
            background-color: #e0f2fe;
            color: #075985;
        }
        
        .badge-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }
        
        .progress-summary {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }
        
        .progress-stats {
            display: table;
            width: 100%;
        }
        
        .progress-stat {
            display: table-cell;
            text-align: center;
            padding: 10px;
            width: 25%;
        }
        
        .progress-stat .value {
            font-size: 24pt;
            font-weight: bold;
            color: #206bc4;
        }
        
        .progress-stat .label {
            color: #64748b;
            font-size: 9pt;
            margin-top: 5px;
        }
        
        .capability-metrics {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .metric-card {
            display: table-cell;
            text-align: center;
            padding: 12px;
            border: 1px solid #e2e8f0;
            width: 33.33%;
        }
        
        .metric-card.target {
            background-color: #dbeafe;
        }
        
        .metric-card.current {
            background-color: #cffafe;
        }
        
        .metric-card.gap {
            background-color: #fed7aa;
        }
        
        .metric-card .metric-label {
            font-size: 9pt;
            color: #64748b;
            margin-bottom: 5px;
        }
        
        .metric-card .metric-value {
            font-size: 20pt;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th {
            background-color: #206bc4;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: 600;
        }
        
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        
        table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .factor-grid {
            display: table;
            width: 100%;
        }
        
        .factor-item {
            display: table-row;
        }
        
        .factor-code {
            display: table-cell;
            width: 15%;
            padding: 8px;
            background-color: #dbeafe;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .factor-content {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .factor-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 3px;
        }
        
        .factor-desc {
            color: #64748b;
            font-size: 9pt;
        }
        
        .gamo-category {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 6px 10px;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        
        .gamo-list {
            margin-left: 0;
            padding-left: 0;
        }
        
        .gamo-item {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }
        
        .gamo-item.not-selected {
            opacity: 0.5;
            background-color: #f8fafc;
        }
        
        .gamo-code-cell {
            display: table-cell;
            width: 12%;
            vertical-align: top;
        }
        
        .gamo-name-cell {
            display: table-cell;
            width: 53%;
            vertical-align: top;
        }
        
        .gamo-level-cell {
            display: table-cell;
            width: 35%;
            text-align: right;
            vertical-align: top;
        }
        
        .level-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
            color: white;
            margin-left: 3px;
        }
        
        .level-2 { background-color: #fb923c; }
        .level-3 { background-color: #fbbf24; }
        .level-4 { background-color: #06b6d4; }
        .level-5 { background-color: #10b981; }
        .level-0 { background-color: #94a3b8; }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px;
            border-top: 1px solid #e2e8f0;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 20mm 15mm;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Assessment Report</h1>
        <div class="subtitle">{{ $assessment->code }} - {{ $assessment->title }}</div>
        <div class="subtitle" style="margin-top: 5px; font-size: 9pt;">Generated on {{ now()->format('d F Y, H:i') }}</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">Basic Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Assessment Code</div>
                <div class="info-value"><span class="badge badge-primary">{{ $assessment->code }}</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Company</div>
                <div class="info-value">{{ $assessment->company->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Assessment Period</div>
                <div class="info-value">
                    {{ $assessment->assessment_period_start?->format('d M Y') ?? 'N/A' }} - 
                    {{ $assessment->assessment_period_end?->format('d M Y') ?? 'N/A' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-{{ $assessment->status == 'completed' ? 'success' : ($assessment->status == 'draft' ? 'secondary' : 'info') }}">
                        {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                    </span>
                </div>
            </div>
            @if($assessment->description)
            <div class="info-row">
                <div class="info-label">Description</div>
                <div class="info-value">{{ $assessment->description }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Created By</div>
                <div class="info-value">{{ $assessment->createdBy?->name ?? 'N/A' }} - {{ $assessment->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Progress Summary -->
    <div class="section">
        <div class="section-title">Progress Summary</div>
        <div class="progress-summary">
            <div class="progress-stats">
                <div class="progress-stat">
                    <div class="value">{{ $progressPercentage }}%</div>
                    <div class="label">Completion</div>
                </div>
                <div class="progress-stat">
                    <div class="value">{{ $answeredGamoCount }}</div>
                    <div class="label">Answered</div>
                </div>
                <div class="progress-stat">
                    <div class="value">{{ $totalGamoCount }}</div>
                    <div class="label">Objectives</div>
                </div>
                <div class="progress-stat">
                    <div class="value">{{ $totalGamoCount - $answeredGamoCount }}</div>
                    <div class="label">Remaining</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Capability Assessment -->
    <div class="section">
        <div class="section-title">Capability Assessment</div>
        <div class="capability-metrics">
            <div class="metric-card target">
                <div class="metric-label">Target Level</div>
                <div class="metric-value">{{ number_format($avgTarget, 2) }}</div>
            </div>
            <div class="metric-card current">
                <div class="metric-label">Current Level</div>
                <div class="metric-value">{{ number_format($avgCurrent, 2) }}</div>
            </div>
            <div class="metric-card gap">
                <div class="metric-label">Gap</div>
                <div class="metric-value" style="color: {{ $avgGap > 0 ? '#059669' : '#dc2626' }};">
                    {{ $avgGap > 0 ? '+' : '' }}{{ number_format($avgGap, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Design Factors -->
    @if($assessment->designFactors->count() > 0)
    <div class="section">
        <div class="section-title">Design Factors ({{ $assessment->designFactors->count() }})</div>
        <div class="factor-grid">
            @foreach($assessment->designFactors as $factor)
            <div class="factor-item">
                <div class="factor-code">{{ $factor->code }}</div>
                <div class="factor-content">
                    <div class="factor-name">{{ $factor->name }}</div>
                    <div class="factor-desc">{{ $factor->description }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="page-break"></div>

    <!-- GAMO Objectives -->
    <div class="section">
        <div class="section-title">GAMO Objectives ({{ count($selectedGamoIds) }}/{{ $allGamos->count() }} selected)</div>
        
        @php
            $categories = $allGamos->groupBy('category');
        @endphp
        
        @foreach($categories as $category => $objectives)
        <div class="gamo-category">
            {{ $category }} - {{ $objectives->whereIn('id', $selectedGamoIds)->count() }}/{{ $objectives->count() }} selected
        </div>
        
        <div class="gamo-list">
            @foreach($objectives as $gamo)
            @php
                $isSelected = in_array($gamo->id, $selectedGamoIds);
                $selectedGamo = $isSelected ? $assessment->gamoObjectives->firstWhere('id', $gamo->id) : null;
                $targetLevel = $selectedGamo?->pivot->target_maturity_level ?? 3;
                $gamoScore = $assessment->gamoScores->where('gamo_objective_id', $gamo->id)->first();
                $resultLevel = $gamoScore?->capability_level ?? $gamoScore?->current_maturity_level ?? 0;
                $resultLevelInt = (int) $resultLevel;
            @endphp
            
            <div class="gamo-item {{ !$isSelected ? 'not-selected' : '' }}">
                <div class="gamo-code-cell">
                    <span class="badge {{ $isSelected ? 'badge-primary' : 'badge-secondary' }}">{{ $gamo->code }}</span>
                </div>
                <div class="gamo-name-cell">
                    <strong>{{ $gamo->name }}</strong>
                    @if(!$isSelected)
                        <span class="badge badge-secondary">Not Selected</span>
                    @endif
                    <br>
                    <span style="color: #64748b; font-size: 8pt;">{{ $gamo->description }}</span>
                </div>
                @if($isSelected)
                <div class="gamo-level-cell">
                    <span style="font-size: 8pt; color: #64748b;">Target / Result</span><br>
                    <span class="level-badge level-{{ $targetLevel }}">Level {{ $targetLevel }}</span>
                    <span style="color: #94a3b8;">/</span>
                    @if($resultLevelInt > 0)
                        <span class="level-badge level-{{ $resultLevelInt }}">Level {{ $resultLevelInt }}</span>
                    @else
                        <span class="badge badge-secondary">-</span>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <!-- Summary per GAMO -->
    @if($summaryData->isNotEmpty())
    <div class="page-break"></div>
    <div class="section">
        <div class="section-title">Summary per GAMO Objective</div>
        <table>
            <thead>
                <tr>
                    <th>GAMO</th>
                    <th>Name</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">Assessed</th>
                    <th style="text-align: center;">Progress</th>
                    <th style="text-align: center;">Target</th>
                    <th style="text-align: center;">Current</th>
                    <th style="text-align: center;">Gap</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaryData as $data)
                <tr>
                    <td><span class="badge badge-primary">{{ $data['code'] }}</span></td>
                    <td>{{ $data['name'] }}</td>
                    <td style="text-align: center;">{{ $data['total_activities'] }}</td>
                    <td style="text-align: center;">{{ $data['assessed_count'] }}</td>
                    <td style="text-align: center;">{{ $data['progress'] }}%</td>
                    <td style="text-align: center;">{{ $data['target_level'] }}</td>
                    <td style="text-align: center;">{{ number_format($data['current_level'], 2) }}</td>
                    <td style="text-align: center; color: {{ $data['gap'] > 0 ? '#059669' : '#dc2626' }};">
                        {{ $data['gap'] > 0 ? '+' : '' }}{{ number_format($data['gap'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        AssessMe - Assessment Report | Generated {{ now()->format('d F Y, H:i') }}
    </div>
</body>
</html>
