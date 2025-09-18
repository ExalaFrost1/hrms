<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BenefitsAllowancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefitsAllowances = [];

        // Create benefits for each employee for the last 6 months
        $employees = [1, 2, 3, 4, 5];
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        foreach ($employees as $employeeId) {
            for ($i = 0; $i < 6; $i++) {
                $month = $currentMonth - $i;
                $year = $currentYear;

                if ($month <= 0) {
                    $month += 12;
                    $year--;
                }

                $benefitsAllowances[] = [
                    'employee_id' => $employeeId,
                    'year' => $year,
                    'month' => $month,
                    'internet_allowance' => $this->getInternetAllowance($employeeId),
                    'medical_allowance' => $this->getMedicalAllowance($employeeId),
                    'home_office_setup' => 1000.00,
                    'home_office_setup_claimed' => $this->getHomeOfficeSetupClaimed($employeeId, $i),
                    'birthday_allowance_claimed' => $this->getBirthdayAllowanceClaimed($employeeId, $month),
                    'other_benefits' => $this->getOtherBenefits($employeeId),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('benefits_allowances')->insert($benefitsAllowances);
    }

    private function getInternetAllowance($employeeId): float
    {
        // Different allowances based on employee grade/role
        $allowances = [
            1 => 2500.00, // Senior Developer
            2 => 3000.00, // HR Manager
            3 => 3500.00, // Marketing Manager
            4 => 2000.00, // Accountant
            5 => 1500.00, // Support Agent (Part-time)
        ];

        return $allowances[$employeeId] ?? 2000.00;
    }

    private function getMedicalAllowance($employeeId): float
    {
        // Different medical allowances based on position
        $allowances = [
            1 => 5000.00,
            2 => 6000.00,
            3 => 7000.00,
            4 => 4000.00,
            5 => 3000.00,
        ];

        return $allowances[$employeeId] ?? 4000.00;
    }

    private function getHomeOfficeSetupClaimed($employeeId, $monthsAgo): bool
    {
        // Only claimed once in the first month of joining recent employees
        if ($employeeId == 4 && $monthsAgo == 5) return true; // Ayesha claimed when joined
        if ($employeeId == 5 && $monthsAgo == 4) return true; // Zain claimed when joined

        return false;
    }

    private function getBirthdayAllowanceClaimed($employeeId, $month): bool
    {
        // Birthday months for employees
        $birthdayMonths = [
            1 => 5,  // Ahmed - May
            2 => 8,  // Fatima - August
            3 => 12, // Usman - December
            4 => 3,  // Ayesha - March
            5 => 11, // Zain - November
        ];

        return isset($birthdayMonths[$employeeId]) && $birthdayMonths[$employeeId] == $month;
    }

    private function getOtherBenefits($employeeId): ?string
    {
        $benefits = [
            1 => json_encode([
                'parking_allowance' => 1000.00,
                'meal_vouchers' => 500.00,
                'gym_membership' => 2000.00
            ]),
            2 => json_encode([
                'parking_allowance' => 1200.00,
                'meal_vouchers' => 600.00,
                'professional_development' => 5000.00,
                'leadership_training' => 3000.00
            ]),
            3 => json_encode([
                'parking_allowance' => 1500.00,
                'meal_vouchers' => 800.00,
                'travel_allowance' => 10000.00,
                'client_entertainment' => 5000.00
            ]),
            4 => json_encode([
                'parking_allowance' => 800.00,
                'meal_vouchers' => 400.00,
                'certification_bonus' => 2000.00
            ]),
            5 => json_encode([
                'meal_vouchers' => 300.00,
                'transport_allowance' => 1000.00
            ]),
        ];

        return $benefits[$employeeId] ?? null;
    }
}
