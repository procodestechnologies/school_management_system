<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: sans-serif; color: #1f2937;">
    <p>Dear {{ $parent->name }},</p>

    <p>This is a reminder that the following fee(s) still have an outstanding balance:</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #d1d5db; text-align: left;">Student</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Fee</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Amount</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Paid</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Balance</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Due Date</th>
                <th style="border: 1px solid #d1d5db; text-align: left;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fees as $fee)
                <tr>
                    <td style="border: 1px solid #d1d5db;">{{ $fee->student?->name }}</td>
                    <td style="border: 1px solid #d1d5db;">{{ $fee->title }}</td>
                    <td style="border: 1px solid #d1d5db;">{{ number_format($fee->amount, 2) }}</td>
                    <td style="border: 1px solid #d1d5db;">{{ number_format($fee->amount_paid, 2) }}</td>
                    <td style="border: 1px solid #d1d5db;">{{ number_format($fee->balance, 2) }}</td>
                    <td style="border: 1px solid #d1d5db;">
                        {{ $fee->due_date?->format('d M Y') ?? '—' }}</td>
                    <td style="border: 1px solid #d1d5db; text-transform: capitalize;">{{ $fee->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Please arrange payment at your earliest convenience. If you've already paid, kindly disregard this
        reminder.</p>

    <p>Thank you.</p>
</body>

</html>
