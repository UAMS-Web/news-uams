# REST API fields

<!-- cspell:ignore entrydate fullcontent uamswp -->

Written against v1.4.1.

The theme adds three fields to every post in the core posts endpoint, `/wp-json/wp/v2/posts`. Other sites read them to syndicate news. The endpoint answers anonymous requests, so everything in these fields is public.

| Field | Type | Contents |
| --- | --- | --- |
| `fullcontent` | string | The rendered content, with the AP-style date inserted at the start of the first paragraph, and the boilerplate from [News Settings](News-Settings.md) appended when the post has *Include Boilerplate* ticked |
| `post-meta-fields` | object | The post's values for an allowlist of meta keys, each as an array of values; a key with no value is left out |
| `post_categories` | array | The post's category terms, as `get_the_terms()` returns them |

## The meta allowlist

`post-meta-fields` returns only these keys, listed in `uamswp_api_post_meta_keys()`:

- `include_boilerplate`
- `media_contact`
- `news_release_pdf_url`
- `show_image_single`

**Warning:** add a key to the list only when a consumer reads it, and never a protected key (one starting with `_`). The list is the whole public surface of the field.

## Example

```sh
curl -s 'https://news.example.org/wp-json/wp/v2/posts?per_page=1&_fields=id,fullcontent,post-meta-fields,post_categories'
```

```json
[
  {
    "id": 123,
    "fullcontent": "<p><span class=\"entrydate\"><time datetime=\"2026-09-04T09:00:00\" itemprop=\"datePublished\">Sept. 4, 2026</time></span> | First paragraph...</p>",
    "post-meta-fields": {
      "include_boilerplate": ["1"],
      "media_contact": ["Media <Surname>-<Surname>"]
    },
    "post_categories": [
      { "term_id": 45, "name": "Health", "slug": "health", "taxonomy": "category" }
    ]
  }
]
```

The host and every value above are placeholders.
