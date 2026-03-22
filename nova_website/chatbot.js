(function () {
    'use strict';

    var root = document.getElementById('nova-chatbot');
    if (!root) return;

    var toggle = document.getElementById('nova-chatbot-toggle');
    var closeBtn = document.getElementById('nova-chatbot-close');
    var panel = document.getElementById('nova-chatbot-panel');
    var messagesEl = document.getElementById('nova-chatbot-messages');
    var form = document.getElementById('nova-chatbot-form');
    var input = document.getElementById('nova-chatbot-input');
    var suggestionsEl = document.getElementById('nova-chatbot-suggestions');
    var sendBtn = form ? form.querySelector('button[type="submit"]') : null;
    var apiUrl = root.getAttribute('data-api') || 'perfume_chatbot_api.php';
    var avatarSrc = root.getAttribute('data-avatar') || 'noa_icon.png';
    var pendingRequest = false;

    function syncNovaChatbotSafeWidth() {
        var w = document.documentElement && document.documentElement.clientWidth;
        if (!w) w = window.innerWidth || 0;
        root.style.setProperty('--nova-chatbot-safe-w', Math.max(0, w - 32) + 'px');
    }

    /** Keeps panel bottom above launcher; max height = space from viewport top to panel bottom so header/close stay visible. */
    function syncNovaChatbotPanelMaxHeight() {
        var launcher = root.querySelector('.nova-chatbot-launcher');
        if (!launcher) return;
        var top = launcher.getBoundingClientRect().top;
        var panelMarginAboveLauncher = 14;
        var minTopInset = 12;
        var avail = Math.floor(top - panelMarginAboveLauncher - minTopInset);
        if (avail < 0) avail = 0;
        var cap = Math.min(620, avail);
        root.style.setProperty('--nova-chatbot-panel-max-h', cap + 'px');
    }

    function syncNovaChatbotLayout() {
        syncNovaChatbotSafeWidth();
        var w = document.documentElement && document.documentElement.clientWidth;
        if (!w) w = window.innerWidth || 0;
        var frac = w < 400 ? 0.96 : w < 520 ? 0.9 : w < 720 ? 0.85 : 0.8;
        root.style.setProperty('--nova-chatbot-panel-w-frac', String(frac));
        var maxW = w < 480 ? 340 : 320;
        root.style.setProperty('--nova-chatbot-panel-max-w', maxW + 'px');
        var fontScale = w < 380 ? 0.88 : w < 450 ? 0.92 : w < 560 ? 0.96 : 1;
        root.style.setProperty('--nova-chatbot-font-scale', String(fontScale));
        syncNovaChatbotPanelMaxHeight();
    }
    syncNovaChatbotLayout();
    window.addEventListener('resize', syncNovaChatbotLayout);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', syncNovaChatbotLayout);
    }

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function formatChatTime(d) {
        return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
    }

    function appendUserMessage(text) {
        var time = formatChatTime(new Date());
        var wrap = document.createElement('div');
        wrap.className = 'nova-chatbot-msg nova-chatbot-msg--user';
        wrap.innerHTML =
            '<div class="nova-chatbot-imessage-row nova-chatbot-imessage-row--user">' +
            '<div class="nova-chatbot-imessage-stack">' +
            '<div class="nova-chatbot-bubble nova-chatbot-bubble--user">' +
            escapeHtml(text) +
            '</div>' +
            '<span class="nova-chatbot-msg-time">' +
            escapeHtml(time) +
            '</span>' +
            '</div></div>';
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function buildProductCard(p) {
        var card = document.createElement('a');
        card.className = 'nova-chatbot-product';
        card.href = p.product_url || 'product_page.php?id=' + p.product_id;
        var imgSrc = p.image ? 'images/' + encodeURIComponent(p.image) : '';
        card.innerHTML =
            (imgSrc
                ? '<span class="nova-chatbot-product-img"><img src="' + escapeHtml(imgSrc) + '" alt=""></span>'
                : '') +
            '<span class="nova-chatbot-product-meta">' +
            '<span class="nova-chatbot-product-name">' +
            escapeHtml(p.name) +
            '</span>' +
            '<span class="nova-chatbot-product-cat">' +
            escapeHtml(p.category_name || '') +
            '</span>' +
            '<span class="nova-chatbot-product-price">£' +
            (typeof p.price === 'number' ? p.price.toFixed(2) : p.price) +
            '</span></span>';
        return card;
    }

    function appendBotResponse(text, products) {
        var time = formatChatTime(new Date());
        var wrap = document.createElement('div');
        wrap.className = 'nova-chatbot-msg nova-chatbot-msg--bot';
        var row = document.createElement('div');
        row.className = 'nova-chatbot-imessage-row';

        var av = document.createElement('img');
        av.className = 'nova-chatbot-msg-avatar';
        av.src = avatarSrc;
        av.width = 30;
        av.height = 30;
        av.alt = 'NOA';
        av.decoding = 'async';

        var stack = document.createElement('div');
        stack.className = 'nova-chatbot-imessage-stack';

        if (text) {
            var bubble = document.createElement('div');
            bubble.className = 'nova-chatbot-bubble nova-chatbot-bubble--bot';
            bubble.textContent = text;
            stack.appendChild(bubble);
        }

        if (products && products.length) {
            var prodWrap = document.createElement('div');
            prodWrap.className = 'nova-chatbot-products';
            products.forEach(function (p) {
                prodWrap.appendChild(buildProductCard(p));
            });
            stack.appendChild(prodWrap);
        }

        var ti = document.createElement('span');
        ti.className = 'nova-chatbot-msg-time';
        ti.textContent = time;
        stack.appendChild(ti);

        row.appendChild(av);
        row.appendChild(stack);
        wrap.appendChild(row);
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendLoadingBot() {
        var time = formatChatTime(new Date());
        var wrap = document.createElement('div');
        wrap.className = 'nova-chatbot-msg nova-chatbot-msg--bot';

        var row = document.createElement('div');
        row.className = 'nova-chatbot-imessage-row nova-chatbot-imessage-row--loading';

        var stack = document.createElement('div');
        stack.className = 'nova-chatbot-imessage-stack';

        var bubble = document.createElement('div');
        bubble.className = 'nova-chatbot-bubble nova-chatbot-bubble--bot nova-chatbot-loading';
        bubble.innerHTML =
            '<span class="nova-chatbot-loading-dots" aria-hidden="true">' +
            '<span class="nova-chatbot-loading-dot"></span>' +
            '<span class="nova-chatbot-loading-dot"></span>' +
            '<span class="nova-chatbot-loading-dot"></span>' +
            '</span>';
        stack.appendChild(bubble);

        var ti = document.createElement('span');
        ti.className = 'nova-chatbot-msg-time';
        ti.textContent = time;
        stack.appendChild(ti);

        row.appendChild(stack);
        wrap.appendChild(row);
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return wrap;
    }

    function setSuggestions(items) {
        suggestionsEl.innerHTML = '';
        if (!items || !items.length) return;
        items.forEach(function (label) {
            var chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'nova-chatbot-chip';
            chip.textContent = label;
            chip.addEventListener('click', function () {
                sendMessageFromUser(label);
            });
            suggestionsEl.appendChild(chip);
        });
    }

    function openPanel() {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        syncNovaChatbotPanelMaxHeight();
        requestAnimationFrame(function () {
            syncNovaChatbotPanelMaxHeight();
            if (input) input.focus();
        });
    }

    function closePanel() {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        if (panel.hidden) openPanel();
        else closePanel();
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closePanel);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) closePanel();
    });

    function fetchAssistantReply(msg) {
        if (pendingRequest) return;
        pendingRequest = true;

        if (sendBtn) sendBtn.disabled = true;
        if (input) input.disabled = true;

        var loadingWrap = appendLoadingBot();
        var start = Date.now();

        fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: msg }),
        })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                        var wait = Math.max(0, 1000 - (Date.now() - start));
                setTimeout(function () {
                    try {
                        if (loadingWrap && loadingWrap.parentNode) {
                            loadingWrap.parentNode.removeChild(loadingWrap);
                        }
                    } catch (e) {}

                    if (!data || !data.ok) {
                        appendBotResponse('Something went wrong. Please try again.', []);
                        pendingRequest = false;
                        if (sendBtn) sendBtn.disabled = false;
                        if (input) input.disabled = false;
                        return;
                    }
                    appendBotResponse(data.reply || '', data.products || []);
                    setSuggestions(data.suggestions);

                    pendingRequest = false;
                    if (sendBtn) sendBtn.disabled = false;
                    if (input) input.disabled = false;
                }, wait);
            })
            .catch(function () {
                var wait = Math.max(0, 1000 - (Date.now() - start));
                setTimeout(function () {
                    try {
                        if (loadingWrap && loadingWrap.parentNode) {
                            loadingWrap.parentNode.removeChild(loadingWrap);
                        }
                    } catch (e) {}
                    appendBotResponse('Could not reach NOA. Check your connection.', []);

                    pendingRequest = false;
                    if (sendBtn) sendBtn.disabled = false;
                    if (input) input.disabled = false;
                }, wait);
            });
    }

    function sendMessageFromUser(text) {
        var msg = String(text).trim();
        if (!msg) return;
        if (pendingRequest) return;
        appendUserMessage(msg);
        fetchAssistantReply(msg);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var msg = (input.value || '').trim();
        if (!msg) return;
        input.value = '';
        sendMessageFromUser(msg);
    });

    fetchAssistantReply('');
})();
