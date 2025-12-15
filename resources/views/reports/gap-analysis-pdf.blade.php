<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gap Analysis Report</title>
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
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #dc3545;
            font-size: 20px;
        }
        .summary-boxes {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .summary-box {
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            color: white;
        }
        .box-critical { background-color: #dc3545; }
        .box-high { background-color: #ffc107; color: #000; }
        .box-medium { background-color: #17a2b8; }
        .box-low { background-color: #28a745; }
        .summary-box h3 {
            margin: 0;
            font-size: 24px;
        }
        .summary-box p {
            margin: 5px 0 0 0;
            font-size: 10px;
        }
        .gap-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .gap-table th {
            background-color: #343a40;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .gap-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .gap-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .priority-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .priority-critical {
            background-color: #dc3545;
            color: white;
        }
        .priority-high {
            background-color: #ffc107;
            color: #000;
        }
        .priority-medium {
            background-color: #17a2b8;
            color: white;
        }
        .priority-low {
            background-color: #28a745;
            color: white;
        }
        .gap-indicator {
            font-size: 14px;
            font-weight: bold;
        }
        .gap-critical { color: #dc3545; }
        .gap-high { color: #ffc107; }
        .gap-medium { color: #17a2b8; }
        .gap-low { color: #28a745; }
        .recommendation-box {
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #0066cc;
        }
        .recommendation-box h3 {
            margin-top: 0;
            color: #0066cc;
        }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>GAP ANALYSIS REPORT</h1>
        <p style="margin: 5px 0; font-size: 11px;">{{ $assessment->title }}</p>
        <p style="margin: 5px 0; font-size: 10px; color: #666;">{{ $company->name }}</p>
    </div>

    <div class="summary-boxes">
        <div class="summary-box box-critical">
            <h3>{{ $criticalGaps }}</h3>
            <p>CRITICAL GAPS</p>
            <p>Gap ≥ 3.0</p>
        </div>
        <div class="summary-box box-high">
            <h3>{{ $highGaps }}</h3>
            <p>HIGH GAPS</p>
            <p>Gap ≥ 2.0</p>
        </div>
        <div class="summary-box box-medium">
            <h3>{{ $mediumGaps }}</h3>
            <p>MEDIUM GAPS</p>
            <p>Gap ≥ 1.0</p>
        </div>
        <div class="summary-box box-low">
            <h3>{{ $lowGaps }}</h3>
            <p>LOW GAPS</p>
            <p>Gap < 1.0</p>
        </div>
    </div>

    <h3 style="color: #dc3545; border-bottom: 2px solid #dc3545; padding-bottom: 5px;">Detailed Gap Analysis</h3>
    <table class="gap-table">
        <thead>
            <tr>
                <th style="width: 8%;">Code</th>
                <th style="width: 32%;">GAMO Objective</th>
                <th style="width: 8%;">Current</th>
                <th style="width: 8%;">Target</th>
                <th style="width: 8%;">Gap</th>
                <th style="width: 12%;">Priority</th>
                <th style="width: 24%;">Effort Estimate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gaps as $item)
            <tr>
                <td><strong>{{ $item['gamo']->code }}</strong></td>
                <td>{{ $item['gamo']->name }}</td>
                <td>{{ number_format($item['current'], 2) }}</td>
                <td>{{ number_format($item['target'], 2) }}</td>
                <td>
                    <span class="gap-indicator 
                        @if($item['gap'] >= 3) gap-critical
                        @elseif($item['gap'] >= 2) gap-high
                        @elseif($item['gap'] >= 1) gap-medium
                        @else gap-low
                        @endif
                    ">
                        {{ number_format($item['gap'], 2) }}
                    </span>
                </td>
                <td>
                    <span class="priority-badge priority-{{ strtolower($item['priority']) }}">
                        {{ $item['priority'] }}
                    </span>
                </td>
                <td>{{ $item['effort'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="recommendation-box">
        <h3>Key Recommendations</h3>
        <ol style="margin: 10px 0; padding-left: 20px;">
            @if($criticalGaps > 0)
            <li><strong>Critical Priority:</strong> Focus immediate attention on {{ $criticalGaps }} GAMO objective(s) with gaps ≥ 3.0. These require urgent action and significant resources.</li>
            @endif
            
            @if($highGaps > 0)
            <li><strong>High Priority:</strong> Plan comprehensive improvement programs for {{ $highGaps }} GAMO objective(s) with gaps ≥ 2.0. Allocate dedicated resources and set clear milestones.</li>
            @endif
            
            @if($mediumGaps > 0)
            <li><strong>Medium Priority:</strong> Schedule incremental improvements for {{ $mediumGaps }} GAMO objective(s) with gaps ≥ 1.0. Integrate into regular improvement cycles.</li>
            @endif
            
            <li><strong>Resource Planning:</strong> Review effort estimates and allocate budget accordingly. Consider external consultants for areas requiring specialized expertise.</li>
            
            <li><strong>Quick Wins:</strong> Identify low-hanging fruits within high-gap areas that can demonstrate early progress and build momentum.</li>
            
            <li><strong>Monitoring:</strong> Establish regular assessment cycles (quarterly recommended) to track progress and adjust improvement plans.</li>
            
            <li><strong>Stakeholder Engagement:</strong> Communicate gaps and improvement plans to senior management. Ensure executive sponsorship for critical initiatives.</li>
        </ol>
    </div>

    <div class="recommendation-box" style="background-color: #fff9e6; border-left-color: #ffc107;">
        <h3 style="color: #856404;">Implementation Strategy</h3>
        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; width: 20%; font-weight: bold;">Phase 1</td>
                <td style="padding: 8px;">Address CRITICAL gaps (0-3 months) - Immediate action required</td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; font-weight: bold;">Phase 2</td>
                <td style="padding: 8px;">Tackle HIGH priority gaps (3-6 months) - Plan comprehensive programs</td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 8px; font-weight: bold;">Phase 3</td>
                <td style="padding: 8px;">Improve MEDIUM gaps (6-12 months) - Incremental enhancements</td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Ongoing</td>
                <td style="padding: 8px;">Maintain and optimize LOW gaps - Continuous improvement</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 20px; padding: 10px; background-color: #e7f3ff; border: 1px solid #0066cc;">
        <h4 style="margin-top: 0; color: #0066cc;">Next Steps</h4>
        <ul style="margin: 5px 0; padding-left: 20px; font-size: 10px;">
            <li>Present findings to senior management and obtain approval for improvement initiatives</li>
            <li>Develop detailed action plans for each high-priority gap</li>
            <li>Assign responsibility and accountability for improvement initiatives</li>
            <li>Establish KPIs and monitoring mechanisms</li>
            <li>Schedule follow-up assessment in 6-12 months to measure progress</li>
        </ul>
    </div>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
        <p>COBIT 2019 Gap Analysis Report - Confidential</p>
    </div>
</body>
</html>
