<?php

return [
    'account_created' => [
        'greeting' => 'Hello :name!',
        'intro' => 'You are receiving this email because an account has been created for you on :app.',
        'setup_action' => 'Setup Your Account',
    ],
    'password_reset' => [
        'subject' => 'Reset Password',
        'intro' => 'You are receiving this email because we received a password reset request for your account.',
        'action' => 'Reset Password',
        'outro' => 'If you did not request a password reset, no further action is required.',
    ],
    'added_to_server' => [
        'greeting' => 'Hello :name!',
        'intro' => 'You have been added as a subuser for the following server, allowing you certain control over the server.',
        'action' => 'Visit Server',
    ],
    'removed_from_server' => [
        'greeting' => 'Hello :name.',
        'intro' => 'You have been removed as a subuser for the following server.',
        'action' => 'Visit Panel',
    ],
    'server_installed' => [
        'greeting' => 'Hello :name.',
        'intro' => 'Your server has finished installing and is now ready for you to use.',
        'action' => 'Login and Begin Using',
    ],
    'mail_tested' => [
        'subject' => 'Pyrodactyl Test Message',
        'greeting' => 'Hello :name!',
        'intro' => 'This is a test of the Pyrodactyl mail system. You\'re good to go!',
    ],
    'labels' => [
        'username' => 'Username: :username',
        'email' => 'Email: :email',
        'server_name' => 'Server Name: :name',
    ],
    'template' => [
        'greeting_default' => 'Hello!',
        'greeting_error' => 'Whoops!',
        'regards' => 'Regards,',
        'trouble_clicking' => 'If you\'re having trouble clicking the ":action" button, copy and paste the URL below into your web browser:',
        'all_rights_reserved' => 'All rights reserved.',
    ],
];
