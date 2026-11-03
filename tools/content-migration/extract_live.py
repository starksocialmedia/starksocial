"""Outline a rendered live (Cornerstone/X theme) page: section breaks, headings,
paragraphs, list items, links, images, buttons.
Usage: python3 extract_live.py page.html > page.md"""
import sys, re, html

src = open(sys.argv[1], encoding='utf-8', errors='ignore').read()
body = re.search(r'<body[^>]*>(.*)</body>', src, re.S).group(1)
# drop chrome and non-content
for tag in ('script', 'style', 'noscript', 'svg', 'video', 'form', 'template'):
    body = re.sub(r'<%s\b.*?</%s>' % (tag, tag), ' ', body, flags=re.S | re.I)
for marker in ('x-masthead', 'x-colophon', 'stark-mobile-nav', 'cmplz-cookiebanner', 'stark-a11y', 'stark-back-to-top'):
    pass  # handled below by cutting header/footer regions
body = re.sub(r'<header\b.*?</header>', ' ', body, flags=re.S)
body = re.sub(r'<footer\b.*?</footer>', ' ', body, flags=re.S)
body = re.split(r'<div[^>]*id="cmplz-|<div[^>]*class="[^"]*cmplz-cookiebanner', body)[0]

def text(s):
    s = re.sub(r'<br\s*/?>', ' ', s)
    s = re.sub(r'<a\b[^>]*href="([^"]+)"[^>]*>(.*?)</a>', lambda m: '[%s](%s)' % (re.sub(r'<[^>]+>', '', m.group(2)).strip(), m.group(1)), s, flags=re.S)
    s = re.sub(r'<[^>]+>', ' ', s)
    return re.sub(r'\s+', ' ', html.unescape(s)).strip()

out = []
tok = re.compile(r'<(?P<t>h[1-6]|p|li|blockquote|figcaption|dt|dd|summary)\b[^>]*>(?P<c>.*?)</(?P=t)>'
                 r'|<(?:div|section)\b[^>]*class="(?P<sec>[^"]*\bx-section\b[^"]*)"'
                 r'|<img\b[^>]*src="(?P<img>[^"]+)"[^>]*?(?:alt="(?P<alt>[^"]*)")?'
                 r'|<a\b[^>]*class="[^"]*(?:x-anchor-button|stark-btn|x-btn)[^"]*"[^>]*href="(?P<bh>[^"]+)"[^>]*>(?P<bt>.*?)</a>'
                 r'|<div\b[^>]*class="[^"]*\bx-text\b[^"]*"[^>]*>(?P<xt>(?:(?!<div).)*?)</div>', re.S)
for m in tok.finditer(body):
    if m.group('t'):
        t = text(m.group('c'))
        if t: out.append('%s %s' % (m.group('t').upper(), t))
    elif m.group('sec'):
        cls = ' '.join(c for c in m.group('sec').split() if not re.match(r'^(e\d|m\w+-\w|x-section$)', c))
        out.append('\n---- section ' + cls)
    elif m.group('img'):
        if not m.group('img').startswith('data:'): out.append('IMG %s alt="%s"' % (m.group('img'), m.group('alt') or ''))
    elif m.group('bh'):
        t = text(m.group('bt'))
        if t: out.append('BUTTON %s -> %s' % (t, m.group('bh')))
    elif m.group('xt') is not None:
        t = text(m.group('xt'))
        if t: out.append('TEXT ' + t)
# collapse exact consecutive duplicates (responsive copies)
clean = []
for l in out:
    if not clean or clean[-1] != l: clean.append(l)
print('\n'.join(clean))
