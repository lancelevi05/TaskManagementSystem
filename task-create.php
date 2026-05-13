<?php

require_once __DIR__ . '/includes/init.php';
require_login();

$error = null;

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $result = create_task((int) current_user()['id'], $_POST);

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
    <p class="eyebrow">New task</p>
    <h1 class="section-title" style="margin-top: 0;">Create a task</h1>
    <p class="section-subtitle">Capture the work in a structured way so it stays easy to sort and track.</p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="task-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="text" name="title" placeholder="Task title" value="<?= e((string) old('title')) ?>" required>
        <textarea name="description" placeholder="Description"><?= e((string) old('description')) ?></textarea>

        <div class="form-row">
            <input type="date" name="due_date" value="<?= e((string) old('due_date')) ?>">
            <select name="priority">
                <option value="low" <?= old('priority') === 'low' ? 'selected' : '' ?>>Low priority</option>
                <option value="medium" <?= old('priority', 'medium') === 'medium' ? 'selected' : '' ?>>Medium priority</option>
                <option value="high" <?= old('priority') === 'high' ? 'selected' : '' ?>>High priority</option>
            </select>
        </div>

        <div class="form-row">
            <select name="status">
                <option value="pending" <?= old('status', 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= old('status') === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                <option value="completed" <?= old('status') === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <a class="button button-ghost" href="dashboard.php">Cancel</a>
        </div>

        <button type="submit" class="button button-dark">Save task</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
