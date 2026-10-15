<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Projects Language Lines - English
    |--------------------------------------------------------------------------
    |
    */

    'titles' => [
        'new-sprint' => 'New Sprint',
        'sprint-overview' => 'Sprint Overview',
    ],

    
    'labels' => [
        'action_for_incomplete_tasks' => 'Action for Incomplete Tasks',
        'active-sprints' => 'Active Sprints',
        'archived-sprints' => 'Archived Sprints',
        'completed-sprints' => 'Completed Sprints',
        'select_action' => 'Select Action',
        'select_entity' => 'Select Backlog / Sprint',
        'sprints' => 'Sprints',

        'name' => 'Name',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'status' => 'Status',
        'select_status' => 'Select Status',

        'cards' => 'Cards',
        'days_to_start' => 'Days to Start',
        'days_left' => 'Days Left',
        'done' => 'Done',
        'duration' => 'Duration',
    ],

    'buttons' => [
        'archive_sprint' => 'Archive Sprint',
        'complete_sprint' => 'Complete Sprint',
        'delete_sprint' => 'Delete Sprint',
        'edit_sprint' => 'Edit Sprint',
        'new_sprint' => 'New Sprint',
        'start_sprint' => 'Start Sprint',
    ],

    'messages' => [
        'no_sprints' => 'No sprints found. Create your first sprint to get started!',
    ],

    'toast' => [
        'archive_sprint' => 'Sprint <b>:name</b> archived',
        'complete_sprint' => 'Sprint <b>:name</b> completed',
        'start_sprint' => 'Sprint <b>:name</b> started',

        'sprint-created' => 'Sprint created successfully!',
        'sprint-completed-error' => 'Cannot move tasks to sprint <b>:name</b> because it is already completed.',
        'sprint-deleted' => 'Sprint deleted successfully!',
        'sprint-updated' => 'Sprint updated successfully!',
    ],

    'modals' => [
        'complete_sprint_title' => 'Complete Sprint - :name',
        'complete_sprint_message' => 'Are you sure you want to complete the sprint <b>:name</b>?',
        'incomplete_tasks_warning' => 'The following tasks are not completed:',
        'edit_sprint_title' => 'Edit Sprint - :name',
        'delete_sprint_title' => 'Delete Sprint - :name',
        'delete_sprint_message' => 'Are you sure you want to delete the sprint <b>:name</b>? This action cannot be undone.',
    ],

    'statuses' => [
        'planned' => 'Planned',
        'active' => 'Active',
        'completed' => 'Completed',
    ],

    'actions' => [
        'move_to_backlog' => 'Move to Backlog',
        'move_to_sprint' => 'Move to another Sprint',
    ],

];