<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact topics
    |--------------------------------------------------------------------------
    |
    | The options offered on the public contact form, used both to render
    | the topic <select> and to validate the submitted value.
    |
    */
    'topics' => [
        'sales' => 'New school / sign up',
        'support' => 'Support for an existing school',
        'general' => 'General inquiry',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recipient
    |--------------------------------------------------------------------------
    |
    | Where contact form notifications are sent. Defaults to the app's
    | configured mail-from address so it works without extra setup.
    |
    */
    'recipient' => env('CONTACT_MAIL_TO', 'solforbs@gmail.com'),

];
