<?php

/**
 * @return array{
 *   category_ids:int[],
 *   price_min:?float,
 *   price_max:?float,
 *   label:string,
 *   vague:bool
 * }
 */
function nova_parse_perfume_intent(string $t, mysqli $conn): array
{
    $t = mb_strtolower($t, 'UTF-8');

    $hasWomen = (bool) preg_match(
        '/\b(women|womans|womens|woman|female|for her|ladies|lady|she)\b/u',
        $t
    );
    $hasMen = (bool) preg_match(
        '/\b(men|mens|man|male|for him|gent|boyfriend|husband|he)\b/u',
        $t
    );

    $hasCitrus = (bool) preg_match(
        '/\b(citrus|fresh|zesty|bergamot|grapefruit|lemon|lime|mandarin|neroli|orange zest|orange)\b/u',
        $t
    );
    $hasFloral = (bool) preg_match(
        '/\b(floral|flower|rose|jasmine|peony|lily|gardenia|blooming|bouquet)\b/u',
        $t
    );
    $hasSpicy = (bool) preg_match(
        '/\b(spicy|spice|pepper|cinnamon|cardamom|saffron|warm spice|ginger)\b/u',
        $t
    );
    $hasOriental = (bool) preg_match(
        '/\b(oriental|oud|incense|resin|tonka|benzoin)\b/u',
        $t
    );
    $hasAmber = (bool) preg_match('/\b(amber)\b/u', $t);

    $hasSale = (bool) preg_match('/\b(sale|discount|discounted|clearance|deal)\b/u', $t);
    $hasGift = (bool) preg_match('/\b(gift|present|box set|gift set|duo)\b/u', $t);
    $hasExclusive = (bool) preg_match('/\b(exclusive|luxury|luxurious|premium|high-end|highend|niche|limited)\b/u', $t);

    $priceMin = null;
    $priceMax = null;

    if (preg_match('/\b(?:under|below|less than|max|maximum|upto|up to|at most)\s*£?\s*(\d+(?:\.\d+)?)/iu', $t, $m)) {
        $priceMax = (float) $m[1];
    }
    if (preg_match('/\b(?:over|above|more than|min|minimum|at least)\s*£?\s*(\d+(?:\.\d+)?)/iu', $t, $m)) {
        $priceMin = (float) $m[1];
    }
    if (preg_match('/\b(?:between)\s*£?\s*(\d+(?:\.\d+)?)\s*(?:and|to|-)\s*£?\s*(\d+(?:\.\d+)?)/iu', $t, $m)) {
        $a = (float) $m[1];
        $b = (float) $m[2];
        $priceMin = min($a, $b);
        $priceMax = max($a, $b);
    }

    $cheapWord = (bool) preg_match('/\b(cheap|budget|affordable|inexpensive)\b/u', $t);

    if ($hasSale && $priceMax === null && $priceMin === null) {
        $priceMax = 55.0;
    }
    if ($cheapWord && $priceMax === null) {
        $priceMax = 65.0;
    }

    $ids = [];
    $labelBits = [];

    // Priority: category name match from DB + known families (sale, gift, exclusive)
    $res = $conn->query('SELECT category_id, category FROM categories ORDER BY category_id ASC');
    $dbCats = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $dbCats[] = $row;
        }
    }

    $normalize = static function (string $s): string {
        $s = mb_strtolower($s, 'UTF-8');
        $s = str_replace(['perfumes', 'perfume'], '', $s);
        return trim(preg_replace('/[^a-z0-9]+/iu', ' ', $s));
    };

    $tNorm = $normalize($t);

    foreach ($dbCats as $row) {
        $catNorm = $normalize((string) $row['category']);
        if ($catNorm === '') {
            continue;
        }
        $tokens = array_values(array_filter(explode(' ', $catNorm), static fn ($w) => strlen($w) > 1));
        $hit = true;
        foreach ($tokens as $word) {
            if ($word === 'women' || $word === 'men') {
                continue;
            }
            if (!preg_match('/\b' . preg_quote($word, '/') . '\b/u', $tNorm)) {
                $hit = false;
                break;
            }
        }
        if ($hit && count($tokens) > 0) {
            $ids[] = (int) $row['category_id'];
            $labelBits[] = $row['category'];
        }
    }
    $ids = array_values(array_unique($ids));

    if ($hasGift) {
        $ids[] = 9;
        $labelBits[] = 'Gift Box';
    }
    if ($hasSale) {
        $ids[] = 8;
        $labelBits[] = 'Sale';
    }
    if ($hasExclusive) {
        $ids[] = 1;
        $labelBits[] = 'Exclusive Perfumes';
    }

    $ids = array_values(array_unique($ids));

    // notes → broaden to families
    $noteFamily = $hasCitrus || $hasFloral || $hasSpicy || $hasOriental || $hasAmber;
    $onlySpecial = !empty($ids) && empty(array_diff($ids, [1, 8, 9]));
    $applyNotes = $noteFamily && ($hasWomen || $hasMen || empty($ids) || $onlySpecial);

    if ($applyNotes) {
        $add = [];
        if ($hasCitrus) {
            if ($hasWomen && !$hasMen) {
                $add[] = 2;
            } elseif ($hasMen && !$hasWomen) {
                $add[] = 5;
            } else {
                $add[] = 2;
                $add[] = 5;
            }
        }
        if ($hasFloral) {
            if (!$hasMen || $hasWomen) {
                $add[] = 3;
            }
        }
        if ($hasSpicy) {
            if ($hasWomen && !$hasMen) {
                $add[] = 4;
            } elseif ($hasMen && !$hasWomen) {
                $add[] = 7;
            } else {
                $add[] = 4;
                $add[] = 7;
            }
        }
        if ($hasOriental || ($hasAmber && !$hasFloral)) {
            if ($hasMen || (!$hasWomen && !$hasMen)) {
                $add[] = 6;
            }
            if ($hasWomen && !$hasMen) {
                $add[] = 1;
                $add[] = 4;
                if (!in_array('Women / Exclusive-Spicy (closest to oriental in our range)', $labelBits, true)) {
                    $labelBits[] = 'Women / Exclusive-Spicy (closest to oriental in our range)';
                }
            }
        }
        foreach ($add as $id) {
            $ids[] = (int) $id;
        }
        $ids = array_values(array_unique($ids));
    }

    $vague = empty($ids) && $priceMin === null && $priceMax === null;

    $label = 'your search';
    if (!empty($labelBits)) {
        $label = implode(', ', array_unique($labelBits));
    } elseif (!empty($ids)) {
        $label = 'your filters';
    }

    return [
        'category_ids' => $ids,
        'price_min' => $priceMin,
        'price_max' => $priceMax,
        'label' => $label,
        'vague' => $vague,
    ];
}

