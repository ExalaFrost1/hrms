<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmployeeSeeder::class,
            PersonalInformationSeeder::class,
            EmploymentHistorySeeder::class,
            CompensationHistorySeeder::class,
            BenefitsAllowancesSeeder::class,
            PerformanceReviewsSeeder::class,
            AssetManagementSeeder::class,
            WarningsSeeder::class,
            AppreciationsSeeder::class,
            PerformanceImprovementPlansSeeder::class,
        ]);
    }
}
