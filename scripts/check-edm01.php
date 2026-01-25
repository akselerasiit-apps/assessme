<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$assessment = App\Models\Assessment::find(9);
$edm01 = App\Models\GamoObjective::where('code', 'EDM01')->first();

echo "📊 Assessment: {$assessment->code}\n";
echo "🎯 GAMO: EDM01 (Target Level: 5)\n\n";
echo "═══════════════════════════════════════════\n\n";

$complianceData = [];

for ($level = 1; $level <= 5; $level++) {
    echo "📌 LEVEL $level\n";
    echo "─────────────────────────────────────────\n";
    
    $activities = App\Models\GamoQuestion::where('gamo_objective_id', $edm01->id)
        ->where('maturity_level', $level)
        ->get();
    
    $totalWeight = 0;
    $weightedScore = 0;
    
    foreach ($activities as $activity) {
        $answer = App\Models\AssessmentAnswer::where('assessment_id', 9)
            ->where('question_id', $activity->id)
            ->first();
        
        $weight = $activity->weight ?? 1;
        $score = $answer ? $answer->capability_score : 0;
        
        $totalWeight += $weight;
        $weightedScore += $weight * $score;
        
        echo "  • {$activity->code} (Weight: $weight, Score: $score)\n";
    }
    
    $compliance = $totalWeight > 0 ? (($weightedScore / $totalWeight) * 100) : 0;
    $complianceData[$level] = $compliance;
    
    echo "  ➜ Total Weight: $totalWeight\n";
    echo "  ➜ Weighted Score: $weightedScore\n";
    echo "  ➜ Compliance: " . number_format($compliance, 2) . "%\n";
    echo "  ➜ Status: " . ($compliance >= 50 ? '✅ TERCAPAI' : '❌ TIDAK TERCAPAI') . "\n\n";
}

// Calculate realisasi
echo "\n═══════════════════════════════════════════\n";
echo "🎯 PERHITUNGAN REALISASI (COBIT 2019)\n";
echo "═══════════════════════════════════════════\n\n";
echo "⚙️  Threshold: 85% (Sesuai COBIT 2019)\n";
echo "📋 Format: Integer + Persen\n\n";

$achievedLevel = 0;
$achievedCompliance = 0;

for ($level = 1; $level <= 5; $level++) {
    $compliance = $complianceData[$level];
    
    // Skip level kosong (compliance = 0 karena tidak ada activities)
    if ($compliance == 0 && $level == 1) {
        echo "Level $level: SKIP (Tidak ada activities)\n";
        continue;
    }
    
    echo "Level $level: " . number_format($compliance, 2) . "% ";
    
    // COBIT 2019: Level achieved if compliance >= 85%
    if ($compliance >= 85) {
        echo "✅ LULUS\n";
        $achievedLevel = $level;
        $achievedCompliance = $compliance;
    } else {
        echo "❌ TIDAK LULUS (< 85%, STOP)\n";
        break;
    }
}

echo "\n─────────────────────────────────────────\n";
if ($achievedLevel > 0) {
    echo "✅ Capability Level: $achievedLevel\n";
    echo "📊 Compliance: " . number_format($achievedCompliance, 0) . "%\n";
    echo "\n📝 Format Laporan:\n";
    echo "   Level $achievedLevel (" . number_format($achievedCompliance, 0) . "%)\n";
} else {
    echo "❌ Capability Level: 0 (Belum mencapai level apapun)\n";
}
