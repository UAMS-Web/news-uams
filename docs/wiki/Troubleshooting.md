# Troubleshooting

<!-- cspell:ignore fullcontent multisite -->

Written against v1.4.2.

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

**Cause:** the top slot of the Education Research Feature, Health, Patient Stories and University pages queries posts in *Featured* and the page's category together. A post in only one of them is not picked. *Here's to Your Health* and *News Release* have no visible top slot.

**Fix:** tick both; see [Category landing pages](Category-landing-pages.md#getting-a-story-into-the-featured-slot).

## The home page shows the wrong stories in a section

**Cause:** the *University News* section, the exclusions on *More Stories* and *Announcements*, and the events shortcode's category name categories by numeric id in `front-page.php`, and the breadcrumb and card labels skip term id `13` as *Featured* (`functions.php`). Those ids are only right on the database they were written for, and go wrong after a site is rebuilt or imported.

**Fix:** find the intended categories' ids under *Posts*, *Categories* (the `tag_ID` in each edit link) and change them in `front-page.php` through a pull request. For a category that should simply stay off the home page, use *Exclude Categories* on [News Settings](News-Settings.md) instead, which needs no code change.

## The boilerplate is missing or outdated on a post

**Cause:** the post does not have *Include Boilerplate* ticked, or the *Boilerplate* field on News Settings is empty or out of date.

**Fix:** tick *Include Boilerplate* on the post, and check the text on [News Settings](News-Settings.md). A consumer of the `fullcontent` REST field picks up the change on its next read.

## A new image size is blank on older posts

**Cause:** WordPress makes the sizes in [Image sizes and post status](Image-sizes-and-post-status.md) only when an image is uploaded.

**Fix:** regenerate thumbnails for the older images, for example with WP-CLI: `wp media regenerate --only-missing`.
