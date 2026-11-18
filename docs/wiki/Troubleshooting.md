# Troubleshooting

<!-- cspell:ignore multisite -->

Written against v1.4.1.

- [The plugin notice will not go away](#the-plugin-notice-will-not-go-away)
- [There is no News Settings menu](#there-is-no-news-settings-menu)
- [A story does not appear in a landing page's top slot](#a-story-does-not-appear-in-a-landing-pages-top-slot)
- [The home page shows the wrong stories in a section](#the-home-page-shows-the-wrong-stories-in-a-section)
- [The boilerplate is missing or outdated on a post](#the-boilerplate-is-missing-or-outdated-on-a-post)
- [A new image size is blank on older posts](#a-new-image-size-is-blank-on-older-posts)

## The plugin notice will not go away

An administrator sees `The Olympian needs these plugins to work:` at the top of every admin page.

**Cause:** one of Meta Box, MB Settings Page or MB Term Meta is not active. The notice lists each missing one and whether it is installed.

**Fix:** follow the link beside each entry. *Activate* turns on an installed plugin. Meta Box installs from wordpress.org. The two extensions are not on wordpress.org: download each from metabox.io, then upload it under *Plugins*, *Add New*, *Upload Plugin*. On a multisite network the links go to the network admin.

## There is no News Settings menu

**Cause:** MB Settings Page is not active, so the settings page is never registered. The plugin notice above says so.

**Fix:** activate MB Settings Page.

## A story does not appear in a landing page's top slot

**Cause:** the top slot of each landing page queries posts in *Featured* and the page's category together. A post in only one of them is not picked.

**Fix:** tick both. For *Here's to Your Health*, tick *Featured* and *Health*; see [Category landing pages](Category-landing-pages.md#getting-a-story-into-the-featured-slot).

## The home page shows the wrong stories in a section

**Cause:** the *Spotlight* section, and the exclusions on *More news* and *Announcements*, name categories by numeric id in `front-page.php`. Those ids are only right on the database they were written for, for example after a site is rebuilt or imported.

**Fix:** find the intended categories' ids under *Posts*, *Categories* (the `tag_ID` in each edit link) and change them in `front-page.php` through a pull request. For a category that should simply stay off the home page, use *Exclude Categories* on [News Settings](News-Settings.md) instead, which needs no code change.

## The boilerplate is missing or outdated on a post

**Cause:** either the post does not have *Include Boilerplate* ticked, or it is rendered by `content-post.php`, which prints its own fixed paragraph and ignores the setting.

**Fix:** tick *Include Boilerplate*. If the text shown is the fixed paragraph, it changes only in `content-post.php`; see [Templates](Templates.md#content-postphp).

## A new image size is blank on older posts

**Cause:** WordPress makes the sizes in [Image sizes and post status](Image-sizes-and-post-status.md) only when an image is uploaded.

**Fix:** regenerate thumbnails for the older images, for example with WP-CLI: `wp media regenerate --only-missing`.
