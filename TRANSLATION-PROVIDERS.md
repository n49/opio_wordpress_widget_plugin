# Translation Provider Analysis

Reference for future scaling decisions on the slider's review-content translation pipeline. As of v1.1.18 the plugin uses **MyMemory** for review/comment/JSON-LD translation. This document records the trade-offs, projected costs at scale, and what it would take to swap providers.

## Current implementation

**Provider:** MyMemory (`api.mymemory.translated.net/get`)
**Auth:** None (anonymous) or `?de=<email>` for higher quota
**Filters:** `opio_translation_endpoint`, `opio_translation_email`

| Limit | Value |
|---|---|
| Free quota (no email) | 5,000 chars / day / IP |
| Free quota (with email) | 50,000 chars / day / IP |
| Per-request char cap | 500 (we chunk at ≤450) |
| Language coverage | All ISO 639-1 codes (✓ all 30 hand-curated + permissive fallback) |
| Per-IP shared bucket | Yes — multiple WP sites on same server share the quota |
| SLA | None (public service) |
| Quality | Mixed: TM lookups (sometimes excellent) + MT fallback (variable) |

**Built-in safeguards already in place:**
- Per-chunk transient cache (`opio_tr2_c_<md5>`) — translation runs once per (content, language) tuple, then never again
- Graceful degradation: returns original text on API failure or rate-limit warning; never breaks the page
- MyMemory error patterns (`QUERY LENGTH LIMIT EXCEEDED`, `MYMEMORY WARNING`, `INVALID LANGUAGE PAIR`) detected and not cached as "translations"
- Lazy translation on first cold-cache render only

## Scalability ceilings

Assumes ~5,600 chars per locale per slider on cold cache (8 reviews × 500 chars + 8 comments × 200 chars).

| Site profile | Cold-cache load | Monthly recurring | MyMemory fit? |
|---|---|---|---|
| 1 slider × 5 langs | ~28 K | ~17 K | ✓ free tier |
| 1 slider × 30 langs | ~168 K | ~105 K | ✓ with email |
| **10 sliders × 30 langs** | **~1.68 M** | **~1 M** | Tight — pre-warming needs ~21 days at 50 K/day |
| 100 sliders × 30 langs | ~16.8 M | ~10.5 M | ✗ exceeds — needs paid provider |

**Real choke point:** first-render latency, not monthly volume. ~10 s typical first-render for 1 slider × 1 language can blow past PHP's default `max_execution_time` of 30 s under load.

## Paid alternatives — 2026 pricing

| Service | Free tier | Paid rate | Covers all 30 langs? |
|---|---|---|---|
| **Microsoft Azure Translator** | **2 M chars/mo forever** | **$10 / 1 M chars** | ✓ |
| AWS Translate | 2 M chars/mo × 12 months | $15 / 1 M | ✓ |
| Google Cloud Translation (NMT v2/v3) | 500 K/mo × 12 months | $20 / 1 M | ✓ |
| Google Cloud Translation (LLM mode) | Same | $10 in + $10 out per 1 M | ✓ |
| DeepL Pro | None | $5.49 base + $25 / 1 M | ✗ — no pa, ta, ur, hk, fa, tl |
| DeepL Free | 500 K/mo forever | — | ✗ — same gap |
| LibreTranslate (self-hosted) | Free (server ~$5–20/mo) | — | ✓ (depending on models loaded) |

### Monthly cost projection at 10 M chars/month

| Provider | Estimated monthly cost |
|---|---|
| Azure Translator | ~$80 (2 M free + 8 M × $10) |
| AWS Translate (post-free-year) | ~$120 |
| Google Cloud Translation NMT | ~$200 |
| DeepL Pro | ~$255 — *and* missing 6 of client's languages |

## Quality notes

For the typical OPIO client locale set (heavy on Arabic, Persian, Punjabi, Tamil, Urdu, Cantonese, Tagalog — exactly the languages DeepL doesn't cover):

- **Best quality + coverage:** Google Cloud Translation (the new LLM mode is excellent)
- **Best value:** Azure Translator (2 M free forever, $10/M, covers everything)
- **Best for European-only sites:** DeepL Pro (highest fluency for fr/es/de/it/nl/pt) — but wrong fit for our standard locale mix
- **Current default:** MyMemory — adequate for English-source business reviews, variable for nuanced or technical content

## Switching providers (v1.1.25+)

`Slider_Translator::translate_chunk()` dispatches to a per-provider implementation via the `opio_translation_provider` filter. Currently shipped: `mymemory` (default) and `azure`. Adding `google` or `deepl` later means a new `translate_chunk_<provider>()` method plus a switch arm — no architectural change.

### Activate Azure on a single site

Drop this into the site's `functions.php` (or an mu-plugin):

```php
add_filter('opio_translation_provider', function() { return 'azure'; });
add_filter('opio_translation_azure_key', function() { return 'xxxxxxxx-your-subscription-key-xxxxxxxx'; });
// Required for *regional* resources (most are). Omit only for the global endpoint.
add_filter('opio_translation_azure_region', function() { return 'eastus'; });
```

To use a custom Azure endpoint (e.g., for a private link or custom subdomain):

```php
add_filter('opio_translation_azure_endpoint', function() {
    return 'https://my-translator.cognitiveservices.azure.com/translate';
});
```

### Provisioning the Azure side

1. Azure Portal → Create resource → "Translator" → pick a region → Free F0 tier (2M chars/month forever).
2. Resource → Keys and Endpoint → copy KEY 1 + Location (region).
3. Paste both into the filters above.

### Behavior details

- Bad / missing key returns HTTP 401/403 → trips the 1-hour circuit breaker so the slider doesn't hammer Azure 23 times per render with bad creds. Bump the plugin version (or delete the `opio_translation_rate_limited` transient) after fixing the key to re-test immediately.
- HTTP 429 / 503 → also trips the breaker. Self-recovers after 60 minutes.
- Language code differences are handled internally: `zh` → `zh-Hans` (Mandarin Simplified), `zh-TW` → `zh-Hant` (Traditional Chinese / written Cantonese), `tl` → `fil` (Filipino).
- Stats log gains a `provider` field showing which backend was used (`mymemory` or `azure`).
- Transient cache is provider-agnostic by design — switching providers doesn't invalidate cached translations. If you want fresh translations from a new provider, bump the version or change the cache prefix.

## Recommendation

- **Default for the WP.org install base: MyMemory.** Zero config, $0 cost, Just Works for small sites.
- **High-volume clients: Azure.** Activate via the three-line `functions.php` filter on a per-site basis. Permanent 2M chars/month free tier; client's egress IP doesn't share quota with random other WP installs on the same host.
- **Skip DeepL** for OPIO's locale mix — the 6 unsupported languages (Punjabi, Tamil, Urdu, Cantonese, Persian, Tagalog) are exactly the ones our clients use.

## Sources

- [DeepL API plans](https://support.deepl.com/hc/en-us/articles/360021200939-DeepL-API-plans)
- [Google Cloud Translation pricing](https://cloud.google.com/translate/pricing)
- [Azure Translator pricing](https://azure.microsoft.com/en-us/pricing/details/translator/)
- [Translation API price comparison 2026 — Langbly](https://langbly.com/blog/translation-api-pricing-comparison/)
- [Best Translation APIs for Developers 2026 — Adara](https://www.adaratranslate.com/blog/best-translation-api-for-developers)
