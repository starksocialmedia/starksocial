# Share (Open Graph) images

**Live's branded share images are used wherever they exist** (12 pages: home, the 6 live
services, About, Contact, Services, Blog, Support/KB/password generator). They're set per
page by `tools/rankmath/import-live-meta.php`. They use the real Stark script logo.

**Generated** (`generate.mjs`, v27.4.7) only for pages with none: SEO (live had no share image),
Audit & Consulting, `/service-areas/`, `/web-design/antelope-valley/`. Output:
`stark-social/img/og/{page-path-with-dashes}.jpg`, 1200×630. `inc/stark-seo.php` uses one only
when the page has no share image in its Rank Math box.

Design: navy → accent-blue gradient, the Stark wordmark (`img/logos/stark-wordmark-light.svg`, since v27.5.0), Barlow 700 headline,
tagline = the page's card description (Audit & Consulting's is **draft copy**; regenerate
when approved). To add a page: add it to `pages.json` and run (headless Chrome on :9333):

```
node tools/images/og/generate.mjs
```

Check after launch (the URLs must be public): Facebook Sharing Debugger and LinkedIn Post
Inspector. X/Twitter has no card validator any more; post a test.
