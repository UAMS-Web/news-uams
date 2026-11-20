# Templates

<!-- cspell:ignore thinstrip uams -->

Written against v1.4.2.

The theme overrides a handful of the parent theme's templates. Anything not listed here comes from `uams-2016`.

## `front-page.php`

The site's home page: the *Featured*, *University News*, *More Stories*, *Announcements* and *Academic & Continuing Education Events* sections. The sections and their queries are listed in [Category landing pages](Category-landing-pages.md#the-home-page).

## `category-<slug>.php`

One file per landing page: `education-research-feature`, `health`, `heres-to-your-health`, `news-release`, `patient-stories` and `university`. See [Category landing pages](Category-landing-pages.md#the-pages) for what each shows.

## `content.php`

The body of a post or page. The parent theme has no `single.php`, so a single post goes through its `index.php`, which loads `content.php` for a standard post.

- On a single post: the featured image with its caption and media credit, the title, the byline when the `show_byline_on_posts` option is set (through Co-Authors Plus when it is active), the content, and the boilerplate from [News Settings](News-Settings.md) when *Include Boilerplate* is ticked.
- On the blog page: a 130 by 130 thumbnail and the excerpt. Other archives use the parent theme's templates; its `archive.php` loads `content-archive.php`, not this file.

## `content-post.php`

Kept in the theme, but nothing loads it: `post` is not a post format, so the parent theme never asks for this file. It holds the title, a mobile sidebar menu (its button shows the title cut to 27 characters at a space), the content, and, when *Include Boilerplate* is ticked, a fixed boilerplate paragraph written into the template rather than the setting.

## `sidebar.php`

Shown when the parent theme's `uams_has_sidebar()` is true:

1. The parent theme's sidebar menu.
2. On a single post with a release PDF, the *View News Release* button.
3. The contact card for the post's *Media Contact* group. Each group's names, numbers and email addresses are written into this file, one `case` per group, so a change to a contact is a code change and a pull request.
4. The parent theme's sidebar widget area.

## `thinstrip.php`

The thin strip across the top of every page: the institution link, the site title, the audience links, and the search and quick links buttons.

## `functions.php`

Registers the theme's fields, settings page, admin notice, admin column, image sizes, post status and REST fields from `uams_news_register()`, which runs once as WordPress loads the file. It also holds the helpers the templates call:

| Function | Returns |
| --- | --- |
| `get_uams_breadcrumbs()` | The breadcrumb trail for the current view |
| `uams_primary_post_category()` | A link to the post's primary category, or to its first category |
| `ap_date($date)` | A date in Associated Press style, such as `Sept. 4, 2026` |
| `text_cut($text, $length, $dots)` | Text cut at a space to at most `$length` characters |
| `exclude_id_list()` | The term ids from *Exclude Categories* |
| `get_uams_boilerplate()` | The boilerplate HTML from News Settings |
