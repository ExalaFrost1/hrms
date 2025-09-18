<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $performanceReviews = [
            // Ahmed Hassan Khan - Multiple reviews
            [
                'employee_id' => 1,
                'review_period' => 'Q2 2023',
                'review_date' => '2023-07-15',
                'goal_completion_rate' => 92.50,
                'overall_rating' => 4.2,
                'manager_feedback' => 'Ahmed has consistently delivered high-quality code and has shown excellent problem-solving skills. His leadership in the recent project was commendable.',
                'peer_feedback' => 'Great team player, always willing to help others. His technical expertise is valuable to the team.',
                'self_assessment' => 'I believe I have grown significantly in my role. I have successfully led two major projects and mentored junior developers.',
                'areas_of_strength' => 'Technical expertise, Leadership, Problem-solving, Mentoring',
                'areas_for_improvement' => 'Time management, Documentation practices',
                'development_goals' => 'Complete advanced architecture certification, Lead cross-functional projects',
                'key_achievements' => json_encode([
                    'Led successful migration of legacy system',
                    'Reduced system downtime by 30%',
                    'Mentored 3 junior developers'
                ]),
                'skills_demonstrated' => json_encode([
                    'Leadership',
                    'Technical Architecture',
                    'Project Management',
                    'Mentoring'
                ]),
                'supporting_documents' => json_encode([
                    'project_reports/migration_success.pdf',
                    'certifications/aws_architect.pdf'
                ]),
                'reviewed_by' => 'Sarah Ahmed',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 1,
                'review_period' => 'Annual 2022',
                'review_date' => '2022-12-30',
                'goal_completion_rate' => 88.00,
                'overall_rating' => 4.0,
                'manager_feedback' => 'Excellent performance throughout the year. Ahmed has shown consistent growth and taken on additional responsibilities.',
                'peer_feedback' => 'Reliable and knowledgeable team member. Good communication skills.',
                'self_assessment' => 'This year has been great for my professional development. I have learned new technologies and improved my skills.',
                'areas_of_strength' => 'Technical skills, Reliability, Learning agility',
                'areas_for_improvement' => 'Leadership skills, Public speaking',
                'development_goals' => 'Take on team lead responsibilities, Complete AWS certification',
                'key_achievements' => json_encode([
                    'Completed 3 major projects on time',
                    'Learned React and Node.js',
                    'Improved system performance by 25%'
                ]),
                'skills_demonstrated' => json_encode([
                    'Full-stack development',
                    'Performance optimization',
                    'Quick learning'
                ]),
                'supporting_documents' => json_encode([
                    'performance_metrics_2022.pdf'
                ]),
                'reviewed_by' => 'Sarah Ahmed',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Fatima Ali Sheikh
            [
                'employee_id' => 2,
                'review_period' => 'Q1 2023',
                'review_date' => '2023-04-20',
                'goal_completion_rate' => 95.00,
                'overall_rating' => 4.5,
                'manager_feedback' => 'Fatima has excelled in her role as HR Manager. Her leadership and strategic thinking have significantly improved our HR processes.',
                'peer_feedback' => 'Exceptional leader and communicator. Always approachable and provides excellent guidance.',
                'self_assessment' => 'I am proud of the HR initiatives I have led this quarter. The employee satisfaction survey results show significant improvement.',
                'areas_of_strength' => 'Leadership, Strategic planning, Employee relations, Communication',
                'areas_for_improvement' => 'Data analytics, Technology adoption',
                'development_goals' => 'Implement HR analytics dashboard, Complete leadership development program',
                'key_achievements' => json_encode([
                    'Increased employee satisfaction by 20%',
                    'Implemented new performance management system',
                    'Reduced hiring time by 35%',
                    'Led diversity and inclusion initiative'
                ]),
                'skills_demonstrated' => json_encode([
                    'Strategic HR management',
                    'Change management',
                    'Employee engagement',
                    'Process improvement'
                ]),
                'supporting_documents' => json_encode([
                    'employee_satisfaction_report.pdf',
                    'hr_metrics_q1_2023.pdf'
                ]),
                'reviewed_by' => 'Nadia Khan',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Muhammad Usman Malik
            [
                'employee_id' => 3,
                'review_period' => 'Annual 2022',
                'review_date' => '2023-01-15',
                'goal_completion_rate' => 90.00,
                'overall_rating' => 4.3,
                'manager_feedback' => 'Usman has delivered outstanding results in marketing campaigns. His creative approach and data-driven decisions have boosted our market presence.',
                'peer_feedback' => 'Creative and analytical. Great at collaborating across departments.',
                'self_assessment' => 'This year I focused on digital marketing transformation and it has paid off with increased ROI on all campaigns.',
                'areas_of_strength' => 'Digital marketing, Analytics, Creativity, Cross-functional collaboration',
                'areas_for_improvement' => 'Budget management, Vendor negotiation',
                'development_goals' => 'Complete digital marketing certification, Lead international expansion marketing',
                'key_achievements' => json_encode([
                    'Increased lead generation by 150%',
                    'Launched successful social media campaigns',
                    'Improved conversion rate by 40%',
                    'Managed budget of $200K+'
                ]),
                'skills_demonstrated' => json_encode([
                    'Digital marketing strategy',
                    'Campaign management',
                    'Data analysis',
                    'Team leadership'
                ]),
                'supporting_documents' => json_encode([
                    'marketing_campaign_results.pdf',
                    'roi_analysis_2022.pdf'
                ]),
                'reviewed_by' => 'Ali Hassan',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Ayesha Iqbal Chaudhry
            [
                'employee_id' => 4,
                'review_period' => 'Q4 2022',
                'review_date' => '2023-01-30',
                'goal_completion_rate' => 85.00,
                'overall_rating' => 3.8,
                'manager_feedback' => 'Ayesha has shown great improvement since joining. Her attention to detail and eagerness to learn are commendable.',
                'peer_feedback' => 'Hardworking and detail-oriented. Always meets deadlines and produces accurate work.',
                'self_assessment' => 'I have learned a lot in my first few months. I am becoming more confident in my role and understanding our financial systems better.',
                'areas_of_strength' => 'Attention to detail, Reliability, Learning ability, Accuracy',
                'areas_for_improvement' => 'Confidence in presentations, Advanced Excel skills, Financial analysis',
                'development_goals' => 'Complete advanced accounting course, Take on more analytical projects',
                'key_achievements' => json_encode([
                    'Completed month-end closings independently',
                    'Improved accounts reconciliation process',
                    'Completed QuickBooks certification'
                ]),
                'skills_demonstrated' => json_encode([
                    'Accounting fundamentals',
                    'Process improvement',
                    'Software proficiency',
                    'Attention to detail'
                ]),
                'supporting_documents' => json_encode([
                    'quickbooks_certification.pdf',
                    'process_improvement_doc.pdf'
                ]),
                'reviewed_by' => 'Tariq Mahmood',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Zain Abbas Bukhari
            [
                'employee_id' => 5,
                'review_period' => 'Q2 2023',
                'review_date' => '2023-07-30',
                'goal_completion_rate' => 78.00,
                'overall_rating' => 3.2,
                'manager_feedback' => 'Zain has potential but needs to improve consistency and time management. Customer feedback has been mixed.',
                'peer_feedback' => 'Friendly and helpful when available. Sometimes seems overwhelmed with tasks.',
                'self_assessment' => 'I am still learning the ropes. Some days are better than others, but I am trying to improve my response times.',
                'areas_of_strength' => 'Customer empathy, Product knowledge, Willingness to help',
                'areas_for_improvement' => 'Response time, Consistency, Time management, Professional communication',
                'development_goals' => 'Complete customer service training, Improve response time by 50%',
                'key_achievements' => json_encode([
                    'Completed initial training program',
                    'Handled 200+ customer inquiries',
                    'Learned multiple product lines'
                ]),
                'skills_demonstrated' => json_encode([
                    'Customer service',
                    'Product knowledge',
                    'Problem-solving'
                ]),
                'supporting_documents' => json_encode([
                    'training_completion_certificate.pdf'
                ]),
                'reviewed_by' => 'Sana Malik',
                'status' => 'completed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('performance_reviews')->insert($performanceReviews);
    }
}
