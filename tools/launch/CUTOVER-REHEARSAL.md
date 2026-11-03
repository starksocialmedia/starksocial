# Cutover rehearsal

November 3 2026. A minute-by-minute walk through `LAUNCH-RUNBOOK.md` §3–4, with
what to check at each step, how long it takes, and what would make us stop. Read it once the
day before; run it with this file open.

**People:** Nathan (runs it). Deanna (notified before and after; spot-checks her bio and the KB).
**Window:** a weekday morning, outside office hours (before 9:00 PT), so a rollback hurts least.
**Prerequisites (day before):** copy approved (runbook §0 items 1–3); Cloudflare caching level
Standard (both dashboards); content freeze on live announced to Deanna; staging deployed with the
latest commit and smoke-tested; `MAILGUN-ACTIVATION.md` read.

| Minutes | Step | Commands / where | Check before moving on | Stop if |
|---|---|---|---|---|
| **−30** | Final content sync (runbook §2) | blog-snapshot, KB, media rsync, new redirects | New live posts since Nov 2 exist on staging | A sync script errors: fix, or move launch |
| **0–5** | **Back up live** | Cloudways → live app → Backups → **Take backup now**. Then `ssh stark-live`, `cd public_html`, `wp db export ~/private_html/live-pre-phase2-$(date +%F).sql`, `md5sum` it; `scp` it down to `~/Downloads/` and compare the md5 | Backup listed in Cloudways with today's time; md5 matches | Backup fails: **do not continue** |
| 3 | Save Mailgun settings | `MAILGUN-ACTIVATION.md` "Before the push" | Two non-empty mode-600 JSON files | — |
| 4 | Maintenance on | live: `wp maintenance-mode activate` | Site shows the maintenance page | — |
| **5–10** | **Deploy Phase 2 to live** | Cloudways "Push to live" (Database + Files) from staging, or the rsync + DB steps of `tools/deploy/deploy-staging.sh` reversed. Then `wp search-replace 'https://staging.starksocial.com' 'https://starksocial.com' --all-tables --skip-columns=guid` | `wp option get home` = `https://starksocial.com`; `wp theme list` shows stark-social active | Push errors, or `home` still staging: re-run search-replace once; else roll back |
| 8 | Remove the staging guard; plugins | `rm wp-content/mu-plugins/stark-staging-guard.php`; restore Mailgun options; `wp plugin activate mailgun gravityformsmailgun breeze object-cache-pro` (Object Cache Pro only if Redis was fixed); `wp plugin deactivate all-in-one-seo-pack-pro aioseo-eeat aioseo-image-seo aioseo-index-now aioseo-local-business broken-link-checker-seo pretty-link` (only those present after the push) | `wp plugin list --status=active` matches `live-inventory.md` | — |
| 9 | Rebuild | `wp eval-file tools/rankmath/configure.php`; `wp rewrite flush --hard`; `wp cache flush`; Breeze purge; **Cloudflare purge everything** (Cloudways → Application → Cloudflare, and Nathan's dashboard) | — | — |
| **10–15** | **DNS** | Normally **no change**: the push goes into the existing live app, so `starksocial.com` keeps pointing at Cloudways' Cloudflare (`secure.cloudways.cloud`). Only if a *new* app is used: point the apex A/AAAA (proxied) to the new app, keep `www` → apex, leave `hub.`, `podcast.`, `client.`, `brands.`, `staging.` alone | `dig +short starksocial.com` unchanged (or the new IP); `curl -sI https://starksocial.com` 200 with `cf-ray` | Wrong IP after 10 min: revert the record |
| 15 | Maintenance off | `wp maintenance-mode deactivate` | — | — |
| **15–20** | **Verification** | Fresh incognito window (and phone on mobile data, which skips this Mac's `/etc/hosts` origin pin) | Homepage shows the new design and the real logo; `view-source` has no `staging.starksocial.com` (`curl -s https://starksocial.com/ \| grep -c staging` = 0); `robots.txt` is not `Disallow: /`; no `noindex` meta | Old site still showing after the Cloudflare purge: purge again, check Breeze/Varnish |
| **20–30** | **Smoke test key URLs** | Runbook §4 curl loop (21 URLs); redirects: `/services/web-design/`, `/37`, `/content-is-king`, `/sitemap.xml`, `/portfolio/valencia-jazz-blues/`; podcast feed GUIDs vs `feed-before.xml`; **contact form** test (`MAILGUN-ACTIVATION.md` "Test") | All 200 or the expected 301; feed GUIDs match `feed-before.xml`; lead in Perfex; email arrives with DMARC pass | Any rollback trigger below |
| **30+** | **Monitor** | `tail -f` the PHP error log (Cloudways → Logs); Rank Math → 404 Monitor; Cloudflare analytics (5xx); Search Console sitemap submit | No PHP fatals; 404s are only junk URLs | — |
| +60 | Close out | Tell Deanna "live"; delete the test entry and Perfex lead; `rm ~/private_html/*mailgun*.json`; update `LAUNCH-RUNBOOK.md` §0 | — | — |
| +1 day | Follow-up | Lighthouse on production (compare `tools/audits/`); Search Console Coverage; check the Nov 15 scheduled post published once | — | — |

## Rollback triggers (any one → `ROLLBACK-PROCEDURE.md`)

- Homepage or any service page returns 5xx, or a PHP fatal error, not fixed within **15 minutes**
- Contact form doesn't create a Perfex lead (the webhook is the lead path; email alone isn't a trigger)
- `/feed/podcast/` broken or its item GUIDs changed (Apple Podcasts would re-list every episode)
- Site still serving `staging.starksocial.com` URLs or `noindex` and search-replace can't fix it
- Login to wp-admin impossible

**Not** rollback triggers (fix forward): Mailgun email failing while Perfex gets the lead; a
missing redirect (add it); a visual bug on one page; Best Practices score (Cloudflare script).

## Who to notify

| When | Who | Message |
|---|---|---|
| Day before | Deanna | Freeze window and launch time |
| Minute 0 | Deanna | "Starting; live will show maintenance for ~15 minutes" |
| Minute 30 | Deanna | "Live." Ask her to check her author page, a KB article, the podcast page |
| Rollback | Deanna | What broke, that the old site is back, next attempt |
| +1 day | Clients | Not needed: portal (`hub.`) and dashboard (`client.`) URLs don't change |

## Timing estimates

From the staging deploys (Nov 2–3): full push ≈ 4–6 minutes (DB ~60 MB with replacements,
theme + plugins rsync); search-replace ≈ 30 seconds; cache rebuild + purges ≈ 2 minutes.
Allow 45 minutes end to end, 60 with a rollback decision.
