<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Language Lines - English
    |--------------------------------------------------------------------------
    |
    */

    'titles' => [
        // ? Roles and Permissions
        'assigned_permissions' => 'Assigned Permissions',
        'available_permissions' => 'Available Permissions',
        'create_permission' => 'Create Permission',
        'create_role' => 'Create Role',
        'delete_permission' => 'Delete Permission',
        'delete_role' => 'Delete Role',
        'edit_permission' => 'Edit Permission',
        'edit_role' => 'Edit Role',
        'permissions' => 'Permissions',
        'roles' => 'Roles',
        'roles_and_permissions' => 'Roles and Permissions',

        // ? Users
        'users' => [
            'edit'=> 'Edit User',
            'delete' => 'Delete User',
            'users' => 'Users',
        ],

        // ? Invite Codes
        'invite_codes' => [
            'overview' => 'Invite Codes Overview',
            'create' => 'Create Invite Code',
            'delete' => 'Delete Invite Code',
        ],

        // ? Settings
        'settings' => [
            'overview' => 'Settings Overview',
            'sprint_id' => 'Sprint ID',
        ]
    ],

    'descriptions' => [
        // ? Settings
        'settings' => [
            'sprint_id' => 'The Sprint ID is used to identify the current sprint in the application. <br>It is used for various features and functionalities within the application.',
        ]
    ],

    'messages' => [
        // ? Roles and Permissions
        'delete_permission_confirmation' => 'Are you sure you want to delete the permission <b>:permission</b>? This action cannot be undone.',
        'delete_role_confirmation' => 'Are you sure you want to delete the role <b>:role</b>? This action cannot be undone.',
        'no_permissions_found' => 'No permissions found.',
        'no_roles_found' => 'No roles found.',
        'superadmin_permissions' => 'The <b>Superadmin</b> role has all permissions and cannot be modified.',

        // ? Users
        'users' => [
            'delete_user_confirmation' => 'Are you sure you want to delete the user <b>:user</b>? This action cannot be undone.',
            'no_other_users_to_reassign_projects' => 'The following projects cannot be reassigned to another user because there are no other users in these projects: <b>:projects</b>. Please choose an option below for what to do.',
            'no_users' => 'No users found?!?!',
            'permission_via_role' => 'This permission is granted via the following role(s): <b>:roles</b>.',
            'user_is_superadmin' => 'This user is a <b>Superadmin</b> and has all permissions. You cannot modify their permissions.',

            'project_action_selected' => 'Action selected',
            'project_action_not_selected' => 'Action required',
            'projects_require_action' => 'Some projects have no other members.',
            'projects_require_action_description' => 'Select what should happen to each project before deleting the user.',
            'project_will_be_transferred_to_you' => 'You will become the owner of this project.',
            'project_will_be_archived' => 'The project will be made inactive but retained.',
            'project_will_be_deleted' => 'Warning: this project and its related data will be deleted.',
        ],

        // ? Invite Codes
        'invite_codes' => [
            'confirm_delete' => 'Are you sure you want to delete this invite code? This action cannot be undone.',
            'create_invite_code' => 'New users can create an account using this invite code. Once used, the code will be marked as used and cannot be reused.',
            'no_invite_codes' => 'No invite codes found.',
        ]
    ],

    'labels' => [
        // ? Universal
        'name' => 'Name',

        // ? Roles and Permissions
        'all_permissions' => 'All Permissions',
        'description' => 'Description',
        'display_name' => 'Display Name',
        'permissions' => 'Permissions',
        'no_description' => 'No description',
        'no_display_name' => 'No display name',

        // ? Users
        'users' => [
            'email' => 'Email',
            'new_project_owner' => 'New Project Owner',
            'project_action' => 'Project Action',
            'projects_completed' => 'Projects configured',
            'superadmin' => 'Superadmin',
        ],

        // ? Invite Codes
        'invite_codes' => [
            'code' => 'Code',
            'used' => 'Used',
            'used_by' => 'Used By',
        ],

        // ? Settings
        'settings' => [
            'sprint_id' => 'Sprint ID',
        ]
    ],

    'buttons' => [
        // ? Roles and Permissions
        'create_role' => 'Create Role',
        'create_permission' => 'Create Permission',
        'edit_permission' => 'Edit Permission',
        'edit_role' => 'Edit Role',
        'new_permission' => 'New Permission',
        'new_role' => 'New Role',
        'update_permission' => 'Update Permission',
        'update_role' => 'Update Role',

        // ? Invite Codes
        'invite_codes' => [
            'create' => 'Create Invite Code',
            'generate' => 'Generate Code',
        ],
    ],

    'toasts' => [
        // ? Roles and Permissions
        'permission_created' => 'Permission <b>:permission</b> created successfully.',
        'permission_deleted_successfully' => 'Permission <b>:permission</b> deleted successfully.',
        'permission_updated' => 'Permission <b>:permission</b> updated successfully.',
        'role_created' => 'Role <b>:role</b> created successfully.',
        'role_deleted_successfully' => 'Role <b>:role</b> deleted successfully.',
        'role_updated' => 'Role <b>:role</b> updated successfully.',

        // ? Users
        'users' => [
            'user_deleted' => 'User <b>:user</b> deleted successfully.',
            'user_is_superadmin' => 'User <b>:user</b> is a <b>Superadmin</b> and cannot be deleted by anyone other than another <b>Superadmin</b>.',
            'user_updated' => 'User <b>:user</b> updated successfully.',
        ],

        // ? Invite Codes
        'invite_codes' => [
            'created' => 'Invite code created successfully.',
            'deleted' => 'Invite code deleted successfully.',
        ]
    ],

    'placeholders' => [
        // ? Users
        'users' => [
            'username' => 'Enter username',
            'email' => 'Enter email address',
            'select_project_action' => 'Select an action',
            'select_new_owner' => 'Select a new owner',
        ],
    ],

    'options' => [
        // ? Users
        'users' => [
            'transfer_to_user' => 'Transfer to another user',
            'transfer_to_me' => 'Transfer to me',
            'archive_project' => 'Archive project',
            'delete_project' => 'Delete project',
        ],
    ],

    'validation' => [
        'users' => [
            'project_action_required' =>
                'Select what should happen to this project.',

            'project_owner_required' =>
                'Select a valid new project owner.',

            'cannot_transfer_project_to_deleted_user' =>
                'The project cannot be transferred to the user being deleted.',
        ],
    ],

];