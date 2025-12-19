<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gap Analysis Report - {{ $assessment->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; line-height: 1.6; color: #333; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { background-color: #f0f0f0; padding: 8px 10px; font-size: 14px; font-weight: bold; border-left: 4px solid #dc3545; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        table th { background-color: #dc3545; color: white; font-weight: bold; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-critical { background-color: #dc3545; color: white; }
        .badge-high { background-color: #f59f00; color: white; }
        .badge-medium { background-color: #f59f00; color: white; }
        .badge-success { background-color: #2fb344; color: white; }
        .stats-box { display: inline-block; width: 23%; padding: 10px; margin-right: 2%; margin-bottom: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; vertical-align: top; }
        .stats-box .value { font-size: 24px; font-weight: bold; color: #333; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; padding: 10px; border-top: 1px solid #ddd; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gap Analysis Report</h1>
        <p>{{ $assessment->title }} | Code: {{ $assessment->code }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <div class="section-title">Gap Summary</div>
        <div class="stats-box">
            <div class="value" style="color: #dc3545;">{{ $criticalGaps ?? 0 }}</div>
            <div>Critical Gaps (≥2)</div>
        </div>
        <div class="stats-box">
            <div class="value" style="color: #f59f00;">{{ $highGaps ?? 0 }}</div>
            <div>High Gaps (1-2)</div>
        </div>
        <div class="stats-box">
            <div class="value" style="color: #f59f00;">{{ $mediumGaps ?? 0 }}</div>
            <div>Medium Gaps (&lt;1)</div>
        </div>
        <div class="stats-box">
            <div class="value" style="color: #2fb344;">{{ isset($gamoGaps) ? $gamoGaps->filter(fn($g) => $g->gap <= 0)->count() : 0 }}</div>
            <div>On Target</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detailed Gap Analysis by GAMO Objective</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Code</th>
                    <th>Objective Name</th>
                    <th style="width: 10%; text-align: center;">Category</th>
                    <th style="width: 10%; text-align: center;">Current</th>
                    <th style="width: 10%; text-align: center;">Target</th>
                    <th style="width: 10%; text-align: center;">Gap</th>
                    <th style="width: 12%; text-align: center;">Priority</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($gamoGaps))
                    @foreach($gamoGaps as $gap)
                    <tr>
                        <td><strong>{{ $gap->code }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($gap->name, 50) }}</td>
                        <td style="text-align: center;">{{ $gap->category }}</td>
                        <td style="text-align: center;">{{ number_format($gap->current_maturity_level, 2) }}</td>
                        <td style="text-align: center;">{{ number_format($gap->target_maturity_level, 2) }}</td>
                        <td style="text-align: center;">{{ number_format($gap->gap, 2) }}</td>
                        <td style="text-align: center;">
                            @if($gap->gap >= 2)
                                <span class="badge badge-critical">CRITICAL</span>
                            @elseif($gap->gap >= 1)
                                <span class="badge badge-high">HIGH</span>
                            @elseif($gap->gap > 0)
                                <span class="badge badge-medium">MEDIUM</span>
                            @else
                                <span class="badge badge-success">ON TARGET</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="7" style="text-align: center;">No gap data available</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Gap Analysis Report - {{ config('app.name') }} | Confidential Information</p>
    </div>
</body>
</html>
