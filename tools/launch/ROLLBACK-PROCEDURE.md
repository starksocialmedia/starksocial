# Rollback procedure

November 3 2026. Expands `LAUNCH-RUNBOOK.md` §5. Use only with the backup taken at
minute 0 of `CUTOVER-REHEARSAL.md`. Target: old site back within **15 minutes** of deciding.

## When to roll back

Decide at most **15 minutes** after the problem appears. Roll back if any of these is true and
not fixed in that time:

| Signal | How you'd see it |
|---|---|
| 5xx or PHP fatal on the homepage, a service page, `/contact/` or `/blog/` | Smoke-test loop; Cloudflare 5xx graph; Cloudways PHP error log |
| Contact form doesn't create a Perfex lead | Test submission: no lead in Perfex, entry note isn't "Webhook → HTTP 200" |
| `/feed/podcast/` broken, or episode GUIDs changed | Feed doesn't load, or its `<guid>` values differ from the copy saved at minute 0 (`curl -s https://starksocial.com/feed/podcast/ > ~/Downloads/feed-before.xml`; live's feed lists the latest 10) |
| Site serves `staging.starksocial.com` links or `noindex`, and search-replace doesn't fix it | `curl -s https://starksocial.com/ \| grep -c 'staging\.\|noindex'` |
| Can't log in to wp-admin | — |

Don't roll back for: Mailgun email failure (leads still reach Perfex), one missing redirect,
one visual glitch, Lighthouse scores. Fix those forward.

## Steps

1. **Maintenance on** (live): `wp maintenance-mode activate`
2. **Restore the backup.** Preferred: Cloudways → live app → Backups → restore today's
   pre-cutover backup (files + database). Takes 5–10 minutes.
   Fallback if the Cloudways restore fails:
   ```bash
   ssh stark-live
   cd ~/public_html
   md5sum ~/private_html/live-pre-phase2-<date>.sql   # compare with the md5 noted at minute 0
   wp db import ~/private_html/live-pre-phase2-<date>.sql
   ```
   then restore `wp-content/themes/` and `wp-content/plugins/` from the Cloudways file backup.
   (The Phase 1 theme, Pro/Cornerstone, is still on the server unless the push deleted it;
   the database import brings back its settings.)
3. **Plugins:** the import restores live's active-plugins list (AIOSEO, Pretty Links, Mailgun…).
   Confirm: `wp plugin list --status=active` includes `all-in-one-seo-pack-pro` and `mailgun`.
   Remove the staging guard if the push brought it: `rm -f wp-content/mu-plugins/stark-staging-guard.php`
4. **Caches:** `wp cache flush`; Breeze purge; **Cloudflare purge everything** (Cloudways →
   Application → Cloudflare, and Nathan's dashboard).
5. **DNS reversal** — only if DNS was changed (a new app). Point `starksocial.com` A/AAAA back to
   the previous values (note them at minute 10 of the rehearsal, before changing anything) and
   `www` back to `secure.cloudways.cloud`. Proxied records switch in seconds; nothing to wait for.
   If DNS wasn't changed (the normal case), skip this.
6. **Check:** `/` (old design), `/contact/` (submit → Perfex lead), `/feed/podcast/` (same GUIDs as `feed-before.xml`),
   `/wp-login.php` (403 as before). Phone on mobile data, not this Mac (its `/etc/hosts` pins
   the origin).
7. **Maintenance off**: `wp maintenance-mode deactivate`
8. **Tell Deanna** (template below) and **log it** in `ERRORLOG.md`: what broke, the time, the
   signal, what the logs said.
9. Keep the failed state for diagnosis: before step 2, if there's time, `wp db export
   ~/private_html/phase2-failed-<date>.sql`. Diagnose on staging, not live.

## Communication template

> **To:** Deanna
> **Subject:** Website launch rolled back, old site is live again
>
> Hi Deanna, we started the new site launch at [time] and rolled back at [time] because
> [one line: what broke]. The old site is back exactly as it was this morning; nothing you did
> is lost, and the content freeze can end. Any form leads during that window went to Perfex as
> usual [or: check Perfex for leads between X and Y]. I'll fix it on staging and suggest a new
> date. — Nathan

Clients don't need a message: the portal (`hub.starksocial.com`) and dashboard
(`client.starksocial.com`) weren't touched.

## After a rollback

- Re-sync anything changed on live during the attempt before the next try (normally nothing:
  the freeze was on)
- Add the failure to `CUTOVER-REHEARSAL.md` as a pre-check so it's caught on staging next time
