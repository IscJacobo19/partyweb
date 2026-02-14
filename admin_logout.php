<?php
require __DIR__ . '/admin_auth.php';
admin_logout();
header('Location: admin-login');
exit;
