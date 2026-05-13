<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function is_post(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function format_date(?string $value): string
{
    if (!$value) {
        return 'No due date';
    }

    return date('M j, Y', strtotime($value));
}

function priority_label(string $priority): string
{
    return match ($priority) {
        'high' => 'High',
        'medium' => 'Medium',
        default => 'Low',
    };
}

function status_label(string $status): string
{
    return match ($status) {
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        default => 'Pending',
    };
}

function status_class(string $status): string
{
    return match ($status) {
        'in_progress' => 'warning',
        'completed' => 'success',
        default => 'neutral',
    };
}

function priority_class(string $priority): string
{
    return match ($priority) {
        'high' => 'danger',
        'medium' => 'warning',
        default => 'success',
    };
}
