# Installation

<!-- cspell:ignore uams -->

Written against v1.4.2.

At the end of this page the theme is active on a WordPress site, with every plugin it reads from in place and no warning on the dashboard.

## Requirements

| Requirement | Version or source | Why |
| --- | --- | --- |
| WordPress | 6.8 or later (`Requires at least` in `style.css` since v1.4.2) | The code is analyzed by PHPStan against the WordPress 6.8 stubs. |
| PHP | 8.2 or later (`Requires PHP` in `style.css`) | Every shipped PHP file is linted under 8.2 in CI. |
| Parent theme | `uams-2016` (`Template` in `style.css`) | The header, footer, menus and sidebar functions come from the parent. |
| Meta Box | [wordpress.org/plugins/meta-box](https://wordpress.org/plugins/meta-box/) | Every custom field and the settings page are Meta Box fields. |
| MB Settings Page | [metabox.io/plugins/mb-settings-page](https://metabox.io/plugins/mb-settings-page/) | Supplies the [News Settings](News-Settings.md) page. Not on wordpress.org. |
| MB Term Meta | [metabox.io/plugins/mb-term-meta](https://metabox.io/plugins/mb-term-meta/) | Supplies the category image field. Not on wordpress.org. |

**Note:** the sidebar reads the release PDF with ACF's `get_field()`. The parent theme bundles ACF Pro and loads it when no ACF plugin is active, so no separate install is needed.

## Steps

1. **Install the parent theme.** Put `uams-2016` in `wp-content/themes/`. It does not need to be active.
2. **Install the theme.** Download the repository's release archive and unzip it as `wp-content/themes/news-uams/`. The archive holds only the files the theme runs (the keep-list in `.gitattributes`), so it carries no tests or tooling.
3. **Activate it.** *Appearance*, *Themes*, then *Activate* on The Olympian.
4. **Install the plugins.** If any of the three Meta Box plugins is missing, an administrator sees a red notice on every admin page listing it, with an *Install*, *Activate* or *Download from metabox.io* link for each. Follow the links until the notice is gone. See [The plugin notice will not go away](Troubleshooting.md#the-plugin-notice-will-not-go-away) if it stays.
5. **Fill in the settings.** Open *News Settings* in the admin menu and set the two fields described in [News Settings](News-Settings.md).
6. **Create the categories the templates name.** The category pages look categories up by slug. The home page uses the slugs `featured` and `announcements` and several numeric category ids; see [Category landing pages](Category-landing-pages.md) for both.

## Next

- [Writing a news post](Writing-a-news-post.md)
- [Category landing pages](Category-landing-pages.md)
