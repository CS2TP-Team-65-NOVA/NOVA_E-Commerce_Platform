<?php

function nova_default_topic_suggestions(): array
{
    return [
        'When will my order arrive?',
        'How long does delivery take?',
        'My recent purchases',
        'How do returns work?',
        'Track my order',
        'Order status',
    ];
}

/**
 * rule based FAQs (shipping, returns, payments, contact).
 */
function nova_try_faq(string $t): ?string
{
    if (preg_match('/\b(when (will|do)|how long|how soon).{0,48}\b(order|parcel|package|deliver|arriv|ship|come)|\b(order|parcel|package).{0,24}\b(arriv|ship|deliver|dispatched)\b/u', $t)) {
        return "Most UK standard deliveries arrive within about 3-5 working days after dispatch. You’ll get an email when your order ships. For what’s recorded on your account (payment & dispatch status), log in and ask me “my recent purchases”-or use Contact if something looks stuck.";
    }

    if (preg_match('/\b(faq|faqs|frequently asked|common questions)\b/u', $t)) {
        return "Here’s what people ask us most:\n\n"
            . "• Shipping & delivery times\n"
            . "• Returns & refunds\n"
            . "• Payments\n"
            . "• Order status (log in first, then ask “my purchases”)\n"
            . "• Perfume recommendations\n\n"
            . 'Ask in your own words-I’ll answer from our shop policies.';
    }

    if (preg_match('/\b(ship|shipping|dispatch|dispatched|when will it arrive|how long|delivery time|courier|parcel|tracked)\b/u', $t)) {
        return "Most UK standard deliveries arrive within about 3-5 working days after dispatch. You’ll get an email when your order ships. For a specific purchase, log in and ask me “my recent purchases”-or use the Contact page if you need help with tracking.";
    }

    if (preg_match('/\b(return|returns|refund|refunded|exchange|send back|money back)\b/u', $t)) {
        return "Want to make a return? Start from your order details after logging in, or reach out via Contact with your order number. We’ll confirm what’s eligible and how to send items back-usually within the timeframe in our returns policy.";
    }

    if (preg_match('/\b(pay|payment|card|paypal|apple pay|klarna|charged|invoice)\b/u', $t)) {
        return 'We accept major cards and the options shown at checkout (e.g. PayPal or Apple Pay where enabled). Your receipt is emailed when payment succeeds. If payment stays pending, wait a few minutes-then Contact us with your order reference.';
    }

    if (preg_match('/\b(contact|email|phone|speak to|customer service|support|help desk)\b/u', $t)) {
        return "The fastest way to reach the team is our Contact page. For order questions, include your order number if you have one-that speeds things up.";
    }

    if (preg_match('/\b(account|log in|login|sign in|password|forgot|register)\b/u', $t)) {
        return 'Use Log in or Register in the site header for your account and order history. If you’ve forgotten your password, use your site’s reset flow or contact support.';
    }

    if (preg_match('/\b(opening|opening hours|store hours|shop address|physical store)\b/u', $t)) {
        return 'This NOVA storefront is mainly online. For anything that needs a person, use Contact-we’ll point you to the right team.';
    }

    return null;
}

/**
 * common words that could be said by the user
 */
function nova_try_topic_button_menu(string $t, string $raw): ?array
{
    if (!preg_match('/\b(order|orders|delivery|deliver|shipping|ship|dispatch|returns?|refund|refunds|tracking|tracked|parcel|package)\b/u', $t)) {
        return null;
    }

    if (preg_match('/\b(perfume|fragrances?|scent|citrus|floral|spicy|oriental|for her|for him|women|men|under\s*£)/u', $t)) {
        return null;
    }

    if (preg_match('/\b(my orders|my recent orders|order history|recent orders|purchase history|order details|show orders|list orders|all orders|order status|track my order|track(?:ing)?\s+(?:my\s+)?orders?|have i ordered)\b/u', $t)) {
        return null;
    }

    if (preg_match('/\b(when|how long|how much|how do|how can|how does|what is|what are|where is|where\'?s|why|can i|could i|should i|will my|won\'?t|haven\'?t|didn\'?t receive|hasn\'?t arrived|already paid)\b/u', $t)) {
        return null;
    }

    if (preg_match('/\b(?:order|ord)\s*[#:]?\s*[A-Za-z0-9][A-Za-z0-9-]{1,39}\b/u', $raw) || preg_match('/\b#([A-Za-z0-9][A-Za-z0-9-]{1,39})\b/u', $raw)) {
        return null;
    }

    $suggestions = nova_default_topic_suggestions();

    $reply = "I can help with delivery, shipping, and returns. Pick a topic below (same as tapping a quick reply in messages)-or type your own question.";

    return [
        'reply'        => $reply,
        'suggestions' => $suggestions,
        'matched_rule' => 'topic_buttons',
    ];
}

