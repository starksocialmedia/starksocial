# Mailgun activation at cutover

November 3 2026. Read from live (read-only; secrets not copied) and public DNS.
This expands `LAUNCH-RUNBOOK.md` §3 steps 2 and 6.

**Why it's critical:** `starksocial.com` publishes `DMARC p=reject; sp=reject; adkim=s; aspf=s`.
Form notifications sent by plain PHP mail from the web server fail DMARC and are **rejected**
by Gmail, Outlook and Google Workspace, so the contact form would silently stop emailing
anyone. Leads still reach Perfex through the webhook, but nobody gets the email.

## What live has today

| Setting (option `mailgun`) | Live value |
|---|---|
| Plugin | Mailgun 2.2.3 + Gravity Forms Mailgun 1.4.0, both active |
| Region | `us` |
| Use HTTP API | yes (`useAPI = 1`); SMTP username/password empty |
| Domain | `mg.starksocial.com` |
| API key | set (50 characters). **Not copied anywhere; read it from live at cutover** |
| From address | `postmaster@mg.starksocial.com` (override From: on) |
| From name | `⭐️ Stark Social Website ⭐️` |
| Reply-To | `admin@starksocial.com` |
| Click tracking | HTML only; open tracking on |
| GF Mailgun add-on (`gravityformsaddon_gravityformsmailgun_settings`) | region `us`, API key set |

Phase 2: both plugins installed, **inactive**, and no `mailgun` option in its database (the
staging guard also force-unloads them on staging). Keep it that way until cutover.

## DNS (no change needed)

| Record | Value | OK? |
|---|---|---|
| SPF `starksocial.com` | `v=spf1 a mx include:_spf.google.com include:mailgun.org -all` | ✓ |
| SPF `mg.starksocial.com` | `v=spf1 include:mailgun.org ~all` | ✓ |
| DKIM `k1._domainkey.mg.starksocial.com` | RSA key present | ✓ |
| MX `mg.starksocial.com` | `mxa.mailgun.org`, `mxb.mailgun.org` | ✓ |
| Tracking CNAME `email.mg.starksocial.com` | `mailgun.org` | ✓ |
| DMARC `_dmarc.starksocial.com` | `p=reject; sp=reject; adkim=s; aspf=s` | ✓ (strict) |

**Don't change the From address.** With strict alignment, mail from `@mg.starksocial.com`
passes because Mailgun signs it as `mg.starksocial.com`. A From address `@starksocial.com`
would **fail** strict DKIM alignment and be rejected, unless the Mailgun domain is changed to
the apex. Gravity Forms notifications can keep their own From fields; the plugin's
"override From" (on) replaces them with `postmaster@mg.starksocial.com`.

## Steps

### Before the push (cutover step 2)

```bash
ssh stark-live
cd ~/public_html
umask 077
wp --skip-themes option get mailgun --format=json > ~/private_html/mailgun.json
wp --skip-themes option get gravityformsaddon_gravityformsmailgun_settings --format=json > ~/private_html/gf-mailgun.json
ls -l ~/private_html/*mailgun*.json     # mode 600, both non-empty
```

These files contain the API key: never commit them or paste them into chat.

### After the push (cutover step 6)

```bash
cd ~/public_html
wp option update mailgun --format=json < ~/private_html/mailgun.json
wp option update gravityformsaddon_gravityformsmailgun_settings --format=json < ~/private_html/gf-mailgun.json
rm wp-content/mu-plugins/stark-staging-guard.php      # runbook step 5; the guard unloads Mailgun
wp plugin activate mailgun gravityformsmailgun
wp option get mailgun --format=json | python3 -c "import json,sys; d=json.load(sys.stdin); print(d['domain'], d['region'], d['from-address'], 'key set' if d['apiKey'] else 'NO KEY')"
```

Expected: `mg.starksocial.com us postmaster@mg.starksocial.com key set`.

### Test (2 minutes)

1. **Settings → Mailgun → Test Configuration** (sends to the admin email). Expect "Success".
2. Submit the **contact form** once with a real address you can read
   (runbook §4 contact-form check): `/thank-you/` loads, the entry note says "Webhook → HTTP 200",
   the lead appears in Perfex, and **the notification arrives at nathan@ and deanna@**.
3. In the received email, **Show original**: `SPF: PASS`, `DKIM: PASS (mg.starksocial.com)`,
   `DMARC: PASS`.
4. Mailgun dashboard → Sending → Logs: the message shows `delivered`.
5. Delete the test entry in Gravity Forms and the test lead in Perfex.
6. Delete the two JSON files: `rm ~/private_html/mailgun.json ~/private_html/gf-mailgun.json`.

### If the test fails

| Symptom | Likely cause | Fix |
|---|---|---|
| Test Configuration: 401 / "Forbidden" | API key not restored, or EU/US region mismatch | Re-run the `option update`; region must be `us` |
| Email arrives, DMARC fail | From address changed to `@starksocial.com` | Restore `from-address` to `postmaster@mg.starksocial.com` |
| No email, Mailgun log empty | Plugin inactive, or the staging guard still present | Check `wp plugin list`; remove the guard |
| No email, Mailgun log "failed" | Recipient rejection | Read the Mailgun log reason; not a site issue |

Mail failure alone isn't a rollback trigger (`ROLLBACK-PROCEDURE.md`): leads still reach
Perfex. Fix forward within the hour; if it can't be fixed, warn Deanna to watch Perfex for leads.
