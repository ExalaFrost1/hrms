<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@confinality.com',
            'password' => Hash::make('Admin12345'),
        ]);

        $employees = [
            [
                'employee_id' => 'EMP001',
                'full_name' => 'Ahmed Hassan Khan',
                'email' => 'ahmed.hassan@company.com',
                'username' => 'ahmed.hassan',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'status' => 'active',
                'profile_photo' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 'EMP002',
                'full_name' => 'Fatima Ali Sheikh',
                'email' => 'fatima.ali@company.com',
                'username' => 'fatima.ali',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'status' => 'active',
                'profile_photo' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 'EMP003',
                'full_name' => 'Muhammad Usman Malik',
                'email' => 'usman.malik@company.com',
                'username' => 'usman.malik',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'status' => 'on_leave',
                'profile_photo' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 'EMP004',
                'full_name' => 'Ayesha Iqbal Chaudhry',
                'email' => 'ayesha.iqbal@company.com',
                'username' => 'ayesha.iqbal',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'status' => 'active',
                'profile_photo' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 'EMP005',
                'full_name' => 'Zain Abbas Bukhari',
                'email' => 'zain.abbas@company.com',
                'username' => 'zain.abbas',
                'email_verified_at' => null,
                'password' => Hash::make('password123'),
                'status' => 'inactive',
                'profile_photo' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('employees')->insert($employees);
    }
}
