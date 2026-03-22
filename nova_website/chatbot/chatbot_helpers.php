<?php

function nova_ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function nova_read_message_from_request(): string
{
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = $_POST;
    }

    return isset($data['message']) ? trim((string) $data['message']) : '';
}

