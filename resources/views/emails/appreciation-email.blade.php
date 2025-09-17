<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Recognition Certificate</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .recognition-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        .category-exceptional_performance { background: linear-gradient(135deg, #28a745, #20c997); }
        .category-innovation { background: linear-gradient(135deg, #17a2b8, #20c997); }
        .category-leadership { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .category-teamwork { background: linear-gradient(135deg, #6f42c1, #007bff); }
        .category-customer_service { background: linear-gradient(135deg, #28a745, #20c997); }
        .category-problem_solving { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
        .category-mentoring { background: linear-gradient(135deg, #fd7e14, #ffc107); }
        .category-milestone_achievement { background: linear-gradient(135deg, #dc3545, #e83e8c); }

        h1 {
            color: #28a745;
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .recognition-number {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
            font-weight: bold;
            border-radius: 4px;
        }
        .congratulations {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            padding: 20px;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #f39c12;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: #856404;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
        }
        .section h3 {
            margin-top: 0;
            color: #495057;
            border-bottom: 2px solid #28a745;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .section h3::before {
            content: '🏆';
            margin-right: 8px;
            font-size: 20px;
        }
        .detail-row {
            display: flex;
            margin: 12px 0;
            align-items: flex-start;
        }
        .detail-label {
            font-weight: bold;
            width: 180px;
            color: #495057;
            flex-shrink: 0;
        }
        .detail-value {
            flex: 1;
        }
        .description-box {
            background: #fff;
            padding: 18px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            margin: 15px 0;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .achievement-highlight {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border: 2px solid #2196f3;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            position: relative;
        }
        .achievement-highlight::before {
            content: '⭐';
            position: absolute;
            top: -15px;
            left: 20px;
            background: white;
            padding: 5px 10px;
            font-size: 24px;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .achievement-highlight h3 {
            color: #1976d2;
            margin-top: 10px;
        }
        .skills-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }
        .skill-tag {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .metric-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
            text-align: center;
        }
        .metric-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .recognition-value {
            background: linear-gradient(135deg, #fff3cd, #d4edda);
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .recognition-value .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            display: block;
        }
        .signature-section {
            margin: 40px 0;
            border: 2px solid #28a745;
            padding: 25px;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 8px;
        }
        .signature-box {
            border: 1px solid #ccc;
            height: 80px;
            margin: 15px 0;
            background: #f8f9fa;
            border-radius: 4px;
            position: relative;
        }
        .signature-box::after {
            content: 'Signature';
            position: absolute;
            bottom: 5px;
            left: 10px;
            color: #6c757d;
            font-size: 12px;
        }
        .certificate-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #28a745;
            text-align: center;
            position: relative;
        }
        .certificate-footer::before {
            content: '🎖️';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 5px 15px;
            font-size: 30px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .team-members {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }
        .team-member {
            background: #e3f2fd;
            color: #1565c0;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
            border: 1px solid #bbdefb;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
            .metrics-grid {
                grid-template-columns: 1fr;
            }
            .skills-tags, .team-members {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="header">
        {{-- Add your company logo here --}}
        {{-- <img src="{{ asset('images/company-logo.png') }}" alt="Company Logo" class="company-logo"> --}}
        <div class="recognition-badge category-{{ $appreciation->category }}">
            {{ strtoupper(str_replace('_', ' ', $appreciation->category)) }}
        </div>
        <h1>🏆 Employee Recognition Certificate 🏆</h1>
        <p style="font-size: 18px; color: #6c757d;"><strong>{{ config('app.name') }}</strong></p>
    </div>

    <!-- Recognition Number -->
    <div class="recognition-number">
        Recognition #{{ $appreciation->appreciation_number }} - {{ $appreciation->employee->full_name }}
    </div>

    <!-- Congratulations Message -->
    <div class="congratulations">
        🎉 CONGRATULATIONS! 🎉
        <br>
        You have been recognized for your outstanding contribution and exceptional performance!
    </div>

    <!-- Employee Information -->
    <div class="section">
        <h3>Employee Information</h3>
        <div class="detail-row">
            <span class="detail-label">Employee ID:</span>
            <span class="detail-value">{{ $appreciation->employee->employee_id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Full Name:</span>
            <span class="detail-value">{{ $appreciation->employee->full_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Department:</span>
            <span class="detail-value">{{ $appreciation->employee->employmentHistory->current_department ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Position:</span>
            <span class="detail-value">{{ $appreciation->employee->employmentHistory->current_role ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Recognition Details -->
    <div class="section">
        <h3>Recognition Details</h3>
        <div class="detail-row">
            <span class="detail-label">Recognition Type:</span>
            <span class="detail-value">{{ ucwords(str_replace('_', ' ', $appreciation->appreciation_type)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Category:</span>
            <span class="detail-value">{{ ucwords(str_replace('_', ' ', $appreciation->category)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Achievement Date:</span>
            <span class="detail-value">{{ $appreciation->achievement_date->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Recognition Date:</span>
            <span class="detail-value">{{ $appreciation->recognition_date->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Nominated By:</span>
            <span class="detail-value">{{ $appreciation->nominated_by }}</span>
        </div>
        @if($appreciation->approved_by)
            <div class="detail-row">
                <span class="detail-label">Approved By:</span>
                <span class="detail-value">{{ $appreciation->approved_by }}</span>
            </div>
        @endif
    </div>

    <!-- Achievement Highlight -->
    <div class="achievement-highlight">
        <h3>{{ $appreciation->title }}</h3>
        <div class="description-box">
            {!! nl2br(e($appreciation->description)) !!}
        </div>
    </div>

    <!-- Business Impact -->
    <div class="section">
        <h3>Business Impact</h3>
        <div class="description-box">
            {!! nl2br(e($appreciation->impact_description)) !!}
        </div>
    </div>

    <!-- Achievement Metrics -->
    @if($appreciation->achievement_metrics && count($appreciation->achievement_metrics) > 0)
        <div class="section">
            <h3>Quantifiable Achievements</h3>
            <div class="metrics-grid">
                @foreach($appreciation->achievement_metrics as $metric => $value)
                    <div class="metric-item">
                        <div class="metric-label">{{ $metric }}</div>
                        <div class="metric-value">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Skills Demonstrated -->
    @if($appreciation->skills_demonstrated && count($appreciation->skills_demonstrated) > 0)
        <div class="section">
            <h3>Skills Demonstrated</h3>
            <div class="skills-tags">
                @foreach($appreciation->skills_demonstrated as $skill)
                    <span class="skill-tag">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Team Members Involved -->
    @if($appreciation->team_members_involved && count($appreciation->team_members_involved) > 0)
        <div class="section">
            <h3>Team Collaboration</h3>
            <p style="margin-bottom: 15px;">This achievement was accomplished in collaboration with:</p>
            <div class="team-members">
                @foreach($appreciation->team_members_involved as $member)
                    <span class="team-member">{{ $member }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Peer Nominations -->
    @if($appreciation->peer_nominations && count($appreciation->peer_nominations) > 0)
        <div class="section">
            <h3>Peer Nominations</h3>
            @foreach($appreciation->peer_nominations as $nomination)
                <div class="description-box" style="margin-bottom: 15px;">
                    <strong>{{ $nomination['nominator_name'] }}</strong>
                    <small>({{ ucwords(str_replace('_', ' ', $nomination['relationship'])) }})</small>
                    <p style="margin: 10px 0 0 0; font-style: italic;">"{{ $nomination['nomination_text'] }}"</p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Recognition Value -->
    @if($appreciation->recognition_value)
        <div class="recognition-value">
            <div style="font-size: 16px; color: #495057; margin-bottom: 10px;">Recognition Award</div>
            <span class="amount">${{ number_format($appreciation->recognition_value, 2) }}</span>
            <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                This monetary recognition will be processed with your next payroll.
            </div>
        </div>
    @endif

    <!-- Employee Response Section -->



</div>
</body>
</html>
