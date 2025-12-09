<?php

namespace Database\Seeders;

use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use Illuminate\Database\Seeder;

class GamoQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create sample questions for GAMO objectives across all maturity levels
     */
    public function run(): void
    {
        // Ensure GAMO objectives exist
        if (GamoObjective::count() === 0) {
            $this->command->warn('⚠️  No GAMO objectives found. Running GamoObjectiveSeeder first...');
            $this->call(GamoObjectiveSeeder::class);
        }

        // Sample questions untuk beberapa GAMO objectives
        $questions = [
            // EDM01 Questions
            [
                'gamo_code' => 'EDM01',
                'questions' => [
                    [
                        'code' => 'EDM01-L1-Q1',
                        'question_text' => 'Apakah organisasi memiliki tujuan bisnis yang jelas dan terdokumentasi?',
                        'guidance' => 'Periksa dokumen strategi bisnis, visi, misi organisasi',
                        'evidence_requirement' => 'Dokumen strategi bisnis, business plan, atau dokumen visi-misi',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'EDM01-L2-Q1',
                        'question_text' => 'Apakah tujuan bisnis dikomunikasikan kepada seluruh stakeholder terkait?',
                        'guidance' => 'Verifikasi mekanisme komunikasi dan sosialisasi tujuan bisnis',
                        'evidence_requirement' => 'Notulen rapat, email komunikasi, atau dokumen sosialisasi',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                    [
                        'code' => 'EDM01-L3-Q1',
                        'question_text' => 'Apakah ada proses formal untuk monitoring pencapaian tujuan bisnis?',
                        'guidance' => 'Tinjau prosedur monitoring, KPI dashboard, atau performance review',
                        'evidence_requirement' => 'SOP monitoring, KPI dashboard, laporan progress',
                        'question_type' => 'yes_no',
                        'maturity_level' => 3,
                        'required' => true,
                        'question_order' => 3,
                    ],
                ],
            ],

            // EDM02 Questions
            [
                'gamo_code' => 'EDM02',
                'questions' => [
                    [
                        'code' => 'EDM02-L1-Q1',
                        'question_text' => 'Apakah organisasi mengenali risiko IT yang dapat mempengaruhi bisnis?',
                        'guidance' => 'Identifikasi dokumen risk register atau risk assessment',
                        'evidence_requirement' => 'Risk register, risk assessment report',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'EDM02-L2-Q1',
                        'question_text' => 'Apakah ada proses pengelolaan risiko IT yang terdokumentasi?',
                        'guidance' => 'Periksa risk management framework atau prosedur',
                        'evidence_requirement' => 'Risk management policy, SOP risk management',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],

            // APO01 Questions
            [
                'gamo_code' => 'APO01',
                'questions' => [
                    [
                        'code' => 'APO01-L1-Q1',
                        'question_text' => 'Apakah organisasi memiliki kerangka kerja manajemen IT?',
                        'guidance' => 'Tinjau struktur organisasi IT dan framework yang digunakan',
                        'evidence_requirement' => 'Struktur organisasi IT, IT governance framework',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'APO01-L2-Q1',
                        'question_text' => 'Apakah kerangka kerja manajemen IT terintegrasi dengan proses bisnis?',
                        'guidance' => 'Verifikasi alignment antara IT framework dengan business process',
                        'evidence_requirement' => 'Dokumen integrasi IT-Business, process mapping',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],

            // APO02 Questions
            [
                'gamo_code' => 'APO02',
                'questions' => [
                    [
                        'code' => 'APO02-L1-Q1',
                        'question_text' => 'Apakah organisasi memiliki strategi IT yang terdefinisi?',
                        'guidance' => 'Periksa dokumen IT strategy atau IT roadmap',
                        'evidence_requirement' => 'Dokumen IT strategy, IT roadmap',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'APO02-L2-Q1',
                        'question_text' => 'Apakah strategi IT selaras dengan strategi bisnis organisasi?',
                        'guidance' => 'Evaluasi alignment antara IT strategy dan business strategy',
                        'evidence_requirement' => 'Strategic alignment document, business-IT mapping',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],

            // BAI01 Questions
            [
                'gamo_code' => 'BAI01',
                'questions' => [
                    [
                        'code' => 'BAI01-L1-Q1',
                        'question_text' => 'Apakah proyek IT dikelola dengan metodologi tertentu?',
                        'guidance' => 'Tinjau project management methodology yang digunakan',
                        'evidence_requirement' => 'Project management framework, methodology documentation',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'BAI01-L2-Q1',
                        'question_text' => 'Apakah ada governance untuk program dan proyek IT?',
                        'guidance' => 'Verifikasi project governance structure dan oversight',
                        'evidence_requirement' => 'Project governance charter, steering committee',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],

            // DSS01 Questions
            [
                'gamo_code' => 'DSS01',
                'questions' => [
                    [
                        'code' => 'DSS01-L1-Q1',
                        'question_text' => 'Apakah operasi IT berjalan sesuai prosedur standar?',
                        'guidance' => 'Periksa SOP operasional IT',
                        'evidence_requirement' => 'SOP IT operations, runbook',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'DSS01-L2-Q1',
                        'question_text' => 'Apakah operasi IT dimonitor dan dilaporkan secara berkala?',
                        'guidance' => 'Tinjau operational monitoring dan reporting mechanism',
                        'evidence_requirement' => 'Operational reports, monitoring dashboard',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],

            // MEA01 Questions
            [
                'gamo_code' => 'MEA01',
                'questions' => [
                    [
                        'code' => 'MEA01-L1-Q1',
                        'question_text' => 'Apakah kinerja IT diukur dengan indikator tertentu?',
                        'guidance' => 'Identifikasi KPI atau metrics yang digunakan',
                        'evidence_requirement' => 'KPI documentation, performance metrics',
                        'question_type' => 'yes_no',
                        'maturity_level' => 1,
                        'required' => true,
                        'question_order' => 1,
                    ],
                    [
                        'code' => 'MEA01-L2-Q1',
                        'question_text' => 'Apakah hasil pengukuran kinerja dikaji dan ditindaklanjuti?',
                        'guidance' => 'Verifikasi performance review process dan corrective actions',
                        'evidence_requirement' => 'Performance review reports, action plans',
                        'question_type' => 'yes_no',
                        'maturity_level' => 2,
                        'required' => true,
                        'question_order' => 2,
                    ],
                ],
            ],
        ];

        $totalCreated = 0;

        foreach ($questions as $gamoQuestions) {
            $gamoObjective = GamoObjective::where('code', $gamoQuestions['gamo_code'])->first();

            if (!$gamoObjective) {
                $this->command->warn("⚠️  GAMO {$gamoQuestions['gamo_code']} not found, skipping...");
                continue;
            }

            foreach ($gamoQuestions['questions'] as $questionData) {
                GamoQuestion::firstOrCreate(
                    ['code' => $questionData['code']],
                    [
                        'gamo_objective_id' => $gamoObjective->id,
                        'question_text' => $questionData['question_text'],
                        'guidance' => $questionData['guidance'],
                        'evidence_requirement' => $questionData['evidence_requirement'],
                        'question_type' => $questionData['question_type'],
                        'maturity_level' => $questionData['maturity_level'],
                        'required' => $questionData['required'],
                        'question_order' => $questionData['question_order'],
                        'is_active' => true,
                    ]
                );
                $totalCreated++;
            }
        }

        $this->command->info("✅ Sample GAMO questions created successfully!");
        $this->command->info("Created {$totalCreated} questions across 7 GAMO objectives");
        $this->command->info('');
        $this->command->info('📊 Questions distribution:');
        $this->command->info('EDM01: 3 questions (Level 1-3)');
        $this->command->info('EDM02: 2 questions (Level 1-2)');
        $this->command->info('APO01: 2 questions (Level 1-2)');
        $this->command->info('APO02: 2 questions (Level 1-2)');
        $this->command->info('BAI01: 2 questions (Level 1-2)');
        $this->command->info('DSS01: 2 questions (Level 1-2)');
        $this->command->info('MEA01: 2 questions (Level 1-2)');
    }
}
