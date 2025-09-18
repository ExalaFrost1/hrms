<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceImprovementPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $performanceImprovementPlans = [
            // Zain Abbas Bukhari - Performance Improvement Plan
            [
                'employee_id' => 5,
                'pip_number' => 1,
                'pip_type' => 'performance_deficiency',
                'severity_level' => 'moderate',
                'start_date' => '2023-07-01',
                'end_date' => '2023-09-30',
                'review_frequency' => 'weekly',
                'initiated_by' => 'Sana Malik',
                'supervisor_assigned' => 'Sana Malik',
                'hr_representative' => 'Fatima Ali Sheikh',
                'title' => '90-Day Performance Improvement Plan - Customer Service Excellence',
                'performance_concerns' => 'Zain has shown consistent issues with response times to customer inquiries, averaging 4-5 hours instead of the required 2 hours. Customer satisfaction ratings for his interactions are below department average at 3.2/5.0. Additionally, there have been instances of incomplete ticket resolution requiring follow-up by senior team members.',
                'root_cause_analysis' => 'Analysis suggests time management difficulties and lack of confidence in handling complex customer issues. New employee may need additional training and mentorship to reach required performance standards.',
                'specific_objectives' => json_encode([
                    'Achieve average response time of 2 hours or less for all customer inquiries',
                    'Maintain customer satisfaction rating of 4.0/5.0 or higher',
                    'Complete ticket resolution without senior intervention in 85% of cases',
                    'Complete advanced customer service training program',
                    'Demonstrate proficiency in all product knowledge areas'
                ]),
                'success_metrics' => json_encode([
                    'Response time metrics tracked daily via support system',
                    'Customer satisfaction scores from post-interaction surveys',
                    'Ticket escalation rates monitored weekly',
                    'Training completion certificates',
                    'Product knowledge assessment scores (minimum 80%)'
                ]),
                'required_actions' => json_encode([
                    'Attend daily 30-minute morning briefing with supervisor',
                    'Complete advanced customer service training (40 hours)',
                    'Shadow senior support agent for 2 weeks',
                    'Practice product knowledge with weekly assessments',
                    'Implement time management techniques discussed in training',
                    'Maintain detailed log of daily activities and challenges'
                ]),
                'support_provided' => 'Company will provide: Daily supervision and feedback, Access to advanced training programs, Mentorship from senior support agent, Flexible schedule adjustment during training period, Additional product knowledge resources and documentation.',
                'training_requirements' => json_encode([
                    'Advanced Customer Service Excellence (40 hours)',
                    'Time Management and Productivity (16 hours)',
                    'Product Knowledge Certification (24 hours)',
                    'Conflict Resolution and De-escalation (8 hours)'
                ]),
                'resources_allocated' => json_encode([
                    'Dedicated mentor: Senior Support Agent - Ahmed Raza',
                    'Training budget: PKR 25,000',
                    'Access to premium learning platforms',
                    'Supervisor time: 1 hour daily for first month, then 30 minutes daily'
                ]),
                'milestone_dates' => json_encode([
                    '2023-07-15: Complete first phase of training',
                    '2023-07-30: First performance review',
                    '2023-08-15: Mid-term assessment',
                    '2023-08-30: Second performance review',
                    '2023-09-15: Final assessment preparation',
                    '2023-09-30: Final performance evaluation'
                ]),
                'consequences_of_failure' => 'If performance standards are not met by the end of this 90-day period, further disciplinary action will be considered, including potential termination of employment. However, the company is committed to providing all necessary support for success.',
                'employee_acknowledgment' => true,
                'employee_comments' => 'I understand the expectations and appreciate the support provided. I am committed to improving my performance and will work hard to meet all objectives.',
                'supervisor_notes' => 'Employee seems motivated to improve. Will provide close supervision and regular feedback. Positive attitude noted.',
                'hr_notes' => 'PIP initiated due to consistent performance issues. Employee is receptive and company is providing extensive support. Monitor progress closely.',
                'status' => 'active',
                'completion_date' => null,
                'final_outcome' => null,
                'supporting_documents' => json_encode([
                    'performance_analysis_report.pdf',
                    'customer_feedback_summary.pdf',
                    'training_plan_detailed.pdf',
                    'pip_acknowledgment_form.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Historical example - Successfully completed PIP
            [
                'employee_id' => 4,
                'pip_number' => 1,
                'pip_type' => 'skills_gap',
                'severity_level' => 'low',
                'start_date' => '2022-08-01',
                'end_date' => '2022-10-31',
                'review_frequency' => 'bi_weekly',
                'initiated_by' => 'Tariq Mahmood',
                'supervisor_assigned' => 'Tariq Mahmood',
                'hr_representative' => 'Fatima Ali Sheikh',
                'title' => '90-Day Skills Development Plan - Advanced Accounting Proficiency',
                'performance_concerns' => 'Ayesha is performing well in basic accounting tasks but needs to develop advanced skills in financial analysis and reporting to fully meet the requirements of her role. This is a development-focused PIP rather than disciplinary.',
                'root_cause_analysis' => 'Recent graduate with strong fundamentals but lacks practical experience with advanced accounting software and complex financial analysis. Needs structured development program.',
                'specific_objectives' => json_encode([
                    'Achieve proficiency in advanced Excel functions and financial modeling',
                    'Complete QuickBooks Professional certification',
                    'Demonstrate ability to prepare complex financial reports independently',
                    'Show understanding of financial ratio analysis and interpretation'
                ]),
                'success_metrics' => json_encode([
                    'Excel proficiency test score (minimum 85%)',
                    'QuickBooks certification completion',
                    'Independent completion of monthly financial reports',
                    'Supervisor assessment of analytical skills'
                ]),
                'required_actions' => json_encode([
                    'Attend advanced Excel training (24 hours)',
                    'Complete QuickBooks Professional course',
                    'Work on increasingly complex assignments with mentor guidance',
                    'Attend bi-weekly progress meetings with supervisor'
                ]),
                'support_provided' => 'Company will provide: Professional training courses, Mentorship from senior accountant, Access to online learning resources, Regular feedback and guidance sessions.',
                'training_requirements' => json_encode([
                    'Advanced Excel for Finance (24 hours)',
                    'QuickBooks Professional Certification (32 hours)',
                    'Financial Analysis Fundamentals (16 hours)'
                ]),
                'resources_allocated' => json_encode([
                    'Training budget: PKR 40,000',
                    'Mentor: Senior Accountant - Rizwan Ali',
                    'Access to professional development resources'
                ]),
                'milestone_dates' => json_encode([
                    '2022-08-15: Complete Excel training',
                    '2022-09-01: First progress review',
                    '2022-09-15: QuickBooks certification',
                    '2022-10-01: Mid-term assessment',
                    '2022-10-15: Advanced assignment completion',
                    '2022-10-31: Final evaluation'
                ]),
                'consequences_of_failure' => 'Extended training period and additional mentoring if objectives are not met. This is a development plan, not punitive action.',
                'employee_acknowledgment' => true,
                'employee_comments' => 'I appreciate this opportunity to develop my skills. I am excited to learn and grow in my role.',
                'supervisor_notes' => 'Ayesha is eager to learn and has great potential. Development-focused approach is appropriate.',
                'hr_notes' => 'Development PIP for promising employee. Strong commitment to growth and learning demonstrated.',
                'status' => 'successful',
                'completion_date' => '2022-10-31',
                'final_outcome' => 'successful_completion',
                'supporting_documents' => json_encode([
                    'skills_assessment_initial.pdf',
                    'training_completion_certificates.pdf',
                    'final_evaluation_report.pdf',
                    'skill_development_portfolio.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('performance_improvement_plans')->insert($performanceImprovementPlans);
    }
}
