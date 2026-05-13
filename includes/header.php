<?php

declare(strict_types=1);

$user = current_user();
$flash = get_flash();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="site-shell">
    <header class="topbar">
        <div>
            <p class="eyebrow">Project workspace</p>
            <a class="brand" href="<?= is_logged_in() ? 'dashboard.php' : 'index.php' ?>"><?= e(APP_NAME) ?></a>
        </div>
        <nav class="topnav">
            <?php if ($user): ?>
                <span class="nav-chip">Signed in as <?= e($user['name']) ?></span>
                <a href="dashboard.php">Dashboard</a>
                <a href="task-create.php" class="button button-ghost">New Task</a>
                <form action="logout.php" method="post" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="button button-dark">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="button button-dark">Create Account</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <main class="content-wrap">
