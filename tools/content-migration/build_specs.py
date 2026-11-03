"""Build Phase 2 content specs (JSON) from live page outlines (extract_live.py).
Every string comes from the live page text; nothing is written here except
the mapping. Usage: python3 build_specs.py <outline dir> <out.json>"""
import json, re, sys

D = sys.argv[1]


def load(pid):
    secs, cur = [], None
    for line in open(f'{D}/{pid}.md', encoding='utf-8'):
        line = line.rstrip('\n')
        if line.startswith('TEXT Search Site'):
            break  # footer / mobile nav copy from here on
        if line.startswith('---- section'):
            cur = {'cls': line[13:].strip(), 'lines': []}
            secs.append(cur)
        elif cur is not None and line.strip():
            kind, _, txt = line.partition(' ')
            cur['lines'].append((kind, txt.strip()))
    return secs


def sec(secs, cls):
    hits = [s for s in secs if cls in s['cls'].split()]
    assert hits, cls
    return hits[0]['lines']


T = lambda lines: [t for k, t in lines if k == 'TEXT']
LI = lambda lines: [t for k, t in lines if k == 'LI']
BTN = lambda lines: [t for k, t in lines if k == 'BUTTON']


def link(btn):
    label, _, url = btn.partition(' -> ')
    url = url.strip()
    if url in ('/contact', 'contact'):
        url = '/contact/'
    return {'title': label.strip(), 'url': url, 'target': ''}


def pairs(texts):
    return [{'title': texts[i], 'description': texts[i + 1]} for i in range(0, len(texts) - 1, 2)]


def steps(texts):
    """[n, title, desc, n, title, desc…] or [title, desc, …] → steps."""
    texts = [t for t in texts if not re.fullmatch(r'0?\d\.?', t)]
    return pairs(texts)


def faq(lines):
    out = []
    for item in LI(lines):
        q, sep, a = item.partition('? ')
        out.append({'question': q + '?', 'answer': a} if sep else {'question': item, 'answer': '[NEEDS REVIEW] answer not found on live'})
    return out


def hero(lines):
    t, b = T(lines), BTN(lines)
    h = {'hero_headline': t[0], 'hero_thesis': t[1], 'hero_note': t[2] if len(t) > 2 else ''}
    if b:
        h['hero_cta_primary'] = link(b[0])
    if len(b) > 1 and not b[1].split(' -> ')[1].startswith('#'):
        h['hero_cta_secondary'] = link(b[1])
    return h


def cta(lines):
    t, b = T(lines), BTN(lines)
    return {'final_cta': {'heading': t[0], 'subhead': t[1] if len(t) > 1 else '', 'note': t[2] if len(t) > 2 else '',
                          'button': link(b[0]) if b else None}}


def faq_block(lines):
    t = T(lines)
    return {'faq_heading': t[1], 'faq_intro': t[2] if len(t) > 2 else '', 'faq_items': faq(lines)}


def fit(lines, eyebrow=True):
    """eyebrow?, heading, intro, then good-heading + items, bad-heading + items.
    Items can be LI or TEXT (the hub page uses TEXT for the not-fit list)."""
    seq = list(lines)
    head = []
    while seq and not seq[0][1].endswith('…'):
        head.append(seq.pop(0)[1])
    good_h = seq.pop(0)[1]
    good = []
    while seq and not seq[0][1].endswith('…'):
        good.append(seq.pop(0)[1])
    bad_h = seq.pop(0)[1] if seq else ''
    bad = [t for k, t in seq]
    out = {'fit_good_heading': good_h, 'fit_good': '\n'.join(good), 'fit_bad_heading': bad_h, 'fit_bad': '\n'.join(bad)}
    if eyebrow and head:
        out['fit_eyebrow'] = head.pop(0)
    if head:
        out['fit_heading'] = head.pop(0)
    if head:
        out['fit_intro'] = ' '.join(head)
    return out


def problem(lines, points):
    t = T(lines)
    body, pts = t[2:-points - 1], t[-points:]
    note = t[-points - 1]
    return {'problem_eyebrow': t[0], 'problem_heading': t[1], 'problem_body': '\n\n'.join(body),
            'problem_note': note, 'problem_points': '\n'.join(pts)}


