# Animated GIF → MP4 (v27.4.1)

November 3 2026. Every GIF over 1 MB in `wp-content/uploads` was converted to a silent,
looping H.264 MP4 next to the original (`tools/images/convert-gifs.sh`, CRF 24, faststart).

| GIF | Used in | GIF | MP4 | Saved |
|---|---|---|---|---|
| 2024/07/jumping-the-shark | Episode "Has Snapchat Jumped The Shark?" (613) | 7.20 MB | 2.19 MB | 70% |
| 2024/07/pengiun-fail | Episode "Common Social Media Fails" (765) | 4.02 MB | 0.25 MB | 94% |
| 2024/08/festivus-for-the-rest-of-us | Episode "Getting the Most of Your Holiday Marketing" (794) | 3.43 MB | 0.18 MB | 95% |
| 2024/07/meerkats | Episode "Does Your Biz Really Need a Video App?" (726), where it is the LCP image | 3.30 MB | 0.29 MB | 91% |
| 2024/07/its-where-i-buy-all-off-my-food | Episode "2015 Social Media Year in Review" (773) | 2.78 MB | 0.14 MB | 95% |
| 2017/05/not-the-droid-you-are-looking-for | Episode "How to Get Your First 100 Followers" (645) | 2.68 MB | 0.14 MB | 95% |
| 2024/08/last-man-on-earth | Episode "Enhancing Your Hyperlocal Marketing" (780) | 2.20 MB | 0.19 MB | 91% |
| 2015/11/no-fooling-google-1 | Episode "What's the Deal with SEO" (801) | 2.18 MB | 0.11 MB | 95% |
| 2024/08/hated-it | Episode "Understanding the Facebook Algorithm" (788) | 1.78 MB | 0.11 MB | 94% |
| **Total** | 9 podcast episodes (no blog posts use a GIF over 1 MB) | **29.6 MB** | **3.6 MB** | **88%** |

## How it's served

`inc/stark-media.php` filters `the_content`: an `<img src="….gif">` becomes

```html
<video class="… stark-gif-video" autoplay loop muted playsinline preload="metadata" width height aria-label="{alt}">
  <source src="….mp4" type="video/mp4"><a href="….gif">{alt}</a>
</video>
```

only when the `.mp4` exists beside the `.gif`. The stored content isn't edited, so the
cutover re-sync from live keeps working and deleting an MP4 brings its GIF back. The
fallback is a **link** to the GIF, not an `<img>`: browsers download an `<img>` inside
`<video>` even when the video plays (tested: the 3.3 MB GIF was still fetched).
`js/stark-global.js` pauses these videos for `prefers-reduced-motion` and lets click or Enter
toggle play/pause (WCAG 2.2.2).

## Target: 50 MB not reachable

All GIFs over 1 MB add up to 29.6 MB, and the originals stay on disk as the fallback,
so **uploads didn't shrink**. Page weight did: each of those 9 episode pages now loads
0.1–2.2 MB instead of 1.8–7.2 MB (−26 MB across them). Deleting the GIFs after launch would
free 29.6 MB, but old links and social shares point at them. Not done.

## Also fixed

Episode 613 linked `/wp-content/uploads/jumping-the-shark.gif` (no year folder), which is
a **404 on live**. Phase 2 points at `/2024/07/` (`tools/images/fix-episode-613-gif.php`).
**Live still has the broken image** (punch list; live was out of scope today).

## New GIFs

Run `tools/images/convert-gifs.sh <uploads-dir>` on the server after uploading a large GIF.
It skips GIFs that already have an MP4.
