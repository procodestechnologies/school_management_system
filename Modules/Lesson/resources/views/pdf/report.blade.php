<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .letterhead {
            width: 100%;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .letterhead td {
            vertical-align: middle;
        }

        .letterhead img {
            height: 56px;
        }

        .institution-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .institution-meta {
            font-size: 10px;
            color: #4b5563;
            margin: 2px 0 0 0;
        }

        .report-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 10px 0 16px 0;
            text-transform: uppercase;
        }

        .class-info {
            width: 100%;
            margin-bottom: 16px;
        }

        .class-info td {
            padding: 2px 0;
            font-size: 12px;
        }

        .class-info .label {
            font-weight: bold;
            width: 100px;
        }

        table.summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.summary th,
        table.summary td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 11px;
            text-align: center;
        }

        table.summary th {
            background-color: #f3f4f6;
        }

        .day-heading {
            font-size: 12px;
            font-weight: bold;
            margin: 14px 0 6px 0;
        }

        table.periods {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.periods th,
        table.periods td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            font-size: 10px;
            text-align: left;
        }

        table.periods th {
            background-color: #f3f4f6;
        }

        .status-attended {
            color: #059669;
            font-weight: bold;
        }

        .status-recovered {
            color: #b45309;
            font-weight: bold;
        }

        .status-not_attended {
            color: #dc2626;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 24px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="letterhead">
        <tr>
            <td style="width: 70px;">
                @if ($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="{{ $institution->name }}">
                @endif
            </td>
            <td>
                <p class="institution-name">{{ $institution->name }}</p>
                <p class="institution-meta">
                    @if ($institution->code)
                        Code: {{ $institution->code }} &nbsp;|&nbsp;
                    @endif
                    @if ($institution->phone)
                        Tel: {{ $institution->phone }} &nbsp;|&nbsp;
                    @endif
                    @if ($institution->email)
                        {{ $institution->email }}
                    @endif
                </p>
            </td>
        </tr>
    </table>

    <p class="report-title">{{ $report->isWeekly() ? 'Weekly' : 'Daily' }} Lesson Attendance Report</p>

    <table class="class-info">
        <tr>
            <td class="label">Class:</td>
            <td>{{ $report->schoolClass->name }}</td>
            <td class="label">Period:</td>
            <td>
                {{ $report->period_start->format('d M Y') }}
                @if ($report->isWeekly())
                    &ndash; {{ $report->period_end->format('d M Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Generated:</td>
            <td>{{ now()->format('d M Y') }}</td>
        </tr>
    </table>

    <table class="summary">
        <thead>
            <tr>
                <th>Total Lessons</th>
                <th>Attended</th>
                <th>Not Attended</th>
                <th>Recovered</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->total_lessons }}</td>
                <td>{{ $report->attended_count }}</td>
                <td>{{ $report->not_attended_count }}</td>
                <td>{{ $report->recovered_count }}</td>
            </tr>
        </tbody>
    </table>

    @foreach ($days as $day)
        <p class="day-heading">{{ $day['date']->format('l, d M Y') }}</p>
        <table class="periods">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($day['periods'] as $period)
                    <tr>
                        <td>{{ $period['entry']->start_time?->format('H:i') }}&ndash;{{ $period['entry']->end_time?->format('H:i') }}</td>
                        <td>{{ $period['entry']->subject }}</td>
                        <td>{{ $period['entry']->teacher?->name ?? '—' }}</td>
                        <td class="status-{{ $period['status'] }}">
                            {{ $period['lesson']?->statusLabel() ?? 'Not Attended' }}
                        </td>
                        <td>{{ $period['lesson']?->remarks ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <p class="footer-note">This report was generated automatically by {{ $institution->name }}'s school management
        system.</p>
</body>

</html>
