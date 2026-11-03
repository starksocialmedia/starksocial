#!/bin/bash
# Convert animated GIFs over a size threshold to silent MP4 next to the original (v27.4.1).
# The theme (inc/stark-media.php) serves <video> instead of <img> wherever X.mp4 exists
# beside X.gif; the GIF stays as the fallback. Re-runnable: existing MP4s are skipped.
# Usage: tools/images/convert-gifs.sh <uploads-dir> [min-bytes, default 1000000]
set -euo pipefail
dir="${1:?uploads dir}"; min="${2:-1000000}"
find "$dir" -iname '*.gif' -size +"${min}"c -not -path '*/stark-assets/*' -print0 |
while IFS= read -r -d '' gif; do
  mp4="${gif%.*}.mp4"
  [ -s "$mp4" ] && { echo "skip  $mp4"; continue; }
  ffmpeg -nostdin -v error -i "$gif" -an -movflags +faststart -pix_fmt yuv420p \
    -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" -c:v libx264 -preset slow -crf 24 "$mp4"
  printf '%s  %9d -> %8d bytes\n' "$gif" "$(stat -f%z "$gif" 2>/dev/null || stat -c%s "$gif")" "$(stat -f%z "$mp4" 2>/dev/null || stat -c%s "$mp4")"
done