function nova_try_perfumes(string $message, mysqli $conn, array $defaultSuggestions): array
{
    $intent = nova_parse_perfume_intent($message, $conn);
    $categoryIds = array_filter(array_map('intval', $intent['category_ids']), static fn ($id) => $id > 0);
    $priceMin = $intent['price_min'];
    $priceMax = $intent['price_max'];

    if ($intent['vague']) {
        $reply = "Finding a fragrance that feels right for you matters to us - and you’re in the right place. Our collection is organised by mood and style, so there’s a good chance we have something that fits your taste, whether you want something fresh, soft and floral, warm and spicy, or bold and luxurious.\n\n"
            . "Whenever you’d like tailored picks, tell me a little more: who it’s for (women’s or men’s), the kind of scent you’re drawn to (citrus, floral, spicy, or oriental), or a rough budget (for example under £75). You can also tap a suggestion below - or explore the full range on our Perfumes page.";

        return [
            'ok' => true,
            'reply' => $reply,
            'products' => [],
            'suggestions' => $defaultSuggestions,
            'matched_rule' => 'guidance',
        ];
    }

    $where = ['1=1'];
    if (!empty($categoryIds)) {
        $where[] = 'p.category_id IN (' . implode(',', $categoryIds) . ')';
    }
    if ($priceMin !== null) {
        $where[] = 'p.price >= ' . (float) $priceMin;
    }
    if ($priceMax !== null) {
        $where[] = 'p.price <= ' . (float) $priceMax;
    }

    $products = [];
    $matchedRule = null;

    $sql = "
        SELECT
            p.product_id,
            p.name,
            p.price,
            p.image,
            c.category AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE " . implode(' AND ', $where) . '
        ORDER BY RAND()
        LIMIT 8
    ';

    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $products[] = [
                'product_id' => (int) $row['product_id'],
                'name' => $row['name'],
                'price' => (float) $row['price'],
                'image' => $row['image'],
                'category_name' => $row['category_name'],
                'product_url' => 'product_page.php?id=' . (int) $row['product_id'],
            ];
        }
    }

    if (empty($products)) {
        $matchedRule = 'no_results';
        $reply = "I couldn't find perfumes for {$intent['label']} with those price limits. Try widening the budget or open the Perfumes page - tell me women's or men's plus a style (citrus, floral, spicy, oriental) and I can narrow again.";
    } else {
        $matchedRule = 'matched';
        $n = count($products);
        $pf = [];
        if ($priceMin !== null) {
            $pf[] = 'from £' . number_format($priceMin, 0);
        }
        if ($priceMax !== null) {
            $pf[] = 'up to £' . number_format($priceMax, 0);
        }
        $pLabel = $pf ? ' (' . implode(', ', $pf) . ')' : '';
        $reply = "Here " . ($n === 1 ? 'is' : 'are') . " {$n} suggestion" . ($n === 1 ? '' : 's') . " for {$intent['label']}{$pLabel}:";
    }

    return [
        'ok' => true,
        'reply' => $reply,
        'products' => $products,
        'suggestions' => $defaultSuggestions,
        'matched_rule' => $matchedRule,
    ];
}