specs = {}

# Hub page — card descriptions for each service come from here.
hub = load(1049)
cards = T(sec(hub, 'lt-blue-section'))[2:]
card_desc = {}
names = {'Social Media Management': 'social-media-management', 'Paid Social Advertising': 'paid-advertising',
         'Content Creation': 'content-creation', 'Web Design & Development': 'web-design', 'SEO': 'seo',
         'Brand Strategy & Identity': 'brand-strategy-identity'}
for i in range(0, len(cards) - 1, 3):
    card_desc[names[cards[i]]] = cards[i + 1]

# Social Media Management (1081)
s = load(1081)
out = sec(s, 'stark-outcomes')
spec = hero(sec(s, 'stark-hero'))
t = T(out)
spec.update({'outcomes_heading': t[0], 'outcomes_items': pairs(t[1:])})
inc = sec(s, 'stark-what-we-do')
spec.update({'included_heading': T(inc)[0], 'included_intro': T(inc)[1], 'included_items': [{'title': x, 'description': '', 'icon': 'check'} for x in LI(inc)]})
p = T(sec(s, 'stark-process'))
spec.update({'process_eyebrow': p[0], 'process_heading': p[1], 'process_intro': p[2], 'process_steps': steps(p[3:])})
spec.update(fit(sec(s, 'stark-fit')))
c = sec(s, 'stark-collab')
ct = T(c)
need = LI(c)[5:]
spec['extra_sections'] = [{'eyebrow': ct[0], 'heading': ct[1], 'intro': ct[2],
                           'items': [{'title': x, 'description': ''} for x in need], 'note': '',
                           '_live_heading_for_items': ct[4]}]
spec.update(faq_block(sec(s, 'stark-faq')))
spec.update(cta(sec(s, 'stark-cta')))
specs['social-media-management'] = spec

# Paid Advertising (1134)
s = load(1134)
spec = hero(sec(s, 'stark-hero'))
t = T(sec(s, 'paid-ad-changes'))
spec.update({'outcomes_eyebrow': t[0], 'outcomes_heading': t[1], 'outcomes_intro': t[2], 'outcomes_items': pairs(t[3:])})
t = T(sec(s, 'paid-process'))
spec.update({'included_heading': t[0], 'included_intro': t[1] + ' ' + t[2],
             'included_items': [{'title': x['title'], 'description': x['description'], 'icon': 'check'} for x in pairs(t[3:-1])],
             'included_note': t[-1]})
t = T(sec(s, 'our-approach'))
spec.update({'process_eyebrow': t[0], 'process_heading': t[1], 'process_intro': t[2], 'process_steps': steps(t[3:])})
t = T(sec(s, 'service-first-30'))
weeks = t[4:-1]
spec['extra_sections'] = [{'eyebrow': t[0], 'heading': t[1], 'intro': t[2] + ' ' + t[3],
                           'items': [{'title': weeks[i] + ': ' + weeks[i + 1], 'description': weeks[i + 2]} for i in range(0, len(weeks) - 2, 3)],
                           'note': t[-1]}]
spec.update(faq_block(sec(s, 'stark-faq')))
spec.update(cta(sec(s, 'stark-cta')))
specs['paid-advertising'] = spec

# Content Creation (1169)
s = load(1169)
spec = hero(sec(s, 'stark-hero'))
t = T(sec(s, 'what-we-create'))
spec.update({'included_heading': t[1], 'included_intro': t[2],
             'included_items': [{'title': x['title'], 'description': x['description'], 'icon': 'check'} for x in pairs(t[3:-1])],
             'included_note': t[-1], '_included_eyebrow_live': t[0]})
t = T(sec(s, 'how-it-fits-flow'))
spec.update({'process_eyebrow': t[0], 'process_heading': t[1], 'process_intro': t[2], 'process_steps': steps(t[3:-1]), 'process_note': t[-1]})
spec.update(faq_block(sec(s, 'stark-faq')))
spec.update(cta(sec(s, 'stark-cta')))
specs['content-creation'] = spec

