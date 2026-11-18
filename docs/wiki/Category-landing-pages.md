# Category landing pages

Written against v1.4.1.

Six categories have a template of their own, chosen by WordPress from the file name `category-<slug>.php`. Every other category uses the parent theme's archive. A post appears on a landing page through its categories alone, so placing a story is a matter of ticking the right ones.

## The pages

| Category slug | Template | What it shows |
| --- | --- | --- |
| `education-research-feature` | `category-education-research-feature.php` | Two featured stories, then up to ten more |
| `health` | `category-health.php` | Two featured stories, then up to ten more |
| `heres-to-your-health` | `category-heres-to-your-health.php` | A header image from the category, two featured stories, then up to ten more |
| `news-release` | `category-news-release.php` | Every news release, newest first, grouped by year and month |
| `patient-stories` | `category-patient-stories.php` | Two featured stories, then up to ten more |
| `university` | `category-university.php` | Two featured stories, then up to ten more |

## Getting a story into the featured slot

1. Tick the landing page's category on the post.
2. Also tick *Featured*. The top of each landing page queries posts in both categories (`featured+<slug>`), so a post in only one of them does not appear there.

**Note:** *Here's to Your Health* takes its featured stories from `featured+health`, not from its own slug. A story for its top slot needs *Featured* and *Health*.

## Setting the header image on Here's to Your Health

1. *Posts*, *Categories*, then edit *Here's to Your Health*.
2. Set *Featured Image* in the *Custom Fields* box (the `cat_image` term field, from MB Term Meta).

## The home page

`front-page.php` builds the home page from five queries, each leaving out posts an earlier section already showed:

1. **Featured:** seven posts in *Featured*, one large and six small.
2. **Spotlight:** three posts from a category the template names by its numeric id.
3. **More news:** eight recent posts, leaving out the categories on [News Settings](News-Settings.md) and four more that the template names by id.
4. **Announcements:** ten posts in `announcements`, with the same kind of exclusions.
5. **College events:** five posts in `academic-continuing-education-events`, leaving out the categories on [News Settings](News-Settings.md).

**Warning:** the category ids written into `front-page.php` belong to one site's database. On a site whose categories were created in another order they point at other categories, or at none.

## Reference

- [Templates](Templates.md)
- [Post and category fields](Post-and-category-fields.md)
