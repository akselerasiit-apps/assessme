<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Summary - {{ $assessment->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .header { background: linear-gradient(135deg, #0054a6 0%, #0d6efd 100%); color: white; padding: 30px; text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 28px; margin-bottom: 8px; }
        .header p { font-size: 14px; margin: 3px 0; }
        .section { margin-bottom: 25px; page-break-inside: avoid; }
        .section-title { background-color: #0054a6; color: white; padding: 10px 12px; font-size: 16px; font-weight: bold; margin-bottom: 12px; }
        .highlight-box { background-color: #f8f9fa; border-left: 4px solid #0054a6; padding: 15px; margin-bottom: 15px; }
        .stats-grid { display: table; width: 100%; margin-bottom: 15px; }
        .stats-item { display: table-cell; width: 25%; padding: 15px; text-align: center; background-color: #f8f9fa; border: 1px solid #dee2e6; }
        .stats-value { font-size: 32px; font-weight: bold; color: #0054a6; }
        .stats-label { font-size: 11px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        table th { background-color: #0054a6; color: white; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .badge-success { background-color: #2fb344; color: white; }
        .badge-warning { background-color: #f59f00; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .recommendation { background-color: #fff3cd; border-left: 4px solid #f59f00; padding: 12px; margin-bottom: 10px; }
        .recommendation strong { color: #f59f00; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; padding: 10px; border-top: 2px solid #0054a6; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Executive Summary</h1>
        <p style="font-size: 16px; margin-top: 10px;">{{ $assessment->title }}</p>
        <p>{{ $assessment->company->name ?? '-' }} | Assessment Code: {{ $assessment->code }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    <!-- Key Highlights -->
    <div class="section">
        <div class="section-title">Key Highlights</div>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-value">{{ number_format($overallMaturity, 1) }}</div>
                <div class="stats-label">Overall Maturity Level</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $completionRate }}%</div>
                <div class="stats-label">Assessment Completion</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $maturityByCategory->count() }}</div>
                <div class="stats-label">GAMO Categories</div>
            </div>
            <div class="stats-item">
                <div class="stats-value">{{ $totalQuestions }}</div>
                <div class="stats-label">Questions Assessed</div>
            </div>
        </div>
    </div>

    <!-- Executive Overview -->
    <div class="section">
        <div class="section-title">Executive Overview</div>
        <div class="highlight-box">
            <p style="margin-bottom: 10px;"><strong>Assessment Period:</strong> 
                {{ $assessment->assessment_period_start?->format('d M Y') ?? '-' }} 
                to 
                {{ $assessment->assessment_period_end?->format('d M Y') ?? '-' }}
            </p>
            <p style="margin-bottom: 10px;"><strong>Assessment Type:</strong> {{ ucfirst($assessment->assessment_type) }}</p>
            <p style="margin-bottom: 10px;"><strong>Scope:</strong> {{ ucfirst($assessment->scope_type) }} COBIT 2019 Framework</p>
            <p><strong>Overall Status:</strong> 
                <span class="badge badge-success">{{ ucfirst($assessment->status) }}</span>
            </p>
        </div>
        <p style="margin-top: 15px; text-align: justify; line-height: 1.8;">
            This executive summary provides a high-level overview of the COBIT 2019 assessment conducted for 
            <strong>{{ $assessment->company->name ?? 'the organization' }}</strong>. The assessment evaluated the 
            organization's IT governance and management capabilities across {{ $maturityByCategory->count() }} GAMO categories, 
            achieving an overall maturity level of <strong>{{ number_format($overallMaturity, 2) }}</strong> out of 5.00. 
            The assessment covered {{ $totalQuestions }} questions with a completion rate of {{ $completionRate }}%.
        </p>
    </div>

    <!-- Maturity Summary -->
    <div class="section">
        <div class="section-title">Maturity Assessment Summary</div>
        <table>
            <thead>
                <tr>
                    <th>GAMO Category</th>
                    <th style="text-align: center; width: 18%;">Current Level</th>
                    <th style="text-align: center; width: 18%;">Target Level</th>
                    <th style="text-align: center; width: 15%;">Gap</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maturityByCategory as $category)
                <tr>
                    <td><strong>{{ $category->category }}</strong></td>
                    <td style="text-align: center; font-size: 14px;">
                        <strong>{{ number_format($category->avg_maturity, 2) }}</strong>
                    </td>
                    <td style="text-align: center;">{{ number_format($category->avg_target, 2) }}</td>
                    <td style="text-align: center;">
                        @php
                            $gap = $category->avg_target - $category->avg_maturity;
                            $badgeClass = $gap >= 2 ? 'badge-danger' : ($gap >= 1 ? 'badge-warning' : 'badge-success');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ number_format($gap, 2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Strengths -->
    @if(isset($topPerforming) && $topPerforming->count() > 0)
    <div class="section">
        <div class="section-title">Key Strengths</div>
        <p style="margin-bottom: 12px;">The following objectives demonstrate strong performance and maturity:</p>
        @foreach($topPerforming as $index => $score)
        <div class="highlight-box" style="background-color: #d4f4b3; border-left-color: #2fb344;">
            <strong style="color: #2fb344;">{{ $index + 1 }}. {{ $score->gamoObjective->code }} - {{ $score->gamoObjective->name }}</strong>
            <div style="margin-top: 5px;">
                Category: <span class="badge" style="background-color: #0054a6;">{{ $score->gamoObjective->category }}</span> | 
                Maturity: <span class="badge badge-success">{{ number_format($score->current_maturity_level, 2) }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Areas for Improvement -->
    @if(isset($needsImprovement) && $needsImprovement->count() > 0)
    <div class="section" style="page-break-before: always;">
        <div class="section-title">Priority Areas for Improvement</div>
        <p style="margin-bottom: 12px;">The following objectives require immediate attention and improvement:</p>
        @foreach($needsImprovement as $index => $score)
        <div class="recommendation">
            <strong>{{ $index + 1 }}. {{ $score->gamoObjective->code }} - {{ $score->gamoObjective->name }}</strong>
            <div style="margin-top: 5px;">
                Category: <span class="badge" style="background-color: #0054a6;">{{ $score->gamoObjective->category }}</span> | 
                Current: <span class="badge badge-danger">{{ number_format($score->current_maturity_level, 2) }}</span> | 
                Target: <span class="badge" style="background-color: #6c757d;">{{ number_format($score->target_maturity_level, 2) }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Key Recommendations -->
    <div class="section">
        <div class="section-title">Key Recommendations</div>
        @if(isset($gamoGaps))
            @php
                $criticalCount = $gamoGaps->filter(fn($g) => $g->gap >= 2)->count();
                $highCount = $gamoGaps->filter(fn($g) => $g->gap >= 1 && $g->gap < 2)->count();
            @endphp
            @if($criticalCount > 0)
            <div class="recommendation" style="background-color: #f8d7da; border-left-color: #dc3545;">
                <strong style="color: #dc3545;">Critical Priority:</strong> Address {{ $criticalCount }} objective(s) with gaps ≥ 2.0 levels. These require immediate action and resource allocation.
            </div>
            @endif
            @if($highCount > 0)
            <div class="recommendation">
                <strong>High Priority:</strong> Focus on {{ $highCount }} objective(s) with gaps between 1.0-2.0 levels. These should be addressed in the next planning cycle.
            </div>
            @endif
        @endif
        <div class="highlight-box">
            <p style="margin-bottom: 8px;"><strong>Next Steps:</strong></p>
            <ul style="margin-left: 20px; line-height: 2;">
                <li>Develop detailed improvement roadmap for priority areas</li>
                <li>Assign ownership and accountability for each objective</li>
                <li>Establish timeline and milestones for maturity improvement</li>
                <li>Schedule follow-up assessment to track progress</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p><strong>Executive Summary</strong> | {{ config('app.name') }}</p>
        <p>© {{ now()->year }} - Confidential & Proprietary Information</p>
    </div>
</body>
</html>
