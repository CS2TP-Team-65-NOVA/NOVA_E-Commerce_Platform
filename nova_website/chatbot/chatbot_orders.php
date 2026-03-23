<?php

function nova_format_order_line(array $row): string
{
    $num = (string) ($row['order_number'] ?? 'Order');
    $d   = isset($row['order_date']) ? date('j M Y', strtotime((string) $row['order_date'])) : '';
    $pay = ucfirst((string) ($row['payment_status'] ?? ''));
    $del = ucfirst((string) ($row['delivery_status'] ?? ''));
    $cur = $row['currency'] ?? 'GBP';
    $tot = isset($row['total_amount']) ? number_format((float) $row['total_amount'], 2) : '-';

    return "• {$num} - {$d}\n  Payment: {$pay} · Delivery: {$del} · Total: {$cur} {$tot}";
}

function nova_format_order_detail(array $row): string
{
    $num = (string) ($row['order_number'] ?? '');
    $d   = isset($row['order_date']) ? date('j F Y, H:i', strtotime((string) $row['order_date'])) : '';
    $pay = ucfirst((string) ($row['payment_status'] ?? ''));
    $del = ucfirst((string) ($row['delivery_status'] ?? ''));
    $cur = $row['currency'] ?? 'GBP';
    $tot = isset($row['total_amount']) ? number_format((float) $row['total_amount'], 2) : '-';

    return "Order {$num}\n\n"
        . "Placed: {$d}\n"
        . "Payment: {$pay}\n"
        . "Delivery: {$del}\n"
        . "Total: {$cur} {$tot}\n\n"
        . 'For line items or tracking, use your account area or Contact with this order number.';
}

/**
 * Order summaries for customers who are logged in only
 *
 * @return array{reply:string, matched_rule:string}|null
 */
function nova_try_order(string $raw, string $t, mysqli $conn): ?array
{
    $hasOrderIntent = (bool) preg_match(
        '/\b(my orders|my recent orders|my purchases|my recent purchases|order status|track(?:ing)?|order history|recent orders|recent purchases|purchase history|order details|show orders|list orders|all orders|have i ordered)\b/u',
        $t
    );

    $ref = null;
    if (preg_match('/\b(?:order|ord)\s*[#:]?\s*([A-Za-z0-9][A-Za-z0-9-]{3,39})\b/u', $raw, $m)) {
        $ref = $m[1];
    } elseif (preg_match('/\b#([A-Za-z0-9][A-Za-z0-9-]{3,39})\b/u', $raw, $m)) {
        $ref = $m[1];
    }

    if (!$hasOrderIntent && $ref === null) {
        return null;
    }

    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    $role   = isset($_SESSION['role']) ? (string) $_SESSION['role'] : 'customer';

    if ($userId <= 0 || $role === 'admin') {
        if ($role === 'admin') {
            return [
                'reply'        => 'Admin accounts don’t use customer purchase history in this chat. Customers should sign in and ask “my purchases”, or you can use Admin Dashboard for orders.',
                'matched_rule' => 'order_admin',
            ];
        }

        return [
            'reply'        => "To see your purchases and statuses, please log in first-then open your account or ask me again for “my recent purchases”. If you’re waiting on delivery, ask “when will my order arrive?” for general timing.\n\nHave a reference number? Keep it handy for Contact if you need direct help.",
            'matched_rule' => 'order_guest',
        ];
    }

    if ($ref !== null && $hasOrderIntent === false) {
        $hasOrderIntent = true;
    }

    if ($ref !== null) {
        if (ctype_digit($ref)) {
            $oid = (int) $ref;
            $stmt = $conn->prepare('
                SELECT order_number, order_date, payment_status, delivery_status, total_amount, currency
                FROM orders
                WHERE user_id = ? AND order_id = ?
                LIMIT 1
            ');
            if ($stmt) {
                $stmt->bind_param('ii', $userId, $oid);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res ? $res->fetch_assoc() : null;
                $stmt->close();
                if ($row) {
                    return [
                        'reply'        => nova_format_order_detail($row),
                        'matched_rule' => 'order_one',
                    ];
                }
            }
        }

        $stmt = $conn->prepare('
            SELECT order_number, order_date, payment_status, delivery_status, total_amount, currency
            FROM orders
            WHERE user_id = ? AND order_number = ?
            LIMIT 1
        ');
        if ($stmt) {
            $stmt->bind_param('is', $userId, $ref);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();
            if ($row) {
                return [
                    'reply'        => nova_format_order_detail($row),
                    'matched_rule' => 'order_one',
                ];
            }
        }

        return [
            'reply'        => "I couldn’t find an order matching \"{$ref}\" on your account. Check the reference from your email, or ask “my recent purchases” to see what we have.",
            'matched_rule' => 'order_missing',
        ];
    }

    $stmt = $conn->prepare('
        SELECT order_number, order_date, payment_status, delivery_status, total_amount, currency
        FROM orders
        WHERE user_id = ?
        ORDER BY order_date DESC
        LIMIT 8
    ');
    if (!$stmt) {
        return [
            'reply'        => 'I couldn’t load your purchases just now. Please try again in a moment or visit your account page.',
            'matched_rule' => 'order_error',
        ];
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $lines = [];
    while ($row = $res->fetch_assoc()) {
        $lines[] = nova_format_order_line($row);
    }
    $stmt->close();

    if ($lines === []) {
        return [
            'reply'        => "You don’t have any purchases on file yet. When you check out, they’ll show up here-ask again anytime, or browse Perfumes to find something new.",
            'matched_rule' => 'order_empty',
        ];
    }

    return [
        'reply'        => "Here are your recent purchases (newest first):\n\n" . implode("\n", $lines),
        'matched_rule' => 'order_list',
    ];
}

