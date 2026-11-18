# News Settings

<!-- cspell:ignore fullcontent UAMS uamswp -->

Written against v1.4.1.

*News Settings* is an admin page the theme adds through MB Settings Page. It holds site-wide values the templates read. Its values are stored in the `uamswp_news` option.

| Field | Field id | Type | Default | Effect |
| --- | --- | --- | --- | --- |
| Exclude Categories | `exclude_cat` | Category checkbox tree | none | Posts in these categories are left out of the home page's *More news*, *Announcements* and *College events* sections. |
| Boilerplate | `boilerplate_html` | Rich text, saved raw | empty | Printed after a post's content when the post has *Include Boilerplate* ticked, and added to the `fullcontent` REST field. |

## Example

To keep internal stories off the home page, create a category such as `Inside UAMS`, tick it under *Exclude Categories*, and save. A post in that category still shows on its own archive and in the REST API.

## How the templates read it

- `exclude_id_list()` returns the term ids ticked under *Exclude Categories*, or an empty list when Meta Box is not active.
- `get_uams_boilerplate()` returns the boilerplate HTML, or an empty string when Meta Box is not active.

**Note:** `content-post.php` prints a fixed boilerplate paragraph of its own rather than this setting. Changing the setting does not change what that template shows; see [Templates](Templates.md#content-postphp).
