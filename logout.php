<?php

require_once __DIR__ . '/includes/init.php';

if (is_post() && verify_csrf_token($_POST['csrf_token'] ?? null)) {
    logout_user();
}

redirect('login.php');
