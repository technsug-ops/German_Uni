{{-- RAG chatbot yüzen widget. doc/CHATBOT-RAG-PLAYBOOK.md (Faz 4) --}}
@php
    $__locale = app()->getLocale();
    $t = match ($__locale) {
        'de' => [
            'title' => 'AlmanyaUni Assistent',
            'open' => 'Frag den Assistenten',
            'greet' => 'Hallo! Ich beantworte Fragen zum Studium und Leben in Deutschland — gestützt auf unsere Inhalte.',
            'placeholder' => 'Stelle deine Frage…',
            'send' => 'Senden',
            'sources' => 'Quellen',
            'disclaimer' => 'KI-Antworten können Fehler enthalten. Wichtige Angaben bitte aus offizieller Quelle prüfen.',
            'suggest' => ['Kann ich ohne Deutsch studieren?', 'Wie viel Geld braucht das Sperrkonto?', 'Wie bewerbe ich mich über uni-assist?'],
            'thinking' => 'Denkt nach…',
            'thanks' => 'Danke für dein Feedback!',
            'fb_up' => 'Hilfreich', 'fb_down' => 'Nicht hilfreich',
            'lead_title' => 'Brauchst du Hilfe bei der Bewerbung?',
            'lead_desc' => 'Lass deine E-Mail da — wir senden dir passende Programme und einen Bewerbungsleitfaden.',
            'lead_email' => 'Deine E-Mail', 'lead_send' => 'Senden',
            'lead_done' => 'Erhalten! Wir melden uns bald.',
        ],
        'en' => [
            'title' => 'AlmanyaUni Assistant',
            'open' => 'Ask the assistant',
            'greet' => 'Hi! I answer questions about studying and living in Germany — grounded in our content.',
            'placeholder' => 'Ask your question…',
            'send' => 'Send',
            'sources' => 'Sources',
            'disclaimer' => 'AI answers can contain mistakes. Please verify important details from official sources.',
            'suggest' => ['Can I study without German?', 'How much money for the blocked account?', 'How do I apply via uni-assist?'],
            'thinking' => 'Thinking…',
            'thanks' => 'Thanks for your feedback!',
            'fb_up' => 'Helpful', 'fb_down' => 'Not helpful',
            'lead_title' => 'Need help with your application?',
            'lead_desc' => 'Leave your email — we\'ll send matching programs and an application guide.',
            'lead_email' => 'Your email', 'lead_send' => 'Send',
            'lead_done' => 'Got it! We\'ll be in touch soon.',
        ],
        default => [
            'title' => 'AlmanyaUni Asistanı',
            'open' => 'Asistana sor',
            'greet' => 'Merhaba! Almanya’da okumak ve yaşamak hakkındaki sorularını içeriğimize dayanarak yanıtlıyorum.',
            'placeholder' => 'Sorunu yaz…',
            'send' => 'Gönder',
            'sources' => 'Kaynaklar',
            'disclaimer' => 'Yapay zekâ yanıtları hata içerebilir. Önemli bilgileri resmi kaynaktan doğrula.',
            'suggest' => ['Almancasız okuyabilir miyim?', 'Bloke hesaba ne kadar para gerekli?', 'uni-assist üzerinden nasıl başvururum?'],
            'thinking' => 'Düşünüyor…',
            'thanks' => 'Geri bildirimin için teşekkürler!',
            'fb_up' => 'Faydalı', 'fb_down' => 'Faydalı değil',
            'lead_title' => 'Başvuru sürecinde yardım ister misin?',
            'lead_desc' => 'E-postanı bırak; sana uygun programları ve başvuru rehberini gönderelim.',
            'lead_email' => 'E-posta adresin', 'lead_send' => 'Gönder',
            'lead_done' => 'Aldık! En kısa sürede ulaşırız.',
        ],
    };
@endphp

