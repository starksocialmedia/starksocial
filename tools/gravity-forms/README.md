# Contact form — Gravity Forms + Perfex webhook

`stark-contact-form.json` is the form definition (fields, notifications, confirmation) and
the webhook feed. `import-contact-form.php` creates or updates it (form ID 1 on a fresh site),
points the Contact page's Form tab at it, and creates `/thank-you/` (noindex).

```
STARK_JSON=tools/gravity-forms/stark-contact-form.json \
STARK_WEBHOOK_SECRET='…' wp eval-file tools/gravity-forms/import-contact-form.php
```

**Secret:** the feed sends `X-Webhook-Secret` to `https://hub.starksocial.com/webhooks/lead.php`
(the custom PHP webhook that replaced the Perfex GF module — ERRORLOG ERR-002). The value is the
one on live's "Form to Stark Hub" feed and is **never committed**. Without `STARK_WEBHOOK_SECRET`
the feed is created **inactive**. Local runs inactive on purpose, so dev submissions can't create real leads.

Requires Gravity Forms and the **Webhooks add-on** (1.7.0, copied from live's plugins folder).

## Fields

| ID | Field | Notes |
|---|---|---|
| 1 | Name | required |
| 2 | Business Name | required |
| 3 | Email | required, reply-to on the admin notification |
| 4 | Phone | optional, international format |
| 5 | What are you looking to improve? | Social Media / Paid Advertising / Web Design / SEO / Content / Brand Strategy / Audit / Not sure yet. Values are service slugs; prefill `?service=web-design` |
| 6 | Your city or market | prefill `?market=Santa Clarita` |
| 7 | Message | optional |
| 8–11 | hidden `source_page_url` (`{referer}`), `source_service` (`?service`), `source_market` (`?market`), `source_campaign` (`?utm_campaign`) | landing-page tracking |

## Webhook mapping (JSON body)

Live's keys are kept so `lead.php` keeps working — `name`, `email`, `phonenumber`, `message`,
`source` ("Website Contact Form") — plus `company` (Business Name) and `city` (market), and
`source_service`, `source_market`, `source_campaign`. Live sends `website`, which this form
doesn't collect. `message` combines the message with the service, market, page and campaign,
so they reach Perfex even if `lead.php` ignores the extra keys. **Check `lead.php` maps
`company` and `city`** (Perfex lead fields of the same name).

## Notifications and confirmation

- **Admin:** to nathan@ + deanna@starksocial.com (same as live), from postmaster@mg.starksocial.com, reply-to the visitor
- **Confirmation:** to the visitor ("we'll get back to you within one business day") with a copy of their message
- Both are rendered in the branded div-based wrapper from `stark-social/inc/stark-emails.php`, with the `{stark_fields}` merge tag instead of the table-based `{all_fields}` (ERR-001)
- **After submit:** redirect to `/thank-you/` (`templates/stark-thank-you.php`)

## Tested

- **Local** (feed inactive): submitted in headless Chrome → redirected to `/thank-you/`, both emails caught by Mailpit and rendered correctly. Test entry deleted
- **Staging:** see CHANGELOG v27.3.2 for the end-to-end Perfex result
