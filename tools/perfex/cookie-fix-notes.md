# Perfex hub — chat cookie SameSite fix (not applied yet)

November 3 2026. **Status: not applied.** There is no SSH route to the hub from this
machine: the staging and live app users are jailed to their own Cloudways apps, and the
`cloudways-n8i` key is rejected. Someone with access to the hub app (Cloudways master
SSH or SFTP) applies the change below. It takes about 5 minutes.

## What the hub sends today

Checked through Cloudflare (from the live server), November 3 2026:

```
GET https://hub.starksocial.com/prchat/Chatbot_Controller/widget/67d3563318b5547bfab53fa6366ace82
HTTP/2 200
set-cookie: csrf_cookie_name=…; expires=…; Max-Age=3660; path=/; HttpOnly
set-cookie: sp_session=…; expires=…; Max-Age=28800; path=/; HttpOnly
body: /* Chatbot widget disabled: no API key configured */
```

- Both cookies come from **Perfex core** (CodeIgniter's CSRF cookie and the session
  cookie), not from the PRChat module. Every hub response sets them, the widget script
  included
- Neither has `Secure` or `SameSite`. Loaded from starksocial.com they are third-party
  cookies, so Chrome treats them as `SameSite=Lax` and doesn't send them on cross-site
  requests. DevTools flags them under Issues
- **The widget itself is switched off on the hub**: it returns the comment above and no
  chat code. So no chat shows on Phase 2 (or anywhere) until an API key is set in the
  PRChat module settings. That's separate from the cookies, and Nathan's call

## The change

### 1. `application/config/config.php` (Perfex root on the hub app)

`cookie_samesite` needs CodeIgniter 3.1.11 or later (check `CI_VERSION` in
`system/core/CodeIgniter.php`; recent Perfex releases ship 3.1.13). Set:

```php
$config['cookie_secure']   = TRUE;
$config['cookie_samesite'] = 'None';
```

Perfex's session settings live in `application/config/app-config.php` on some versions.
If `sess_cookie_name` is defined there, add the same two lines there too.

This covers `sp_session` and `csrf_cookie_name` via CodeIgniter's `setcookie()` options.

### 2. `Partitioned` (CHIPS), optional

CodeIgniter has no option for `Partitioned`. Add it at the web server, on the hub app only.
For Cloudways (Apache behind nginx), add this to the hub's `public_html/.htaccess`:

```apache
<IfModule mod_headers.c>
  Header always edit Set-Cookie "^(sp_session|csrf_cookie_name)=(.*)$" "$1=$2; Partitioned"
</IfModule>
```

`Partitioned` keeps the chat working in browsers that block third-party cookies
(Chrome's tracking protection, Safari). Without it the cookie is still sent once
`SameSite=None; Secure` is set, but only where third-party cookies are allowed.

**Risk:** the staff admin login on hub.starksocial.com uses the same session cookie.
`SameSite=None; Secure` doesn't change first-party logins. `Partitioned` on a first-party
visit is harmless: the partition is hub.starksocial.com itself. Check the staff login and the
client portal login after the change.

## Verify

```
curl -sI https://hub.starksocial.com/authentication/login | grep -i set-cookie
# expect: …; path=/; secure; HttpOnly; SameSite=None   (and "; Partitioned" with step 2)
```

Then open staging or Local with the chat enabled (`define( 'STARK_PRCHAT_ON_STAGING', true );`
in staging's `wp-config.php`, and a widget API key on the hub). Accept cookies and check
DevTools → Application → Cookies → hub.starksocial.com, plus DevTools → Issues: no SameSite
warnings.

## When Perfex updates

A Perfex update can replace `application/config/config.php` (not verified which versions
do). After any Perfex update, re-run the `curl` check above and re-apply step 1 if needed.

## Related finding: origin certificate on the hub

This Mac's `/etc/hosts` pins `starksocial.com`, `hub.starksocial.com` and
`brands.starksocial.com` to the origin (`147.182.207.244`), which bypasses Cloudflare. On the
origin, hub's certificate (Let's Encrypt, CN `updates.starksocial.com`) **expired November 2
2026 18:59 GMT**. Visitors are unaffected: Cloudflare serves its own valid certificate
(`*.starksocial.com`, valid to December 26 2026) and the Cloudflare → origin leg tolerates it
(SSL mode "Full", not "Full (strict)"). Still:
- Renew or reissue the hub app's certificate in Cloudways (SSL Management), or Cloudflare
  "Full (strict)" will break the hub
- Remove those `/etc/hosts` lines when not testing the origin: with them, the hub
  shows certificate errors on this Mac
