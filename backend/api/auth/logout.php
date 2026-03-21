<?php
// auth/logout.php
require_once __DIR__ . '/../includes/session.php';

session_unset();
session_destroy();

echo json_encode([
    'success' => true
]);
