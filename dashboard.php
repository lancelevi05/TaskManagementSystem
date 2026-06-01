<?php

require_once __DIR__ . '/includes/init.php';
require_login();

$user = current_user();
$summary = task_summary((int) $user['id']);

$filters = [
    'query' => trim((string) ($_GET['q'] ?? '')),
    'status' => trim((string) ($_GET['status'] ?? '')),
    'priority' => trim((string) ($_GET['priority'] ?? '')),
    'archived' => (int) ($_GET['archived'] ?? 0),
    'sort' => trim((string) ($_GET['sort'] ?? 'newest')),
];

$tasks = list_tasks((int) $user['id'], $filters);

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">Task dashboard</p>
        <h1>Keep the board moving without losing the details.</h1>
        <p class="section-subtitle">Search, filter, complete, and edit tasks from one clear workspace. Your data stays scoped to your account.</p>
        <div class="hero-cta">
            <a class="button button-dark" href="task-create.php" >Create new task</a>
            <a class="button button-ghost" href="#task-list">Browse tasks</a>
        </div>
    </div>
    <div class="stats-grid">
        <div class="stat"><strong><?= (int) $summary['total'] ?></strong><span class="muted">Total tasks</span></div>
        <div class="stat"><strong><?= (int) $summary['pending'] ?></strong><span class="muted">Pending</span></div>
        <div class="stat"><strong><?= (int) $summary['in_progress'] ?></strong><span class="muted">In progress</span></div>
        <div class="stat"><strong><?= (int) $summary['completed'] ?></strong><span class="muted">Completed</span></div>
        <div class="stat"><strong><?= (int) $summary['overdue'] ?></strong><span class="muted">Overdue</span></div>
    </div>
</section>

<section class="panel" id="task-list">
    <h2 class="section-title" style="margin-top: 0;">Your tasks</h2>
    <p class="section-subtitle">Use the filters to focus on the work that matters right now.</p>

    <form method="get" class="toolbar">
        <input type="search" name="q" placeholder="Search tasks" value="<?= e($filters['query']) ?>">
        <select name="status">
            <option value="">All statuses</option>
            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
            <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <select name="priority">
            <option value="">All priorities</option>
            <option value="low" <?= $filters['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
            <option value="medium" <?= $filters['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= $filters['priority'] === 'high' ? 'selected' : '' ?>>High</option>
        </select>
        <select name="archived">
            <option value="0" <?= $filters['archived'] === 0 ? 'selected' : '' ?>>Active tasks</option>
            <option value="1" <?= $filters['archived'] === 1 ? 'selected' : '' ?>>Archived tasks</option>
        </select>
        <select name="sort">
            <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest first</option>
            <option value="due_date" <?= $filters['sort'] === 'due_date' ? 'selected' : '' ?>>Due date</option>
            <option value="priority" <?= $filters['sort'] === 'priority' ? 'selected' : '' ?>>Priority</option>
            <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Oldest first</option>
        </select>
        <button type="submit" class="button button-dark">Apply</button>
    </form>

    <?php if (!$tasks): ?>
        <div class="task-card">
            <p class="muted" style="margin: 0;">No tasks match your filters. Create one to get started.</p>
        </div>
    <?php else: ?>
        <div class="task-list">
            <?php foreach ($tasks as $task): ?>
                <article class="task-card">
                    <div class="task-top">
                        <div>
                            <h3><?= e($task['title']) ?></h3>
                            <p class="muted" style="margin-top: 0; white-space: pre-wrap;"><?= e($task['description'] ?: 'No description added yet.') ?></p>
                        </div>
                        <span class="tag <?= e(status_class($task['status'])) ?>"><?= e(status_label($task['status'])) ?></span>
                    </div>

                    <div class="tag-row">
                        <span class="tag <?= e(priority_class($task['priority'])) ?>"><?= e(priority_label($task['priority'])) ?> priority</span>
                        <span class="tag neutral">Due: <?= e(format_date($task['due_date'])) ?></span>
                        <span class="tag neutral">Created: <?= e(date('M j, Y', strtotime($task['created_at']))) ?></span>
                    </div>

                    <div class="task-actions">
                        <a class="link-button" href="task-edit.php?id=<?= (int) $task['id'] ?>">Edit</a>
                        <form method="post" action="task-toggle.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                            <button type="submit" class="link-button" style="color:white;"><?= $task['status'] === 'completed' ? 'Reopen' : 'Mark complete' ?></button>
                        </form>
                        <form method="post" action="task-archive.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                            <button type="submit" class="link-button danger"><?= $task['archived'] === 1 ? 'Unarchive' : 'Archive' ?></button>
                        </form>
                        <?php if ($task['archived'] === 1): ?>
                            <form method="post" action="task-delete.php" onsubmit="return confirm('Permanently delete this archived task? This cannot be undone.');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <button type="submit" class="link-button danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
