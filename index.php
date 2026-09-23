<?php
require_once __DIR__ . '/includes/auth.php';
redirect(is_login() ? home_url() : base_url('auth/login.php'));
