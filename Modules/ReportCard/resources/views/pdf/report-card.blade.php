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

        .student-info {
            width: 100%;
            margin-bottom: 16px;
        }

        .student-info td {
            padding: 2px 0;
            font-size: 12px;
        }

        .student-info .label {
            font-weight: bold;
            width: 100px;
        }

        .prose {
            margin-bottom: 16px;
            line-height: 1.5;
        }

        table.subjects {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.subjects th,
        table.subjects td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 11px;
            text-align: left;
        }

        table.subjects th {
            background-color: #f3f4f6;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            padding: 4px 0;
        }

        .summary .label {
            font-weight: bold;
        }

        .signature {
            margin-top: 40px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            width: 220px;
            margin-top: 30px;
            padding-top: 4px;
            font-size: 11px;
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
                @if ($institution->physical_address || $institution->postal_address)
                    <p class="institution-meta">
                        {{ $institution->physical_address }}
                        @if ($institution->postal_address)
                            , {{ $institution->postal_address }}
                        @endif
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <p class="report-title">Student Report Card</p>

    <table class="student-info">
        <tr>
            <td class="label">Student:</td>
            <td>{{ $student->name }}</td>
            <td class="label">Class:</td>
            <td>{{ $reportCard->schoolClass?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Term:</td>
            <td>{{ $reportCard->term }}</td>
            <td class="label">Generated:</td>
            <td>{{ now()->format('d M Y') }}</td>
        </tr>
    </table>

    <div class="prose">{!! $openingHtml !!}</div>

    <table class="subjects">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>{{ $result->examination?->subject?->name ?? $result->examination?->subject_name }}</td>
                    <td>{{ $result->marks_obtained }}</td>
                    <td>{{ $result->examination?->total_marks }}</td>
                    <td>
                        @if ($result->examination?->total_marks)
                            {{ number_format(($result->marks_obtained / $result->examination->total_marks) * 100, 2) }}%
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $result->grade ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No results recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="label">Mean Percentage:</td>
            <td>{{ $meanPercentage !== null ? number_format($meanPercentage, 2).'%' : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Mean Grade:</td>
            <td>{{ $meanGrade ?? '—' }}</td>
        </tr>
    </table>

    <div class="prose">{!! $closingHtml !!}</div>

    @if ($signatoryName)
        <div class="signature">
            <div class="signature-line">
                {{ $signatoryName }}
                @if ($signatoryTitle)
                    <br>{{ $signatoryTitle }}
                @endif
            </div>
        </div>
    @endif

    <p class="footer-note text-center">This report card was generated automatically by {{ $institution->name }}'s
        school management system.</p>
</body>

</html>