# Web Design (1275)
s = load(1275)
spec = hero(sec(s, 'stark-hero'))
t = T(sec(s, 'stark-outcomes'))
spec.update({'outcomes_eyebrow': t[0], 'outcomes_heading': t[1], 'outcomes_intro': t[2], 'outcomes_items': pairs(t[3:])})
spec.update(problem(sec(s, 'stark-webdesign-problem'), 4))
inc = sec(s, 'stark-what-we-do')
spec.update({'included_heading': T(inc)[0], 'included_intro': T(inc)[1], 'included_items': [{'title': x, 'description': '', 'icon': 'check'} for x in LI(inc)]})
p = T(sec(s, 'stark-process'))
spec.update({'process_eyebrow': p[0], 'process_heading': p[1], 'process_intro': p[2], 'process_steps': steps(p[3:])})
spec.update(fit(sec(s, 'stark-fit')))
spec.update(faq_block(sec(s, 'stark-faq')))
spec.update(cta(sec(s, 'stark-cta')))
specs['web-design'] = spec

# SEO (1392)
s = load(1392)
spec = hero(sec(s, 'stark-hero'))
t = T(sec(s, 'stark-outcomes'))
spec.update({'outcomes_eyebrow': t[0], 'outcomes_heading': t[1], 'outcomes_intro': t[2], 'outcomes_items': pairs(t[3:])})
spec.update(problem(sec(s, 'stark-seo-problem'), 4))
p = T(sec(s, 'stark-process'))
spec.update({'process_eyebrow': p[0], 'process_heading': p[1], 'process_intro': p[2], 'process_steps': steps(p[3:])})
inc = sec(s, 'stark-what-we-do')
spec.update({'included_heading': T(inc)[0], 'included_intro': T(inc)[1] + ' ' + T(inc)[2],
             'included_items': [{'title': x, 'description': '', 'icon': 'check'} for x in LI(inc)]})
spec.update(faq_block(sec(s, 'stark-faq')))
spec.update(cta(sec(s, 'stark-cta')))
specs['seo'] = spec

# Brand Strategy & Identity (1427) — process and FAQ on live are copies of the
# SEO and Web Design pages (known Phase 1 bug), so they are NOT carried over.
s = load(1427)
spec = hero(sec(s, 'stark-hero'))
t = T(sec(s, 'stark-outcomes'))
spec.update({'outcomes_eyebrow': t[0], 'outcomes_heading': t[1], 'outcomes_intro': t[2], 'outcomes_items': pairs(t[3:-1]), 'outcomes_note': t[-1]})
spec.update(problem(sec(s, 'stark-seo-problem'), 4))
p = T(sec(s, 'stark-process'))
spec.update({'process_eyebrow': p[0], 'process_heading': p[1], 'process_intro': p[2],
             '_skip': 'process_steps and faq_items: live shows the SEO steps and Web Design FAQ (Phase 1 bug) — keep the Phase 2 draft until Nathan writes them'})
f = sec(s, 'stark-faq')
spec.update({'faq_heading': T(f)[1], 'faq_intro': T(f)[2]})
spec.update(cta(sec(s, 'stark-cta')))
specs['brand-strategy-identity'] = spec

for slug, d in card_desc.items():
    specs[slug]['card_description'] = d

# Services hub (1049)
h = load(1049)
t = T(sec(h, 'stark-hero'))
o = T(sec(h, 'stark-outcomes'))
specs['_hub'] = {'hero_title': t[0], 'hero_thesis': t[1], 'hero_note': t[2],
                 'principles': pairs(o), 'services_heading': cards and T(sec(h, 'lt-blue-section'))[0],
                 'services_intro': T(sec(h, 'lt-blue-section'))[1], **fit(sec(h, 'stark-fit'), eyebrow=False),
                 **cta(sec(h, 'stark-cta'))}

json.dump(specs, open(sys.argv[2], 'w'), indent=1, ensure_ascii=False)
print({k: len(v) for k, v in specs.items()})
