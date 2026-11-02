import sys, re, json, html as H
out = {}
for slug in sys.argv[2:]:
    t = open(f"{sys.argv[1]}/legal-{slug}.html", encoding='utf-8', errors='ignore').read()
    m = re.search(r"<div id=\"[^\"]+\" class=\"x-text x-content[^\"]*\"[^>]*>", t)
    start = m.end(); depth = 1; i = start
    for tag in re.finditer(r'<(/?)div\b[^>]*>', t[start:]):
        depth += -1 if tag.group(1) else 1
        if depth == 0: end = start + tag.start(); break
    body = t[start:end]
    body = re.sub(r'\s+', ' ', body).strip()
    body = re.sub(r'\s*(<(?:h2|h3|p|ul|ol|li|/ul|/ol)\b)', r'\n\1', body).strip()
    body = re.sub(r' (</(?:p|li|h2|h3)>)', r'\1', body); body = re.sub(r'(<(?:p|li)>) ', r'\1', body)
    upd = re.search(r'Last updated:(?:</strong>)?\s*([A-Z][a-z]+ \d{1,2}, \d{4})', t)
    out[slug] = {'updated': upd.group(1) if upd else '', 'html': body}
    print(slug, 'h2:', body.count('<h2'), 'h3:', body.count('<h3'), 'chars:', len(body), 'updated:', out[slug]['updated'], file=sys.stderr)
json.dump(out, open(f"{sys.argv[1]}/legal.json", 'w'))
