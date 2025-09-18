<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarningsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warnings = [
            // Zain Abbas Bukhari - Multiple warnings (Performance issues)
            [
                'employee_id' => 5,
                'warning_number' => 1,
                'warning_type' => 'performance',
                'severity_level' => 'minor',
                'incident_date' => '2023-04-15',
                'warning_date' => '2023-04-18',
                'issued_by' => 'Sana Malik',
                'subject' => 'Slow Response Times to Customer Inquiries',
                'description' => 'Zain has been taking longer than the standard response time of 2 hours to respond to customer inquiries. Multiple customers have complained about delayed responses.',
                'incident_location' => 'Customer Support Department',
                'witnesses' => 'Customer feedback emails, Support ticket timestamps',
                'previous_discussions' => 'Informal discussion on 2023-04-10 about response time expectations',
                'expected_improvement' => 'Respond to all customer inquiries within 2 hours during business hours. Improve ticket prioritization and time management.',
                'consequences_if_repeated' => 'Further disciplinary action including formal performance improvement plan',
                'follow_up_date' => '2023-05-01',
                'employee_acknowledgment' => true,
                'employee_comments' => 'I understand the expectations and will work on improving my response times. I will prioritize urgent tickets better.',
                'hr_notes' => 'Employee seems receptive to feedback. Monitor progress closely.',
                'status' => 'acknowledged',
                'resolution_date' => null,
                'supporting_documents' => json_encode([
                    'customer_complaints/april_2023.pdf',
                    'response_time_report.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 5,
                'warning_number' => 2,
                'warning_type' => 'attendance',
                'severity_level' => 'moderate',
                'incident_date' => '2023-06-20',
                'warning_date' => '2023-06-22',
                'issued_by' => 'Sana Malik',
                'subject' => 'Pattern of Late Arrivals',
                'description' => 'Zain has been consistently arriving 30-45 minutes late to work over the past two weeks. This affects team productivity and customer service coverage.',
                'incident_location' => 'Office premises',
                'witnesses' => 'Security logs, Team members',
                'previous_discussions' => 'Verbal reminder about punctuality on 2023-06-15',
                'expected_improvement' => 'Arrive on time for all scheduled shifts. Notify supervisor in advance if there are any unavoidable delays.',
                'consequences_if_repeated' => 'Escalation to formal disciplinary action and potential salary deduction',
                'follow_up_date' => '2023-07-05',
                'employee_acknowledgment' => false,
                'employee_comments' => null,
                'hr_notes' => 'Employee has not yet acknowledged this warning. Schedule follow-up meeting.',
                'status' => 'active',
                'resolution_date' => null,
                'supporting_documents' => json_encode([
                    'attendance_records/june_2023.pdf',
                    'security_logs/entry_times.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Muhammad Usman Malik - Policy violation (Minor)
            [
                'employee_id' => 3,
                'warning_number' => 1,
                'warning_type' => 'policy_violation',
                'severity_level' => 'minor',
                'incident_date' => '2022-11-10',
                'warning_date' => '2022-11-12',
                'issued_by' => 'Ali Hassan',
                'subject' => 'Unauthorized Use of Company Resources',
                'description' => 'Usman used company printer and paper for personal documents without prior approval. While minor, this violates company policy regarding resource usage.',
                'incident_location' => 'Marketing Department Office',
                'witnesses' => 'Office assistant noticed personal documents in printer queue',
                'previous_discussions' => null,
                'expected_improvement' => 'Seek approval before using company resources for personal use. Review company policy on resource usage.',
                'consequences_if_repeated' => 'Cost deduction from salary for unauthorized usage',
                'follow_up_date' => '2022-12-01',
                'employee_acknowledgment' => true,
                'employee_comments' => 'It was an emergency situation and I apologize. I will ask for permission next time and have reviewed the policy.',
                'hr_notes' => 'Minor incident. Employee showed understanding and remorse.',
                'status' => 'resolved',
                'resolution_date' => '2022-12-01',
                'supporting_documents' => json_encode([
                    'printer_logs/november_2022.pdf',
                    'policy_acknowledgment.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Ayesha Iqbal Chaudhry - Safety warning
            [
                'employee_id' => 4,
                'warning_number' => 1,
                'warning_type' => 'safety',
                'severity_level' => 'minor',
                'incident_date' => '2023-03-08',
                'warning_date' => '2023-03-10',
                'issued_by' => 'Tariq Mahmood',
                'subject' => 'Improper Workstation Setup',
                'description' => 'Ayesha was found working with cables scattered on the floor around her workstation, creating a tripping hazard. Additionally, her monitor was placed too close, potentially causing eye strain.',
                'incident_location' => 'Finance Department',
                'witnesses' => 'Facilities manager during routine safety inspection',
                'previous_discussions' => null,
                'expected_improvement' => 'Organize workstation properly with cable management. Adjust monitor distance according to ergonomic guidelines. Complete workplace safety training.',
                'consequences_if_repeated' => 'Mandatory ergonomics assessment and potential workstation restriction',
                'follow_up_date' => '2023-03-20',
                'employee_acknowledgment' => true,
                'employee_comments' => 'I didn\'t realize it was a safety hazard. I have organized my workstation and completed the safety training.',
                'hr_notes' => 'Employee was cooperative and immediately fixed the issues. No further action needed.',
                'status' => 'resolved',
                'resolution_date' => '2023-03-20',
                'supporting_documents' => json_encode([
                    'safety_inspection_report.pdf',
                    'workplace_safety_training_certificate.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('warnings')->insert($warnings);
    }
}
