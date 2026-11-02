# Legal pages import

Recreates Privacy Policy, Terms of Service, Cookie Policy and Accessibility
Statement as children of a Support page (`/support/{slug}/`, same as live)
from the **rendered** live pages. Live stores them as Cornerstone data whose
SEO text has lost its heading tags; the rendered HTML keeps h2/h3/lists.

```bash
mkdir -p /tmp/legal && for p in privacy-policy terms-of-service cookie-policy accessibility-statement; do
  curl -sL https://starksocial.com/support/$p/ -o /tmp/legal/legal-$p.html; done
python3 tools/legal-import/extract_legal.py /tmp/legal privacy-policy terms-of-service cookie-policy accessibility-statement
STARK_JSON=/tmp/legal/legal.json wp eval-file tools/legal-import/seed-legal.php   # theme must be active first
```

Reuses WordPress's privacy page (`wp_page_for_privacy_policy`) for Privacy Policy.
First run: November 2 2026 (all four "Last updated: January 15, 2026").
