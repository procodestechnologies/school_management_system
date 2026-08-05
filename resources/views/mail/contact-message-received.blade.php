<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>New contact message</title>
</head>

<body style="font-family: sans-serif; color: #18181b; margin: 0; padding: 24px; background: #fafafa;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 12px; padding: 32px;">
        <p style="margin: 0 0 4px; font-size: 13px; font-weight: 600; color: #4f46e5; text-transform: uppercase; letter-spacing: .04em;">
            {{ config('contact.topics')[$contactMessage->topic] ?? $contactMessage->topic }}
        </p>
        <h1 style="margin: 0 0 24px; font-size: 20px;">New message from {{ config('app.name') }}</h1>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 6px 0; color: #71717a; font-size: 13px; width: 90px;">Name</td>
                <td style="padding: 6px 0; font-size: 14px;">{{ $contactMessage->name }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #71717a; font-size: 13px;">Email</td>
                <td style="padding: 6px 0; font-size: 14px;">{{ $contactMessage->email }}</td>
            </tr>
            @if ($contactMessage->phone)
                <tr>
                    <td style="padding: 6px 0; color: #71717a; font-size: 13px;">Phone</td>
                    <td style="padding: 6px 0; font-size: 14px;">{{ $contactMessage->phone }}</td>
                </tr>
            @endif
        </table>

        <p style="margin: 0 0 8px; font-size: 13px; color: #71717a;">Message</p>
        <p style="margin: 0; font-size: 14px; line-height: 1.6; white-space: pre-line;">{{ $contactMessage->message }}</p>
    </div>
</body>

</html>
