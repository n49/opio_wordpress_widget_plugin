=== Widget for OPIO Reviews ===
Author: Dhiraj Timalsina
Tags: Widget for OPIO Reviews, opio, reviews, rating, widget, google business, testimonials
Tested up to: 6.4
Stable tag: 1.1.21
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
