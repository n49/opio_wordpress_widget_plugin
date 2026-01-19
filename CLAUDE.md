# CLAUDE.md

Be extremely concise in all interactions.

## Overview

WordPress plugin for embedding OPIO review feeds and sliders on client websites. Displays reviews from op.io aggregation service.

**Plugin Name:** Widget for OPIO Reviews
**Version:** 1.1.11

## Architecture

### Two Rendering Modes

1. **Feed Shortcode** (`[opio_feed id='X']`)
   - Fetches pre-rendered HTML from `feed.op.io`
   - No local review rendering - just displays server HTML
   - File: `includes/class-feed-shortcode.php`

2. **Slider Widgets** (review carousels)
   - Fetches JSON from `op.io/api/entities/reviews-slider`
   - Renders reviews locally in PHP templates
   - 6 template files (3 public + 3 admin previews):
     - `reviews-slider-horizontal-template.php`
     - `reviews-slider-horizontal-carousel-template.php`
     - `reviews-slider-vertical-template.php`
     - `admin-reviews-slider-*` (admin preview versions)

### Key Files

- `opio.php` - Plugin entry point
- `includes/class-plugin.php` - Plugin initialization
- `includes/class-feed-shortcode.php` - Feed shortcode handler
- `includes/class-review-slider.php` - Slider widget handler
- `includes/class-opio-handler.php` - API communication
- `assets/js/opio-main.js` - Feed JS functions
- `assets/js/opio-slider-main.js` - Slider JS functions (lightbox, slick carousel)

### External Dependencies

- Slick Carousel (bundled)
- Moment.js (bundled)
- jQuery (WordPress)

## API Endpoints

- Feed HTML: `https://feed.op.io/reviewFeed/{bizId}` or `/allReviewFeed/{bizId}`
- Slider JSON: `https://op.io/api/entities/reviews-slider`
- Images: `https://images.files.ca/{width}x{height}/{imageId}.jpg?nocrop=1`
- Videos: `https://videocdn.n49.ca/mp4sdpad480p/{videoId}.mp4`

## Media Handling

Current support:
- `rev.images[]` - array of `{imageId}` objects
- `rev.videos[]` - array of `{videoId}` objects

NOT yet supported:
- `rev.embeds[]` - social media embeds (YouTube, etc.)

### JS Functions

In `opio-main.js` (feeds):
- `displayLargeImage(imageId, revId)` - shows large image (has bugs: fixed height, cover mode)
- `addReview()` - builds review HTML dynamically

In `opio-slider-main.js` (sliders):
- `displayLargeImage(imageId, revId)` - different implementation for lightbox
- `openPhotoLightbox(reviewData)` - opens review detail modal
- `hideLargeImage(revId)` - closes large image view

## Shortcodes

```php
[opio_feed id='123']     // Review feed
[opio_slider id='456']   // Review slider
```

## Testing

No automated tests. Manual testing via WordPress admin preview.

## Deployment

Plugin distributed via WordPress plugin directory or direct ZIP upload.
