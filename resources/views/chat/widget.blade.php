{{-- ── Widget Sara — Assistant Vocal ── --}}
<div id="sara-widget">

    {{-- Bouton flottant --}}
    <button id="sara-toggle" onclick="toggleSara()" aria-label="Assistant vocal Sara">
        <span id="sara-icon-open"><i class="bi bi-mic-fill"></i></span>
        <span id="sara-icon-close" style="display:none;"><i class="bi bi-x-lg"></i></span>
    </button>

    {{-- Fenêtre --}}
    <div id="sara-window" style="display:none;">

        {{-- Header --}}
        <div id="sara-header">
            <div class="d-flex align-items-center gap-2">
                <div id="sara-avatar">🤖</div>
                <div>
                    <div style="font-weight:800;font-size:.9rem;">Sara</div>
                    <div style="font-size:.72rem;opacity:.85;" id="sara-status">Assistante vocale · ShopCI</div>
                </div>
            </div>
            <button onclick="toggleSara()" style="background:none;border:none;color:#fff;font-size:1.1rem;cursor:pointer;">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        {{-- Zone vocale centrale --}}
        <div id="sara-body">

            {{-- Visualiseur ondes --}}
            <div id="sara-orb-wrap">
                <div id="sara-orb">
                    <div class="orb-ring r1"></div>
                    <div class="orb-ring r2"></div>
                    <div class="orb-ring r3"></div>
                    <i class="bi bi-mic-fill" id="sara-orb-icon"></i>
                </div>
            </div>

            {{-- Texte reconnu --}}
            <div id="sara-transcript-wrap">
                <div id="sara-transcript">Appuyez sur le micro pour parler à Sara</div>
            </div>

            {{-- Réponse Sara --}}
            <div id="sara-reply-wrap" style="display:none;">
                <div id="sara-reply"></div>
            </div>

            {{-- Bouton micro principal --}}
            <button id="sara-mic-btn" onclick="startListening()">
                <i class="bi bi-mic-fill" id="sara-mic-icon"></i>
                <span id="sara-mic-label">Parler</span>
            </button>

            {{-- Bouton arrêter Sara --}}
            <button id="sara-stop-btn" onclick="stopSpeaking()" style="display:none;">
                <i class="bi bi-stop-fill"></i> Arrêter Sara
            </button>

        </div>

        {{-- Historique conversation (scroll) --}}
        <div id="sara-history"></div>

    </div>
</div>

<style>
#sara-widget {
    position: fixed;
    bottom: 24px; right: 24px;
    z-index: 9999;
    font-family: inherit;
}

/* ── Bouton flottant ── */
#sara-toggle {
    width: 58px; height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none; color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(245,158,11,.55);
    transition: transform .2s, box-shadow .2s;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
#sara-toggle:hover { transform: scale(1.08); }
#sara-toggle.listening {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    box-shadow: 0 4px 20px rgba(220,38,38,.55);
    animation: togglePulse 1.4s ease-in-out infinite;
}
#sara-toggle.speaking {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 4px 20px rgba(37,99,235,.55);
}
@keyframes togglePulse {
    0%,100% { box-shadow: 0 4px 20px rgba(220,38,38,.55); }
    50%      { box-shadow: 0 4px 32px rgba(220,38,38,.85); transform: scale(1.06); }
}

/* ── Fenêtre ── */
#sara-window {
    position: absolute;
    bottom: 70px; right: 0;
    width: 320px;
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    overflow: hidden;
    display: flex; flex-direction: column;
    animation: saraSlideIn .25s ease;
}
@keyframes saraSlideIn {
    from { opacity:0; transform:translateY(14px) scale(.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}

/* ── Header ── */
#sara-header {
    background: linear-gradient(135deg, #f59e0b, #b45309);
    color: #fff; padding: .85em 1em;
    display: flex; align-items: center; justify-content: space-between;
}
#sara-avatar {
    width: 36px; height: 36px;
    background: rgba(255,255,255,.25);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}

/* ── Body ── */
#sara-body {
    padding: 1.2em 1em;
    display: flex; flex-direction: column; align-items: center; gap: .9em;
    background: #f8fafc;
}

