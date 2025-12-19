<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Summary Report - {{ $assessment->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            margin: 3px 0;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 10px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #0d6efd;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 5px 10px;
            font-weight: bold;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .info-value {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }
        .stats-box {
            display: inline-block;
            width: 48%;
            padding: 10px;
            margin-right: 2%;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            vertical-align: top;
        }
        .stats-box h4 {
            color: #0d6efd;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .stats-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stats-box .label {
            color: #666;
            font-size: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #2fb344; color: white; }
        .badge-warning { background-color: #f59f00; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #0054a6; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            padding: 10px;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-after: always;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background-color: #0d6efd;
            text-align: center;
            color: white;
            font-size: 10px;
            line-height: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Assessment Summary Report</h1>
        <p>{{ $assessment->title }}</p>
        <p>Assessment Code: {{ $assessment->code }} | Generated: {{ $generatedAt }}</p>
    </div>

    <!-- Assessment Information -->
    <div class="section">
        <div class="section-title">Assessment Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Company</div>
                <div class="info-value">{{ $assessment->company->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Assessment Type</div>
                <div class="info-value">{{ ucfirst($assessment->assessment_type) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Scope Type</div>
                <div class="info-value">{{ ucfirst($assessment->scope_type) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-success">{{ ucfirst($assessment->status) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Created By</div>
                <div class="info-value">{{ $assessment->createdBy->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Assessment Period</div>
                <div class="info-value">
                    {{ $assessment->assessment_period_start?->format('d M Y') ?? '-' }} 
                    to 
                    {{ $assessment->assessment_period_end?->format('d M Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="section">
        <div class="section-title">Assessment Progress & Statistics</div>
        <div class="stats-box">
            <h4>Total Questions</h4>
            <div class="value">{{ $totalQuestions }}</div>
            <div class="label">Questions in assessment</div>
        </div>
        <div class="stats-box">
            <h4>Completion Rate</h4>
            <div class="value">{{ $completionRate }}%</div>
            <div class="label">{{ $answeredQuestions }} of {{ $totalQuestions }} answered</div>
        </div>
        <div class="stats-box">
            <h4>Evidence Rate</h4>
            <div class="value">{{ $evidenceRate }}%</div>
            <div class="label">{{ $withEvidence }} questions with evidence</div>
        </div>
        <div class="stats-box">
            <h4>Overall Maturity</h4>
            <div class="value">{{ number_format($overallMaturity, 2) }}</div>
            <div class="label">Current maturity level (0-5 scale)</div>
        </div>
    </div>

    <!-- Maturity by Category -->
    <div class="section">
        <div class="section-title">Maturity Level by GAMO Category</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: center;">Current</th>
                    <th style="text-align: center;">Target</th>
                    <th style="text-align: center;">Gap</th>
                    <th style="text-align: center;">Objectives</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maturityByCategory as $category)
                <tr>
                    <td><strong>{{ $category->category }}</strong></td>
                    <td style="text-align: center;">{{ number_format($category->avg_maturity, 2) }}</td>
                    <td style="text-align: center;">{{ number_format($category->avg_target, 2) }}</td>
                    <td style="text-align: center;">
                        @php
                            $gap = $category->avg_target - $category->avg_maturity;
                            $badgeClass = $gap >= 2 ? 'badge-danger' : ($gap >= 1 ? 'badge-warning' : 'badge-success');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ number_format($gap, 2) }}</span>
                    </td>
                    <td style="text-align: center;">{{ $category->objective_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Performing Objectives -->
    @if(isset($topPerforming) && $topPerforming->count() > 0)
    <div class="section">
        <div class="section-title">Top 5 Performing Objectives</div>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Objective Name</th>
                    <th style="text-align: center;">Category</th>
                    <th style="text-align: center;">Maturity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topPerforming as $score)
                <tr>
                    <td><strong>{{ $score->gamoObjective->code }}</strong></td>
                    <td>{{ $score->gamoObjective->name }}</td>
                    <td style="text-align: center;">
                        <span class="badge badge-info">{{ $score->gamoObjective->category }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-success">{{ number_format($score->current_maturity_level, 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Areas Needing Improvement -->
    @if(isset($needsImprovement) && $needsImprovement->count() > 0)
    <div class="section page-break">
        <div class="section-title">Top 5 Areas Needing Improvement</div>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Objective Name</th>
                    <th style="text-align: center;">Category</th>
                    <th style="text-align: center;">Maturity</th>
                    <th style="text-align: center;">Target</th>
                </tr>
            </thead>
            <tbody>
                @foreach($needsImprovement as $score)
                <tr>
                    <td><strong>{{ $score->gamoObjective->code }}</strong></td>
                    <td>{{ $score->gamoObjective->name }}</td>
                    <td style="text-align: center;">
                        <span class="badge badge-info">{{ $score->gamoObjective->category }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-danger">{{ number_format($score->current_maturity_level, 2) }}</span>
                    </td>
                    <td style="text-align: center;">{{ number_format($score->target_maturity_level, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Design Factors -->
    @if($assessment->designFactors && $assessment->designFactors->count() > 0)
    <div class="section">
        <div class="section-title">Selected Design Factors ({{ $assessment->designFactors->count() }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Code</th>
                    <th>Design Factor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessment->designFactors as $factor)
                <tr>
                    <td><strong>{{ $factor->code }}</strong></td>
                    <td>{{ $factor->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by COBIT 2019 Assessment System</p>
        <p>© {{ now()->year }} {{ config('app.name') }} - Confidential Information</p>
    </div>
</body>
</html>
