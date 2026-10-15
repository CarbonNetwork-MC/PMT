<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Profile Language Lines - English
    |--------------------------------------------------------------------------
    |
    */

    'titles' => [
        'change_password' => 'Change Password',
        'delete_account' => 'Delete Account',
        'personal_information' => 'Personal Information',
        'profile' => 'Profile',
        'sessions' => 'Sessions',
        'settings' => [
            'general' => 'Settings',
            'language' => 'Language',
            'dark_mode' => 'Dark Mode',
        ],
    ],

    'descriptions' => [
        'change_password' => 'Ensure your account is using a long, random password to stay secure.',
        'personal_information' => 'Manage your personal information and profile settings.',
        'delete_account' => 'Permanently delete your account.',
        'sessions' => 'Manage and log out your active sessions on other browsers and devices.',

        'settings' => [
            'language' => 'Select your preferred language for the application.',
            'dark_mode' => 'Toggle between light and dark mode for the application interface.',
        ]
    ],

    'messages' => [
        'delete_account' => 'Once your account is deleted, all of its resources and data will be permanently deleted. <br>Are you sure you want to delete your account? This action cannot be undone.',
        'sessions' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. <br>Some of your recent sessions are listed below; however, this list may not be exhaustive. <br>If you feel your account has been compromised, you should also update your password.',
        
        'delete_account_modal' => 'Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',

        'delete_account_owner' => 'You cannot delete your account because you are the owner of one or more projects. Please transfer ownership of your projects before deleting your account.',
    ],

    'labels' => [
        'confirm_password' => 'Confirm Password',
        'current_password' => 'Current Password',
        'dark_mode' => 'Dark Mode',
        'email' => 'Email Address',
        'language' => 'Language',
        'last_active' => 'Last Active',
        'new_password' => 'New Password',
        'username' => 'Username',
        'this_device' => 'This Device',
    ],

    'buttons' => [
        'delete_account' => 'Delete Account',
        'logout_other_sessions' => 'Log Out Other Browser Sessions',
    ],

    'toasts' => [
        'account_deleted' => 'Successfully deleted your account.',
        'current_password_incorrect' => 'The current password you entered is incorrect.',
        'password_updated' => 'Successfully updated your password.',
        'profile_image_updated' => 'Successfully updated your profile image.',
        'profile_updated' => 'Successfully updated your profile information.',
        'sessions_logged_out' => 'Successfully logged out of other browser sessions.',
        'settings_updated' => 'Successfully updated your settings. Refresh the page to see the changes take effect.',
    ],

    'placeholders' => [
        'image_upload_helper' => 'PNG, JPG or JPEG. (Max size: 2MB)',
    ]

];