/* ── Orbe animée ── */
#sara-orb-wrap { position: relative; width: 90px; height: 90px; }
#sara-orb {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    display: flex; align-items: center; justify-content: center;
    position: relative; z-index: 2;
    transition: background .3s;
}
#sara-orb.listening {
    background: linear-gradient(135deg, #fee2e2, #fca5a5);
}
#sara-orb.speaking {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
}
#sara-orb-icon { font-size: 1.8rem; color: #d97706; z-index: 3; }
#sara-orb.listening #sara-orb-icon { color: #dc2626; }
#sara-orb.speaking  #sara-orb-icon { color: #2563eb; }

.orb-ring {
    position: absolute; border-radius: 50%;
    border: 2px solid rgba(245,158,11,.3);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%) scale(1);
    opacity: 0;
}
.r1 { width: 100px; height: 100px; }
.r2 { width: 116px; height: 116px; }
.r3 { width: 132px; height: 132px; }

#sara-orb.listening .orb-ring,
#sara-orb.speaking  .orb-ring {
    animation: orbRing 1.8s ease-out infinite;
}
#sara-orb.listening .orb-ring { border-color: rgba(220,38,38,.35); }
#sara-orb.speaking  .orb-ring { border-color: rgba(37,99,235,.35); }
.r2 { animation-delay: .4s !important; }
.r3 { animation-delay: .8s !important; }
@keyframes orbRing {
    0%   { transform: translate(-50%,-50%) scale(.8); opacity: .8; }
    100% { transform: translate(-50%,-50%) scale(1.5); opacity: 0; }
}

/* ── Transcript ── */
#sara-transcript-wrap {
    width: 100%;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: .65em .9em;
    min-height: 44px;
    display: flex; align-items: center;
}
#sara-transcript {
    font-size: .82rem; color: #64748b;
    line-height: 1.4; width: 100%;
}
#sara-transcript.user-text { color: #0f172a; font-weight: 500; }

/* ── Réponse Sara ── */
#sara-reply-wrap {
    width: 100%;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1.5px solid #bfdbfe;
    border-radius: 12px;
    padding: .65em .9em;
}
#sara-reply {
    font-size: .82rem; color: #1e40af;
    line-height: 1.5;
}

/* ── Bouton micro ── */
#sara-mic-btn {
    display: flex; align-items: center; gap: .5em;
    padding: .65em 1.6em;
    border-radius: 50px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none; color: #fff;
    font-size: .88rem; font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(245,158,11,.4);
    transition: all .2s;
}
#sara-mic-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,.5); }
#sara-mic-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
#sara-mic-btn.listening {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    box-shadow: 0 4px 14px rgba(220,38,38,.4);
    animation: btnPulse 1s ease-in-out infinite;
}
@keyframes btnPulse {
    0%,100% { box-shadow: 0 4px 14px rgba(220,38,38,.4); }
    50%      { box-shadow: 0 4px 22px rgba(220,38,38,.7); }
}

/* ── Bouton stop ── */
#sara-stop-btn {
    display: flex; align-items: center; gap: .4em;
    padding: .4em 1.1em;
    border-radius: 50px;
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    color: #64748b; font-size: .78rem; font-weight: 600;
    cursor: pointer; transition: all .2s;
}
#sara-stop-btn:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

