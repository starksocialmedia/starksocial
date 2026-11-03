# Podlove migration — live → Phase 2

**Why it's launch-critical:** Podlove generates the podcast RSS feed
(`/feed/podcast/`) that Apple Podcasts and other apps subscribe to, and the
tracked enclosure URLs (`/podlove/file/…`). Without it, subscribers break.

Done on Local November 2 2026 (v27.3.1); staging via the v27.3.x deploy.

## Versions (match live)

- `podlove-podcasting-plugin-for-wordpress` **4.5.8** (Podlove database version 167)
- `podlove-web-player` **5.10.0**

## Steps

1. **Install, don't activate yet:**
   `wp plugin install podlove-podcasting-plugin-for-wordpress --version=4.5.8` and `wp plugin install podlove-web-player --version=5.10.0`
2. **Export from live (read-only).** These 10 tables (live prefix `wp_49z880xoig_`):
   `podlove_episode, podlove_episodeasset, podlove_feed, podlove_filetype, podlove_mediafile, podlove_template, podlove_modules_seasons_season, podlove_modules_transcripts_transcript, podlove_modules_transcripts_voiceassignment, podlovesubscribebutton_button`
   ```
   ssh stark-live 'cd ~/public_html && wp --skip-themes --skip-plugins db export ~/tmp/podlove-export.sql --tables=<the 10 tables, comma-separated>'
   ```
   Not copied: download-intent analytics (raw listener data), geo, user agents, jobs, logs.
3. **Options** — all 28 `podlove%` options as raw serialized values (JSON), the podlove_* options query in this folder's history:
   `wp eval` → `json_encode` of `SELECT option_name, option_value … LIKE "podlove%"`, then
   `STARK_JSON=podlove-options.json wp eval-file import-options.php`. Import before activation, so the plugin sees DB version 167 and doesn't re-run its setup.
4. **Tables** — rename the prefix (`sed 's/wp_49z880xoig_podlove/wp_podlove/g'`) and import with `mysql`.
5. **Post IDs** — `podlove_episode.post_id` holds live's post IDs. Export live `ID<TAB>post_name` for the podcast posts, then
   `STARK_POSTS=podlove-posts.tsv wp eval-file remap-episode-posts.php` (matches by slug). On Local: 37 remapped; row 1 has `post_id` 0 on live too (an empty placeholder).
6. **Activate** both plugins, then `wp rewrite flush`.

## Verified on Local

- `/feed/podcast/`: 200 `application/rss+xml`, 10 items with enclosures. **Same item count and enclosure URL format as live** (`/podlove/file/3/s/feed/c/podcast/…mp3`), `itunes:image` from podcast.starksocial.com
- Episode pages render the Podlove Web Player (chapters, download, share). The theme's native `<audio>` fallback is only used when Podlove is inactive
- The theme still registers the `podcast` post type; Podlove's registration coexists (all podcast URLs 200)

## At cutover

The feed URL is unchanged. Re-run steps 2–5 if episodes were added on live after this
export (none since 2017). MP3s stay on podcast.starksocial.com. Phase 2 needs the
**same feed GUIDs**: they come from `podlove_episode.guid`, copied verbatim, so apps
won't re-download episodes.
