<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: sans-serif; color: #1f2937;">
    <p>Dear Parent/Guardian,</p>

    <p>
        {{ $student->name }}'s report card for {{ $reportCard->term }} at
        {{ $institution->name }} is ready.
    </p>

    <p style="margin: 24px 0;">
        <a href="{{ $downloadUrl }}"
            style="background-color: #4f46e5; color: #ffffff; padding: 12px 20px; border-radius: 6px; text-decoration: none; display: inline-block;">
            Download report card
        </a>
    </p>

    <p style="font-size: 13px; color: #6b7280;">
        For security, this link works only once. If you need another copy after downloading it, please contact the
        school.
    </p>

    <p>Thank you for your continued partnership in your child's education.</p>

    <p>Regards,<br>
        {{ $institution->name }}</p>
</body>

</html>
