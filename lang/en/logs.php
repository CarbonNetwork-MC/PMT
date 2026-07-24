<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Log Language Lines - English
    |--------------------------------------------------------------------------
    |
    */

    'project' => [
        'archived' => 'Project <b>:project</b> was archived.',
        'created' => 'Project <b>:project</b> was created.',
        'updated' => 'Project <b>:project</b> was updated.',
        'owner_changed' => 'Project ownership was transferred from <b>:oldOwner</b> to <b>:newOwner</b>.',
    ],

    'project_members' => [
        'added' => 'User <b>:user</b> was added to the project with role <b>:role</b>.',
        'removed' => 'User <b>:user</b> was removed from the project.',
        'role_changed' => 'User <b>:user</b>\'s role was changed to <b>:role</b>.',
    ],

    'sprints' => [
        'archived' => 'Sprint <b>:sprint</b> was archived.',
        'created' => 'Sprint <b>:sprint</b> was created.',
        'deleted' => 'Sprint <b>:sprint</b> was deleted.',
        'status_changed' => 'Sprint <b>:sprint</b> status changed to <b>:status</b>.',
        'updated' => 'Sprint <b>:sprint</b> was updated.',
    ],

    'board' => [
        'card_approval_status_updated' => 'Card <b>:card</b> approval status changed from <b>:originalStatus</b> to <b>:status</b> in sprint <b>:sprint</b>.',
        'card_assignee_added' => 'User <b>:user</b> was assigned to card <b>:card</b> in sprint <b>:sprint</b>.',
        'card_assignee_removed' => 'User <b>:user</b> was unassigned from card <b>:card</b> in sprint <b>:sprint</b>.',
        'card_assignee_removed_all' => 'All assignees were removed from card <b>:card</b> in sprint <b>:sprint</b>.',
        'card_copied' => 'Card <b>:card</b> was copied in sprint <b>:sprint</b>.',
        'card_created' => 'Card <b>:card</b> was created in column <b>:column</b>. (Sprint: <b>:sprint</b>)',
        'card_created_from_task' => 'Task <b>:task</b> from <b>:card</b> was converted into a card in sprint <b>:sprint</b>.',
        'card_deadline_cleared' => 'Card <b>:card</b> deadline was removed in sprint <b>:sprint</b>.',
        'card_deadline_updated' => 'Card <b>:card</b> deadline was updated to <b>:deadline</b> in sprint <b>:sprint</b>.',
        'card_deleted' => 'Card <b>:card</b> was deleted from sprint <b>:sprint</b>.',
        'card_moved_backlog' => 'Card <b>:card</b> was moved from sprint <b>:fromSprint</b> to backlog <b>:toBacklog</b>. From column <b>:fromColumn</b>.',
        'card_moved_same_board' => 'Card <b>:card</b> was moved from column <b>:fromColumn</b> to column <b>:toColumn</b> in sprint <b>:sprint</b>.',
        'card_moved_sprints' => 'Card <b>:card</b> was moved from sprint <b>:fromSprint</b> to sprint <b>:toSprint</b>. From column <b>:fromColumn</b> to column <b>:toColumn</b>.',
        'card_updated_description' => 'Card <b>:card</b> description was updated in sprint <b>:sprint</b>.',
        'card_updated_title' => 'Card title was updated from <b>:title</b> to <b>:newTitle</b> in sprint <b>:sprint</b>.',
        'task_assignee_added' => 'User <b>:user</b> was assigned to task <b>:task</b> in sprint <b>:sprint</b>.',
        'task_assignee_removed' => 'User <b>:user</b> was unassigned from task <b>:task</b> in sprint <b>:sprint</b>.',
        'task_assignee_removed_all' => 'All assignees were removed from task <b>:task</b> in sprint <b>:sprint</b>.',
        'task_actual_time_updated' => 'Actual time for task <b>:task</b> was updated to <b>:actual_time</b> in sprint <b>:sprint</b>.',
        'task_actual_time_cleared' => 'Actual time for task <b>:task</b> was cleared in sprint <b>:sprint</b>.',
        'task_created' => 'Task <b>:task</b> was created on card <b>:card</b> in sprint <b>:sprint</b>.',
        'task_deadline_updated' => 'Deadline for task <b>:task</b> was updated to <b>:deadline</b> in sprint <b>:sprint</b>.',
        'task_deadline_cleared' => 'Deadline for task <b>:task</b> was cleared in sprint <b>:sprint</b>.',
        'task_deleted' => 'Task <b>:task</b> was deleted from card <b>:card</b> in sprint <b>:sprint</b>.',
        'task_estimated_time_updated' => 'Estimated time for task <b>:task</b> was updated to <b>:estimated_time</b> in sprint <b>:sprint</b>.',
        'task_estimated_time_cleared' => 'Estimated time for task <b>:task</b> was cleared in sprint <b>:sprint</b>.',
        'task_order_updated' => 'Task <b>:task</b> was moved from <b>:from</b> to <b>:to</b> on card <b>:card</b> in sprint <b>:sprint</b>.',
    ],

    'backlog' => [
        'bucket_created' => 'Backlog bucket <b>:bucket</b> was created.',
        'bucket_deleted' => 'Backlog bucket <b>:bucket</b> was deleted.',
        'card_approval_status_updated' => 'Card <b>:card</b> approval status changed from <b>:originalStatus</b> to <b>:status</b>.',
        'card_assignee_added' => 'User <b>:user</b> was assigned to card <b>:card</b> on backlog <b>:backlog</b>.',
        'card_assignee_removed' => 'User <b>:user</b> was unassigned from card <b>:card</b> on backlog <b>:backlog</b>.',
        'card_assignee_removed_all' => 'All assignees were removed from card <b>:card</b> on backlog <b>:backlog</b>.',
        'card_copied' => 'Card <b>:card</b> was copied on backlog <b>:backlog</b>.',
        'card_created' => 'Card <b>:card</b> was created on backlog <b>:backlog</b>.',
        'card_created_from_task' => 'Task <b>:task</b> was converted into a card on backlog <b>:backlog</b>.',
        'card_deleted' => 'Card <b>:card</b> was deleted from backlog <b>:backlog</b>.',
        'card_moved_backlog' => 'Card <b>:card</b> was moved from backlog <b>:fromBacklog</b> to backlog <b>:toBacklog</b>.',
        'card_moved_sprints' => 'Card <b>:card</b> was moved from backlog <b>:fromBacklog</b> to sprint <b>:toSprint</b>. To column <b>:toColumn</b>.',
        'card_updated_description' => 'Card <b>:card</b> description was updated on backlog <b>:backlog</b>.',
        'task_created' => 'Task <b>:task</b> was created on card <b>:card</b> in backlog <b>:backlog</b>.',
        'task_deleted' => 'Task <b>:task</b> was deleted from card <b>:card</b> on backlog <b>:backlog</b>.',
        'card_updated_title' => 'Card title was updated from <b>:title</b> to <b>:newTitle</b> on backlog <b>:backlog</b>.',
        'task_assignee_added' => 'User <b>:user</b> was assigned to task <b>:task</b> on card <b>:card</b> in backlog <b>:backlog</b>.',
        'task_assignee_removed' => 'User <b>:user</b> was unassigned from task <b>:task</b> on card <b>:card</b> in backlog <b>:backlog</b>.',
        'task_assignee_removed_all' => 'All assignees were removed from task <b>:task</b> on card <b>:card</b> in backlog <b>:backlog</b>.',
        'task_order_updated' => 'Task <b>:task</b> was moved from <b>:from</b> to <b>:to</b> on card <b>:card</b> in backlog <b>:backlog</b>.',
    ]

];