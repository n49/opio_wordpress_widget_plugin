# Slider Translation Support

The `[opio_slider]` shortcode accepts an optional `lang` attribute to translate UI labels and review content into a target language.

## Usage

```
[opio_slider id="37" lang="fr"]
```

- Omit `lang` to render English (no translation overhead).
- `lang=` accepts ISO 639-1 short codes (`fr`, `de`, etc.) or full WordPress locales (`fr_CA`, `de_DE`). BCP 47 dash form (`pt-BR`) is auto-normalised to underscore (`pt_BR`).
- No coupling to Polylang / WPML / `get_locale()`. The shortcode attribute is the single source of truth.

## Shortcodes for all hand-curated locales

Copy/paste any of these into a page or post — replace `id="37"` with your slider's post ID.

| Shortcode | Language |
|-----------|----------|
| `[opio_slider id="37"]` *(or `lang="en"`)* | English (default) |
| `[opio_slider id="37" lang="fr"]` | French — Français |
| `[opio_slider id="37" lang="es"]` | Spanish — Español |
| `[opio_slider id="37" lang="pt"]` | Portuguese — Português |
| `[opio_slider id="37" lang="de"]` | German — Deutsch |
| `[opio_slider id="37" lang="it"]` | Italian — Italiano |
| `[opio_slider id="37" lang="nl"]` | Dutch — Nederlands |
| `[opio_slider id="37" lang="tr"]` | Turkish — Türkçe |
| `[opio_slider id="37" lang="pl"]` | Polish — Polski |
| `[opio_slider id="37" lang="el"]` | Greek — Ελληνικά |
| `[opio_slider id="37" lang="sv"]` | Swedish — Svenska |
| `[opio_slider id="37" lang="uk"]` | Ukrainian — Українська |
| `[opio_slider id="37" lang="ru"]` | Russian — Русский |
| `[opio_slider id="37" lang="he"]` | Hebrew — עברית |
| `[opio_slider id="37" lang="ar"]` | Arabic — العربية |
| `[opio_slider id="37" lang="fa"]` | Persian / Farsi — فارسی |
| `[opio_slider id="37" lang="ur"]` | Urdu — اردو |
| `[opio_slider id="37" lang="hi"]` | Hindi — हिन्दी |
| `[opio_slider id="37" lang="pa"]` | Punjabi — ਪੰਜਾਬੀ |
| `[opio_slider id="37" lang="bn"]` | Bengali — বাংলা |
| `[opio_slider id="37" lang="ta"]` | Tamil — தமிழ் |
| `[opio_slider id="37" lang="ja"]` | Japanese — 日本語 |
| `[opio_slider id="37" lang="ko"]` | Korean — 한국어 |
| `[opio_slider id="37" lang="zh"]` | Mandarin (Simplified Chinese) — 普通话 |
| `[opio_slider id="37" lang="hk"]` | Cantonese (Traditional Chinese) — 粵語 |
| `[opio_slider id="37" lang="vi"]` | Vietnamese — Tiếng Việt |
| `[opio_slider id="37" lang="tl"]` | Tagalog — Filipino |
| `[opio_slider id="37" lang="id"]` | Indonesian — Bahasa Indonesia |
| `[opio_slider id="37" lang="ms"]` | Malay — Bahasa Melayu |
| `[opio_slider id="37" lang="th"]` | Thai — ไทย |

### Permissive fallback for any other language

Any other ISO 639-1 code accepted by MyMemory will translate **review content + comments + JSON-LD schema** via the MyMemory API, but UI labels stay English. Examples: `sw` (Swahili), `nb` (Norwegian), `fi` (Finnish), `cs` (Czech), `ro` (Romanian).

If MyMemory rejects the language code (or the input doesn't look like a locale), the slider renders identically to the no-`lang` baseline — graceful English fallback, no errors.

## How translation works

Two independent surfaces, both gated by the `lang` attribute:

- **UI labels** ("Read more", "Powered by", "Write a review", "See all X Reviews", "Recommends", lightbox strings, date format) — standard WordPress gettext. The matching `.mo` file in `languages/` is loaded directly via `load_textdomain()` for the duration of the shortcode render, then unloaded. Works on vanilla WordPress without any locale files installed.

- **Review content + comments + JSON-LD `reviewBody` / `description`** — translated via the free MyMemory API (5,000 chars/day per IP, 50,000 with email). Long content is chunked at sentence boundaries (≤450 chars per request) and cached forever as transients keyed by `md5(content + lang)`. Translation only runs on cold cache; subsequent loads are instant. JSON-LD Review nodes also get `inLanguage` set to the target language code for SEO.

## Raising the MyMemory quota

To get the 50,000 chars/day tier, set an email in your theme's `functions.php`:

```php
add_filter('opio_translation_email', function() {
    return 'you@example.com';
});
```

You can also override the endpoint entirely (e.g., to use a self-hosted LibreTranslate or commercial provider):

```php
add_filter('opio_translation_endpoint', function() {
    return 'https://your-translator.example.com/get';
});
```

## Adding a new hand-curated language

1. Add the short → full locale mapping to `Slider_Translator::$locale_map` in `includes/class-slider-translator.php`.
2. Add the full locale → MyMemory ISO 639-1 mapping to `Slider_Translator::$translator_codes`.
3. Create `languages/widget-for-opio-reviews-<locale>.po`. Translatable string list is in `languages/widget-for-opio-reviews.pot` (14 UI strings total).
4. Compile to `.mo`:

   ```
   msgfmt languages/widget-for-opio-reviews-<locale>.po -o languages/widget-for-opio-reviews-<locale>.mo
   ```

5. Create `languages/widget-for-opio-reviews-<locale>-opio-slider-main-js.json` with the 7 JS lightbox strings. Use any existing `*-opio-slider-main-js.json` as a template.

No PHP code changes needed beyond steps 1 and 2.

## Limitations

- **Numbers** (rating values, review counts, date day/year digits) stay Western regardless of language.
- **moment.js month abbreviations** in dates stay English (the format string *ordering* is translatable, the abbreviations themselves would require additional moment locale files).
- **RTL layout** for Arabic / Persian / Urdu / Hebrew — text glyphs render RTL inside tiles automatically, but the surrounding flex layout stays LTR.
- **First cold-cache render** per language can be slow if there are many long reviews (one MyMemory call per chunk). Subsequent renders are instant. Set `opio_translation_email` to raise the rate limit if needed.
- **Multiple sliders on one page with different `lang` values** — lightbox label translations use a shared `window.opioSliderI18nActive` global; the last shortcode on the page wins for that surface. Review/comment content is still translated correctly per-tile.
