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
        'new_column' => 'New Column',
        'edit_column' => 'Edit Column',
        'settings' => 'Settings',

        'add_member' => 'Add Member',
        'change_owner' => 'Change Owner',
        'change_role' => 'Change Role',
        'description' => 'Description',
        'delete_project' => 'Delete Project',
        'name' => 'Name',
        'remove_column' => 'Remove Column',
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
        'column_type' => 'Column Type',
        'new_owner' => 'New Owner',
        'member_name' => 'Member Name',
        'member_role' => 'Member Role',
        'name' => 'Name',
        'position' => 'Position',

        'background_color' => 'Background',
        'text_color' => 'Text',

        'todo' => 'To Do',
        'doing' => 'Doing',
        'done' => 'Done',
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
        'action_cannot_be_undone' => '<b>This action cannot be undone.</b>',
        'change_owner_warning' => 'Are you sure you want to transfer ownership of this project to another member?',
        'delete_project_warning' => 'Are you sure you want to delete this project?',
        'remove_column_confirmation' => 'Are you sure you want to remove this column? All tasks in this column will be moved to the first column or the next available column.',
        'remove_member_warning' => 'Are you sure you want to remove this member from the project?',
    ],

    'toast' => [
        'column_added' => 'Column <b>:name</b> has been added.',
        'column_removed' => 'Column has been removed.',
        'column_updated' => 'Column <b>:name</b> has been updated.',
        'max_columns_reached' => 'Maximum number of columns (5) has been reached.',
        'member_added' => '<b>:name</b> has been added to the project.',
        'member_removed' => '<b>:name</b> has been removed from the project.',
        'owner_changed' => 'Project ownership has been transferred to <b>:newOwner</b>.',
        'project_deleted' => 'Project has been deleted.',
        'role_changed' => '<b>:name</b>\'s role has been updated to <b>:role</b>.',
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