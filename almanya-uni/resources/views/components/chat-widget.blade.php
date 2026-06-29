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
        ],
    };
@endphp

<div x-data="chatWidget({ locale: '{{ $__locale }}', greet: @js($t['greet']), suggest: @js($t['suggest']) })" x-cloak>
    {{-- Açma butonu --}}
    <button type="button" x-show="!open" @click="toggle()"
        class="fixed bottom-5 right-5 z-[55] flex items-center gap-2 rounded-full bg-primary-600 px-4 py-3 text-white shadow-lg transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-400"
        aria-label="{{ $t['open'] }}">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-11.6 7.1L3 21l1.9-6.4A8 8 0 1121 12z"/></svg>
        <span class="hidden sm:inline text-sm font-semibold">{{ $t['open'] }}</span>
    </button>

    {{-- Panel --}}
    <div x-show="open" x-transition.opacity
        class="fixed bottom-5 right-5 z-[60] flex h-[min(580px,calc(100vh-2rem))] w-[min(390px,calc(100vw-1.5rem))] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900">
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
                        </div>
                    </template>
                </div>
            </template>

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
        suggest: cfg.suggest || [],
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
                });
            } catch (e) {
                this.messages.push({ role: 'assistant', html: '<p>—</p>', text: '', sources: [] });
            } finally {
                this.loading = false;
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
