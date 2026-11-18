# Post and category fields

<!-- cspell:ignore pricat rwmb -->

Written against v1.4.1.

Every field below is registered on the `rwmb_meta_boxes` filter in `functions.php` and stored as post or term meta under its field id. Field ids are meta keys, so a field renamed in code loses the values already saved under the old id.

## Newsroom Features (posts)

| Field | Meta key | Type | Default | Effect |
| --- | --- | --- | --- | --- |
| Include Boilerplate | `include_boilerplate` | Checkbox | not ticked | Adds the boilerplate after the content. |
| News Release PDF | `news_release_pdf_url` | File input (URL) | empty | Shows a *View News Release* button in the sidebar of the single post. |
| Media Contact | `media_contact` | Select | none | Shows the contact card for the chosen group in the sidebar. The options are pairs of staff surnames, for example `Media <Surname>-<Surname>`. |

## Custom Fields (categories)

| Field | Meta key | Type | Effect |
| --- | --- | --- | --- |
| Featured Image | `cat_image` | Image | The header image on the *Here's to Your Health* landing page. Needs MB Term Meta. |

## Meta the theme reads but does not register

| Meta key | Where it comes from | Read by |
| --- | --- | --- |
| `pricat_get_categories` | A primary-category plugin | The breadcrumb, which links the primary category when one is set |
| `show_image_single` | Another plugin or an import | `content.php` compares it with `false`, which a single-value read inside the loop never returns, so in practice the featured image shows on every single post that has one |
| `_media_credit` | A media credit plugin, on the attachment | `content.php`, as `Image by <credit>` under the featured image |

## Admin columns

The posts list gets a *Featured Image* column, second from the left, showing each post's thumbnail at the `thimble` size.
