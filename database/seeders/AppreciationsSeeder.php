<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppreciationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appreciations = [
            // Ahmed Hassan Khan - Multiple appreciations
            [
                'employee_id' => 1,
                'appreciation_number' => 1,
                'appreciation_type' => 'quarterly_award',
                'category' => 'exceptional_performance',
                'achievement_date' => '2023-06-30',
                'recognition_date' => '2023-07-15',
                'nominated_by' => 'Sarah Ahmed',
                'approved_by' => 'CEO',
                'title' => 'Outstanding Developer of Q2 2023',
                'description' => 'Ahmed led the successful migration of our legacy system to a modern architecture, completing the project 2 weeks ahead of schedule and significantly improving system performance.',
                'impact_description' => 'The migration resulted in 30% reduction in system downtime, 40% improvement in response times, and saved the company approximately $50,000 in maintenance costs.',
                'recognition_value' => 25000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'Junior Developer - Ali Raza',
                    'QA Engineer - Fatima Shah',
                    'DevOps Engineer - Hassan Ali'
                ]),
                'skills_demonstrated' => json_encode([
                    'Technical Leadership',
                    'Project Management',
                    'System Architecture',
                    'Team Collaboration'
                ]),
                'achievement_metrics' => json_encode([
                    'Project completed 2 weeks early',
                    '30% reduction in downtime',
                    '40% performance improvement',
                    '$50,000 cost savings'
                ]),
                'peer_nominations' => json_encode([
                    'Technical excellence and leadership',
                    'Always willing to help team members',
                    'Great mentor and problem solver'
                ]),
                'employee_response' => 'I am honored to receive this recognition. This achievement was only possible due to the excellent teamwork and support from my colleagues.',
                'hr_notes' => 'Exceptional performance and leadership. Great role model for other developers.',
                'status' => 'published',
                'publication_date' => '2023-07-20',
                'supporting_documents' => json_encode([
                    'project_completion_report.pdf',
                    'performance_metrics.pdf',
                    'team_feedback.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 1,
                'appreciation_number' => 2,
                'appreciation_type' => 'peer_nomination',
                'category' => 'mentoring',
                'achievement_date' => '2023-03-15',
                'recognition_date' => '2023-03-20',
                'nominated_by' => 'Ali Raza',
                'approved_by' => 'Sarah Ahmed',
                'title' => 'Exceptional Mentor and Guide',
                'description' => 'Ahmed has been an outstanding mentor to junior developers, helping them grow their skills and confidence. His patient guidance and knowledge sharing have been invaluable.',
                'impact_description' => 'Under Ahmed\'s mentorship, junior developers have shown 50% faster skill development and increased confidence in handling complex tasks.',
                'recognition_value' => 10000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'Junior Developer - Ali Raza',
                    'Junior Developer - Zara Khan'
                ]),
                'skills_demonstrated' => json_encode([
                    'Mentoring',
                    'Knowledge Transfer',
                    'Leadership',
                    'Communication'
                ]),
                'achievement_metrics' => json_encode([
                    'Mentored 3 junior developers',
                    '50% faster skill development in mentees',
                    'Conducted 20+ knowledge sharing sessions'
                ]),
                'peer_nominations' => json_encode([
                    'Always available to help',
                    'Explains complex concepts clearly',
                    'Patient and encouraging'
                ]),
                'employee_response' => 'Thank you for this recognition. I believe in growing together as a team and I\'m happy to contribute to our collective success.',
                'hr_notes' => 'Great mentoring skills. Consider for leadership development program.',
                'status' => 'published',
                'publication_date' => '2023-03-25',
                'supporting_documents' => json_encode([
                    'mentee_feedback.pdf',
                    'skill_development_metrics.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Fatima Ali Sheikh - HR Excellence
            [
                'employee_id' => 2,
                'appreciation_number' => 1,
                'appreciation_type' => 'annual_award',
                'category' => 'leadership',
                'achievement_date' => '2022-12-31',
                'recognition_date' => '2023-01-15',
                'nominated_by' => 'Nadia Khan',
                'approved_by' => 'CEO',
                'title' => 'HR Leader of the Year 2022',
                'description' => 'Fatima has transformed our HR processes and significantly improved employee satisfaction. Her strategic initiatives have made our workplace more inclusive and efficient.',
                'impact_description' => 'Employee satisfaction increased by 25%, hiring time reduced by 40%, and turnover reduced by 30% under her leadership.',
                'recognition_value' => 50000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'HR Assistant - Sara Ahmed',
                    'Recruiter - Hassan Malik'
                ]),
                'skills_demonstrated' => json_encode([
                    'Strategic HR Management',
                    'Change Leadership',
                    'Employee Relations',
                    'Process Improvement'
                ]),
                'achievement_metrics' => json_encode([
                    '25% increase in employee satisfaction',
                    '40% reduction in hiring time',
                    '30% reduction in turnover',
                    'Implemented 5 new HR policies'
                ]),
                'peer_nominations' => json_encode([
                    'Visionary leadership',
                    'Employee-focused approach',
                    'Excellent communication and empathy'
                ]),
                'employee_response' => 'This recognition belongs to the entire HR team. Together, we have created a better workplace for everyone.',
                'hr_notes' => 'Outstanding leadership and strategic thinking. Key contributor to company culture.',
                'status' => 'published',
                'publication_date' => '2023-01-20',
                'supporting_documents' => json_encode([
                    'employee_satisfaction_survey.pdf',
                    'hr_metrics_2022.pdf',
                    'policy_implementation_report.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'employee_id' => 2,
                'appreciation_number' => 2,
                'appreciation_type' => 'spot_recognition',
                'category' => 'cultural_values',
                'achievement_date' => '2023-08-15',
                'recognition_date' => '2023-08-18',
                'nominated_by' => 'Multiple Employees',
                'approved_by' => 'Nadia Khan',
                'title' => 'Champion of Diversity and Inclusion',
                'description' => 'Fatima organized and led the company\'s first Diversity and Inclusion week, creating awareness and fostering an inclusive environment for all employees.',
                'impact_description' => 'The D&I initiative received 95% positive feedback from employees and has been adopted as an annual company tradition.',
                'recognition_value' => 15000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'Employee volunteers from all departments'
                ]),
                'skills_demonstrated' => json_encode([
                    'Event Management',
                    'Cultural Awareness',
                    'Community Building',
                    'Initiative Taking'
                ]),
                'achievement_metrics' => json_encode([
                    '95% positive employee feedback',
                    '100% employee participation',
                    '5 awareness sessions conducted',
                    'New D&I policy framework created'
                ]),
                'peer_nominations' => json_encode([
                    'Created inclusive environment',
                    'Organized meaningful events',
                    'Promoted understanding and respect'
                ]),
                'employee_response' => 'Diversity and inclusion are close to my heart. I\'m glad we could create such a positive impact together.',
                'hr_notes' => 'Excellent initiative that has become a company tradition. Great cultural impact.',
                'status' => 'published',
                'publication_date' => '2023-08-22',
                'supporting_documents' => json_encode([
                    'di_week_feedback.pdf',
                    'event_photos.pdf',
                    'policy_framework.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Muhammad Usman Malik - Marketing Excellence
            [
                'employee_id' => 3,
                'appreciation_number' => 1,
                'appreciation_type' => 'quarterly_award',
                'category' => 'innovation',
                'achievement_date' => '2023-03-31',
                'recognition_date' => '2023-04-10',
                'nominated_by' => 'Ali Hassan',
                'approved_by' => 'CMO',
                'title' => 'Digital Marketing Innovator Q1 2023',
                'description' => 'Usman developed and executed a groundbreaking social media campaign that went viral, significantly increasing brand awareness and lead generation.',
                'impact_description' => 'The campaign generated 200% more leads than expected, increased social media followers by 150%, and achieved an ROI of 400%.',
                'recognition_value' => 30000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'Graphic Designer - Sana Ali',
                    'Content Writer - Zain Ahmad'
                ]),
                'skills_demonstrated' => json_encode([
                    'Creative Strategy',
                    'Digital Marketing',
                    'Campaign Management',
                    'Data Analysis'
                ]),
                'achievement_metrics' => json_encode([
                    '200% increase in lead generation',
                    '150% growth in social media followers',
                    '400% ROI on campaign investment',
                    '2M+ campaign impressions'
                ]),
                'peer_nominations' => json_encode([
                    'Creative and innovative approach',
                    'Data-driven decision making',
                    'Excellent team collaboration'
                ]),
                'employee_response' => 'Innovation comes from teamwork. This success belongs to the entire marketing team who supported this creative vision.',
                'hr_notes' => 'Exceptional creative thinking and execution. Consider for advanced marketing training.',
                'status' => 'published',
                'publication_date' => '2023-04-15',
                'supporting_documents' => json_encode([
                    'campaign_performance_report.pdf',
                    'social_media_analytics.pdf',
                    'roi_analysis.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Ayesha Iqbal Chaudhry - Rising Star
            [
                'employee_id' => 4,
                'appreciation_number' => 1,
                'appreciation_type' => 'milestone_celebration',
                'category' => 'continuous_improvement',
                'achievement_date' => '2023-05-10',
                'recognition_date' => '2023-05-15',
                'nominated_by' => 'Tariq Mahmood',
                'approved_by' => 'CFO',
                'title' => 'First Anniversary Excellence Award',
                'description' => 'Ayesha has shown remarkable growth and dedication in her first year. She has streamlined several accounting processes and shown great initiative in learning new skills.',
                'impact_description' => 'Her process improvements have reduced month-end closing time by 25% and eliminated several manual errors in financial reporting.',
                'recognition_value' => 12000.00,
                'public_recognition' => true,
                'team_members_involved' => json_encode([
                    'Senior Accountant - Rizwan Ali'
                ]),
                'skills_demonstrated' => json_encode([
                    'Process Improvement',
                    'Attention to Detail',
                    'Learning Agility',
                    'Initiative Taking'
                ]),
                'achievement_metrics' => json_encode([
                    '25% reduction in month-end closing time',
                    '100% accuracy in assigned reports',
                    '3 process improvements implemented',
                    '2 professional certifications completed'
                ]),
                'peer_nominations' => json_encode([
                    'Quick learner and reliable',
                    'Always willing to take on new challenges',
                    'Brings fresh perspective to the team'
                ]),
                'employee_response' => 'Thank you for this encouragement. I look forward to continuing to grow and contribute to the finance team.',
                'hr_notes' => 'Great potential and growth mindset. Consider for advanced finance training programs.',
                'status' => 'published',
                'publication_date' => '2023-05-18',
                'supporting_documents' => json_encode([
                    'process_improvement_documentation.pdf',
                    'performance_metrics_year1.pdf',
                    'certification_achievements.pdf'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('appreciations')->insert($appreciations);
    }
}
