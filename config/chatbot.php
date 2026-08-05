<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Verification settings
    |--------------------------------------------------------------------------
    */
    'otp_length' => 6,
    'otp_expiry_minutes' => 10,
    'max_attempts_per_code' => 5,

    // Per admission number: how many codes may be sent in the given window.
    'max_sends_per_admission_number' => 3,
    'send_window_minutes' => 60,

    // Per IP: how many admission-number lookups are allowed in the window,
    // to blunt bulk scanning for valid admission numbers.
    'max_lookups_per_ip' => 5,
    'lookup_window_minutes' => 10,

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    |
    | The fixed set of slash-commands the widget offers. "verified" commands
    | require the admission-number + email-code flow before answering;
    | unverified ones answer immediately from the "answer" string below.
    |
    */
    'commands' => [
        'result' => [
            'label' => 'My child\'s results',
            'description' => 'See the latest exam results',
            'verified' => true,
        ],
        'attendance' => [
            'label' => 'My child\'s attendance',
            'description' => 'See recent attendance records',
            'verified' => true,
        ],
        'fees' => [
            'label' => 'My child\'s fee balance',
            'description' => 'See the outstanding fee balance',
            'verified' => true,
        ],
        'timetable' => [
            'label' => 'My child\'s timetable',
            'description' => 'See the weekly class timetable',
            'verified' => true,
        ],
        'profile' => [
            'label' => 'My child\'s profile',
            'description' => 'See class, teacher and admission details',
            'verified' => true,
        ],
        'help' => [
            'label' => 'How this assistant works',
            'description' => 'A quick guide to using this chat',
            'verified' => false,
            'answer' => "Type a command starting with / to get started, for example /result. If you're asking about your child, you'll be asked for their admission number and we'll email a 6-digit code to the email address we have on file for verification before showing anything. Type / at any time to see the full list of commands again.",
        ],
        'about' => [
            'label' => 'About this platform',
            'description' => 'What this system is',
            'verified' => false,
            'answer' => 'This is a school management platform used by institutions to manage students, staff, results, attendance, fees and more in one place. Each school runs its own private space on the platform.',
        ],
        'contact' => [
            'label' => 'Contact support',
            'description' => 'How to reach us',
            'verified' => false,
            'answer' => 'For anything this assistant can\'t help with, please reach out to your school\'s office directly, or contact our support team at '.(env('MAIL_FROM_ADDRESS', 'support@example.com')).'.',
        ],
        'enroll' => [
            'label' => 'How to get started',
            'description' => 'Enrolling a child or onboarding a school',
            'verified' => false,
            'answer' => 'If you\'d like to enroll your child, please contact your school directly - they manage admissions on the platform. If you\'re looking to bring your own school onto the platform, sign up for an account and create your institution from there; it will be reviewed and activated shortly after.',
        ],
    ],

];
