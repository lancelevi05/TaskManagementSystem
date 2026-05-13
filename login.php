<?php

require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = null;

if (is_post()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (attempt_login($email, $password)) {
            flash('success', 'Welcome back.');
            redirect('dashboard.php');
        }

        $error = 'Invalid email or password.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
    <section class="auth-card">
        <p class="eyebrow">Sign in</p>
        <h1>Run your tasks from one place.</h1>
        <p class="muted">Log in to see your task board, filters, and progress overview.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="task-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="email" name="email" placeholder="Email address" value="<?= e((string) old('email')) ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="button button-dark">Login</button>
        </form>

        <p class="muted" style="margin-top: 18px;">No account yet? <a href="register.php">Create one here</a>.</p>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
