<?php
/**
 * NOA API for perfumes, FAQs, orders, delivery, returns 
 */
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config.php';

require_once __DIR__ . '/chatbot_helpers.php';
require_once __DIR__ . '/chatbot_general.php';
require_once __DIR__ . '/chatbot_orders.php';
require_once __DIR__ . '/chatbot_perfumes.php';

nova_ensure_session_started();

$message = nova_read_message_from_request();

$defaultSuggestions = [
    'When will my order arrive?',
    'How do returns work?',
    'My recent purchases',
    'Women citrus under £70',
    'Men oriental around £100',
    'Gift box under £65',
];

if ($message === '') {
    echo json_encode([
        'ok' => true,
        'reply' => "Hi, I'm NOA.\n\n"
            . "Not an ordinary assistant - I'm here to help you discover fragrances you'll love, as well as assist with orders, delivery, and returns.\n\n"
            . "How can I help you today?",
        'products' => [],
        'suggestions' => $defaultSuggestions,
        'matched_rule' => null,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$tLower = mb_strtolower($message, 'UTF-8');

// 1) Quick topic buttons when the user isn't asking for a specific order reference.
$topicMenu = nova_try_topic_button_menu($tLower, $message);
if ($topicMenu !== null) {
    echo json_encode([
        'ok' => true,
        'reply' => $topicMenu['reply'],
        'products' => [],
        'suggestions' => $topicMenu['suggestions'],
        'matched_rule' => $topicMenu['matched_rule'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) FAQ
$faqReply = nova_try_faq($tLower);
if ($faqReply !== null) {
    echo json_encode([
        'ok' => true,
        'reply' => $faqReply,
        'products' => [],
        'suggestions' => $defaultSuggestions,
        'matched_rule' => 'faq',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3) Orders
$orderOut = nova_try_order($message, $tLower, $conn);

if ($orderOut !== null) {
    echo json_encode([
        'ok' => true,
        'reply' => $orderOut['reply'],
        'products' => [],
        'suggestions' => $defaultSuggestions,
        'matched_rule' => $orderOut['matched_rule'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4) Perfumes (default)
// Use $conn from config.php
$perfOut = nova_try_perfumes($message, $conn, $defaultSuggestions);
echo json_encode($perfOut, JSON_UNESCAPED_UNICODE);

