# Newsletter — Brevo Setup Checklist

**Goal:** real outbound newsletter delivery with bounce/complaint feedback loop and an aggregate-rate guardrail. Code is fully in place — only Brevo account + DNS configuration remain.

## 1. Brevo account (≈10 min)

1. Sign up at https://www.brevo.com (free plan: **300 emails/day**, no credit card needed for setup)
2. Verify the sender email — **hello@applytogerman.com** (or whichever you'll use)
3. Settings → SMTP & API → **Generate SMTP credentials**:
    - Server: `smtp-relay.brevo.com`
    - Port: `587` (STARTTLS)
    - User: `<your-login>@smtp-brevo.com`
    - Password: API token (Brevo generates)
4. Settings → SMTP & API → API Keys → **Create new API key** (label "Webhook", full scope is fine)

## 2. DNS records (≈15 min — propagation up to 24h)

Add to **both domains** (almanyauni.com + applytogerman.com) in your DNS panel:

| Type | Host | Value |
|---|---|---|
| TXT | `@` | `v=spf1 include:spf.brevo.com mx ~all` |
| TXT | `mail._domainkey` | (Brevo Settings → Senders → Authenticate domain → DKIM key — they give you a long string) |
| TXT | `_dmarc` | `v=DMARC1; p=quarantine; rua=mailto:dmarc@applytogerman.com; pct=100` |

Brevo's domain authentication wizard will verify these once propagated. Without DKIM, ~30% of emails land in spam.

## 3. Production `.env` additions

Add to GitHub `ENV_PRODUCTION` secret (multiline edit):

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-login@smtp-brevo.com
MAIL_PASSWORD=<brevo-smtp-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@applytogerman.com
MAIL_FROM_NAME="ApplyToGerman"

# Queue worker — async mail delivery (database driver already configured)
QUEUE_CONNECTION=database

# Brevo event webhook — random 32-char token (we use this in the controller)
BREVO_WEBHOOK_TOKEN=<openssl rand -hex 16>
BREVO_API_KEY=<from brevo settings>
```

Then trigger production deploy + `/_system/migrate` to apply.

## 4. Brevo webhook configuration (≈5 min)

In Brevo: **Settings → Transactional → Webhooks → Create**

- **URL:** `https://applytogerman.com/api/webhooks/brevo`
- **Method:** POST
- **Events:** check ALL — Sent, Delivered, Soft bounce, Hard bounce, Invalid, Blocked, Spam, Unsubscribed, Opened, Clicked
- **Custom Header:** `X-Brevo-Webhook-Token: <same token as BREVO_WEBHOOK_TOKEN above>`

Click "Test webhook" — should respond `{"ok":true,"handled":1}`.

## 5. Cron — schedule:run

If not already configured (deploy postmortem mentions KAS Cronjob), add:

```
* * * * * cd /www/htdocs/w02196cc/almanya-uni && php artisan schedule:run >> /dev/null 2>&1
```

This drives:
- Weekly newsletter digest (Mon 09:00) — `routes/console.php:49`
- Favorites digest (Sun 18:00)
- Partner API sync, DAAD sync, translation jobs, etc.

## 6. Queue worker — process the mail queue

KAS shared hosting has no daemon, so use a per-minute cron that drains the queue:

```
* * * * * cd /www/htdocs/w02196cc/almanya-uni && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

This processes jobs in <50 s slices, stops cleanly, restarts next minute. Outbound mails (confirm + welcome + digest) all flow through this queue.

## 7. Verify (smoke test)

After cron + secrets are in place:

1. Submit a test subscription via `/newsletter` form (use a real inbox you control)
2. Confirm in inbox — expect: confirm email arrives + 1-2 min later welcome email arrives
3. Check Brevo dashboard: **Transactional → Statistics** — should show 2 sends, both "delivered"
4. Check webhook activity: **Settings → Transactional → Webhooks → View activity** — should show events
5. DB check: `SELECT email, confirmed_at, last_sent_at, open_count FROM subscribers WHERE email='your-test@email'`

## 8. Free tier limits to know

- **300 emails/day** total (Brevo free plan)
- ~100/hour soft limit — `--throttle=100` (100 ms between sends) keeps us safe at 10/sec, well under
- Brevo blocks senders over 5% hard-bounce rate — our webhook-driven `bounced_at` flag prevents repeat sends
- Spam complaint over 0.1% triggers Brevo review — webhook auto-unsubscribes on `spam` event

## 9. Upgrade trigger

When you cross **300 mails/day** sustained (≈400 subscribers × weekly digest), upgrade to Brevo Starter (€9/mo, 20k/mo). Or migrate to Postmark / Resend / Amazon SES.

## What the code already does

- `App\Mail\NewsletterConfirmation` (double opt-in)
- `App\Mail\NewsletterWelcome` (locale-aware, 5 top tools)
- `App\Mail\WeeklyDigest`
- `App\Http\Controllers\Webhooks\BrevoWebhookController` — handles 9 event types
- `Subscriber::scopeReachable()` — excludes bounced + complained
- Rate-limited digest send (`--throttle=100`)
- Queue-based send (no sync block on confirm/subscribe)
