<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    // Çok-kutulu mail koordinasyonu. Her kutu: gönderim (SMTP mailer) + gelen (IMAP).
    // Yeni kutu eklemek için buraya bir giriş + .env'e ilgili *_MAIL_* / *_IMAP_* anahtarları.
    // Kredansiyel SADECE env'den; koda asla yazma. ext-imap yoksa gelen kutusu nazik uyarı verir.
    'mailboxes' => [
        // Genel/kurumsal — Lead yanıtları buradan gider.
        'admin' => [
            'label'  => 'Admin (admin@)',
            'email'  => env('ADMIN_MAIL_FROM', 'admin@applytogerman.com'),
            'name'   => env('MAIL_FROM_NAME', 'ApplyToGerman'),
            'mailer' => 'mailbox_admin',
            'imap'   => [
                'host'          => env('ADMIN_IMAP_HOST', env('IMAP_HOST')),
                'port'          => env('ADMIN_IMAP_PORT', 993),
                'encryption'    => env('ADMIN_IMAP_ENCRYPTION', 'ssl'),
                'validate_cert' => env('ADMIN_IMAP_VALIDATE_CERT', true),
                'username'      => env('ADMIN_IMAP_USERNAME'),
                'password'      => env('ADMIN_IMAP_PASSWORD'),
                'folder'        => env('ADMIN_IMAP_FOLDER', 'INBOX'),
            ],
        ],
        // Partnerlik / affiliate outreach.
        'partnerships' => [
            'label'  => 'Partnerlik (partnerships@)',
            'email'  => env('OUTREACH_MAIL_FROM', 'partnerships@applytogerman.com'),
            'name'   => env('MAIL_FROM_NAME', 'ApplyToGerman'),
            'mailer' => 'outreach', // mevcut OUTREACH_MAIL_* mailer'ı
            'imap'   => [
                'host'          => env('IMAP_HOST'),
                'port'          => env('IMAP_PORT', 993),
                'encryption'    => env('IMAP_ENCRYPTION', 'ssl'),
                'validate_cert' => env('IMAP_VALIDATE_CERT', true),
                'username'      => env('IMAP_USERNAME'),
                'password'      => env('IMAP_PASSWORD'),
                'folder'        => env('IMAP_FOLDER', 'INBOX'),
            ],
        ],
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'key'   => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        // RAG chatbot — çok dilli embedding (TR/DE/EN tek vektör uzayı).
        // gemini-embedding-001: outputDimensionality ayarlanabilir; 768 = isabet/boyut dengesi.
        // Vektörler L2-normalize saklanır → cosine = nokta çarpımı. (doc/CHATBOT-RAG-PLAYBOOK.md)
        'embed_model' => env('GEMINI_EMBED_MODEL', 'gemini-embedding-001'),
        'embed_dims'  => (int) env('GEMINI_EMBED_DIMS', 768),
        // Sohbet üretim modeli (zor cevaplarda 2.5-pro'ya yükseltilebilir).
        'chat_model'  => env('GEMINI_CHAT_MODEL', 'gemini-2.5-flash'),
        // Chatbot widget herkese görünür mü? Doğrulandı → varsayılan AÇIK (herkese).
        // Kill-switch: prod .env'de GEMINI_CHAT_PUBLIC=false ile gizlenebilir.
        'chat_public' => (bool) env('GEMINI_CHAT_PUBLIC', true),
    ],

    'elevenlabs' => [
        'key'      => env('ELEVENLABS_API_KEY'),
        'voice_id' => env('ELEVENLABS_VOICE_ID', '9BWtsMINqrJLrRacOk9x'), // Aria (multilingual default)
        'model'    => env('ELEVENLABS_MODEL', 'eleven_multilingual_v2'),
    ],

    'image' => [
        // Provider priority — sırayla dene, ilki fail ederse sonrakine düş.
        // nano_banana = Gemini 2.5 Flash Image (paid tier gerek)
        // pollinations = ücretsiz FLUX (key gerek yok)
        'providers' => array_filter(array_map('trim', explode(',', env('IMAGE_PROVIDERS', 'nano_banana,pollinations')))),
    ],

    'partner' => [
        'base_url'       => env('PARTNER_API_BASE_URL'),
        'api_key'        => env('PARTNER_API_KEY'),
        'auth_header'    => env('PARTNER_API_AUTH_HEADER', 'X-API-Key'),
        'timeout'        => (int) env('PARTNER_API_TIMEOUT', 60),
        'page_size'      => (int) env('PARTNER_API_PAGE_SIZE', 200),
        'webhook_secret' => env('PARTNER_WEBHOOK_SECRET'),
        'sync_schedule'  => env('PARTNER_SYNC_SCHEDULE', 'daily'),
    ],

    'brevo' => [
        // Brevo transactional event webhook — must match X-Brevo-Webhook-Token header
        'webhook_token' => env('BREVO_WEBHOOK_TOKEN'),
        // Optional API key for transactional sends + list management (if not using SMTP)
        'api_key'       => env('BREVO_API_KEY'),
    ],

    'resend' => [
        // Resend Svix-signed webhook secret (whsec_… format, copy from Resend dashboard)
        'webhook_secret' => env('RESEND_WEBHOOK_SECRET'),
        // Optional Resend API key (re_…) for native API sends instead of SMTP
        'key'            => env('RESEND_API_KEY'),
    ],

    // Token-gated /_system/* operasyon route'ları (cron/elle tetik). KRİTİK: route'larda
    // env('SYSTEM_TOKEN') KULLANMA — prod'da post-deploy config:cache çalıştırıyor, config
    // cache'liyken env() config dosyaları DIŞINDA null döner → token hep null → her zaman 403.
    // config('services.system_token') ise cache'li config'ten okur, çalışır.
    'system_token' => env('SYSTEM_TOKEN'),

    // Ticketmaster Discovery API — kültürel etkinlik (konser/tiyatro) importu (/events).
    // Ücretsiz key: https://developer.ticketmaster.com. Limit: 5 req/sn, 5000/gün.
    'ticketmaster' => [
        'key' => env('TICKETMASTER_API_KEY'),
    ],

    // Web Push (VAPID) — şehir etkinlik bildirimleri için tarayıcı push'u.
    // Anahtar üret: php -r "require 'vendor/autoload.php'; print_r(Minishlink\WebPush\VAPID::createVapidKeys());"
    // public_key frontend'e açılır (gizli değil); private_key SECRET. Prod → ENV_PRODUCTION secret.
    'webpush' => [
        'public_key'  => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject'     => env('VAPID_SUBJECT', 'https://applytogerman.com'),
    ],

];
