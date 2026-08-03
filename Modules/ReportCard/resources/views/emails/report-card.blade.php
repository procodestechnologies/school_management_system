<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: sans-serif; color: #1f2937;">
    <p>Dear Parent/Guardian,</p>

    <p>
        Please find attached {{ $student->name }}'s report card for {{ $reportCard->term }} at
        {{ $institution->name }}.
    </p>

    <p>Thank you for your continued partnership in your child's education.</p>

    <p>Regards,<br>
        {{ $institution->name }}</p>
</body>

</html>
