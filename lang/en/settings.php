<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Language Lines - English
    |--------------------------------------------------------------------------
    |
    */

    'titles' => [
        'admin' => 'Admin Settings',
        'general' => 'General Settings',
        'settings' => 'Settings',

        'name' => 'Name',
        'description' => 'Description',
        'change_owner' => 'Change Owner',
        'delete_project' => 'Delete Project',
    ],

    'descriptions' => [
        'name' => 'The name of the project.',
        'description' => 'A brief description of the project.',
        'change_owner' => 'Transfer ownership of the project to another member. <br>The new owner will have full control over the project.',
        'delete_project' => 'Permanently delete this project with all its data. This action cannot be undone.',
    ],

    
    'labels' => [
        'new_owner' => 'New Owner',
    ],

    'buttons' => [
        'change_owner' => 'Change Owner',
        'delete_project' => 'Delete Project',
    ],

    'messages' => [
        'change_owner_warning' => 'Are you sure you want to transfer ownership of this project to another member? <br><b>This action cannot be undone.</b>',
        'delete_project_warning' => 'Are you sure you want to delete this project? <br><b>This action cannot be undone.</b>',
    ],

    'toast' => [
        'owner_changed' => 'Project ownership has been transferred to <b>:newOwner</b>.',
        'project_deleted' => 'Project has been deleted.',
    ],

    'placeholders' => [
        'select_new_owner' => 'Select a new owner',
    ],

];