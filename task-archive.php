<?php

require_once __DIR__ . '/includes/init.php';
require_login();

if (!is_post() || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('danger', 'Invalid request.');
    redirect('dashboard.php');
}

$taskId = (int) ($_POST['id'] ?? 0);

if ($taskId <= 0) {
    redirect('dashboard.php');
}

$userId = (int) current_user()['id'];
$task = find_task($userId, $taskId);

if (!$task) {
    flash('danger', 'Task not found.');
    redirect('dashboard.php');
}

$newArchived = $task['archived'] == 1 ? 0 : 1;

$statement = db()->prepare('UPDATE tasks SET archived = :archived, updated_at = NOW() WHERE id = :id AND user_id = :user_id');
$statement->execute([
    'archived' => $newArchived,
    'id' => $taskId,
    'user_id' => $userId,
]);

flash('success', $newArchived === 1 ? 'Task archived.' : 'Task unarchived.');
redirect('dashboard.php');
