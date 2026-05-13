<?php

declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function attempt_login(string $email, string $password): bool
{
    $statement = db()->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    unset($user['password']);
    $_SESSION['user'] = $user;

    return true;
}

function register_user(string $name, string $email, string $password): array
{
    $name = trim($name);
    $email = strtolower(trim($email));

    if ($name === '' || $email === '' || $password === '') {
        return ['ok' => false, 'message' => 'All fields are required.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Enter a valid email address.'];
    }

    if (strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    $exists = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $exists->execute(['email' => $email]);

    if ($exists->fetch()) {
        return ['ok' => false, 'message' => 'An account with that email already exists.'];
    }

    $statement = db()->prepare('INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())');
    $statement->execute([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    return ['ok' => true, 'message' => 'Account created successfully.'];
}

function logout_user(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}
