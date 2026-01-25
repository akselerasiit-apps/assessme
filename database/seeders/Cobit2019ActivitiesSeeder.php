<?php

namespace Database\Seeders;

use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use Illuminate\Database\Seeder;

class Cobit2019ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Populating COBIT 2019 Activities...');
        
        // Clear all existing questions
        GamoQuestion::query()->delete();
        
        // Populate all GAMOs
        $this->populateEDM();
        $this->populateAPO();
        $this->populateBAI();
        $this->populateDSS();
        $this->populateMEA();
        
        $this->command->info('COBIT 2019 Activities populated successfully!');
    }
    
    private function populateEDM()
    {
        $this->command->info('Populating EDM (Evaluate, Direct and Monitor)...');
        
        // EDM01 - Ensure Governance Framework Setting and Maintenance
        $this->createActivities('EDM01', [
            // Management Practice 01: Evaluate the governance system
            ['code' => 'EDM01.01.L2.A1', 'text_en' => 'Obtain awareness of, and endorsement of, the business value of IT.', 'text_id' => 'Memperoleh kesadaran dan dukungan terhadap nilai bisnis TI.', 'level' => 2, 'order' => 1],
            ['code' => 'EDM01.01.L2.A2', 'text_en' => 'Evaluate, direct and monitor (EDM) the achievement of business goals and objectives enabled by IT.', 'text_id' => 'Mengevaluasi, mengarahkan dan memantau (EDM) pencapaian tujuan bisnis yang didukung oleh TI.', 'level' => 2, 'order' => 2],
            ['code' => 'EDM01.01.L3.A1', 'text_en' => 'Define and implement governance practices to ensure IT investments deliver business value.', 'text_id' => 'Mendefinisikan dan menerapkan praktik tata kelola untuk memastikan investasi TI memberikan nilai bisnis.', 'level' => 3, 'order' => 3],
            ['code' => 'EDM01.01.L3.A2', 'text_en' => 'Monitor and evaluate the achievement of value delivery.', 'text_id' => 'Memantau dan mengevaluasi pencapaian penyampaian nilai.', 'level' => 3, 'order' => 4],
            ['code' => 'EDM01.01.L4.A1', 'text_en' => 'Establish governance activities based on agreed-upon goals, with input from stakeholders.', 'text_id' => 'Menetapkan aktivitas tata kelola berdasarkan tujuan yang disepakati, dengan masukan dari pemangku kepentingan.', 'level' => 4, 'order' => 5],
            ['code' => 'EDM01.01.L4.A2', 'text_en' => 'Ensure governance activities are optimized through regular reviews and improvements.', 'text_id' => 'Memastikan aktivitas tata kelola dioptimalkan melalui tinjauan dan peningkatan rutin.', 'level' => 4, 'order' => 6],
            ['code' => 'EDM01.01.L5.A1', 'text_en' => 'Continuously improve governance practices based on industry best practices.', 'text_id' => 'Meningkatkan praktik tata kelola secara berkelanjutan berdasarkan praktik terbaik industri.', 'level' => 5, 'order' => 7],
            ['code' => 'EDM01.01.L5.A2', 'text_en' => 'Align governance framework with changing business and technology environments.', 'text_id' => 'Menyelaraskan kerangka tata kelola dengan perubahan lingkungan bisnis dan teknologi.', 'level' => 5, 'order' => 8],
            
            // Management Practice 02: Direct the governance system
            ['code' => 'EDM01.02.L2.A1', 'text_en' => 'Communicate governance principles, policies and procedures to stakeholders.', 'text_id' => 'Mengkomunikasikan prinsip, kebijakan dan prosedur tata kelola kepada pemangku kepentingan.', 'level' => 2, 'order' => 11],
            ['code' => 'EDM01.02.L2.A2', 'text_en' => 'Establish accountability for governance outcomes.', 'text_id' => 'Menetapkan akuntabilitas untuk hasil tata kelola.', 'level' => 2, 'order' => 12],
            ['code' => 'EDM01.02.L3.A1', 'text_en' => 'Set direction for governance practices and ensure alignment with enterprise strategy.', 'text_id' => 'Menetapkan arah untuk praktik tata kelola dan memastikan keselarasan dengan strategi perusahaan.', 'level' => 3, 'order' => 13],
            ['code' => 'EDM01.02.L3.A2', 'text_en' => 'Approve governance-related investments and resource allocation.', 'text_id' => 'Menyetujui investasi terkait tata kelola dan alokasi sumber daya.', 'level' => 3, 'order' => 14],
            ['code' => 'EDM01.02.L4.A1', 'text_en' => 'Ensure governance direction is integrated into enterprise planning and decision-making.', 'text_id' => 'Memastikan arah tata kelola terintegrasi dalam perencanaan dan pengambilan keputusan perusahaan.', 'level' => 4, 'order' => 15],
            ['code' => 'EDM01.02.L4.A2', 'text_en' => 'Review and approve governance frameworks and standards.', 'text_id' => 'Meninjau dan menyetujui kerangka kerja dan standar tata kelola.', 'level' => 4, 'order' => 16],
            ['code' => 'EDM01.02.L5.A1', 'text_en' => 'Continuously optimize governance direction based on emerging trends and innovations.', 'text_id' => 'Terus mengoptimalkan arah tata kelola berdasarkan tren dan inovasi yang muncul.', 'level' => 5, 'order' => 17],
            ['code' => 'EDM01.02.L5.A2', 'text_en' => 'Ensure governance direction enables enterprise agility and transformation.', 'text_id' => 'Memastikan arah tata kelola mendukung kelincahan dan transformasi perusahaan.', 'level' => 5, 'order' => 18],
            
            // Management Practice 03: Monitor the governance system
            ['code' => 'EDM01.03.L2.A1', 'text_en' => 'Monitor achievement of governance objectives and compliance with policies.', 'text_id' => 'Memantau pencapaian tujuan tata kelola dan kepatuhan terhadap kebijakan.', 'level' => 2, 'order' => 21],
            ['code' => 'EDM01.03.L2.A2', 'text_en' => 'Identify and escalate governance-related issues and risks.', 'text_id' => 'Mengidentifikasi dan meningkatkan isu dan risiko terkait tata kelola.', 'level' => 2, 'order' => 22],
            ['code' => 'EDM01.03.L3.A1', 'text_en' => 'Establish governance performance metrics and monitoring mechanisms.', 'text_id' => 'Menetapkan metrik kinerja tata kelola dan mekanisme pemantauan.', 'level' => 3, 'order' => 23],
            ['code' => 'EDM01.03.L3.A2', 'text_en' => 'Report on governance performance to stakeholders regularly.', 'text_id' => 'Melaporkan kinerja tata kelola kepada pemangku kepentingan secara berkala.', 'level' => 3, 'order' => 24],
            ['code' => 'EDM01.03.L4.A1', 'text_en' => 'Analyze governance performance trends and implement corrective actions.', 'text_id' => 'Menganalisis tren kinerja tata kelola dan menerapkan tindakan korektif.', 'level' => 4, 'order' => 25],
            ['code' => 'EDM01.03.L4.A2', 'text_en' => 'Conduct regular governance maturity assessments and benchmarking.', 'text_id' => 'Melakukan penilaian kematangan tata kelola dan pembandingan secara berkala.', 'level' => 4, 'order' => 26],
            ['code' => 'EDM01.03.L5.A1', 'text_en' => 'Use predictive analytics to anticipate governance challenges and opportunities.', 'text_id' => 'Menggunakan analitik prediktif untuk mengantisipasi tantangan dan peluang tata kelola.', 'level' => 5, 'order' => 27],
            ['code' => 'EDM01.03.L5.A2', 'text_en' => 'Continuously improve governance monitoring based on insights and best practices.', 'text_id' => 'Terus meningkatkan pemantauan tata kelola berdasarkan wawasan dan praktik terbaik.', 'level' => 5, 'order' => 28],
        ]);
        
        // EDM02-EDM05 with similar structure (simplified for brevity)
        $this->createSimplifiedEDM('EDM02', 'Ensure Benefits Delivery', 'Memastikan Penyampaian Manfaat');
        $this->createSimplifiedEDM('EDM03', 'Ensure Risk Optimization', 'Memastikan Optimasi Risiko');
        $this->createSimplifiedEDM('EDM04', 'Ensure Resource Optimization', 'Memastikan Optimasi Sumber Daya');
        $this->createSimplifiedEDM('EDM05', 'Ensure Stakeholder Engagement', 'Memastikan Keterlibatan Pemangku Kepentingan');
    }
    
    private function createSimplifiedEDM($code, $nameEn, $nameId)
    {
        $activities = [];
        $order = 1;
        
        for ($mp = 1; $mp <= 3; $mp++) {
            for ($level = 2; $level <= 5; $level++) {
                for ($activity = 1; $activity <= 2; $activity++) {
                    $activities[] = [
                        'code' => "{$code}.0{$mp}.L{$level}.A{$activity}",
                        'text_en' => "Practice {$mp} - Level {$level} - Activity {$activity}: {$nameEn}",
                        'text_id' => "Praktik {$mp} - Level {$level} - Aktivitas {$activity}: {$nameId}",
                        'level' => $level,
                        'order' => $order++,
                    ];
                }
            }
        }
        
        $this->createActivities($code, $activities);
    }
    
    private function populateAPO()
    {
        $this->command->info('Populating APO (Align, Plan and Organize)...');
        
        $apoObjectives = [
            'APO01' => 'Managed IT Management Framework',
            'APO02' => 'Managed Strategy',
            'APO03' => 'Managed Enterprise Architecture',
            'APO04' => 'Managed Innovation',
            'APO05' => 'Managed Portfolio',
            'APO06' => 'Managed Budget and Costs',
            'APO07' => 'Managed Human Resources',
        ];
        
        foreach ($apoObjectives as $code => $name) {
            $this->createSimplifiedActivities($code, $name);
        }
    }
    
    private function populateBAI()
    {
        $this->command->info('Populating BAI (Build, Acquire and Implement)...');
        
        $baiObjectives = [
            'BAI01' => 'Managed Programs',
            'BAI02' => 'Managed Requirements Definition',
            'BAI03' => 'Managed Solutions Identification and Build',
            'BAI04' => 'Managed Availability and Capacity',
        ];
        
        foreach ($baiObjectives as $code => $name) {
            $this->createSimplifiedActivities($code, $name);
        }
    }
    
    private function populateDSS()
    {
        $this->command->info('Populating DSS (Deliver, Service and Support)...');
        
        $dssObjectives = [
            'DSS01' => 'Managed Operations',
            'DSS02' => 'Managed Service Requests and Incidents',
            'DSS03' => 'Managed Problems',
            'DSS04' => 'Managed Continuity',
            'DSS05' => 'Managed Security Services',
        ];
        
        foreach ($dssObjectives as $code => $name) {
            $this->createSimplifiedActivities($code, $name);
        }
    }
    
    private function populateMEA()
    {
        $this->command->info('Populating MEA (Monitor, Evaluate and Assess)...');
        
        $meaObjectives = [
            'MEA01' => 'Managed Performance and Conformance Monitoring',
            'MEA02' => 'Managed System of Internal Control',
            'MEA03' => 'Managed Compliance With External Requirements',
        ];
        
        foreach ($meaObjectives as $code => $name) {
            $this->createSimplifiedActivities($code, $name);
        }
    }
    
    private function createSimplifiedActivities($code, $name)
    {
        $activities = [];
        $order = 1;
        
        // 3 Management Practices, 4 Levels (2-5), 2-3 Activities per level
        for ($mp = 1; $mp <= 3; $mp++) {
            for ($level = 2; $level <= 5; $level++) {
                $activityCount = $level <= 3 ? 3 : 2; // More activities in lower levels
                
                for ($activity = 1; $activity <= $activityCount; $activity++) {
                    $activities[] = [
                        'code' => "{$code}.0{$mp}.L{$level}.A{$activity}",
                        'text_en' => "{$name} - Practice {$mp}, Level {$level}, Activity {$activity}",
                        'text_id' => "{$name} - Praktik {$mp}, Level {$level}, Aktivitas {$activity}",
                        'level' => $level,
                        'order' => $order++,
                    ];
                }
            }
        }
        
        $this->createActivities($code, $activities);
    }
    
    private function createActivities($gamoCode, $activities)
    {
        $gamo = GamoObjective::where('code', $gamoCode)->first();
        
        if (!$gamo) {
            $this->command->warn("GAMO {$gamoCode} not found, skipping...");
            return;
        }
        
        foreach ($activities as $activity) {
            GamoQuestion::create([
                'code' => $activity['code'],
                'gamo_objective_id' => $gamo->id,
                'question_text' => $activity['text_en'] . ' | ' . $activity['text_id'],
                'question_type' => 'rating',
                'maturity_level' => $activity['level'],
                'question_order' => $activity['order'],
                'is_active' => true,
                'required' => true,
            ]);
        }
        
        $this->command->info("  ✓ {$gamoCode}: " . count($activities) . " activities");
    }
}
