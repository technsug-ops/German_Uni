# Newsletter — Resend Setup Checklist

**Why Resend (vs Brevo):** modern API, cryptographically signed webhooks, better $/mail at scale, and AlmanyaUni's existing account familiarity. 100 emails/day free, 3,000/month — comfortable for the first 1-2 years.

Code is fully wired. This doc is the **account + DNS + .env** to-do list.

---

## 1. Resend account preparation (~5 min)

You already have an account.

1. Log in at https://resend.com
2. Top-right account menu → if your current login is the old/personal account, decide whether to:
    - Add `applytogerman.com` domain to that existing account, OR
    - Create a new team/workspace for this project (recommended for billing clarity)
3. **Domains** → **Add domain** → `applytogerman.com`
4. Resend shows you 3 DNS records to add. Copy them.

> ⚠️ Resend authenticates the *root* domain. If you add `applytogerman.com`, you can send from `news@applytogerman.com`, `hello@applytogerman.com`, etc. Repeat for `almanyauni.com`.

## 2. DNS records (~10 min — propagation 5 min to 24 h)

In your DNS panel (KAS or wherever the nameservers point), add for **each domain** (almanyauni.com + applytogerman.com):

| Type | Host | Value | Purpose |
|---|---|---|---|
| MX | `send` | `feedback-smtp.eu-west-1.amazonses.com` (priority 10) | Bounce return-path |
| TXT | `send` | `v=spf1 include:amazonses.com ~all` | SPF |
| TXT | `resend._domainkey` | (long value Resend gives) | DKIM signing |

> Resend's recommended config is "Restricted" — only mails from your own account can pass DKIM. Use "Restricted" not "Open".

After adding records, hit **Verify DNS** in Resend dashboard. Each row turns green within 5–60 min.

### Optional (recommended): DMARC

Add one more TXT for each domain:

| Type | Host | Value |
|---|---|---|
| TXT | `_dmarc` | `v=DMARC1; p=quarantine; rua=mailto:dmarc@applytogerman.com; pct=100` |

Without DMARC you'll still send; with it Gmail/Outlook gives you 5-10% extra inbox rate.

## 3. Resend API key (~1 min)

1. **API Keys** → **Create API Key**
2. Name: `ApplyToGerman Production`
3. Permission: **Sending access** (full would also work but Sending-only is safer)
4. Copy the key — starts with `re_…`. You won't see it again.

## 4. Webhook setup (~3 min)

1. **Webhooks** → **Add endpoint**
2. **Endpoint URL:** `https://applytogerman.com/api/webhooks/resend`
3. **Events to listen for:** check all:
    - `email.sent`
    - `email.delivered`
    - `email.delivery_delayed`
    - `email.complained`
    - `email.bounced`
    - `email.opened`
    - `email.clicked`
4. Click **Add endpoint**
5. Resend shows the **Signing Secret** — copy it. Starts with `whsec_…`.

## 5. Production `.env` updates (`ENV_PRODUCTION` GitHub secret)

Edit the `ENV_PRODUCTION` secret in GitHub repo settings (`Settings → Secrets → ENV_PRODUCTION → Update`). Add or replace:

```bash
# Switch from log to resend
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=news@applytogerman.com
MAIL_FROM_NAME="ApplyToGerman"

# Resend API + webhook secrets
RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
RESEND_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Queue worker — async mail delivery (database driver already configured)
QUEUE_CONNECTION=database
```

Save. The next push to `main` triggers a deploy that writes the new `.env` to KAS.

> Resend's PHP driver is built into Laravel 11+ via `resend-laravel`. Run `composer require resend/resend-laravel` on local first (then commit + push) — see step 7.

## 6. KAS Cronjobs (~2 min)

KAS panel → **Cronjobs** → **Add new**. Add **two** entries:

| Schedule | Command |
|---|---|
| `* * * * *` (every minute) | `cd /www/htdocs/w02196cc/almanya-uni && php artisan schedule:run` |
| `* * * * *` (every minute) | `cd /www/htdocs/w02196cc/almanya-uni && php artisan queue:work --stop-when-empty --max-time=50` |

These drive:
- Weekly digest blast (`newsletter:digest` runs Mon 09:00)
- Confirm + welcome email queue drain
- DAAD scholarship sync, partner API sync, translation jobs

Output to `/dev/null` if you don't want logs piling up. Or pipe to a logfile for debugging.

## 7. Install Resend Laravel driver

This step is the only thing not yet in the repo. Run locally:

```bash
cd almanya-uni
composer require resend/resend-laravel
```

Then commit + push the `composer.json` + `composer.lock` change. The deploy workflow installs the package on production.

## 8. Smoke test (after everything is live)

1. Visit `https://applytogerman.com/newsletter` (or trigger from blog footer form)
2. Submit your real email → expect confirmation email within ~30 sec
3. Click confirmation link → expect welcome email within ~1 min
4. **Resend dashboard → Emails** → both should appear with status `delivered`
5. **Resend dashboard → Webhooks → Endpoint details → Activity** — should show webhook deliveries with HTTP 200 response
6. DB check (production via `php artisan tinker`):
   ```php
   App\Models\Subscriber::where('email','your@email')->first()->only([
       'email','confirmed_at','last_sent_at','open_count','webhook_meta'
   ]);
   ```

## 9. Free tier monitoring

- **100 emails/day** hard limit on free plan (resets at midnight UTC)
- **3,000/month** soft limit
- Use `php artisan newsletter:digest --send --throttle=200` (≈5 mails/sec) for blasts
- At ~30 subscribers × weekly digest = 30/week ≈ 4/day → very safe
- Resend dashboard shows live usage gauge

## 10. Upgrade trigger

When sustained `>100/day` (≈500 subscribers × weekly), upgrade to **Pro $20/mo → 50,000 mail/mo**. Resend rotates new IPs as you scale, but the upgrade is transparent — no migration.

---

## What the code already does

- `App\Mail\NewsletterConfirmation` — double opt-in (queue-dispatched)
- `App\Mail\NewsletterWelcome` — locale-aware, 5 top tools (queue-dispatched)
- `App\Mail\WeeklyDigest` — rate-limited blast
- `App\Http\Controllers\Webhooks\ResendWebhookController` — handles 7 Resend event types with Svix signature verification
- `App\Models\Subscriber::scopeReachable()` — excludes bounced + complained from blasts
- Queue-based send everywhere (no sync block on form submit)

## Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| `MAIL_MAILER=resend` but mails not arriving | Driver not installed | `composer require resend/resend-laravel` |
| Webhook returns 401 | Signature mismatch | Copy webhook secret again — it must start with `whsec_` |
| Webhook returns 503 | `RESEND_WEBHOOK_SECRET` env empty | Re-add to ENV_PRODUCTION secret, redeploy |
| Mails in Resend dashboard "sent" but never delivered | DKIM not verified | Re-check DNS, hit "Verify DNS" in Resend |
| Mails go to spam | DMARC missing or new IP warmup | Add DMARC, wait 7-14 days for IP reputation |
| Queue not draining | Cron not running | Verify KAS cronjob, check `storage/logs/laravel.log` |
