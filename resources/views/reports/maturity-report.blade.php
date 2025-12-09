<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maturity Level Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
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
            font-size: 18px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .summary-card {
            border: 2px solid #ddd;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
        .summary-card h4 {
            margin: 0 0 5px 0;
            color: #0066cc;
            font-size: 11px;
        }
        .summary-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .summary-card .sub {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        .category-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .category-header {
            background-color: #0066cc;
            color: white;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .gamo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .gamo-table th {
            background-color: #f0f0f0;
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .gamo-table td {
            padding: 5px 6px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .level-indicator {
            display: inline-block;
            width: 50px;
            text-align: center;
            padding: 3px;
            border-radius: 3px;
            font-weight: bold;
            color: white;
        }
        .level-0 { background-color: #dc3545; }
        .level-1 { background-color: #ffc107; color: #000; }
        .level-2 { background-color: #17a2b8; }
        .level-3 { background-color: #28a745; }
        .level-4 { background-color: #007bff; }
        .level-5 { background-color: #6f42c1; }
        .maturity-bar {
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .maturity-bar-fill {
            height: 100%;
            background: linear-gradient(to right, #ffc107, #28a745, #007bff);
            transition: width 0.3s;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MATURITY LEVEL REPORT</h1>
        <p style="margin: 5px 0; font-size: 10px;">{{ $assessment->title }}</p>
        <p style="margin: 5px 0; font-size: 9px; color: #666;">{{ $company->name }}</p>
    </div>

    <div class="summary-grid">
        @foreach($categoryAverages as $category => $data)
        <div class="summary-card">
            <h4>{{ $category }}</h4>
            <div class="value">{{ number_format($data['average'], 2) }}</div>
            <div class="sub">Target: {{ number_format($data['target'], 2) }}</div>
            <div class="sub">{{ $data['completed'] }}/{{ $data['count'] }} completed</div>
        </div>
        @endforeach
    </div>

    <div style="margin: 20px 0; padding: 15px; background-color: #f0f8ff; border-left: 4px solid #0066cc;">
        <h3 style="margin-top: 0; color: #0066cc;">Overall Assessment Maturity</h3>
        <div style="font-size: 32px; font-weight: bold; color: #0066cc;">
            Level {{ number_format($overallMaturity ?? 0, 2) }}
        </div>
        <div class="maturity-bar" style="margin-top: 10px;">
            <div class="maturity-bar-fill" style="width: {{ ($overallMaturity / 5) * 100 }}%;"></div>
        </div>
    </div>

    @foreach($gamosByCategory as $category => $scores)
    <div class="category-section">
        <div class="category-header">
            {{ $category }} - 
            @if($category == 'EDM')
                Evaluate, Direct and Monitor
            @elseif($category == 'APO')
                Align, Plan and Organize
            @elseif($category == 'BAI')
                Build, Acquire and Implement
            @elseif($category == 'DSS')
                Deliver, Service and Support
            @elseif($category == 'MEA')
                Monitor, Evaluate and Assess
            @endif
        </div>

        <table class="gamo-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Code</th>
                    <th style="width: 42%;">GAMO Objective</th>
                    <th style="width: 12%;">Current Level</th>
                    <th style="width: 12%;">Target Level</th>
                    <th style="width: 10%;">Gap</th>
                    <th style="width: 16%;">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores->sortBy('gamoObjective.objective_order') as $score)
                <tr>
                    <td><strong>{{ $score->gamoObjective->code }}</strong></td>
                    <td>{{ $score->gamoObjective->name }}</td>
                    <td>
                        <span class="level-indicator level-{{ floor($score->current_maturity_level) }}">
                            {{ number_format($score->current_maturity_level, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="level-indicator level-{{ floor($score->target_maturity_level) }}">
                            {{ number_format($score->target_maturity_level, 2) }}
                        </span>
                    </td>
                    <td>{{ number_format($score->target_maturity_level - $score->current_maturity_level, 2) }}</td>
                    <td>
                        <div class="maturity-bar" style="height: 15px;">
                            <div class="maturity-bar-fill" style="width: {{ $score->percentage_complete }}%;"></div>
                        </div>
                        <small>{{ $score->percentage_complete }}%</small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px; font-size: 9px; color: #666;">
            <strong>Category Average:</strong> 
            Current: {{ number_format($categoryAverages[$category]['average'], 2) }} | 
            Target: {{ number_format($categoryAverages[$category]['target'], 2) }} | 
            Completed: {{ $categoryAverages[$category]['completed'] }}/{{ $categoryAverages[$category]['count'] }}
        </div>
    </div>
    @endforeach

    <div style="margin-top: 20px; padding: 10px; background-color: #fff9e6; border: 1px solid #ffc107;">
        <h4 style="margin-top: 0; color: #856404;">Maturity Level Reference</h4>
        <table style="width: 100%; font-size: 9px;">
            <tr>
                <td style="width: 15%;"><span class="level-indicator level-0">Level 0</span></td>
                <td>Incomplete - Process not performed or largely ineffective</td>
            </tr>
            <tr>
                <td><span class="level-indicator level-1">Level 1</span></td>
                <td>Performed - Process is performed and purpose is achieved</td>
            </tr>
            <tr>
                <td><span class="level-indicator level-2">Level 2</span></td>
                <td>Managed - Process is performed and results are managed</td>
            </tr>
            <tr>
                <td><span class="level-indicator level-3">Level 3</span></td>
                <td>Defined - Process is defined, tailored, and results are predictable</td>
            </tr>
            <tr>
                <td><span class="level-indicator level-4">Level 4</span></td>
                <td>Quantitatively Managed - Process is measured and controlled</td>
            </tr>
            <tr>
                <td><span class="level-indicator level-5">Level 5</span></td>
                <td>Optimizing - Process is continually improved and optimized</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
        <p>COBIT 2019 Maturity Report - Confidential</p>
    </div>
</body>
</html>
