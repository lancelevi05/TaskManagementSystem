<?php

require_once __DIR__ . '/includes/init.php';
require_login();

if (!is_post() || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Invalid request.');
    redirect('dashboard.php');
}

$taskId = (int) ($_POST['id'] ?? 0);

$userId = (int) current_user()['id'];

if ($taskId > 0) {
    $task = find_task($userId, $taskId);

    if ($task) {
        if ((int) $task['archived'] !== 1) {
            flash('danger', 'Only archived tasks can be deleted.');
        } else {
            delete_task($userId, $taskId);
            flash('success', 'Task deleted successfully.');
        }
    } else {
        flash('danger', 'Task not found.');
    }
}

redirect('dashboard.php');
