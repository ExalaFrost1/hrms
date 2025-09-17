<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Improvement Plan</title>
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
            border-bottom: 3px solid #ffc107;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .pip-badge {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #000;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }
        .severity-low { background: linear-gradient(135deg, #17a2b8, #20c997); color: white; }
        .severity-moderate { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .severity-high { background: linear-gradient(135deg, #fd7e14, #dc3545); color: white; }
        .severity-critical { background: linear-gradient(135deg, #dc3545, #721c24); color: white; }

        h1 {
            color: #856404;
            margin: 0;
            font-size: 28px;
            font-weight: 400;
        }
        .pip-number {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            font-weight: bold;
            border-radius: 4px;
        }
        .important-notice {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
            font-weight: 600;
            color: #721c24;
        }
        .timeline-alert {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            border: 2px solid #17a2b8;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
            font-weight: 600;
            color: #0c5460;
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
            border-bottom: 2px solid #ffc107;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .section h3::before {
            content: '📋';
            margin-right: 8px;
            font-size: 20px;
        }
        .objectives-section h3::before { content: '🎯'; }
        .support-section h3::before { content: '🤝'; }
        .consequences-section h3::before { content: '⚠️'; }

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
        .objectives-list {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        .objective-item, .metric-item, .action-item {
            background: #fff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .objective-item h4, .metric-item h4, .action-item h4 {
            margin: 0 0 8px 0;
            color: #495057;
        }
        .milestone-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .milestone-item {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #ffc107;
        }
        .milestone-date {
            font-weight: bold;
            color: #856404;
            font-size: 14px;
        }
        .training-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .training-item {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #28a745;
        }
        .training-priority {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: black; }
        .priority-low { background: #28a745; color: white; }

        .signature-section {
            margin: 40px 0;
            border: 2px solid #ffc107;
            padding: 25px;
            background: linear-gradient(135deg, #ffffff, #fffbf0);
            border-radius: 8px;
        }
        .signature-box {
            border: 2px solid #ffc107;
            height: 80px;
            margin: 15px 0;
            background: #fff;
            border-radius: 4px;
            position: relative;
        }
        .signature-box::after {
            content: 'Employee Signature';
            position: absolute;
            bottom: 5px;
            left: 10px;
            color: #6c757d;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .progress-tracker {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            transition: width 0.3s ease;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
            .milestone-grid, .training-grid {
                grid-template-columns: 1fr;
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
        <div class="pip-badge severity-{{ $pip->severity_level }}">
            {{ strtoupper($pip->severity_level) }} PRIORITY PIP
        </div>
        <h1>📋 Performance Improvement Plan 📋</h1>
        <p style="font-size: 18px; color: #6c757d;"><strong>{{ config('app.name') }}</strong></p>
    </div>

    <!-- PIP Number -->
    <div class="pip-number">
        PIP #{{ $pip->pip_number }} - {{ $pip->employee->full_name }}
    </div>

    <!-- Important Notice -->
    <div class="important-notice">
        ⚠️ FORMAL PERFORMANCE IMPROVEMENT PLAN ⚠️
        <br>
        This document outlines specific performance expectations and improvement requirements.
        Your full cooperation and commitment to this plan are essential.
    </div>

    <!-- Timeline Alert -->
    <div class="timeline-alert">
        🗓️ PIP DURATION: {{ $pip->start_date->format('F j, Y') }} to {{ $pip->end_date->format('F j, Y') }}
        <br>
        Total Duration: {{ $pip->start_date->diffInDays($pip->end_date) }} days
        @if($pip->status === 'active')
            | Days Remaining: {{ $pip->days_remaining }}
        @endif
    </div>

    <!-- Progress Tracker (if active) -->
    @if($pip->status === 'active')
        <div class="progress-tracker">
            <h3 style="margin-top: 0; color: #495057;">PIP Progress Tracker</h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ round($pip->progress_percentage) }}%;"></div>
            </div>
            <p style="margin-bottom: 0;">
                {{ round($pip->progress_percentage) }}% Complete
                @if($pip->days_remaining > 0)
                    | {{ $pip->days_remaining }} days remaining
                @elseif($pip->days_remaining === 0)
                    | Due TODAY
                @else
                    | OVERDUE by {{ abs($pip->days_remaining) }} days
                @endif
            </p>
        </div>
    @endif

    <!-- Employee Information -->
    <div class="section">
        <h3>Employee Information</h3>
        <div class="detail-row">
            <span class="detail-label">Employee ID:</span>
            <span class="detail-value">{{ $pip->employee->employee_id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Full Name:</span>
            <span class="detail-value">{{ $pip->employee->full_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Department:</span>
            <span class="detail-value">{{ $pip->employee->employmentHistory->current_department ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Position:</span>
            <span class="detail-value">{{ $pip->employee->employmentHistory->current_role ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- PIP Details -->
    <div class="section">
        <h3>PIP Details</h3>
        <div class="detail-row">
            <span class="detail-label">PIP Type:</span>
            <span class="detail-value">{{ ucwords(str_replace('_', ' ', $pip->pip_type)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Severity Level:</span>
            <span class="detail-value">{{ ucfirst($pip->severity_level) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Review Frequency:</span>
            <span class="detail-value">{{ ucfirst(str_replace('_', '-', $pip->review_frequency)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Initiated By:</span>
            <span class="detail-value">{{ $pip->initiated_by }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Assigned Supervisor:</span>
            <span class="detail-value">{{ $pip->supervisor_assigned }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">HR Representative:</span>
            <span class="detail-value">{{ $pip->hr_representative }}</span>
        </div>
    </div>

    <!-- PIP Title and Performance Concerns -->
    <div class="section">
        <h3>Performance Issues</h3>
        <h4 style="color: #495057; margin: 15px 0 10px 0;">{{ $pip->title }}</h4>

        <h5 style="color: #6c757d; margin: 15px 0 5px 0;">Performance Concerns:</h5>
        <div class="description-box">
            {!! nl2br(e($pip->performance_concerns)) !!}
        </div>

        @if($pip->root_cause_analysis)
            <h5 style="color: #6c757d; margin: 15px 0 5px 0;">Root Cause Analysis:</h5>
            <div class="description-box">
                {!! nl2br(e($pip->root_cause_analysis)) !!}
            </div>
        @endif
    </div>

    <!-- Specific Objectives -->
    @if($pip->specific_objectives && count($pip->specific_objectives) > 0)
        <div class="section objectives-section">
            <h3>Specific Objectives</h3>
            <div class="objectives-list">
                @foreach($pip->specific_objectives as $index => $objective)
                    <div class="objective-item">
                        <h4>Objective {{ $index + 1 }}: {{ $objective['objective'] }}</h4>
                        <p>{{ $objective['description'] }}</p>
                        @if(isset($objective['target_date']))
                            <small><strong>Target Date:</strong> {{ \Carbon\Carbon::parse($objective['target_date'])->format('F j, Y') }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Success Metrics -->
    @if($pip->success_metrics && count($pip->success_metrics) > 0)
        <div class="section">
            <h3>Success Metrics</h3>
            @foreach($pip->success_metrics as $index => $metric)
                <div class="metric-item">
                    <h4>{{ $metric['metric'] }}</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div><strong>Current:</strong> {{ $metric['current_performance'] ?? 'Baseline' }}</div>
                        <div><strong>Target:</strong> {{ $metric['target_performance'] }}</div>
                        <div><strong>Measured:</strong> {{ ucfirst($metric['measurement_frequency'] ?? 'Weekly') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Required Actions -->
    @if($pip->required_actions && count($pip->required_actions) > 0)
        <div class="section">
            <h3>Required Actions</h3>
            @foreach($pip->required_actions as $index => $action)
                <div class="action-item">
                    <h4>Action {{ $index + 1 }}: {{ $action['action'] }}</h4>
                    <p>{{ $action['description'] }}</p>
                    @if(isset($action['due_date']))
                        <small><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($action['due_date'])->format('F j, Y') }}</small>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Support Provided -->
    <div class="section support-section">
        <h3>Company Support</h3>
        <div class="description-box">
            {!! nl2br(e($pip->support_provided)) !!}
        </div>
    </div>

    <!-- Training Requirements -->
    @if($pip->training_requirements && count($pip->training_requirements) > 0)
        <div class="section">
            <h3>Required Training</h3>
            <div class="training-grid">
                @foreach($pip->training_requirements as $training)
                    <div class="training-item">
                        <h4>{{ $training['training_name'] }}</h4>
                        <p><strong>Provider:</strong> {{ $training['provider'] ?? 'To be determined' }}</p>
                        @if(isset($training['completion_deadline']))
                            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($training['completion_deadline'])->format('F j, Y') }}</p>
                        @endif
                        <span class="training-priority priority-{{ $training['priority'] ?? 'medium' }}">
                        {{ ucfirst($training['priority'] ?? 'medium') }} Priority
                    </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Milestones -->
    @if($pip->milestone_dates && count($pip->milestone_dates) > 0)
        <div class="section">
            <h3>Important Milestones</h3>
            <div class="milestone-grid">
                @foreach($pip->milestone_dates as $milestone)
                    <div class="milestone-item">
                        <h4>{{ $milestone['milestone'] }}</h4>
                        <div class="milestone-date">{{ \Carbon\Carbon::parse($milestone['date'])->format('F j, Y') }}</div>
                        @if(isset($milestone['criteria']))
                            <p style="margin: 8px 0 0 0; font-size: 14px;">{{ $milestone['criteria'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Consequences -->
    <div class="section consequences-section">
        <h3>Consequences of Failure to Improve</h3>
        <div class="description-box" style="border-left-color: #dc3545; background: #fff5f5;">
            {!! nl2br(e($pip->consequences_of_failure)) !!}
        </div>
    </div>

    <!-- Supervisor/HR Notes -->
    @if($pip->supervisor_notes || $pip->hr_notes)
        <div class="section">
            <h3>Additional Notes</h3>
            @if($pip->supervisor_notes)
                <h5 style="color: #6c757d;">Supervisor Notes:</h5>
                <div class="description-box">
                    {!! nl2br(e($pip->supervisor_notes)) !!}
                </div>
            @endif
            @if($pip->hr_notes)
                <h5 style="color: #6c757d;">HR Notes:</h5>
                <div class="description-box">
                    {!! nl2br(e($pip->hr_notes)) !!}
                </div>
            @endif
        </div>
    @endif

    <!-- Important Final Notice -->
    <div class="important-notice">
        <strong>Remember:</strong> This Performance Improvement Plan represents an opportunity
        for professional growth and development. We are committed to providing you with the
        support and resources needed to succeed. Your active participation and commitment
        to improvement are essential for a successful outcome.
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is an official Performance Improvement Plan document from {{ config('app.name') }}.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>PIP Reference: PIP-{{ $pip->id }}-{{ $pip->created_at->format('Ymd') }}</p>
        <p><strong>Questions or concerns?</strong> Contact your HR representative: {{ $pip->hr_representative }}</p>
    </div>
</div>
</body>
</html>
