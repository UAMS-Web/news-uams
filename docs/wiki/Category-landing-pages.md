# Category landing pages

Written against v1.4.1.

Six categories have a template of their own, chosen by WordPress from the file name `category-<slug>.php`. Every other category uses the parent theme's archive. A post appears on a landing page through its categories alone, so placing a story is a matter of ticking the right ones.

## The pages

| Category slug | Template | What it shows |
| --- | --- | --- |
| `education-research-feature` | `category-education-research-feature.php` | Two featured stories on the first page, then ten posts per page |
| `health` | `category-health.php` | Two featured stories on the first page, then ten posts per page |
| `heres-to-your-health` | `category-heres-to-your-health.php` | A header image from the category, the category description, then ten posts per page under *Latest Episodes* |
| `news-release` | `category-news-release.php` | Every news release, newest first, grouped by year and month |
| `patient-stories` | `category-patient-stories.php` | Two featured stories on the first page, then ten posts per page |
| `university` | `category-university.php` | Two featured stories on the first page, then ten posts per page |

## Getting a story into the featured slot

On the Education Research Feature, Health, Patient Stories and University pages:

1. Tick the landing page's category on the post.
2. Also tick *Featured*. The top of each of those pages queries posts in both categories (`featured+<slug>`), so a post in only one of them does not appear there.

**Note:** *Here's to Your Health* has no visible featured slot. Its template still runs a featured query, but the markup that would print it is inside an HTML comment, so it never shows.

## Setting the header image on Here's to Your Health

1. *Posts*, *Categories*, then edit *Here's to Your Health*.
2. Set *Featured Image* in the *Custom Fields* box (the `cat_image` term field, from MB Term Meta). Each image set there is shown as a header; with none set, the page falls back to the site's header image.

## The home page

`front-page.php` builds the home page in five sections. Sections two to four each leave out posts an earlier section already showed:

1. **Featured:** seven posts in *Featured*, one large and six small.
2. **University News** (the `spotlight` section): three posts from a category the template names by its numeric id.
3. **More Stories:** eight recent posts, leaving out the categories on [News Settings](News-Settings.md) and four more that the template names by id.
4. **Announcements:** ten posts in `announcements`, leaving out the categories on News Settings and two more named by id.
5. **Academic & Continuing Education Events:** an events list from the `[events_list]` shortcode, for a category named by id. The template also runs a five-post query for `academic-continuing-education-events`, but prints it inside an HTML comment.

**Warning:** the category ids written into `front-page.php`, and the id `13` the breadcrumb and card labels skip as *Featured*, belong to one site's database. On a site whose categories were created in another order they point at other categories, or at none.

## Reference

- [Templates](Templates.md)
- [Post and category fields](Post-and-category-fields.md)
