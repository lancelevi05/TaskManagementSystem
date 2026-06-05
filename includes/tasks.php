<?php

declare(strict_types=1);

function task_summary(int $userId): array
{
    $statement = db()->prepare(
        'SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) AS in_progress,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN due_date IS NOT NULL AND due_date < CURDATE() AND status <> "completed" THEN 1 ELSE 0 END) AS overdue
         FROM tasks
         WHERE user_id = :user_id'
    );
    $statement->execute(['user_id' => $userId]);

    $summary = $statement->fetch() ?: [];

    return [
        'total' => (int) ($summary['total'] ?? 0),
        'pending' => (int) ($summary['pending'] ?? 0),
        'in_progress' => (int) ($summary['in_progress'] ?? 0),
        'completed' => (int) ($summary['completed'] ?? 0),
        'overdue' => (int) ($summary['overdue'] ?? 0),
    ];
}

function list_tasks(int $userId, array $filters = []): array
{
    $conditions = ['user_id = :user_id'];
    $params = ['user_id' => $userId];

    if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'in_progress', 'completed'], true)) {
        $conditions[] = 'status = :status';
        $params['status'] = $filters['status'];
    }

    if (!empty($filters['priority']) && in_array($filters['priority'], ['low', 'medium', 'high'], true)) {
        $conditions[] = 'priority = :priority';
        $params['priority'] = $filters['priority'];
    }

    if (!empty($filters['query'])) {
        $conditions[] = '(title LIKE :query_title OR description LIKE :query_description)';
        $params['query_title'] = '%' . $filters['query'] . '%';
        $params['query_description'] = '%' . $filters['query'] . '%';
    }

    // archived filter: show active tasks by default (archived = 0)
    if (isset($filters['archived'])) {
        $archived = (int) $filters['archived'];
        $conditions[] = 'archived = :archived';
        $params['archived'] = $archived;
    } else {
        $conditions[] = 'archived = 0';
    }

    $allowedSorts = [
        'due_date' => 'due_date ASC',
        'newest' => 'created_at DESC',
        'oldest' => 'created_at ASC',
        'priority' => 'FIELD(priority, "high", "medium", "low") ASC, created_at DESC',
    ];

    $sort = $allowedSorts[$filters['sort'] ?? 'newest'] ?? $allowedSorts['newest'];

    $sql = sprintf(
        'SELECT id, title, description, due_date, priority, status, archived, created_at, updated_at
         FROM tasks
         WHERE %s
         ORDER BY %s',
        implode(' AND ', $conditions),
        $sort
    );

    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function find_task(int $userId, int $taskId): ?array
{
    $statement = db()->prepare('SELECT * FROM tasks WHERE id = :id AND user_id = :user_id LIMIT 1');
    $statement->execute([
        'id' => $taskId,
        'user_id' => $userId,
    ]);

    $task = $statement->fetch();

    return $task ?: null;
}

function create_task(int $userId, array $data): array
{
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $dueDate = trim((string) ($data['due_date'] ?? '')) ?: null;
    $priority = $data['priority'] ?? 'medium';
    $status = $data['status'] ?? 'pending';

    if ($title === '') {
        return ['ok' => false, 'message' => 'Task title is required.'];
    }

    if (!in_array($priority, ['low', 'medium', 'high'], true)) {
        $priority = 'medium';
    }

    if (!in_array($status, ['pending', 'in_progress', 'completed'], true)) {
        $status = 'pending';
    }

    $statement = db()->prepare(
        'INSERT INTO tasks (user_id, title, description, due_date, priority, status, created_at, updated_at)
         VALUES (:user_id, :title, :description, :due_date, :priority, :status, NOW(), NOW())'
    );
    $statement->execute([
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate ?: null,
        'priority' => $priority,
        'status' => $status,
    ]);

    return ['ok' => true, 'message' => 'Task created successfully.'];
}

function update_task(int $userId, int $taskId, array $data): array
{
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $dueDate = trim((string) ($data['due_date'] ?? '')) ?: null;
    $priority = $data['priority'] ?? 'medium';
    $status = $data['status'] ?? 'pending';

    if ($title === '') {
        return ['ok' => false, 'message' => 'Task title is required.'];
    }

    if (!in_array($priority, ['low', 'medium', 'high'], true)) {
        $priority = 'medium';
    }

    if (!in_array($status, ['pending', 'in_progress', 'completed'], true)) {
        $status = 'pending';
    }

    $statement = db()->prepare(
        'UPDATE tasks
         SET title = :title,
             description = :description,
             due_date = :due_date,
             priority = :priority,
             status = :status,
             updated_at = NOW()
         WHERE id = :id AND user_id = :user_id'
    );
    $statement->execute([
        'id' => $taskId,
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
        'due_date' => $dueDate ?: null,
        'priority' => $priority,
        'status' => $status,
    ]);

    return ['ok' => true, 'message' => 'Task updated successfully.'];
}

function delete_task(int $userId, int $taskId): void
{
    $statement = db()->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :user_id');
    $statement->execute([
        'id' => $taskId,
        'user_id' => $userId,
    ]);
}

function toggle_task_status(int $userId, int $taskId): void
{
    $task = find_task($userId, $taskId);

    if (!$task) {
        return;
    }

    $newStatus = $task['status'] === 'completed' ? 'pending' : 'completed';

    $statement = db()->prepare(
        'UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :id AND user_id = :user_id'
    );
    $statement->execute([
        'status' => $newStatus,
        'id' => $taskId,
        'user_id' => $userId,
    ]);
}
