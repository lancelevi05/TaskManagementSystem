<?php

require_once __DIR__ . '/includes/init.php';
require_login();

$user = current_user();
$taskId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$task = find_task((int) $user['id'], $taskId);

if (!$task) {
    flash('danger', 'Task not found.');
    redirect('dashboard.php');
}

$error = null;

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $result = update_task((int) $user['id'], $taskId, $_POST);

        if ($result['ok']) {
            flash('success', $result['message']);
            redirect('dashboard.php');
        }

        $error = $result['message'];
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="panel">
    <p class="eyebrow">Edit task</p>
    <h1 class="section-title" style="margin-top: 0;">Update your task</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="task-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
        <input type="text" name="title" value="<?= e((string) old('title', $task['title'])) ?>" required>
        <textarea name="description"><?= e((string) old('description', $task['description'])) ?></textarea>

        <div class="form-row">
            <input type="date" name="due_date" value="<?= e((string) old('due_date', $task['due_date'])) ?>">
            <select name="priority">
                <option value="low" <?= old('priority', $task['priority']) === 'low' ? 'selected' : '' ?>>Low priority</option>
                <option value="medium" <?= old('priority', $task['priority']) === 'medium' ? 'selected' : '' ?>>Medium priority</option>
                <option value="high" <?= old('priority', $task['priority']) === 'high' ? 'selected' : '' ?>>High priority</option>
            </select>
        </div>

        <div class="form-row">
            <select name="status">
                <option value="pending" <?= old('status', $task['status']) === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= old('status', $task['status']) === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                <option value="completed" <?= old('status', $task['status']) === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <a class="button button-ghost" href="dashboard.php">Cancel</a>
        </div>

        <button type="submit" class="button button-dark">Update task</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
