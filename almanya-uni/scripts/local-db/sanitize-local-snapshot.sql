-- ─────────────────────────────────────────────────────────────────────────────
-- Local DB baseline: production snapshot'ını LOCAL kullanım için temizler.
-- YALNIZCA local snapshot veritabanında çalıştır (ör. almanyauni_baseline).
-- Production'da ASLA çalıştırma: kişisel veriyi ve aktif abonelikleri siler.
--
--   bash scripts/local-db/sanitize.sh almanyauni_baseline
--
-- Ne yapar:
--  1) Local'in gerçek kişilere/servislere dokunmasını engeller: oturumlar, token'lar,
--     kuyruk, push abonelikleri, webhook abonelikleri, giden e-posta kayıtları boşaltılır.
--  2) Kişisel verileri anonimleştirir (e-posta, telefon, IP, isim, parola hash'i).
--  3) Kamuya açık içerik (yazılar, FAQ, programlar, üniversiteler, kurum iletişim
--     bilgileri, yazar profilleri) ve migrations tablosu DEĞİŞMEZ.
-- Tekrar çalıştırılabilir (idempotent).
-- ─────────────────────────────────────────────────────────────────────────────
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Güvenlik kilidi bu dosyada değil, sarmalayıcıdadır: scripts/local-db/sanitize.sh yalnız 127.0.0.1 üzerindeki
-- 'almanyauni_baseline*' adlı ve uygulama şemasını içeren veritabanında çalıştırır. Bu SQL'i doğrudan çalıştırıyorsan hedefi iki kez kontrol et.

-- 1) Servis bağlantısı olan / kısa ömürlü tablolar → boşalt
TRUNCATE TABLE sessions;
TRUNCATE TABLE cache;
TRUNCATE TABLE cache_locks;
TRUNCATE TABLE jobs;
TRUNCATE TABLE job_batches;
TRUNCATE TABLE failed_jobs;
TRUNCATE TABLE password_reset_tokens;
TRUNCATE TABLE personal_access_tokens;
TRUNCATE TABLE push_subscriptions;          -- gerçek tarayıcı push uç noktaları
TRUNCATE TABLE webhook_deliveries;
TRUNCATE TABLE webhook_subscriptions;       -- dış URL + imza sırrı
TRUNCATE TABLE email_messages;              -- gönderilmiş/alınmış e-posta içerikleri
TRUNCATE TABLE api_usage_logs;              -- IP içerir

-- 2) Kişisel veri → anonim
UPDATE users SET
  email = CONCAT('user', id, '@local.invalid'),
  password = '$2y$12$localonlylocalonlylocalonlylocalonlylocalonlylocalon',  -- geçersiz hash: girişe kapalı
  remember_token = NULL;

-- confirm_token / unsubscribe_token NOT NULL (varchar 64): gerçek token yerine kayda özgü anlamsız değer
UPDATE subscribers SET email = CONCAT('subscriber', id, '@local.invalid'), ip_address = NULL,
  confirm_token = SHA2(CONCAT('local-confirm-', id), 256), unsubscribe_token = SHA2(CONCAT('local-unsub-', id), 256),
  webhook_meta = NULL;
UPDATE event_city_subscriptions SET email = CONCAT('eventsub', id, '@local.invalid'), ip_address = NULL,
  confirm_token = NULL, unsubscribe_token = NULL;
UPDATE event_rsvps SET attendee_email = CONCAT('rsvp', id, '@local.invalid'), ip_address = NULL;
UPDATE event_reviews SET attendee_email = CONCAT('eventreview', id, '@local.invalid'), ip_address = NULL;
UPDATE feedbacks SET email = NULL;
UPDATE leads SET email = CONCAT('lead', id, '@local.invalid'), phone = NULL;
UPDATE premium_interests SET email = CONCAT('premium', id, '@local.invalid');
UPDATE post_comments SET author_email = CONCAT('commenter', id, '@local.invalid'), ip_address = NULL;
UPDATE university_reviews SET author_email = CONCAT('reviewer', id, '@local.invalid'), ip_address = NULL,
  verification_token = NULL;
UPDATE university_review_votes SET session_token = SHA2(CONCAT('vote', id), 256);
UPDATE outreach_contacts SET email = CONCAT('outreach', id, '@local.invalid'), phone = NULL;
UPDATE api_clients SET contact_email = CONCAT('apiclient', id, '@local.invalid'), contact_name = NULL;

-- 3) Veritabanında saklanan sırlar → boş (ör. flatreklam_api_token: dış API'nin bize gönderdiği Bearer token).
--    Boş token'da ilgili API uç noktası local'de kimlik doğrulamayı reddeder. Analytics/doğrulama kimlikleri
--    herkese açık kodlardır ve yalnız production/staging ortamında render edilir; dokunulmaz.
UPDATE settings SET `value` = '' WHERE `key` REGEXP '(token|secret|password|api_key|apikey)$';

SET FOREIGN_KEY_CHECKS = 1;

-- 4) Özet (kontrol için)
SELECT 'users with real-looking email' AS chk, COUNT(*) AS n FROM users WHERE email NOT LIKE '%@local.invalid'
UNION ALL SELECT 'subscribers with real-looking email', COUNT(*) FROM subscribers WHERE email NOT LIKE '%@local.invalid'
UNION ALL SELECT 'push_subscriptions', COUNT(*) FROM push_subscriptions
UNION ALL SELECT 'webhook_subscriptions', COUNT(*) FROM webhook_subscriptions
UNION ALL SELECT 'jobs', COUNT(*) FROM jobs
UNION ALL SELECT 'sessions', COUNT(*) FROM sessions;
