<style>
    /* Animasi muncul halus */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Layout Baris Pesan */
    .bb-msg-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        animation: fadeIn 0.3s ease;
        width: 100%;
    }

    /* Avatar (Foto Profil Emoji) */
    .bb-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Bubble Chat Dasar */
    .bb-bubble {
        position: relative;
        padding: 10px 15px;
        border-radius: 8px;
        max-width: 70%;
        font-size: 14px;
        line-height: 1.5;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        word-wrap: break-word;
    }

    /* --- STYLE BOT (Kiri) --- */
    .bb-msg-bot { justify-content: flex-start; }
    .bb-msg-bot .bb-avatar { margin-right: 10px; background: #2c3e50; color: white; }
    .bb-msg-bot .bb-bubble { 
        background: #ffffff; 
        color: #333; 
        border-top-left-radius: 0; /* Sudut lancip kiri atas */
    }
    /* Buntut Lancip Bot */
    .bb-msg-bot .bb-bubble::before {
        content: ""; position: absolute; top: 0; left: -10px;
        width: 0; height: 0;
        border-top: 10px solid #ffffff;
        border-left: 10px solid transparent;
    }

    /* --- STYLE USER (Kanan) --- */
    .bb-msg-user { flex-direction: row-reverse; } /* Avatar pindah kanan */
    .bb-msg-user .bb-avatar { margin-left: 10px; background: #dcf8c6; border: 1px solid #cceebd; }
    .bb-msg-user .bb-bubble { 
        background: #dcf8c6; /* Hijau WA */
        color: #333; 
        border-top-right-radius: 0; /* Sudut lancip kanan atas */
    }
    /* Buntut Lancip User */
    .bb-msg-user .bb-bubble::before {
        content: ""; position: absolute; top: 0; right: -10px;
        width: 0; height: 0;
        border-top: 10px solid #dcf8c6;
        border-right: 10px solid transparent;
    }
</style>

<div id="bang-batik-wrapper" style="position: fixed; bottom: 25px; right: 25px; z-index: 9999; font-family: 'Segoe UI', Arial, sans-serif;">
    
    <button onclick="toggleChat()" style="background: #2c3e50; color: white; border: none; border-radius: 50%; width: 65px; height: 65px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.3); font-size: 30px; transition: transform 0.2s;">
        🤖
    </button>

    <div id="chat-box" style="display: none; position: absolute; bottom: 85px; right: 0; 
        width: 500px; /* LEBAR */
        height: 600px; /* TINGGI */
        max-width: 90vw; max-height: 80vh; /* Aman di HP */
        background: #efe7dd; /* Background warna Cream ala WA */
        border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.25); 
        flex-direction: column; overflow: hidden; border: 1px solid #ccc;">
        
        <div style="background: #2c3e50; color: white; padding: 15px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 35px; height: 35px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">🤖</div>
                <div style="line-height: 1.2;">
                    <div style="font-weight: bold; font-size: 16px;">Bang-Batik</div>
                    <div style="font-size: 11px; opacity: 0.8;">Asisten Jakbar • Online</div>
                </div>
            </div>
            <span onclick="toggleChat()" style="cursor:pointer; font-size: 24px;">&times;</span>
        </div>
        
        <div id="chat-messages" style="flex: 1; padding: 20px; overflow-y: auto; background-color: #efe7dd;">
            
            <div class="bb-msg-row bb-msg-bot">
                <div class="bb-avatar">🤖</div>
                <div class="bb-bubble">
                    <strong>Halo Sobat! 👋</strong><br>
                    Saya Bang-Batik. Ada yang bisa saya bantu terkait wilayah Jakarta Barat?
                </div>
            </div>

        </div>

        <div style="padding: 10px 15px; background: #f0f0f0; display: flex; align-items: center; gap: 10px; border-top: 1px solid #ddd;">
            <input type="text" id="chat-input" placeholder="Ketik pesan..." 
                style="flex: 1; border: none; border-radius: 25px; padding: 12px 20px; outline: none; font-size: 14px; background: white; box-shadow: 0 1px 1px rgba(0,0,0,0.1);">
            
            <button onclick="sendMsg()" style="background: #2c3e50; color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                ➤
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function toggleChat() {
        const box = document.getElementById('chat-box');
        // Fokus ke input saat dibuka
        if (box.style.display === 'none') {
            box.style.display = 'flex';
            setTimeout(() => document.getElementById("chat-input").focus(), 100);
        } else {
            box.style.display = 'none';
        }
    }

    // Fungsi Pembantu untuk nambahin pesan (Biar kodenya rapi)
    function appendChatBubble(sender, message) {
        const area = document.getElementById('chat-messages');
        
        let rowClass = sender === 'user' ? 'bb-msg-user' : 'bb-msg-bot';
        let avatarIcon = sender === 'user' ? '🧑' : '🤖'; // Emoji Profile
        
        // HTML Bubble WA
        const html = `
            <div class="bb-msg-row ${rowClass}">
                <div class="bb-avatar">${avatarIcon}</div>
                <div class="bb-bubble">${message}</div>
            </div>
        `;
        
        area.insertAdjacentHTML('beforeend', html);
        area.scrollTop = area.scrollHeight; // Auto scroll ke bawah
    }

    async function sendMsg() {
        const input = document.getElementById('chat-input');
        const area = document.getElementById('chat-messages');
        const text = input.value.trim();

        if (!text) return;

        // 1. Tampilkan Pesan User (Pakai style WA)
        appendChatBubble('user', text);
        
        input.value = ''; // Kosongkan input
        
        // 2. Tampilkan Loading...
        const loadingId = 'loading-' + Date.now();
        area.insertAdjacentHTML('beforeend', `<div id="${loadingId}" style="font-style: italic; font-size: 12px; color: #7f8c8d; margin-left: 50px; margin-bottom: 10px;">Bang-Batik sedang mengetik...</div>`);
        area.scrollTop = area.scrollHeight;

        try {
            const response = await axios.post("{{ route('bang-batik.ask') }}", 
                { message: text },
                { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }
            );

            // Hapus loading
            document.getElementById(loadingId).remove();

            // 3. Tampilkan Balasan Bot (Pakai style WA)
            // Ganti baris baru (\n) jadi <br> biar rapi
            let botReply = response.data.answer.replace(/\n/g, "<br>");
            appendChatBubble('bot', botReply);

        } catch (e) {
            if(document.getElementById(loadingId)) document.getElementById(loadingId).remove();
            
            let errorMsg = "Maaf, koneksi terputus.";
            if (e.response && e.response.data && e.response.data.answer) {
                errorMsg = e.response.data.answer;
            }
            
            // Tampilkan Error merah kecil
            area.insertAdjacentHTML('beforeend', `<div style="text-align: center; color: red; font-size: 12px; margin: 10px;">⚠️ ${errorMsg}</div>`);
            area.scrollTop = area.scrollHeight;
        }
    }

    // Biar bisa tekan Enter
    document.getElementById('chat-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMsg();
    });
</script>