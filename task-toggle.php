<?php

require_once __DIR__ . '/includes/init.php';
require_login();

if (!is_post() || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Invalid request.');
    redirect('dashboard.php');
}

$taskId = (int) ($_POST['id'] ?? 0);

if ($taskId > 0) {
    toggle_task_status((int) current_user()['id'], $taskId);
    flash('success', 'Task status updated.');
}

redirect('dashboard.php');
