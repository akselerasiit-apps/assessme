<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'PT. Teknologi Indonesia Maju',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'phone' => '021-12345678',
                'email' => 'contact@tekindo.com',
                'industry' => 'Information Technology',
                'size' => 'enterprise',
                'established_year' => 2010,
            ],
            [
                'name' => 'CV. Solusi Digital',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung',
                'phone' => '022-87654321',
                'email' => 'info@solusidigital.co.id',
                'industry' => 'Software Development',
                'size' => 'sme',
                'established_year' => 2015,
            ],
            [
                'name' => 'PT. Bank Nasional',
                'address' => 'Jl. Thamrin No. 789, Jakarta',
                'phone' => '021-98765432',
                'email' => 'contact@banknasional.id',
                'industry' => 'Financial Services',
                'size' => 'enterprise',
                'established_year' => 2000,
            ],
        ];

        foreach ($companies as $company) {
            DB::table('companies')->updateOrInsert(
                ['email' => $company['email']],
                [
                    'name' => $company['name'],
                    'address' => $company['address'],
                    'phone' => $company['phone'],
                    'industry' => $company['industry'],
                    'size' => $company['size'],
                    'established_year' => $company['established_year'],
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
