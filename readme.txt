=== Widget for OPIO Reviews ===
Author: Dhiraj Timalsina
Tags: Widget for OPIO Reviews, opio, reviews, rating, widget, google business, testimonials
Tested up to: 6.4
Stable tag: 1.1.32
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin relies on third party service called OPIO - https://www.op.io to pull reviews of registered entities, organizations and users.
This plugin also uses third party images, videos and media content for showing reviews

** Details about the third party service **
Link to third party service - https://op.io
Privacy policy - https://opioapp.com/privacy-policy/
Features - https://opioapp.com/features/

This plugin relies on the domain - images.files.ca to retrieve images of the registered entities, organizations and users.
And also any reviews that are posted on the third party service - https://op.io

** Details about the third party service **
Link to third party service - https://n49.com
Privacy policy - https://www.n49.com/privacy-policy.php

This plugin relies on the domain - videocdn.n49.ca to retrieve videos of the registered entities, organizations and users.
And also any reviews that are posted on the third party service - https://op.io

** Details about the third party service **
Link to third party service - https://n49.com
Privacy policy - https://www.n49.com/privacy-policy.php

Official way to show OPIO Reviews on WordPress site without manual code settings and other unofficial methods. Boost user trust and sales on your website!

== Description ==

This plugin displays **OPIO Reviews** on your WordPress website through a public and approved by OPIO API without crawling and other unofficial methods. With this plugin, you can be sure of the right way for showing **OPIO Reviews**.

Displaying **OPIO Rating** and Reviews on your site is the easiest and most effective way to increase your customer confidence, show stars and increase conversion!

== Multilingual / Slider Translation ==

The `[opio_slider]` shortcode accepts an optional `lang` attribute to render the slider in another language. UI labels translate from shipped translation files; review content + comments + JSON-LD schema translate via a free machine-translation service (cached after first load).

**Usage:**

`[opio_slider id="37" lang="fr"]`

Omit `lang=` to keep English. Setup-agnostic — no Polylang/WPML/`get_locale()` coupling. Works on any WordPress site without installing extra locale files.

**30 languages ship with hand-curated UI translations:**

| Code | Language |
|------|----------|
| `fr` | French — Français |
| `es` | Spanish — Español |
| `pt` | Portuguese — Português |
| `de` | German — Deutsch |
| `it` | Italian — Italiano |
| `nl` | Dutch — Nederlands |
| `tr` | Turkish — Türkçe |
| `pl` | Polish — Polski |
| `el` | Greek — Ελληνικά |
| `sv` | Swedish — Svenska |
| `uk` | Ukrainian — Українська |
| `ru` | Russian — Русский |
| `he` | Hebrew — עברית *(RTL)* |
| `ar` | Arabic — العربية *(RTL)* |
| `fa` | Persian / Farsi — فارسی *(RTL)* |
| `ur` | Urdu — اردو *(RTL)* |
| `hi` | Hindi — हिन्दी |
| `pa` | Punjabi — ਪੰਜਾਬੀ |
| `bn` | Bengali — বাংলা |
| `ta` | Tamil — தமிழ் |
| `ja` | Japanese — 日本語 |
| `ko` | Korean — 한국어 |
| `zh` | Mandarin (Simplified Chinese) — 普通话 |
| `hk` | Cantonese (Traditional Chinese) — 粵語 |
| `vi` | Vietnamese — Tiếng Việt |
| `tl` | Tagalog — Filipino |
| `id` | Indonesian — Bahasa Indonesia |
| `ms` | Malay — Bahasa Melayu |
| `th` | Thai — ไทย |

Any other ISO 639-1 code (e.g., `sw`, `nb`, `fi`, `cs`) will translate review content via the machine-translation API, while UI labels stay English. Invalid codes silently fall back to English everywhere.

Full developer documentation, filter hooks for raising translation quota, and instructions for adding a 31st hand-curated language are in `LANGUAGES.md` in the plugin folder.

== Changelog ==

