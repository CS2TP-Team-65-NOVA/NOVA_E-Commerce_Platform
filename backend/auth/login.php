<?php
// auth/login.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if ($password === '') {
        $errors[] = 'Password is required';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors'  => $errors
        ]);
        exit;
    }

    // Look up user
    $stmt = $pdo->prepare("SELECT user_id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'errors'  => ['Invalid email or password']
        ]);
        exit;
    }

    // Login OK
    $_SESSION['user_id'] = (int)$user['user_id'];

    echo json_encode([
        'success' => true,
        'user_id' => (int)$user['user_id']
    ]);
    exit;
}

echo "Login endpoint";
