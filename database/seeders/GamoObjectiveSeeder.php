<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamoObjectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed all 23 GAMO Objectives dengan deskripsi Indonesia
     */
    public function run(): void
    {
        $objectives = [
            // EDM (5 objectives)
            [
                'code' => 'EDM01',
                'name' => 'Evaluate, Direct and Monitor the Set of Enterprise Goals',
                'name_id' => 'Evaluasi, Arahkan, dan Pantau Pemenuhan Tujuan Perusahaan',
                'description' => 'Ensure that business goals and objectives are understood, achieved, and monitored in alignment with IT strategy',
                'description_id' => 'Memastikan tujuan dan objektif bisnis dipahami, dicapai, dan dipantau sesuai dengan strategi IT',
                'category' => 'EDM',
                'objective_order' => 1,
            ],
            [
                'code' => 'EDM02',
                'name' => 'Evaluate, Direct and Monitor IT-Related Business Risk',
                'name_id' => 'Evaluasi, Arahkan, dan Pantau Risiko Bisnis Terkait IT',
                'description' => 'Manage and monitor IT-related business risks and ensure proper risk mitigation strategies are in place',
                'description_id' => 'Kelola dan pantau risiko bisnis terkait IT serta pastikan strategi mitigasi risiko yang tepat diterapkan',
                'category' => 'EDM',
                'objective_order' => 2,
            ],
            [
                'code' => 'EDM03',
                'name' => 'Evaluate, Direct and Monitor IT Compliance',
                'name_id' => 'Evaluasi, Arahkan, dan Pantau Kepatuhan IT',
                'description' => 'Ensure IT operations are compliant with laws, regulations, and contractual obligations',
                'description_id' => 'Pastikan operasi IT mematuhi hukum, regulasi, dan kewajiban kontraktual',
                'category' => 'EDM',
                'objective_order' => 3,
            ],
            [
                'code' => 'EDM04',
                'name' => 'Evaluate, Direct and Monitor IT Governance',
                'name_id' => 'Evaluasi, Arahkan, dan Pantau Governance IT',
                'description' => 'Establish and monitor IT governance framework to ensure effective management and oversight',
                'description_id' => 'Tetapkan dan pantau kerangka kerja governance IT untuk memastikan manajemen dan pengawasan yang efektif',
                'category' => 'EDM',
                'objective_order' => 4,
            ],
            [
                'code' => 'EDM05',
                'name' => 'Evaluate, Direct and Monitor IT Investments',
                'name_id' => 'Evaluasi, Arahkan, dan Pantau Investasi IT',
                'description' => 'Manage and optimize IT investments to ensure proper allocation and value realization',
                'description_id' => 'Kelola dan optimalkan investasi IT untuk memastikan alokasi yang tepat dan realisasi nilai',
                'category' => 'EDM',
                'objective_order' => 5,
            ],
            // APO (7 objectives)
            [
                'code' => 'APO01',
                'name' => 'Manage IT Management Framework',
                'name_id' => 'Kelola Kerangka Kerja Manajemen IT',
                'description' => 'Establish and maintain an integrated IT management framework aligned with business objectives',
                'description_id' => 'Tetapkan dan pertahankan kerangka kerja manajemen IT yang terintegrasi sesuai dengan tujuan bisnis',
                'category' => 'APO',
                'objective_order' => 1,
            ],
            [
                'code' => 'APO02',
                'name' => 'Manage Strategy',
                'name_id' => 'Kelola Strategi',
                'description' => 'Develop and maintain IT strategy aligned with business strategy and stakeholder needs',
                'description_id' => 'Kembangkan dan pertahankan strategi IT yang selaras dengan strategi bisnis dan kebutuhan pemangku kepentingan',
                'category' => 'APO',
                'objective_order' => 2,
            ],
            [
                'code' => 'APO03',
                'name' => 'Manage Enterprise Architecture',
                'name_id' => 'Kelola Arsitektur Enterprise',
                'description' => 'Define and maintain enterprise architecture to guide IT decision-making and transformation',
                'description_id' => 'Tentukan dan pertahankan arsitektur enterprise untuk membimbing pengambilan keputusan IT dan transformasi',
                'category' => 'APO',
                'objective_order' => 3,
            ],
            [
                'code' => 'APO04',
                'name' => 'Manage Innovation',
                'name_id' => 'Kelola Inovasi',
                'description' => 'Identify and evaluate IT innovations to maintain competitive advantage',
                'description_id' => 'Identifikasi dan evaluasi inovasi IT untuk mempertahankan keunggulan kompetitif',
                'category' => 'APO',
                'objective_order' => 4,
            ],
            [
                'code' => 'APO05',
                'name' => 'Manage Portfolio',
                'name_id' => 'Kelola Portfolio',
                'description' => 'Manage IT portfolio to ensure optimal allocation of resources and value delivery',
                'description_id' => 'Kelola portfolio IT untuk memastikan alokasi sumber daya yang optimal dan pengiriman nilai',
                'category' => 'APO',
                'objective_order' => 5,
            ],
            [
                'code' => 'APO06',
                'name' => 'Manage Budget and Costs',
                'name_id' => 'Kelola Budget dan Biaya',
                'description' => 'Plan, manage, and control IT budget and costs effectively',
                'description_id' => 'Rencanakan, kelola, dan kontrol budget dan biaya IT secara efektif',
                'category' => 'APO',
                'objective_order' => 6,
            ],
            [
                'code' => 'APO07',
                'name' => 'Manage Human Resources',
                'name_id' => 'Kelola Sumber Daya Manusia',
                'description' => 'Ensure IT department has appropriate skills, competencies, and organizational structure',
                'description_id' => 'Pastikan departemen IT memiliki keterampilan, kompetensi, dan struktur organisasi yang tepat',
                'category' => 'APO',
                'objective_order' => 7,
            ],
            // BAI (4 objectives)
            [
                'code' => 'BAI01',
                'name' => 'Manage Programmes and Projects',
                'name_id' => 'Kelola Program dan Proyek',
                'description' => 'Plan and execute IT programmes and projects according to approved plans and governance',
                'description_id' => 'Rencanakan dan eksekusi program dan proyek IT sesuai rencana dan governance yang disetujui',
                'category' => 'BAI',
                'objective_order' => 1,
            ],
            [
                'code' => 'BAI02',
                'name' => 'Manage Requirements Definition',
                'name_id' => 'Kelola Definisi Requirement',
                'description' => 'Gather, document, and manage IT requirements from business stakeholders',
                'description_id' => 'Kumpulkan, dokumentasikan, dan kelola requirement IT dari pemangku kepentingan bisnis',
                'category' => 'BAI',
                'objective_order' => 2,
            ],
            [
                'code' => 'BAI03',
                'name' => 'Manage Solutions Identification and Build',
                'name_id' => 'Kelola Identifikasi dan Pembangunan Solusi',
                'description' => 'Identify, design, build, and implement IT solutions to address business requirements',
                'description_id' => 'Identifikasi, desain, bangun, dan implementasikan solusi IT untuk mengatasi requirement bisnis',
                'category' => 'BAI',
                'objective_order' => 3,
            ],
            [
                'code' => 'BAI04',
                'name' => 'Manage Availability and Capacity',
                'name_id' => 'Kelola Ketersediaan dan Kapasitas',
                'description' => 'Plan and manage IT availability and capacity to meet current and future business demands',
                'description_id' => 'Rencanakan dan kelola ketersediaan dan kapasitas IT untuk memenuhi permintaan bisnis saat ini dan masa depan',
                'category' => 'BAI',
                'objective_order' => 4,
            ],
            // DSS (5 objectives)
            [
                'code' => 'DSS01',
                'name' => 'Manage Operations',
                'name_id' => 'Kelola Operasi',
                'description' => 'Execute and manage IT operations to ensure reliable and efficient delivery of IT services',
                'description_id' => 'Eksekusi dan kelola operasi IT untuk memastikan pengiriman layanan IT yang andal dan efisien',
                'category' => 'DSS',
                'objective_order' => 1,
            ],
            [
                'code' => 'DSS02',
                'name' => 'Manage Service Requests and Incidents',
                'name_id' => 'Kelola Permintaan Layanan dan Insiden',
                'description' => 'Process and manage IT service requests and incidents to minimize disruption',
                'description_id' => 'Proses dan kelola permintaan layanan IT dan insiden untuk meminimalkan gangguan',
                'category' => 'DSS',
                'objective_order' => 2,
            ],
            [
                'code' => 'DSS03',
                'name' => 'Manage Problems',
                'name_id' => 'Kelola Masalah',
                'description' => 'Identify, analyze, and resolve problems to prevent service disruptions',
                'description_id' => 'Identifikasi, analisis, dan selesaikan masalah untuk mencegah gangguan layanan',
                'category' => 'DSS',
                'objective_order' => 3,
            ],
            [
                'code' => 'DSS04',
                'name' => 'Manage Continuity',
                'name_id' => 'Kelola Kontinuitas',
                'description' => 'Plan and ensure business continuity of IT services during disruptions',
                'description_id' => 'Rencanakan dan pastikan kontinuitas bisnis layanan IT selama gangguan',
                'category' => 'DSS',
                'objective_order' => 4,
            ],
            [
                'code' => 'DSS05',
                'name' => 'Manage Security Services',
                'name_id' => 'Kelola Layanan Keamanan',
                'description' => 'Implement and maintain security controls to protect IT assets and data',
                'description_id' => 'Implementasikan dan pertahankan kontrol keamanan untuk melindungi aset dan data IT',
                'category' => 'DSS',
                'objective_order' => 5,
            ],
            // MEA (Monitor, Evaluate and Assess) - 4 objectives
            [
                'code' => 'MEA01',
                'name' => 'Monitor, Evaluate and Assess Performance and Conformance',
                'name_id' => 'Pantau, Evaluasi, dan Asesmen Kinerja dan Kesesuaian',
                'description' => 'Monitor IT performance and conformance to ensure objectives are being met',
                'description_id' => 'Pantau kinerja IT dan kesesuaian untuk memastikan tujuan tercapai',
                'category' => 'MEA',
                'objective_order' => 1,
            ],
            [
                'code' => 'MEA02',
                'name' => 'Monitor, Evaluate and Assess the System of Internal Control',
                'name_id' => 'Pantau, Evaluasi, dan Asesmen Sistem Pengendalian Internal',
                'description' => 'Evaluate the effectiveness of IT internal control systems',
                'description_id' => 'Evaluasi efektivitas sistem pengendalian internal IT',
                'category' => 'MEA',
                'objective_order' => 2,
            ],
            [
                'code' => 'MEA03',
                'name' => 'Monitor, Evaluate and Assess Compliance with External Requirements',
                'name_id' => 'Pantau, Evaluasi, dan Asesmen Kepatuhan Terhadap Requirement Eksternal',
                'description' => 'Monitor IT compliance with external laws, regulations, and standards',
                'description_id' => 'Pantau kepatuhan IT terhadap hukum, regulasi, dan standar eksternal',
                'category' => 'MEA',
                'objective_order' => 3,
            ],
            [
                'code' => 'MEA04',
                'name' => 'Managed Assurance',
                'name_id' => 'Pengelolaan Jaminan',
                'description' => 'Managed Assurance',
                'description_id' => 'Pengelolaan Jaminan',
                'category' => 'MEA',
                'objective_order' => 4,
            ],
        ];

        foreach ($objectives as $objective) {
            DB::table('gamo_objectives')->updateOrInsert(
                ['code' => $objective['code']],
                [
                    'name' => $objective['name'],
                    'name_id' => $objective['name_id'],
                    'description' => $objective['description'],
                    'description_id' => $objective['description_id'],
                    'category' => $objective['category'],
                    'objective_order' => $objective['objective_order'],
                    'is_active' => true,
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
