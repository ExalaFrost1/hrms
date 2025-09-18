<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmploymentHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employmentHistory = [
            [
                'employee_id' => 1, // Ahmed Hassan Khan
                'joining_date' => '2020-01-15',
                'probation_end_date' => '2020-04-15',
                'initial_department' => 'Software Development',
                'initial_role' => 'Junior Developer',
                'initial_grade' => 'G-1',
                'reporting_manager' => 'Sarah Ahmed',
                'current_department' => 'Software Development',
                'current_role' => 'Senior Developer',
                'current_grade' => 'G-3',
                'current_manager' => 'Sarah Ahmed',
                'initial_salary' => 45000.00,
                'current_salary' => 85000.00,
                'employment_type' => 'full_time',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2, // Fatima Ali Sheikh
                'joining_date' => '2019-03-20',
                'probation_end_date' => '2019-06-20',
                'initial_department' => 'Human Resources',
                'initial_role' => 'HR Assistant',
                'initial_grade' => 'G-1',
                'reporting_manager' => 'Nadia Khan',
                'current_department' => 'Human Resources',
                'current_role' => 'HR Manager',
                'current_grade' => 'G-4',
                'current_manager' => 'Nadia Khan',
                'initial_salary' => 40000.00,
                'current_salary' => 95000.00,
                'employment_type' => 'full_time',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 3, // Muhammad Usman Malik
                'joining_date' => '2018-07-01',
                'probation_end_date' => '2018-10-01',
                'initial_department' => 'Marketing',
                'initial_role' => 'Marketing Executive',
                'initial_grade' => 'G-2',
                'reporting_manager' => 'Ali Hassan',
                'current_department' => 'Marketing',
                'current_role' => 'Marketing Manager',
                'current_grade' => 'G-4',
                'current_manager' => 'Ali Hassan',
                'initial_salary' => 50000.00,
                'current_salary' => 110000.00,
                'employment_type' => 'full_time',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 4, // Ayesha Iqbal Chaudhry
                'joining_date' => '2022-05-10',
                'probation_end_date' => '2022-08-10',
                'initial_department' => 'Finance',
                'initial_role' => 'Junior Accountant',
                'initial_grade' => 'G-1',
                'reporting_manager' => 'Tariq Mahmood',
                'current_department' => 'Finance',
                'current_role' => 'Accountant',
                'current_grade' => 'G-2',
                'current_manager' => 'Tariq Mahmood',
                'initial_salary' => 42000.00,
                'current_salary' => 62000.00,
                'employment_type' => 'full_time',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 5, // Zain Abbas Bukhari
                'joining_date' => '2023-02-01',
                'probation_end_date' => '2023-05-01',
                'initial_department' => 'Customer Support',
                'initial_role' => 'Support Agent',
                'initial_grade' => 'G-1',
                'reporting_manager' => 'Sana Malik',
                'current_department' => 'Customer Support',
                'current_role' => 'Support Agent',
                'current_grade' => 'G-1',
                'current_manager' => 'Sana Malik',
                'initial_salary' => 38000.00,
                'current_salary' => 38000.00,
                'employment_type' => 'part_time',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('employment_history')->insert($employmentHistory);
    }
}
