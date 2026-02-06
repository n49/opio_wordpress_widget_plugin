# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Be extremely concise in all interactions.

## Overview

WordPress plugin ("Widget for OPIO Reviews") for embedding OPIO review feeds and sliders on client websites. Displays reviews from the op.io aggregation service. Current version: **1.1.15**.

## Build & Development

**No build system exists.** No package.json, composer.json, webpack, gulp, or linting configs. Assets (JS/CSS) are edited directly.

**Testing:** No automated tests. Manual testing only via WordPress admin preview.

**Deployment:** GitHub Actions (`.github/workflows/main.yml`) deploys to WordPress.org SVN on git tag push using `10up/action-wordpress-plugin-deploy`. Slug: `widget-for-opio-reviews`.

**Version bump checklist** — update all three locations when releasing:
1. `opio.php` — `Version:` in plugin header comment
2. `opio.php` — `OPIO_PLUGIN_VERSION` constant
3. `readme.txt` — `Stable tag:`

**Debug mode:** Set WP option `opio_debug_mode` to `'1'` to load unminified assets from `assets/src/` instead of `assets/`.

## Architecture

### Initialization Flow

```
opio.php → autoloader.php (PSR-4 for WP_Opio_Reviews\Includes)
  → Plugin::register() on plugins_loaded
    → Assets, Post_Types (opio_feed, opio_slider CPTs)
    → Feed_Deserializer → Feed_Page, Builder_Page, Feed_Shortcode
    → Slider_Deserializer → Slider_Feed_Page, Review_Slider, Slider_Shortcode
    → Admin services (only when is_admin())
```

### Two Rendering Modes

1. **Feed** (`[opio_feed id='X']`) — Fetches pre-rendered HTML from `feed.op.io`, injects schema into `<head>` via output buffering. Handler: `class-feed-shortcode.php`.

2. **Slider** (`[opio_slider id='X']`) — Fetches JSON from `op.io/api/entities/reviews-slider`, renders locally via PHP templates. Three layout variants (horizontal, horizontal-carousel, vertical), each with public + admin-preview templates in `includes/`.

### Data Storage

Widget settings are stored as JSON in `post_content` of custom post types (`opio_feed`, `opio_slider`). Serializers validate and save form data; deserializers retrieve and process it (RGBA→hex conversion, `wp_kses` allowlist filtering).

### Asset Loading

`class-assets.php` registers all CSS/JS. Debug mode toggle (`opio_debug_mode` option) switches between `assets/` and `assets/src/` paths. Bundled vendor libs: Slick Carousel, Moment.js, jQuery fallback (if WP jQuery unavailable).

**Known issue:** `assets/css/public-main.css` and `public-main-rtl.css` are 0 bytes — styles only work in debug mode loading from `assets/src/css/`.

### REST API

Single endpoint: `GET /opioreviews/v1/get_businesses?orgID={id}` — proxies to `op.io/api/organizations/landingpageUsername`.

## API Endpoints

- Feed HTML: `https://feed.op.io/reviewFeed/{bizId}` or `/allReviewFeed/{bizId}`
- Slider JSON: `https://op.io/api/entities/reviews-slider`
- Images: `https://images.files.ca/{width}x{height}/{imageId}.jpg?nocrop=1`
- Videos: `https://videocdn.n49.ca/mp4sdpad480p/{videoId}.mp4`

## Media Handling

Supported: `rev.images[]` (imageId objects), `rev.videos[]` (videoId objects).
Not yet supported: `rev.embeds[]` (social media embeds).

JS lightbox/gallery functions exist in both `opio-main.js` (feeds) and `opio-slider-main.js` (sliders) with separate implementations.

## Conventions

- **Namespace:** `WP_Opio_Reviews\Includes` with PSR-4 autoloading
- **File naming:** `class-{name}.php` maps to `Class_Name`
- **Methods/functions:** `snake_case`
- **Security:** `wp_verify_nonce()` for forms, `sanitize_text_field()` for input, `wp_kses()` with allowlist for output, `esc_url()`/`esc_html()` for escaping
