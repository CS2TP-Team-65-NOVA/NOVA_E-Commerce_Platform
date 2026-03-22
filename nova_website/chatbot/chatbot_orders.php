<?php

/**
 * Friendly labels for orders.payment_status (enum: success, pending, refunded).
 */
function nova_payment_status_friendly(string $status): string
{
    switch (strtolower($status)) {
        case 'success':
            return 'Paid';
        case 'pending':
            return 'Payment pending';
        case 'refunded':
            return 'Refunded';
        default:
            return ucfirst($status);
    }
}

/**
 * Friendly labels for orders.delivery_status (enum: processing, shipped, delivered, returned).
 */
function nova_delivery_status_friendly(string $status): string
{
    switch (strtolower($status)) {
        case 'processing':
            return 'Processing — we are preparing your order.';
        case 'shipped':
            return 'Shipped — it is on the way to you.';
        case 'delivered':
            return 'Delivered.';
        case 'returned':
            return 'Returned.';
        default:
            return ucfirst($status);
    }
}

function nova_format_order_line(array $row): string
{
    $num = (string) ($row['order_number'] ?? 'Order');
    $d   = isset($row['order_date']) ? date('j M Y', strtotime((string) $row['order_date'])) : '';
    $pay = nova_payment_status_friendly((string) ($row['payment_status'] ?? ''));
    $del = ucfirst((string) ($row['delivery_status'] ?? ''));
    $cur = $row['currency'] ?? 'GBP';
    $tot = isset($row['total_amount']) ? number_format((float) $row['total_amount'], 2) : '-';

    return "• {$num} - {$d}\n  Payment: {$pay} · Delivery: {$del} · Total: {$cur} {$tot}";
}

function nova_format_order_detail(array $row): string
{
    $num = (string) ($row['order_number'] ?? '');
    $d   = isset($row['order_date']) ? date('j F Y, H:i', strtotime((string) $row['order_date'])) : '';
    $pay = nova_payment_status_friendly((string) ($row['payment_status'] ?? ''));
    $del = nova_delivery_status_friendly((string) ($row['delivery_status'] ?? ''));
    $cur = $row['currency'] ?? 'GBP';
    $tot = isset($row['total_amount']) ? number_format((float) $row['total_amount'], 2) : '-';

    $lines = [
        "Order {$num}",
        '',
        "Placed: {$d}",
        "Payment: {$pay}",
        "Status: {$del}",
        "Total: {$cur} {$tot}",
    ];

    $products = trim((string) ($row['product_names'] ?? ''));
    if ($products !== '') {
        $lines[] = '';
        $lines[] = 'Items: ' . $products;
    }

    $lines[] = '';
    $lines[] = 'Need more help? Use Contact with this order number or visit your account area.';

    return implode("\n", $lines);
}

/**
 * Pull order reference from the message: "order NOVA-1", "#ABC", or a bare order number on its own line.
 *
 * @return string|null Normalized reference (digits or order_number string)
 */
function nova_extract_order_reference(string $raw): ?string
{
    // Words after "order" that start a budget phrase, not an order reference (e.g. "order under £65" → perfumes).
    static $notOrderRef = [
        'under', 'below', 'above', 'over', 'around', 'about', 'between', 'within', 'near', 'upto', 'up-to',
    ];

    // "order …" / "ord …" — min 3 characters in reference (e.g. order 102)
    if (preg_match('/\b(?:order|ord)\s*[#:]?\s*([A-Za-z0-9][A-Za-z0-9-]{1,39})\b/u', $raw, $m)) {
        $cand = $m[1];
        if (!in_array(mb_strtolower($cand, 'UTF-8'), $notOrderRef, true)) {
            return $cand;
        }
    }
    if (preg_match('/\b#([A-Za-z0-9][A-Za-z0-9-]{1,39})\b/u', $raw, $m)) {
        return $m[1];
    }

    $trim = trim($raw);
    if ($trim === '') {
        return null;
    }

    // Whole message is digits only → treat as order_id / reference (3+ digits avoids noise)
    if (preg_match('/^\d{3,12}$/', $trim)) {
        return $trim;
    }

    // Whole message is one token: hyphenated refs (NOVA-2024-001) or mixed alphanumerics with digit (NOVA1024)
    if (preg_match('/^[#]?([A-Za-z0-9][A-Za-z0-9-]{1,39})$/u', $trim, $m)) {
        $c = $m[1];
        if (strpos($c, '-') !== false) {
            return $c;
        }
        if (preg_match('/\d/', $c) && strlen($c) >= 4) {
            return $c;
        }
    }

    return null;
}

/**
 * Single-order SELECT with product names (matches DB: orders, order_items, product_versions, products).
 *
 * @return array<string,mixed>|null
 */
function nova_fetch_order_row_for_user(mysqli $conn, int $userId, string $whereColumn, string $whereValue): ?array
{
    $sub = '(SELECT GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR \', \') '
        . 'FROM order_items oi '
        . 'LEFT JOIN product_versions pv ON pv.size_id = oi.size_id '
        . 'LEFT JOIN products p ON p.product_id = pv.product_id '
        . 'WHERE oi.order_id = o.order_id)';

    if ($whereColumn === 'order_id') {
        $oid = (int) $whereValue;
        $sql = "SELECT o.order_number, o.order_date, o.payment_status, o.delivery_status, o.total_amount, o.currency, {$sub} AS product_names "
            . 'FROM orders o WHERE o.user_id = ? AND o.order_id = ? LIMIT 1';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('ii', $userId, $oid);
    } else {
        $sql = "SELECT o.order_number, o.order_date, o.payment_status, o.delivery_status, o.total_amount, o.currency, {$sub} AS product_names "
            . 'FROM orders o WHERE o.user_id = ? AND o.order_number = ? LIMIT 1';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('is', $userId, $whereValue);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
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

    $ref = nova_extract_order_reference($raw);

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
            $row = nova_fetch_order_row_for_user($conn, $userId, 'order_id', $ref);
            if ($row) {
                return [
                    'reply'        => nova_format_order_detail($row),
                    'matched_rule' => 'order_one',
                ];
            }
        }

        $row = nova_fetch_order_row_for_user($conn, $userId, 'order_number', $ref);
        if ($row) {
            return [
                'reply'        => nova_format_order_detail($row),
                'matched_rule' => 'order_one',
            ];
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
