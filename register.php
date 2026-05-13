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
        $result = register_user(
            (string) ($_POST['name'] ?? ''),
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['password'] ?? '')
        );

        if ($result['ok']) {
            attempt_login((string) $_POST['email'], (string) $_POST['password']);
            flash('success', 'Your account has been created.');
            redirect('dashboard.php');
        }

        $error = $result['message'];
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
    <section class="auth-card">
        <p class="eyebrow">Create account</p>
        <h1>Start your task board.</h1>
        <p class="muted">Create a secure account and organize every task in your personal workspace.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="task-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="text" name="name" placeholder="Full name" value="<?= e((string) old('name')) ?>" required>
            <input type="email" name="email" placeholder="Email address" value="<?= e((string) old('email')) ?>" required>
            <input type="password" name="password" placeholder="Password (min 6 characters)" required>
            <button type="submit" class="button button-dark">Create Account</button>
        </form>

        <p class="muted" style="margin-top: 18px;">Already have an account? <a href="login.php">Sign in here</a>.</p>
    </section>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