/* ── Historique ── */
#sara-history {
    max-height: 160px;
    overflow-y: auto;
    padding: .5em .85em .85em;
    display: flex; flex-direction: column; gap: .5em;
    background: #fff;
    border-top: 1px solid #f1f5f9;
}
#sara-history:empty { display: none; }
.sara-hist-item {
    font-size: .78rem; line-height: 1.45;
    padding: .45em .75em;
    border-radius: 10px;
}
.sara-hist-item.user     { background: #fef3c7; color: #92400e; align-self: flex-end; max-width: 88%; }
.sara-hist-item.assistant { background: #eff6ff; color: #1e40af; align-self: flex-start; max-width: 88%; }

@media (max-width: 480px) {
    #sara-window { width: calc(100vw - 32px); right: -8px; }
}
</style>

<script>
const GROQ_KEY = "{{ config('services.groq.key') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";
let chatHistory = [];

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition  = null;
let isListening  = false;
let isSpeaking   = false;
let currentUtter = null;

// ── Ouvrir / fermer ──────────────────────────────────────────
function toggleSara() {
    const win  = document.getElementById('sara-window');
    const open = document.getElementById('sara-icon-open');
    const cls  = document.getElementById('sara-icon-close');
    const isOpen = win.style.display !== 'none';
    win.style.display = isOpen ? 'none' : 'flex';
    win.style.flexDirection = 'column';
    open.style.display = isOpen ? '' : 'none';
    cls.style.display  = isOpen ? 'none' : '';
    if (isOpen) { stopAll(); }
}

// ── Démarrer l'écoute ────────────────────────────────────────
function startListening() {
    if (!SpeechRecognition) {
        speakAndShow("Désolée, votre navigateur ne supporte pas la reconnaissance vocale. Essayez Chrome.");
        return;
    }
    if (isListening) { stopListening(); return; }
    if (isSpeaking)  { stopSpeaking(); }

    recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    recognition.continuous = false;
    recognition.interimResults = true;

    recognition.onstart = () => {
        isListening = true;
        setMode('listening');
        setTranscript('Je vous écoute…', false);
    };

    recognition.onresult = (e) => {
        const transcript = Array.from(e.results).map(r => r[0].transcript).join('');
        setTranscript(transcript, e.results[e.results.length - 1].isFinal);
        if (e.results[e.results.length - 1].isFinal) {
            recognition.stop();
            askSara(transcript);
        }
    };

    recognition.onerror = (e) => {
        stopListening();
        if (e.error === 'not-allowed') {
            speakAndShow("Accès au microphone refusé. Autorisez le micro dans votre navigateur.");
        } else if (e.error !== 'no-speech') {
            setTranscript("Je n'ai pas bien entendu, réessayez.", false);
        }
    };

    recognition.onend = () => { stopListening(); };
    recognition.start();
}

function stopListening() {
    isListening = false;
    if (recognition) { try { recognition.abort(); } catch(e){} recognition = null; }
    if (!isSpeaking) setMode('idle');
}

// ── Envoyer à Sara (appel Groq direct depuis le navigateur) ──
async function askSara(message) {
    if (!message.trim()) return;

    addHistory('user', message);
    setMode('thinking');
    setStatus('Sara réfléchit…');
    document.getElementById('sara-reply-wrap').style.display = 'none';

    // Garder les 6 derniers échanges pour le contexte
    chatHistory.push({ role: 'user', content: message });
    if (chatHistory.length > 12) chatHistory = chatHistory.slice(-12);

    const systemPrompt = "Tu es Sara, assistante vocale de ShopCI, boutique en ligne en Côte d'Ivoire. "
        + "Réponds TOUJOURS en français, de façon naturelle et concise (2-3 phrases max pour la voix). "
        + "Livraison gratuite 24-72h, paiement Orange Money/MTN/Wave/carte, paiement 2x 3x 4x sans frais, support aimesoro81@gmail.com. "
        + "Pour suivi commande : se connecter puis Mes commandes. Sois chaleureuse et utile.";

    try {
        const res = await fetch('https://api.groq.com/openai/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + GROQ_KEY,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                model: 'llama-3.1-8b-instant',
                max_tokens: 200,
                temperature: 0.7,
                messages: [
                    { role: 'system', content: systemPrompt },
                    ...chatHistory,
                ],
            }),
        });

        const data = await res.json();
        const reply = data.choices?.[0]?.message?.content
            || "Je n'ai pas pu obtenir de réponse, réessayez.";

        chatHistory.push({ role: 'assistant', content: reply });
        speakAndShow(reply);
        addHistory('assistant', reply);

    } catch (e) {
        speakAndShow("Impossible de contacter le serveur. Vérifiez votre connexion.");
    }
}

