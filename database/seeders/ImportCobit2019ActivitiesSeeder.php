<?php

namespace Database\Seeders;

use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCobit2019ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Importing COBIT 2019 Activities from Excel...');
        
        $file = base_path('COBIT_2019_All_GAMO_Activities.xlsx');
        
        if (!file_exists($file)) {
            $this->command->error('Excel file not found: ' . $file);
            return;
        }
        
        // Clear existing questions
        $this->command->info('Clearing existing activities...');
        GamoQuestion::query()->delete();
        
        // Load Excel file
        $this->command->info('Reading Excel file...');
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        
        $totalRows = $sheet->getHighestRow();
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        // Start from row 2 (skip header)
        for ($row = 2; $row <= $totalRows; $row++) {
            $domain = trim($sheet->getCell('A' . $row)->getValue() ?? '');
            $gamoCode = trim($sheet->getCell('B' . $row)->getValue() ?? '');
            $gamoNameEn = trim($sheet->getCell('C' . $row)->getValue() ?? '');
            $gamoNameId = trim($sheet->getCell('D' . $row)->getValue() ?? '');
            $practiceCode = trim($sheet->getCell('E' . $row)->getValue() ?? '');
            $activityNo = trim($sheet->getCell('F' . $row)->getValue() ?? '');
            $activityEn = trim($sheet->getCell('G' . $row)->getValue() ?? '');
            $activityId = trim($sheet->getCell('H' . $row)->getValue() ?? '');
            
            // Skip empty rows
            if (empty($gamoCode) || empty($practiceCode) || empty($activityEn)) {
                $skipped++;
                continue;
            }
            
            // Find GAMO objective
            $gamo = GamoObjective::where('code', $gamoCode)->first();
            
            if (!$gamo) {
                $errors[] = "Row {$row}: GAMO {$gamoCode} not found";
                $skipped++;
                continue;
            }
            
            // Create unique activity code
            // Format: EDM01.01.A1, EDM01.01.A2, etc.
            $activityCode = $practiceCode . '.A' . $activityNo;
            
            // Determine maturity level from practice code
            // For now, we'll distribute activities across levels 2-5
            // You can adjust this logic based on your needs
            $level = $this->determineLevel($row, $totalRows, $gamoCode);
            
            try {
                GamoQuestion::create([
                    'code' => $activityCode,
                    'gamo_objective_id' => $gamo->id,
                    'question_text' => $activityEn . ' | ' . $activityId,
                    'question_type' => 'rating',
                    'maturity_level' => $level,
                    'question_order' => $row - 1,
                    'is_active' => true,
                    'required' => true,
                ]);
                
                $imported++;
                
                if ($imported % 20 == 0) {
                    $this->command->info("  Imported {$imported} activities...");
                }
            } catch (\Exception $e) {
                $errors[] = "Row {$row}: " . $e->getMessage();
                $skipped++;
            }
        }
        
        $this->command->info('');
        $this->command->info('Import completed!');
        $this->command->info("  ✓ Imported: {$imported} activities");
        $this->command->info("  ✗ Skipped: {$skipped} rows");
        
        if (!empty($errors)) {
            $this->command->warn('');
            $this->command->warn('Errors encountered:');
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->command->warn("  - {$error}");
            }
            if (count($errors) > 10) {
                $this->command->warn("  ... and " . (count($errors) - 10) . " more errors");
            }
        }
        
        // Show summary by GAMO
        $this->command->info('');
        $this->command->info('Activities per GAMO:');
        $gamos = GamoObjective::withCount('questions')->orderBy('code')->get();
        foreach ($gamos as $gamo) {
            if ($gamo->questions_count > 0) {
                $this->command->info("  {$gamo->code}: {$gamo->questions_count} activities");
            }
        }
    }
    
    /**
     * Determine maturity level for activity
     * Distributes activities across levels 1-5 for each GAMO
     */
    private static $gamoRowCounts = [];
    
    private function determineLevel($row, $totalRows, $gamoCode): int
    {
        // Initialize counter for this GAMO
        if (!isset(self::$gamoRowCounts[$gamoCode])) {
            self::$gamoRowCounts[$gamoCode] = 0;
        }
        
        self::$gamoRowCounts[$gamoCode]++;
        $position = self::$gamoRowCounts[$gamoCode];
        
        // Distribute activities across levels 1-5
        // For 3 activities per GAMO:
        // Activity 1 -> Level 1 (Initial/Ad hoc)
        // Activity 2 -> Level 3 (Defined)
        // Activity 3 -> Level 5 (Optimizing)
        
        // For more activities, distribute evenly
        if ($position == 1) return 1;
        if ($position == 2) return 3;
        if ($position == 3) return 5;
        
        // For additional activities (if any), cycle through levels
        return (($position - 1) % 5) + 1;
    }
}
