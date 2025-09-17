<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Warning Notice</title>
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
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .warning-badge {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .severity-critical { background-color: #721c24; }
        .severity-major { background-color: #dc3545; }
        .severity-moderate { background-color: #fd7e14; }
        .severity-minor { background-color: #ffc107; color: #000; }

        h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .warning-number {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
            font-weight: bold;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            background-color: #f8f9fa;
        }
        .section h3 {
            margin-top: 0;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            margin: 10px 0;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        .detail-value {
            flex: 1;
        }
        .description-box {
            background-color: #fff;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin: 10px 0;
        }
        .important-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .signature-section {
            margin: 30px 0;
            border: 2px solid #dee2e6;
            padding: 20px;
            background-color: #fff;
        }
        .signature-box {
            border: 1px solid #ccc;
            height: 60px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
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
        <div class="warning-badge severity-{{ $warning->severity_level }}">
            {{ strtoupper($warning->severity_level) }} WARNING
        </div>
        <h1>Employee Warning Notice</h1>
        <p><strong>{{ config('app.name') }}</strong></p>
    </div>

    <!-- Warning Number -->
    <div class="warning-number">
        Warning #{{ $warning->warning_number }} - {{ $warning->employee->full_name }}
    </div>

    <!-- Employee Information -->
    <div class="section">
        <h3>Employee Information</h3>
        <div class="detail-row">
            <span class="detail-label">Employee ID:</span>
            <span class="detail-value">{{ $warning->employee->employee_id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Full Name:</span>
            <span class="detail-value">{{ $warning->employee->full_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Department:</span>
            <span class="detail-value">{{ $warning->employee->employmentHistory->current_department ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Position:</span>
            <span class="detail-value">{{ $warning->employee->employmentHistory->current_role ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Warning Details -->
    <div class="section">
        <h3>Warning Details</h3>
        <div class="detail-row">
            <span class="detail-label">Warning Type:</span>
            <span class="detail-value">{{ ucwords(str_replace('_', ' ', $warning->warning_type)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Severity Level:</span>
            <span class="detail-value">{{ ucfirst($warning->severity_level) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Incident Date:</span>
            <span class="detail-value">{{ $warning->incident_date->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Warning Date:</span>
            <span class="detail-value">{{ $warning->warning_date->format('F j, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Issued By:</span>
            <span class="detail-value">{{ $warning->issued_by }}</span>
        </div>
    </div>

    <!-- Subject and Description -->
    <div class="section">
        <h3>Subject</h3>
        <div class="description-box">
            <strong>{{ $warning->subject }}</strong>
        </div>

        <h3>Incident Description</h3>
        <div class="description-box">
            {!! nl2br(e($warning->description)) !!}
        </div>

        @if($warning->incident_location)
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $warning->incident_location }}</span>
            </div>
        @endif

        @if($warning->witnesses)
            <h4>Witnesses:</h4>
            <div class="description-box">
                {!! nl2br(e($warning->witnesses)) !!}
            </div>
        @endif
    </div>

    <!-- Previous Discussions -->
    @if($warning->previous_discussions)
        <div class="section">
            <h3>Previous Discussions</h3>
            <div class="description-box">
                {!! nl2br(e($warning->previous_discussions)) !!}
            </div>
        </div>
    @endif

    <!-- Expected Improvement -->
    <div class="section">
        <h3>Expected Improvement</h3>
        <div class="description-box">
            {!! nl2br(e($warning->expected_improvement)) !!}
        </div>
    </div>

    <!-- Consequences -->
    <div class="section">
        <h3>Consequences if Behavior Continues</h3>
        <div class="description-box">
            {!! nl2br(e($warning->consequences_if_repeated)) !!}
        </div>
    </div>

    <!-- Follow-up Date -->
    @if($warning->follow_up_date)
        <div class="important-notice">
            <strong>Follow-up Meeting Scheduled:</strong> {{ $warning->follow_up_date->format('F j, Y') }}
            <br>
            Please ensure you are prepared to discuss your progress on the expected improvements.
        </div>
    @endif

    <!-- Employee Acknowledgment Section -->

    <!-- HR Notes (if any) -->
    @if($warning->hr_notes)
        <div class="section">
            <h3>HR Notes</h3>
            <div class="description-box">
                {!! nl2br(e($warning->hr_notes)) !!}
            </div>
        </div>
    @endif

    <!-- Important Notice -->
    <div class="important-notice">
        <strong>Important:</strong> This warning will be placed in your personnel file.
        You have the right to provide a written response to this warning, which will also
        be placed in your file. Please contact HR within 5 business days if you wish to
        provide a response or discuss this matter further.
    </div>
</div>
</body>
</html>
