<?php

// Script to delete Level 1 activities from database
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🗑️ Deleting Level 1 Activities...\n";
echo "═══════════════════════════════════════\n\n";

// Check answers for level 1
$answersCount = \App\Models\AssessmentAnswer::whereHas('question', function($q) {
    $q->where('maturity_level', 1);
})->count();

echo "📊 Answers for level 1 activities: {$answersCount}\n";

if ($answersCount > 0) {
    echo "⚠️ WARNING: {$answersCount} answers will be cascade deleted!\n\n";
}

// Delete level 1 activities
$deleted = \App\Models\GamoQuestion::where('maturity_level', 1)->delete();

echo "✅ Successfully deleted {$deleted} level 1 activities\n";
echo "📊 Remaining activities: " . \App\Models\GamoQuestion::count() . "\n";
