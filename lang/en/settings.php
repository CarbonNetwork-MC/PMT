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
        'columns' => 'Board Columns',
        'general' => 'General Settings',
        'members' => 'Members',
        'settings' => 'Settings',

        'change_owner' => 'Change Owner',
        'change_role' => 'Change Role',
        'description' => 'Description',
        'delete_project' => 'Delete Project',
        'name' => 'Name',
        'remove_member' => 'Remove Member',
    ],

    'descriptions' => [
        'name' => 'The name of the project.',
        'description' => 'A brief description of the project.',
        'change_owner' => 'Transfer ownership of the project to another member. <br>The new owner will have full control over the project.',
        'delete_project' => 'Permanently delete this project with all its data. This action cannot be undone.',
    ],
    
    'labels' => [
        'color' => 'Color',
        'column_name' => 'Column Name',
        'new_owner' => 'New Owner',
        'member_name' => 'Member Name',
        'member_role' => 'Member Role',
        'position' => 'Position',

        'background_color' => 'Background',
        'text_color' => 'Text',
    ],

    'buttons' => [
        'add_column' => 'Add Column',
        'add_member' => 'Add Member',
        'change_owner' => 'Change Owner',
        'change_role' => 'Change Role',
        'delete_project' => 'Delete Project',
        'remove' => 'Remove',
    ],

    'messages' => [
        'change_owner_warning' => 'Are you sure you want to transfer ownership of this project to another member?',
        'delete_project_warning' => 'Are you sure you want to delete this project?',
        'remove_member_warning' => 'Are you sure you want to remove this member from the project?',
        'action_cannot_be_undone' => '<b>This action cannot be undone.</b>',
    ],

    'toast' => [
        'owner_changed' => 'Project ownership has been transferred to <b>:newOwner</b>.',
        'project_deleted' => 'Project has been deleted.',
        'role_changed' => '<b>:name</b>\'s role has been updated.',
        'member_added' => '<b>:name</b> has been added to the project.',
        'member_removed' => '<b>:name</b> has been removed from the project.',
    ],

    'placeholders' => [
        'select_new_owner' => 'Select a new owner',
    ],

    'nav' => [
        'general' => 'General',
        'members' => 'Members',
        'columns' => 'Columns',
        'admin' => 'Admin',
    ]

];