<div x-data="chatWidget({ locale: '{{ $__locale }}', greet: @js($t['greet']), suggest: @js($t['suggest']), t: @js($t) })" x-cloak>
    {{-- Açma butonu --}}
    {{-- Geri bildirim butonu (bottom-4 right-4) ile çakışmasın → onun üstüne yerleş. --}}
    <button type="button" x-show="!open" @click="toggle()"
        class="fixed bottom-20 right-4 z-[55] flex items-center gap-2 rounded-full bg-primary-600 px-4 py-3 text-white shadow-lg transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-400"
        aria-label="{{ $t['open'] }}">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-11.6 7.1L3 21l1.9-6.4A8 8 0 1121 12z"/></svg>
        <span class="hidden sm:inline text-sm font-semibold">{{ $t['open'] }}</span>
    </button>

    {{-- Panel --}}
    <div x-show="open" x-transition.opacity
        class="fixed bottom-4 right-4 z-[60] flex h-[min(580px,calc(100vh-2rem))] w-[min(390px,calc(100vw-1.5rem))] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900">
        {{-- Başlık --}}
        <div class="flex items-center justify-between bg-primary-600 px-4 py-3 text-white">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-11.6 7.1L3 21l1.9-6.4A8 8 0 1121 12z"/></svg>
                <span class="font-semibold">{{ $t['title'] }}</span>
            </div>
            <button type="button" @click="toggle()" class="rounded p-1 hover:bg-white/20" aria-label="close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Mesajlar --}}
        <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50 p-3 dark:bg-gray-950" x-ref="scroll">
            <template x-for="(m, i) in messages" :key="i">
                <div>
                    {{-- Kullanıcı --}}
                    <template x-if="m.role === 'user'">
                        <div class="ml-auto max-w-[82%] rounded-2xl rounded-br-sm bg-primary-600 px-3 py-2 text-sm text-white" x-text="m.content"></div>
                    </template>
                    {{-- Asistan --}}
                    <template x-if="m.role === 'assistant'">
                        <div class="mr-auto max-w-[92%]">
                            <div class="prose prose-sm max-w-none rounded-2xl rounded-bl-sm bg-white px-3 py-2 text-sm text-gray-800 shadow-sm dark:bg-gray-800 dark:text-gray-100 dark:prose-invert" x-html="m.html"></div>
                            <template x-if="m.sources && m.sources.length">
                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                    <span class="self-center text-[11px] font-medium text-gray-400">{{ $t['sources'] }}:</span>
                                    <template x-for="(s, si) in m.sources" :key="si">
                                        <a :href="s.url" target="_blank" rel="noopener"
                                           class="inline-flex max-w-[200px] items-center gap-1 truncate rounded-full bg-primary-50 px-2 py-0.5 text-[11px] text-primary-700 hover:bg-primary-100 dark:bg-primary-900/40 dark:text-primary-200"
                                           x-text="(si+1) + '. ' + s.title"></a>
                                    </template>
                                </div>
                            </template>

                            {{-- 👍/👎 geri bildirim (selam mesajı hariç) --}}
                            <template x-if="m.q">
                                <div class="mt-1.5 flex items-center gap-1.5 pl-1">
                                    <template x-if="!m.voted">
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="vote(i, 1)" :title="t.fb_up" class="text-gray-300 transition hover:text-green-500" aria-label="up">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>
                                            </button>
                                            <button type="button" @click="vote(i, -1)" :title="t.fb_down" class="text-gray-300 transition hover:text-red-500" aria-label="down">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3zm7-13h2.67A2.31 2.31 0 0122 4v7a2.31 2.31 0 01-2.33 2H17"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <span x-show="m.voted" x-cloak class="text-[11px] text-gray-400" x-text="t.thanks"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Lead yakalama kartı (yüksek niyet + oturumda bir kez) --}}
            <div x-show="lead.show && !lead.done" x-cloak x-transition
                 class="mr-auto w-full rounded-xl border border-primary-200 bg-primary-50 p-3 dark:border-primary-700 dark:bg-primary-900/30">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-[13px] font-semibold text-primary-800 dark:text-primary-200" x-text="t.lead_title"></p>
                    <button type="button" @click="lead.show=false; lead.dismissed=true" class="text-primary-400 hover:text-primary-600" aria-label="close">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="mt-0.5 text-[11px] text-primary-700/80 dark:text-primary-300/80" x-text="t.lead_desc"></p>
                <form @submit.prevent="submitLead()" class="mt-2 flex gap-1.5">
                    <input type="email" x-model="lead.email" required :placeholder="t.lead_email"
                           class="min-w-0 flex-1 rounded-lg border border-primary-300 bg-white px-2.5 py-1.5 text-[13px] focus:border-primary-500 focus:outline-none dark:border-primary-600 dark:bg-gray-800 dark:text-white">
                    <button type="submit" :disabled="lead.sending"
                            class="shrink-0 rounded-lg bg-primary-600 px-3 py-1.5 text-[13px] font-medium text-white hover:bg-primary-700 disabled:opacity-50"
                            x-text="t.lead_send"></button>
                </form>
            </div>
            <div x-show="lead.done" x-cloak class="mr-auto rounded-xl bg-green-50 px-3 py-2 text-[12px] text-green-700 dark:bg-green-900/30 dark:text-green-300" x-text="t.lead_done"></div>

            {{-- Yazıyor göstergesi --}}
            <div x-show="loading" class="mr-auto flex max-w-[60%] items-center gap-1 rounded-2xl rounded-bl-sm bg-white px-3 py-2 shadow-sm dark:bg-gray-800">
                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay:0ms"></span>
                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay:150ms"></span>
                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400" style="animation-delay:300ms"></span>
            </div>

            {{-- Önerilen sorular (sadece başlangıçta) --}}
            <div x-show="messages.length === 1" class="flex flex-wrap gap-1.5 pt-1">
                <template x-for="(s, si) in suggest" :key="si">
                    <button type="button" @click="send(s)" class="rounded-full border border-primary-200 bg-white px-2.5 py-1 text-[12px] text-primary-700 hover:bg-primary-50 dark:border-primary-700 dark:bg-gray-800 dark:text-primary-200" x-text="s"></button>
                </template>
            </div>
        </div>

        {{-- Girdi --}}
        <div class="border-t border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex items-end gap-2">
                <textarea x-ref="input" x-model="input" @keydown.enter.prevent="send()" rows="1"
                    class="max-h-24 flex-1 resize-none rounded-xl border border-gray-300 bg-gray-50 px-3 py-2 text-sm focus:border-primary-400 focus:outline-none focus:ring-1 focus:ring-primary-400 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    :placeholder="loading ? '{{ $t['thinking'] }}' : '{{ $t['placeholder'] }}'" :disabled="loading"></textarea>
                <button type="button" @click="send()" :disabled="loading || !input.trim()"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white transition hover:bg-primary-700 disabled:opacity-40"
                    aria-label="{{ $t['send'] }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>
            <p class="mt-1 px-1 text-[10px] leading-tight text-gray-400">{{ $t['disclaimer'] }}</p>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function chatWidget(cfg) {
    return {
        open: false,
        input: '',
        loading: false,
        t: cfg.t || {},
        suggest: cfg.suggest || [],
        lead: { show: false, done: false, dismissed: false, sending: false, email: '', q: '' },
        messages: [{ role: 'assistant', html: '<p>' + escapeHtml(cfg.greet) + '</p>', sources: [] }],
        toggle() {
            this.open = !this.open;
            if (this.open) this.$nextTick(() => this.$refs.input?.focus());
        },
        async send(preset) {
            const text = (preset !== undefined ? preset : this.input).trim();
            if (!text || this.loading) return;
            this.input = '';
            this.messages.push({ role: 'user', content: text });
            this.loading = true;
            this.scrollDown();

            // Son turlardan kısa geçmiş (kullanıcı + asistan düz metin)
            const history = this.messages
                .filter(m => m.role === 'user' || (m.role === 'assistant' && m.text))
                .slice(-6)
                .map(m => ({ role: m.role, content: m.role === 'user' ? m.content : m.text }));

            try {
                const res = await fetch('/' + cfg.locale + '/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ message: text, history: history.slice(0, -1) }),
                });
                const data = await res.json();
                this.messages.push({
                    role: 'assistant',
                    html: data.answer_html || ('<p>' + escapeHtml(data.answer || '') + '</p>'),
                    text: data.answer || '',
                    sources: data.sources || [],
                    q: text,          // oylanan turun sorusu
                    voted: 0,
                    confidence: data.confidence || null,
                    top: data.top ?? null,
                });
                // Yüksek niyetli cevapta lead teklifini bir kez göster
                if (data.lead_offer && !this.lead.done && !this.lead.dismissed) {
                    this.lead.q = text;
                    this.lead.show = true;
                }
            } catch (e) {
                this.messages.push({ role: 'assistant', html: '<p>—</p>', text: '', sources: [], q: '', voted: 0 });
            } finally {
                this.loading = false;
                this.scrollDown();
            }
        },
        async vote(i, v) {
            const m = this.messages[i];
            if (!m || m.voted) return;
            m.voted = v;
            try {
                await fetch('/' + cfg.locale + '/chat/feedback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        vote: v, question: m.q, answer: m.text || '',
                        confidence: m.confidence || null, top: m.top ?? null, sources: m.sources || [],
                    }),
                });
            } catch (e) { /* sessizce yut — oy UI'da işaretli kaldı */ }
        },
        async submitLead() {
            if (this.lead.sending || !this.lead.email.trim()) return;
            this.lead.sending = true;
            try {
                await fetch('/' + cfg.locale + '/chat/lead', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ email: this.lead.email, question: this.lead.q }),
                });
                this.lead.done = true; this.lead.show = false;
            } catch (e) { /* yut */ } finally {
                this.lead.sending = false;
                this.scrollDown();
            }
        },
        scrollDown() {
            this.$nextTick(() => { const el = this.$refs.scroll; if (el) el.scrollTop = el.scrollHeight; });
        },
    };
}
function escapeHtml(s) {
    const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML;
}
</script>
@endpush
@endonce
