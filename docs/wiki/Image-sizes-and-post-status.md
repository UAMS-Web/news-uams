# Image sizes and post status

<!-- cspell:ignore dashicon uams -->

Written against v1.4.2.

## Image sizes

Registered with `add_image_size()`, all hard-cropped. WordPress makes each size when an image is uploaded; images uploaded before a size existed need their thumbnails regenerated.

| Name | Width | Height | Ratio |
| --- | --- | --- | --- |
| `news-full` | 960 | 540 | 16:9 |
| `news-half` | 560 | 315 | 16:9 |
| `news-third` | 400 | 225 | 16:9 |
| `news-quarter` | 320 | 180 | 16:9 |
| `uams_news` | 560 | 350 | 16:10 |
| `aspect-16-9` | 1024 | 576 | 16:9 |
| `aspect-16-9-small` | 512 | 288 | 16:9 |

## The To Convert post status

The theme registers a `convert` status, shown as *To Convert* in the post lists. It is not public, so a post in it is never shown on the site, and it is left out of the *All* count. The `post_type` limit (posts and pages), the *To Be Converted* dropdown label, the dashicon and the `show_in_*_dropdown` flags are arguments the WP Statuses plugin reads; WordPress itself ignores them, so they take effect only where that plugin is active.
