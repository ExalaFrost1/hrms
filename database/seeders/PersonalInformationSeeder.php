<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersonalInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personalInfo = [
            [
                'employee_id' => 1, // Ahmed Hassan Khan
                'date_of_birth' => '1990-05-15',
                'age' => 35,
                'gender' => 'male',
                'marital_status' => 'married',
                'phone_number' => '+92-300-1234567',
                'personal_email' => 'ahmed.hassan.personal@gmail.com',
                'residential_address' => 'House No. 123, Block A, Gulberg III',
                'city' => 'Lahore',
                'state' => 'Punjab',
                'postal_code' => '54000',
                'country' => 'Pakistan',
                'emergency_contact_name' => 'Sara Hassan Khan',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_phone' => '+92-301-7654321',
                'national_id' => '35201-1234567-1',
                'passport_number' => 'AB1234567',
                'tax_number' => 'TAX001234567',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2, // Fatima Ali Sheikh
                'date_of_birth' => '1992-08-20',
                'age' => 33,
                'gender' => 'female',
                'marital_status' => 'single',
                'phone_number' => '+92-321-2345678',
                'personal_email' => 'fatima.ali.personal@gmail.com',
                'residential_address' => 'Flat 45, Tower B, DHA Phase 5',
                'city' => 'Karachi',
                'state' => 'Sindh',
                'postal_code' => '75500',
                'country' => 'Pakistan',
                'emergency_contact_name' => 'Mohammad Ali Sheikh',
                'emergency_contact_relationship' => 'Father',
                'emergency_contact_phone' => '+92-322-8765432',
                'national_id' => '42201-2345678-2',
                'passport_number' => 'CD2345678',
                'tax_number' => 'TAX002345678',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 3, // Muhammad Usman Malik
                'date_of_birth' => '1988-12-10',
                'age' => 36,
                'gender' => 'male',
                'marital_status' => 'married',
                'phone_number' => '+92-333-3456789',
                'personal_email' => 'usman.malik.personal@gmail.com',
                'residential_address' => 'Street 15, F-8/1, Islamabad',
                'city' => 'Islamabad',
                'state' => 'Federal Capital',
                'postal_code' => '44000',
                'country' => 'Pakistan',
                'emergency_contact_name' => 'Khadija Usman',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_phone' => '+92-334-9876543',
                'national_id' => '61101-3456789-3',
                'passport_number' => 'EF3456789',
                'tax_number' => 'TAX003456789',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 4, // Ayesha Iqbal Chaudhry
                'date_of_birth' => '1995-03-25',
                'age' => 30,
                'gender' => 'female',
                'marital_status' => 'divorced',
                'phone_number' => '+92-345-4567890',
                'personal_email' => 'ayesha.iqbal.personal@gmail.com',
                'residential_address' => 'House 67, Model Town Extension',
                'city' => 'Rawalpindi',
                'state' => 'Punjab',
                'postal_code' => '46000',
                'country' => 'Pakistan',
                'emergency_contact_name' => 'Nasreen Iqbal',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_phone' => '+92-346-0987654',
                'national_id' => '37405-4567890-4',
                'passport_number' => 'GH4567890',
                'tax_number' => 'TAX004567890',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 5, // Zain Abbas Bukhari
                'date_of_birth' => '1993-11-08',
                'age' => 31,
                'gender' => 'male',
                'marital_status' => 'single',
                'phone_number' => '+92-312-5678901',
                'personal_email' => 'zain.abbas.personal@gmail.com',
                'residential_address' => 'Apartment 23, Clifton Block 4',
                'city' => 'Karachi',
                'state' => 'Sindh',
                'postal_code' => '75600',
                'country' => 'Pakistan',
                'emergency_contact_name' => 'Abbas Bukhari',
                'emergency_contact_relationship' => 'Father',
                'emergency_contact_phone' => '+92-313-1098765',
                'national_id' => '42301-5678901-5',
                'passport_number' => 'IJ5678901',
                'tax_number' => 'TAX005678901',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('personal_information')->insert($personalInfo);
    }
}
