<?php
// includes/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Guest session ID for guests
if (!isset($_SESSION['guest_session_id'])) {
    $_SESSION['guest_session_id'] = bin2hex(random_bytes(18));
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}
