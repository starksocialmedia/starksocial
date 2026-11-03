# Complianz (cookie consent) on Phase 2

**Finding (v27.3.4):** Complianz was installed on Phase 2 but never configured, so there was **no
cookie banner** on Local or staging. Live is configured: US region, opt-out, one banner, 374
cookies, 9 services. Live also has the **cookie blocker off** (`safe_mode` 1, `enable_cookie_blocker` no).

## Copy live's configuration (read-only on live)

1. Export the tables `cmplz_cookiebanners`, `cmplz_cookies`, `cmplz_services` (live prefix
   `wp_49z880xoig_`), rename the prefix and import with `mysql` (same method as `tools/podlove-migration/`).
2. Copy the options `cmplz_options`, `cmplz_serviceTypes_en`, `cmplz_purposes_en`,
   `cmplz_detected_thirdparty_services`, `cmplz_detected_social_media`, `cmplz_detected_stats` and
   `cmplz_active_integrations` as raw values (JSON export → `tools/podlove-migration/import-options.php`).
3. `wp eval-file tools/complianz/set-flags.php` sets the "wizard completed" and sync flags and points
   the cookie and privacy statements at this site's `/support/` pages. The license key is **not** copied.
4. `wp --user=1 eval-file tools/complianz/add-perfex-service.php` adds **Perfex Chat** (Preferences, third party).

Done on Local November 2 2026: banner renders (same settings as live), 10 services.

## Perfex chat gating

`stark_inject_perfex_chat_widget()` (functions.php) loads the widget only when
`cmplz_has_consent('preferences')` or `('marketing')` is true, and re-checks on Complianz's
`cmplz_status_change` / `cmplz_enable_category` / `cmplz_fire_categories` events.
`inc/stark-forms.php` also registers it in `cmplz_known_script_tags` (category preferences), so it
stays blocked if the cookie blocker is turned on later.

Tested in a fresh browser context:

| Case | Requests to hub.starksocial.com |
|---|---|
| Complianz missing (unconfigured) | 0 — chat never loads |
| Consent given (event fired) | widget loads |
| US opt-out default (no choice yet) | widget loads — opt-out region: consent is assumed until refused |
| Opted out (preferences + marketing denied) | **0** — no chat |

**About the browser cookie warning:** Chrome's "third-party cookie" issue appears whenever the widget
loads, because hub.starksocial.com sets its cookies without `SameSite=None; Secure; Partitioned`.
Consent gating decides *when* it loads; it can't change how Perfex sets cookies. **The full fix
is on the Perfex server** (punch list). For opt-in behaviour, switch Complianz to an opt-in
region or set `regions` to include EU.
