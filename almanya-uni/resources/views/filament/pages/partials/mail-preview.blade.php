{{-- Gerçek mail HTML'i, izole bir iframe içinde. sandbox: gövdede script
     olmamalı; olsa bile panelde çalışmasın. --}}
<div x-data="{ w: '100%' }" class="space-y-3">

    <div class="flex flex-wrap items-center gap-2">
        <x-filament::button size="xs" color="gray" type="button"
                            x-on:click="w = '100%'"
                            x-bind:class="w === '100%' ? '' : 'opacity-60'">
            Masaüstü
        </x-filament::button>

        <x-filament::button size="xs" color="gray" type="button"
                            x-on:click="w = '390px'"
                            x-bind:class="w === '390px' ? '' : 'opacity-60'">
            Mobil
        </x-filament::button>

        <span class="ml-auto truncate text-xs text-gray-500 dark:text-gray-400">
            Konu: <span class="font-medium text-gray-700 dark:text-gray-200">{{ $subject ?: '(boş)' }}</span>
        </span>
    </div>

    <div class="flex justify-center rounded-xl bg-gray-100 p-3 dark:bg-gray-900">
        <iframe
            srcdoc="{{ $html }}"
            sandbox=""
            title="Mail önizleme"
            class="h-[70vh] rounded-lg border-0 bg-white shadow-sm"
            x-bind:style="`width: ${w}; max-width: 100%; transition: width .18s ease`"
        ></iframe>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400">
        Gmail ve Outlook görselleri varsayılan olarak engeller. Görseller kapalıyken
        mail hâlâ anlaşılıyor mu — mobil görünümde bir kez kontrol et.
    </p>
</div>
