<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assessment Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #0066cc;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .score-summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f8ff;
            border-left: 4px solid #0066cc;
        }
        .score-summary h3 {
            margin-top: 0;
            color: #0066cc;
        }
        .maturity-box {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            font-size: 24px;
            font-weight: bold;
            border-radius: 5px;
            margin: 10px 0;
        }
        .gamo-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .gamo-table th {
            background-color: #0066cc;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .gamo-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .gamo-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #ffc107; color: #000; }
        .status-in_progress { background-color: #17a2b8; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-reviewed { background-color: #6f42c1; color: #fff; }
        .status-approved { background-color: #007bff; color: #fff; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ASSESSMENT SUMMARY REPORT</h1>
        <p>COBIT 2019 IT Governance Assessment</p>
    </div>

    <div class="info-section">
        <h3 style="color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 5px;">Assessment Information</h3>
        <table class="info-table">
            <tr>
                <td>Assessment Code</td>
                <td><strong>{{ $assessment->code }}</strong></td>
            </tr>
            <tr>
                <td>Title</td>
                <td>{{ $assessment->title }}</td>
            </tr>
            <tr>
                <td>Company</td>
                <td>{{ $company->name }}</td>
            </tr>
            <tr>
                <td>Industry</td>
                <td>{{ $company->industry ?? '-' }}</td>
            </tr>
            <tr>
                <td>Company Size</td>
                <td>{{ strtoupper($company->size) }}</td>
            </tr>
            <tr>
                <td>Assessment Type</td>
                <td>{{ ucfirst($assessment->assessment_type) }}</td>
            </tr>
            <tr>
                <td>Scope Type</td>
                <td>{{ ucfirst($assessment->scope_type) }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span class="status-badge status-{{ $assessment->status }}">{{ strtoupper($assessment->status) }}</span></td>
            </tr>
            <tr>
                <td>Start Date</td>
                <td>{{ $assessment->start_date ? \Carbon\Carbon::parse($assessment->start_date)->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>End Date</td>
                <td>{{ $assessment->end_date ? \Carbon\Carbon::parse($assessment->end_date)->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Progress</td>
                <td>{{ $assessment->progress_percentage }}%</td>
            </tr>
            <tr>
                <td>Created By</td>
                <td>{{ $assessment->createdBy?->name ?? 'N/A' }}</td>
            </tr>
            @if($assessment->reviewed_by)
            <tr>
                <td>Reviewed By</td>
                <td>{{ $assessment->reviewedBy?->name ?? 'N/A' }}</td>
            </tr>
            @endif
            @if($assessment->approved_by)
            <tr>
                <td>Approved By</td>
                <td>{{ $assessment->approvedBy->name }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="score-summary">
        <h3>Overall Maturity Assessment</h3>
        <p>Based on {{ $completedGamos }} out of {{ $totalGamos }} completed GAMO objectives:</p>
        <div class="maturity-box">
            Level {{ number_format($overallMaturity, 2) }}
        </div>
        <p style="margin-top: 10px; font-size: 10px;">
            @if($overallMaturity < 1)
                <strong>Incomplete Process:</strong> Process not performed or largely ineffective
            @elseif($overallMaturity < 2)
                <strong>Performed Process:</strong> Process is performed and purpose is achieved
            @elseif($overallMaturity < 3)
                <strong>Managed Process:</strong> Process is performed and results are managed
            @elseif($overallMaturity < 4)
                <strong>Defined Process:</strong> Process is defined, tailored, and results are predictable
            @elseif($overallMaturity < 5)
                <strong>Quantitatively Managed:</strong> Process is measured and controlled
            @else
                <strong>Optimizing Process:</strong> Process is continually improved and optimized
            @endif
        </p>
    </div>

    @if($assessment->designFactors->count() > 0)
    <div class="info-section">
        <h3 style="color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 5px;">Design Factors Selection</h3>
        <table class="gamo-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Code</th>
                    <th style="width: 35%;">Design Factor</th>
                    <th style="width: 50%;">Selected Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessment->designFactors as $df)
                <tr>
                    <td>{{ $df->designFactor->code }}</td>
                    <td>{{ $df->designFactor->name }}</td>
                    <td>{{ $df->selected_value ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($assessment->gamoScores->count() > 0)
    <div class="info-section page-break">
        <h3 style="color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 5px;">GAMO Objectives Maturity Levels</h3>
        <table class="gamo-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Code</th>
                    <th style="width: 40%;">Objective</th>
                    <th style="width: 10%;">Category</th>
                    <th style="width: 13%;">Current</th>
                    <th style="width: 13%;">Target</th>
                    <th style="width: 14%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessment->gamoScores->sortBy('gamoObjective.code') as $score)
                <tr>
                    <td>{{ $score->gamoObjective->code }}</td>
                    <td>{{ $score->gamoObjective->name }}</td>
                    <td><strong>{{ $score->gamoObjective->category }}</strong></td>
                    <td>{{ number_format($score->current_maturity_level, 2) }}</td>
                    <td>{{ number_format($score->target_maturity_level, 2) }}</td>
                    <td><span class="status-badge status-{{ $score->status }}">{{ strtoupper($score->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
        <p>COBIT 2019 Assessment Report - Confidential</p>
    </div>
</body>
</html>
