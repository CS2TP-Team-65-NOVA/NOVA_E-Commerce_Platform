<?php
// auth/register.php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name    = trim($_POST['full_name'] ?? '');
    $email        = strtolower(trim($_POST['email'] ?? ''));
    $password_raw = $_POST['password'] ?? '';
    $phone        = trim($_POST['phone_number'] ?? '');

    $errors = [];

    // Validation
    if ($full_name === '') {
        $errors[] = 'Full name is required';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if (strlen($password_raw) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors'  => $errors
        ]);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'errors'  => ['Email already registered']
        ]);
        exit;
    }

    // Hash the password
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password, phone_number, role, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'customer', NOW(), NOW())
    ");

    $stmt->execute([$full_name, $email, $password_hash, $phone]);

    $newUserId = (int)$pdo->lastInsertId();

    // Log in the user
    $_SESSION['user_id'] = $newUserId;

    echo json_encode([
        'success' => true,
        'user_id' => $newUserId
    ]);
    exit;
}

echo "Registration endpoint";
