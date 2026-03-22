<?php
/**
 * NOA include once per page before </body>.
 * Requires style.css (site global) for NOVA variables / look.
 */
$novaChatbotApi = 'chatbot/perfume_chatbot_api.php';
$novaPage = strtolower(basename($_SERVER['PHP_SELF'] ?? ''));
$novaCalloutLine = 'Need help finding your perfect scent?';

if ($novaPage === 'perfumes.php') {
    $novaCalloutLine = 'Looking for citrus under £70?';
} elseif ($novaPage === 'checkout.php') {
    $novaCalloutLine = 'Questions about delivery?';
} elseif ($novaPage === 'index.php') {
    $novaCalloutLine = 'Discover your signature scent';
}
?>
<div id="nova-chatbot" class="nova-chatbot" data-api="<?php echo htmlspecialchars($novaChatbotApi, ENT_QUOTES, 'UTF-8'); ?>" data-avatar="<?php echo htmlspecialchars('noa_icon.png', ENT_QUOTES, 'UTF-8'); ?>">
    <div id="nova-chatbot-panel" class="nova-chatbot-panel" hidden>
        <div class="nova-chatbot-panel-inner">
            <div class="nova-chatbot-header">
                <div class="nova-chatbot-title">
                    <span class="nova-chatbot-title-main">NOA</span>
                    <span class="nova-chatbot-title-sub">Messages</span>
                </div>
                <button type="button" class="nova-chatbot-close" id="nova-chatbot-close" aria-label="Close NOA">&times;</button>
            </div>
            <div id="nova-chatbot-messages" class="nova-chatbot-messages" aria-live="polite"></div>
            <div id="nova-chatbot-suggestions" class="nova-chatbot-suggestions"></div>
            <form id="nova-chatbot-form" class="nova-chatbot-form">
                <label class="nova-chatbot-sr-only" for="nova-chatbot-input">Your message</label>
                <input type="text" id="nova-chatbot-input" class="nova-chatbot-input" autocomplete="off" placeholder="Ask about perfumes, delivery, returns…" />
                <button type="submit" class="nova-chatbot-send">Send</button>
            </form>
        </div>
    </div>
    <div class="nova-chatbot-launcher">
        <div class="nova-chatbot-callout" id="nova-chatbot-callout">
            <div class="nova-chatbot-callout-inner">
                <span class="nova-chatbot-callout-line"><?php echo htmlspecialchars($novaCalloutLine, ENT_QUOTES, 'UTF-8'); ?></span>
                <button
                    type="button"
                    class="nova-chatbot-callout-cta"
                    id="nova-chatbot-callout-cta"
                    aria-label="Try NOA"
                    onclick="(function(){var t=document.getElementById('nova-chatbot-toggle'); if(t){t.click();}})();"
                >
                    TRY NOA ✨
                </button>
            </div>
        </div>
        <button type="button" id="nova-chatbot-toggle" class="nova-chatbot-toggle" aria-expanded="false" aria-controls="nova-chatbot-panel" aria-describedby="nova-chatbot-callout" title="Open NOA">
            <span class="nova-chatbot-avatar-ring" aria-hidden="true">
                <img src="noa_icon.png" class="nova-chatbot-toggle-img" width="48" height="48" alt="" decoding="async" />
            </span>
            <span class="nova-chatbot-sr-only">Open NOA</span>
        </button>
    </div>
</div>
<script>
(function () {
    var r = document.getElementById('nova-chatbot');
    if (!r) return;
    var w = document.documentElement && document.documentElement.clientWidth;
    if (!w) w = window.innerWidth || 0;
    r.style.setProperty('--nova-chatbot-safe-w', Math.max(0, w - 32) + 'px');
    var frac = w < 400 ? 0.96 : w < 520 ? 0.9 : w < 720 ? 0.85 : 0.8;
    r.style.setProperty('--nova-chatbot-panel-w-frac', String(frac));
    r.style.setProperty('--nova-chatbot-panel-max-w', (w < 480 ? 340 : 320) + 'px');
    var fontScale = w < 380 ? 0.88 : w < 450 ? 0.92 : w < 560 ? 0.96 : 1;
    r.style.setProperty('--nova-chatbot-font-scale', String(fontScale));
    var launcher = r.querySelector('.nova-chatbot-launcher');
    if (launcher) {
        var top = launcher.getBoundingClientRect().top;
        var avail = Math.floor(top - 14 - 12);
        if (avail < 0) avail = 0;
        r.style.setProperty('--nova-chatbot-panel-max-h', Math.min(620, avail) + 'px');
    }
})();
</script>
<script src="chatbot.js" defer></script>