= 1.1.32 =
* Slider: support Google reviews with photos and videos. Google media carries direct URLs (`thumbnailUrl`/`url`) instead of opio image/video IDs, so the slider now renders these directly. Google videos play inside a no-referrer iframe to bypass Google's cross-site hotlink protection.

= 1.1.25 =
* Add Microsoft Azure Translator as a second translation provider alongside MyMemory. Activate per-site with `add_filter('opio_translation_provider', fn() => 'azure');` plus `opio_translation_azure_key` (subscription key) and optionally `opio_translation_azure_region` (regional resource) in the site's `functions.php`. MyMemory remains the default — existing installs unchanged. Azure gives a 2 M chars/month permanent free tier (vs. MyMemory's 50K/day per IP) and isolates each client's quota from other plugin users sharing the same hosting egress IP.
* Stats log now includes a `provider` field (`mymemory` or `azure`) so the browser debug panel reflects which backend handled each render.

= 1.1.24 =
* Auto-clear the translation rate-limit circuit breaker on every plugin version change. Each deploy gets a fresh chance to reach MyMemory; if quota is still exhausted, the breaker re-trips automatically on the next 429.

= 1.1.23 =
* Add MyMemory circuit breaker. On `HTTP 429`, `HTTP 503`, or a `MYMEMORY WARNING` payload, sets a 1-hour transient (`opio_translation_rate_limited`) that short-circuits subsequent API calls. Stops the slider from firing 23+ doomed requests per render and burning more quota when the limit eventually resets. Counter `api_skipped` and field `circuit_breaker` in the stats log reflect the live state.
* Add `email_filter` indicator to the translation stats so you can confirm whether the `opio_translation_email` filter actually loaded ("set" or "empty").
* Compact CSS for the rating widget area when a `lang` attribute is active — shorter font sizes and `white-space: nowrap` on "Powered by" / "See all X Reviews" / "Write a review" so longer translated strings (Spanish "Con tecnología de", German "Bereitgestellt von", Tagalog "Pinapatakbo ng", etc.) don't wrap across multiple lines or get cut off.

= 1.1.22 =
* Add translation telemetry. Browser console now logs `[OPIO slider stats]` after each render with counters: `translation_calls`, `cache_full_hits`, `chunk_cache_hits`, `chunks_total`, `api_calls`, `api_success`, `api_errors`, `last_error`, `last_http_code`, `last_endpoint_host`, `schema_fetched`, `schema_fetch_success`, `schema_translated`. Lets you diagnose translation failures (rate limits, network blocks, API errors) from the browser without server access.
* Server-side `error_log` lines for translation failures now include the target language code and endpoint host for easier grepping.

= 1.1.21 =
* Fix: slider tiles invisible on RTL host pages (Arabic / Persian / Urdu / Hebrew). The slider's outer wrapper now declares `direction: ltr` so page-level RTL inheritance doesn't break Slick carousel's internal positioning math. Arabic/Persian/Urdu/Hebrew text inside tiles still renders right-to-left via Unicode bidi.

= 1.1.20 =
* Docs: Mark which 4 of the 30 supported languages are right-to-left (`ar`, `fa`, `ur`, `he`) in `LANGUAGES.md`, `readme.txt`, and the in-admin Support tab.

= 1.1.19 =
* Docs: Support tab now pairs each language with its short code (e.g. "French — fr") for easier copy/paste.
* Docs: Add `TRANSLATION-PROVIDERS.md` covering MyMemory limits, scalability ceilings, and paid-provider comparison.

= 1.1.18 =
* Add slider translation support — `[opio_slider id='X' lang='fr']` (and 29 other languages).
* Translate UI labels (Read more, Powered by, Write a review, etc.) via shipped `.mo` files.
* Translate review content, comments, and JSON-LD `reviewBody`/`description` via free MyMemory API with per-chunk transient caching.
* Add `inLanguage` to JSON-LD Review nodes for SEO.
* JSON-LD schema now also emits from the vertical and horizontal-carousel layouts (previously only horizontal).
