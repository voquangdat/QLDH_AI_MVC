{{-- ========== VOXBOT AI CHATBOT WIDGET ========== --}}
<style>
/* ---- Floating Button ---- */
.voxbot-fab {
    position: fixed;
    bottom: 100px;
    right: 28px;
    z-index: 9998;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-radius: 50%;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(37,99,235,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
.voxbot-fab:hover { transform: scale(1.1); box-shadow: 0 6px 26px rgba(37,99,235,.55); }
.voxbot-fab i { color: #fff; font-size: 22px; transition: opacity .2s; }
.voxbot-fab .icon-close { display: none; }
.voxbot-fab.open .icon-chat  { display: none; }
.voxbot-fab.open .icon-close { display: block; }

/* Notification dot */
.voxbot-notif {
    position: absolute;
    top: 2px; right: 2px;
    width: 12px; height: 12px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid #fff;
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%,100% { transform: scale(1); }
    50%      { transform: scale(1.3); }
}

/* ---- Chat Window ---- */
.voxbot-window {
    position: fixed;
    bottom: 165px;
    right: 28px;
    z-index: 9997;
    width: 360px;
    max-height: 520px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.8) translateY(20px);
    transform-origin: bottom right;
    opacity: 0;
    pointer-events: none;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
}
.voxbot-window.open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: all;
}

/* Header */
.voxbot-header {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.voxbot-avatar {
    width: 38px; height: 38px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff;
    flex-shrink: 0;
}
.voxbot-header-info { flex: 1; }
.voxbot-header-info strong { color: #fff; font-size: 14px; display: block; }
.voxbot-header-info span  { color: rgba(255,255,255,.8); font-size: 11px; }
.voxbot-status-dot {
    width: 8px; height: 8px;
    background: #4ade80;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}

/* Messages */
.voxbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8faff;
}
.voxbot-messages::-webkit-scrollbar { width: 4px; }
.voxbot-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

/* Message bubbles */
.vb-msg { display: flex; gap: 8px; align-items: flex-end; max-width: 100%; }
.vb-msg.bot  { justify-content: flex-start; }
.vb-msg.user { justify-content: flex-end; }
.vb-msg-avatar {
    width: 28px; height: 28px;
    background: #2563eb;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: #fff;
    flex-shrink: 0;
}
.vb-bubble {
    max-width: 78%;
    padding: 9px 13px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
}
.vb-msg.bot  .vb-bubble { background: #fff; color: #333; border-radius: 4px 16px 16px 16px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
.vb-msg.user .vb-bubble { background: #2563eb; color: #fff; border-radius: 16px 16px 4px 16px; }

/* Typing indicator */
.vb-typing { display: flex; align-items: center; gap: 4px; padding: 10px 13px; }
.vb-typing span {
    width: 7px; height: 7px;
    background: #9ca3af;
    border-radius: 50%;
    animation: typing-bounce .8s infinite;
}
.vb-typing span:nth-child(2) { animation-delay: .15s; }
.vb-typing span:nth-child(3) { animation-delay: .3s; }
@keyframes typing-bounce {
    0%,80%,100% { transform: translateY(0); }
    40%          { transform: translateY(-6px); }
}

/* Quick chips */
.voxbot-chips {
    padding: 6px 12px 2px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    flex-shrink: 0;
    background: #f8faff;
    border-top: 1px solid #f0f0f0;
}
.vb-chip {
    padding: 4px 10px;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    font-size: 11px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s;
}
.vb-chip:hover { background: #2563eb; color: #fff; border-color: #2563eb; }

/* Input */
.voxbot-input-wrap {
    padding: 10px 12px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
    background: #fff;
}
.voxbot-input {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 22px;
    padding: 8px 14px;
    font-size: 13px;
    outline: none;
    transition: border-color .2s;
    resize: none;
    max-height: 80px;
    overflow-y: auto;
    line-height: 1.4;
}
.voxbot-input:focus { border-color: #2563eb; }
.voxbot-send {
    width: 36px; height: 36px;
    background: #2563eb;
    border: none;
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .2s;
}
.voxbot-send:hover { background: #1d4ed8; }
.voxbot-send:disabled { background: #9ca3af; cursor: not-allowed; }

@media (max-width: 420px) {
    .voxbot-window { width: calc(100vw - 24px); right: 12px; }
    .voxbot-fab    { bottom: 16px; right: 16px; }
}
</style>

{{-- FAB Button --}}
<button class="voxbot-fab" id="voxbot-fab" title="Chat với VoxBot AI">
    <span class="voxbot-notif" id="voxbot-notif"></span>
    <i class="fas fa-comments icon-chat"></i>
    <i class="fas fa-times icon-close"></i>
</button>

{{-- Chat Window --}}
<div class="voxbot-window" id="voxbot-window">
    <div class="voxbot-header">
        <div class="voxbot-avatar"><i class="fas fa-robot"></i></div>
        <div class="voxbot-header-info">
            <strong>VoxBot AI</strong>
            <span><span class="voxbot-status-dot"></span>Trợ lý VoxFootball</span>
        </div>
    </div>

    <div class="voxbot-messages" id="voxbot-messages">
        <div class="vb-msg bot">
            <div class="vb-msg-avatar"><i class="fas fa-robot"></i></div>
            <div class="vb-bubble">
                Xin chào! Tôi là <strong>VoxBot</strong> 👋<br>
                Tôi có thể giúp bạn tư vấn sản phẩm, tra cứu đơn hàng hoặc giải đáp thắc mắc. Bạn cần hỗ trợ gì?
            </div>
        </div>
    </div>

    {{-- Quick question chips --}}
    <div class="voxbot-chips" id="voxbot-chips">
        <button class="vb-chip" data-msg="Cho tôi xem các sản phẩm áo đấu">👕 Áo đấu</button>
        <button class="vb-chip" data-msg="Đơn hàng của tôi đang ở đâu?">📦 Đơn hàng</button>
        <button class="vb-chip" data-msg="Chính sách đổi trả như thế nào?">🔄 Đổi trả</button>
        <button class="vb-chip" data-msg="Phí ship bao nhiêu?">🚚 Phí ship</button>
    </div>

    <div class="voxbot-input-wrap">
        <textarea class="voxbot-input" id="voxbot-input"
                  placeholder="Nhập câu hỏi..." rows="1"></textarea>
        <button class="voxbot-send" id="voxbot-send" disabled>
            <i class="fas fa-paper-plane" style="font-size:13px;"></i>
        </button>
    </div>
</div>

<script>
(function () {
    const CHAT_URL   = "{{ route('chat.ai') }}";
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const STORE_KEY  = 'voxbot_session'; // sessionStorage key

    const fab      = document.getElementById('voxbot-fab');
    const win      = document.getElementById('voxbot-window');
    const messages = document.getElementById('voxbot-messages');
    const input    = document.getElementById('voxbot-input');
    const sendBtn  = document.getElementById('voxbot-send');
    const notif    = document.getElementById('voxbot-notif');
    const chips    = document.getElementById('voxbot-chips');

    // ── Khôi phục session từ sessionStorage ──────────────────────────────────
    let session  = loadSession();   // { history: [{role,text}], messages: [{role,html}] }
    let isTyping = false;

    function loadSession() {
        try {
            const raw = sessionStorage.getItem(STORE_KEY);
            if (raw) return JSON.parse(raw);
        } catch {}
        return { history: [], messages: [] };
    }

    function saveSession() {
        try { sessionStorage.setItem(STORE_KEY, JSON.stringify(session)); } catch {}
    }

    function clearSession() {
        session = { history: [], messages: [] };
        sessionStorage.removeItem(STORE_KEY);
    }

    // ── Vẽ lại tin nhắn đã lưu khi load trang mới ────────────────────────────
    function restoreMessages() {
        if (session.messages.length === 0) return;

        // Ẩn lời chào mặc định
        const welcome = messages.querySelector('.vb-msg.bot');
        if (welcome) welcome.style.display = 'none';

        // Ẩn chips nếu đã từng chat
        chips.style.display = 'none';

        session.messages.forEach(({ role, html }) => {
            const div    = document.createElement('div');
            div.className = 'vb-msg ' + role;
            if (role === 'bot') {
                div.innerHTML = '<div class="vb-msg-avatar"><i class="fas fa-robot"></i></div>'
                    + '<div class="vb-bubble">' + html + '</div>';
            } else {
                div.innerHTML = '<div class="vb-bubble">' + html + '</div>';
            }
            messages.appendChild(div);
        });
    }

    // Khôi phục ngay khi trang load
    restoreMessages();

    // ── Toggle open/close ─────────────────────────────────────────────────────
    fab.addEventListener('click', () => {
        const isOpen = win.classList.toggle('open');
        fab.classList.toggle('open', isOpen);
        notif.style.display = 'none';
        if (isOpen) { input.focus(); scrollBottom(); }
    });

    // ── Input auto-resize + enable send ──────────────────────────────────────
    input.addEventListener('input', function () {
        sendBtn.disabled = !this.value.trim();
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled && !isTyping) sendMessage();
        }
    });

    sendBtn.addEventListener('click', () => { if (!isTyping) sendMessage(); });

    // ── Quick chips ───────────────────────────────────────────────────────────
    chips.querySelectorAll('.vb-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            input.value = this.dataset.msg;
            sendBtn.disabled = false;
            chips.style.display = 'none';
            sendMessage();
        });
    });

    // ── Gửi tin nhắn ─────────────────────────────────────────────────────────
    async function sendMessage() {
        const text = input.value.trim();
        if (!text || isTyping) return;

        // Ẩn chips sau lần chat đầu
        chips.style.display = 'none';

        const html = formatText(text);
        appendBubble('user', html);
        session.history.push({ role: 'user', text });
        session.messages.push({ role: 'user', html });
        saveSession();

        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;

        showTyping();
        isTyping = true;

        try {
            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('message', text);

            // Gửi history (trừ tin nhắn vừa thêm)
            session.history.slice(0, -1).forEach((msg, i) => {
                fd.append(`history[${i}][role]`, msg.role);
                fd.append(`history[${i}][text]`, msg.text);
            });

            const res  = await fetch(CHAT_URL, { method: 'POST', body: fd });
            const data = await res.json();

            hideTyping();
            const reply     = data.message ?? 'Xin lỗi, có lỗi xảy ra!';
            const replyHtml = formatText(reply);

            appendBubble('bot', replyHtml);
            session.history.push({ role: 'model', text: reply });
            session.messages.push({ role: 'bot', html: replyHtml });

            // Giới hạn 20 lượt
            if (session.history.length > 20)  session.history  = session.history.slice(-20);
            if (session.messages.length > 20)  session.messages = session.messages.slice(-20);

            saveSession();

        } catch {
            hideTyping();
            appendBubble('bot', 'Kết nối thất bại. Vui lòng thử lại sau!');
        } finally {
            isTyping = false;
        }
    }

    // ── Render bubble ─────────────────────────────────────────────────────────
    function appendBubble(role, html) {
        const isBot = role === 'bot';
        const div   = document.createElement('div');
        div.className = 'vb-msg ' + role;
        if (isBot) {
            div.innerHTML = '<div class="vb-msg-avatar"><i class="fas fa-robot"></i></div>'
                + '<div class="vb-bubble">' + html + '</div>';
        } else {
            div.innerHTML = '<div class="vb-bubble">' + html + '</div>';
        }
        messages.appendChild(div);
        scrollBottom();
    }

    function showTyping() {
        const div = document.createElement('div');
        div.className = 'vb-msg bot';
        div.id = 'vb-typing-row';
        div.innerHTML = '<div class="vb-msg-avatar"><i class="fas fa-robot"></i></div>'
            + '<div class="vb-bubble vb-typing"><span></span><span></span><span></span></div>';
        messages.appendChild(div);
        scrollBottom();
    }

    function hideTyping() {
        document.getElementById('vb-typing-row')?.remove();
    }

    function scrollBottom() {
        setTimeout(() => { messages.scrollTop = messages.scrollHeight; }, 30);
    }

    function formatText(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
    }

    // ── Notification dot sau 3s ───────────────────────────────────────────────
    setTimeout(() => {
        if (!win.classList.contains('open')) notif.style.display = 'block';
    }, 3000);
})();
</script>
