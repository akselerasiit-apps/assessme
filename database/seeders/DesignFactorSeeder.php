<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designFactors = [
            [
                'code' => 'ES',
                'name' => 'Enterprise Strategy',
                'description' => 'Visi, misi, dan strategi bisnis organisasi',
                'factor_order' => 1,
            ],
            [
                'code' => 'EG',
                'name' => 'Enterprise Goals',
                'description' => 'Tujuan perusahaan yang aligned dengan strategi',
                'factor_order' => 2,
            ],
            [
                'code' => 'RP',
                'name' => 'Risk Profile',
                'description' => 'Risk appetite dan tolerance level organisasi',
                'factor_order' => 3,
            ],
            [
                'code' => 'ITI',
                'name' => 'I&T Related Issues',
                'description' => 'Isu-isu yang berkaitan dengan IT',
                'factor_order' => 4,
            ],
            [
                'code' => 'TL',
                'name' => 'Threat Landscape',
                'description' => 'Ancaman internal dan eksternal',
                'factor_order' => 5,
            ],
            [
                'code' => 'CR',
                'name' => 'Compliance Requirements',
                'description' => 'Requirement regulasi dan compliance',
                'factor_order' => 6,
            ],
            [
                'code' => 'RIT',
                'name' => 'Role of IT',
                'description' => 'Peran IT dalam organisasi (Support/Defense/Factory/Strategic)',
                'factor_order' => 7,
            ],
            [
                'code' => 'SM',
                'name' => 'Sourcing Model for IT',
                'description' => 'Model sumber IT (Insourced/Outsourced/Co-sourced)',
                'factor_order' => 8,
            ],
            [
                'code' => 'IM',
                'name' => 'IT Implementation Methods',
                'description' => 'Metode implementasi IT (Waterfall/Agile/Hybrid/DevOps)',
                'factor_order' => 9,
            ],
            [
                'code' => 'TA',
                'name' => 'Technology Strategy Adoption',
                'description' => 'Strategi adopsi teknologi (Legacy/Steady/Progressive/Innovative)',
                'factor_order' => 10,
            ],
        ];

        foreach ($designFactors as $factor) {
            DB::table('design_factors')->updateOrInsert(
                ['code' => $factor['code']],
                [
                    'name' => $factor['name'],
                    'description' => $factor['description'],
                    'factor_order' => $factor['factor_order'],
                    'is_active' => true,
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
