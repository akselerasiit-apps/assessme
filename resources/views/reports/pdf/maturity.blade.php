<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maturity Level Report - {{ $assessment->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; line-height: 1.6; color: #333; }
        .header { background-color: #0054a6; color: white; padding: 20px; text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .section { margin-bottom: 20px; page-break-inside: avoid; }
        .section-title { background-color: #f0f0f0; padding: 8px 10px; font-size: 14px; font-weight: bold; border-left: 4px solid #0054a6; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        table th { background-color: #0054a6; color: white; font-weight: bold; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 35%; padding: 5px 10px; font-weight: bold; background-color: #f5f5f5; border: 1px solid #ddd; }
        .info-value { display: table-cell; padding: 5px 10px; border: 1px solid #ddd; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-success { background-color: #2fb344; color: white; }
        .badge-warning { background-color: #f59f00; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #0054a6; color: white; }
        .maturity-bar { width: 100%; height: 20px; background-color: #e9ecef; position: relative; border-radius: 3px; }
        .maturity-fill { height: 100%; background: linear-gradient(to right, #dc3545, #f59f00, #2fb344); position: absolute; border-radius: 3px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; padding: 10px; border-top: 1px solid #ddd; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Maturity Level Assessment Report</h1>
        <p>{{ $assessment->title }} | {{ $assessment->code }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <div class="section-title">Company & Assessment Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Company</div>
                <div class="info-value">{{ $assessment->company->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Overall Maturity</div>
                <div class="info-value"><strong style="font-size: 14px; color: #0054a6;">{{ number_format($overallMaturity, 2) }}</strong> / 5.00</div>
            </div>
            <div class="info-row">
                <div class="info-label">Target Maturity</div>
                <div class="info-value"><strong style="font-size: 14px;">{{ number_format($overallTarget, 2) }}</strong> / 5.00</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Maturity Levels by GAMO Category</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Category</th>
                    <th style="text-align: center; width: 15%;">Current</th>
                    <th style="text-align: center; width: 15%;">Target</th>
                    <th style="text-align: center; width: 15%;">Gap</th>
                    <th style="width: 25%;">Maturity Progress</th>
                    <th style="text-align: center; width: 15%;">Objectives</th>
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
                    <td>
                        <div class="maturity-bar">
                            <div class="maturity-fill" style="width: {{ ($category->avg_maturity / 5) * 100 }}%;"></div>
                        </div>
                    </td>
                    <td style="text-align: center;">{{ $category->objective_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Maturity Level Scale (COBIT 2019)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%; text-align: center;">Level</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-danger">Level 0</span></td>
                    <td><strong>Incomplete:</strong> Process not implemented or fails to achieve its purpose</td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-danger">Level 1</span></td>
                    <td><strong>Performed:</strong> Process achieves its purpose</td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-warning">Level 2</span></td>
                    <td><strong>Managed:</strong> Process is planned, monitored, and adjusted</td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-warning">Level 3</span></td>
                    <td><strong>Established:</strong> Process is defined and capable of achieving outcomes</td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-success">Level 4</span></td>
                    <td><strong>Predictable:</strong> Process operates within defined limits</td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="badge badge-success">Level 5</span></td>
                    <td><strong>Optimizing:</strong> Process is continuously improved to meet business needs</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Maturity Level Report - {{ config('app.name') }} | Confidential Information</p>
    </div>
</body>
</html>