// ── Sara parle (synthèse vocale) ─────────────────────────────
function speakAndShow(text) {
    // Afficher la réponse écrite
    const replyWrap = document.getElementById('sara-reply-wrap');
    const replyEl   = document.getElementById('sara-reply');
    replyEl.textContent = text;
    replyWrap.style.display = 'block';

    // Voix
    if (!window.speechSynthesis) {
        setMode('idle'); setStatus('Assistante vocale · ShopCI');
        return;
    }

    window.speechSynthesis.cancel();
    currentUtter = new SpeechSynthesisUtterance(text);
    currentUtter.lang = 'fr-FR';
    currentUtter.rate = 1.0;
    currentUtter.pitch = 1.1;

    // Choisir une voix française si disponible
    const voices = window.speechSynthesis.getVoices();
    const frVoice = voices.find(v => v.lang.startsWith('fr') && v.name.toLowerCase().includes('female'))
                 || voices.find(v => v.lang.startsWith('fr'));
    if (frVoice) currentUtter.voice = frVoice;

    currentUtter.onstart = () => {
        isSpeaking = true;
        setMode('speaking');
        setStatus('Sara parle…');
        document.getElementById('sara-stop-btn').style.display = 'flex';
    };

    currentUtter.onend = currentUtter.onerror = () => {
        isSpeaking = false;
        currentUtter = null;
        setMode('idle');
        setStatus('Assistante vocale · ShopCI');
        document.getElementById('sara-stop-btn').style.display = 'none';
    };

    window.speechSynthesis.speak(currentUtter);
}

function stopSpeaking() {
    if (window.speechSynthesis) window.speechSynthesis.cancel();
    isSpeaking = false;
    setMode('idle');
    setStatus('Assistante vocale · ShopCI');
    document.getElementById('sara-stop-btn').style.display = 'none';
}

function stopAll() {
    stopListening();
    stopSpeaking();
}

// ── Helpers UI ───────────────────────────────────────────────
function setMode(mode) {
    const orb    = document.getElementById('sara-orb');
    const orbIco = document.getElementById('sara-orb-icon');
    const micBtn = document.getElementById('sara-mic-btn');
    const micLbl = document.getElementById('sara-mic-label');
    const micIco = document.getElementById('sara-mic-icon');
    const toggle = document.getElementById('sara-toggle');

    orb.className    = mode === 'idle' ? '' : mode;
    toggle.className = mode === 'idle' ? '' : mode;

    if (mode === 'listening') {
        orbIco.className = 'bi bi-mic-fill';
        micBtn.classList.add('listening');
        micBtn.disabled  = false;
        micLbl.textContent = 'Arrêter';
        micIco.className = 'bi bi-stop-fill';
    } else if (mode === 'speaking') {
        orbIco.className = 'bi bi-volume-up-fill';
        micBtn.classList.remove('listening');
        micBtn.disabled  = true;
        micLbl.textContent = 'Parler';
        micIco.className = 'bi bi-mic-fill';
    } else if (mode === 'thinking') {
        orbIco.className = 'bi bi-three-dots';
        micBtn.classList.remove('listening');
        micBtn.disabled  = true;
        micLbl.textContent = 'Parler';
        micIco.className = 'bi bi-mic-fill';
    } else {
        orbIco.className = 'bi bi-mic-fill';
        micBtn.classList.remove('listening');
        micBtn.disabled  = false;
        micLbl.textContent = 'Parler';
        micIco.className = 'bi bi-mic-fill';
    }
}

function setTranscript(text, isFinal) {
    const el = document.getElementById('sara-transcript');
    el.textContent = text;
    el.className   = isFinal ? 'user-text' : '';
}

function setStatus(text) {
    document.getElementById('sara-status').textContent = text;
}

function addHistory(role, text) {
    const hist = document.getElementById('sara-history');
    const div  = document.createElement('div');
    div.className   = 'sara-hist-item ' + role;
    div.textContent = (role === 'user' ? '🗣 ' : '🤖 ') + text;
    hist.appendChild(div);
    hist.scrollTop = hist.scrollHeight;
}

// Charger les voix dès que disponibles (Chrome les charge en async)
if (window.speechSynthesis) {
    window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
}
</script>
