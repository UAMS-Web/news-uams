# Writing a news post

<!-- cspell:ignore fullcontent pricat -->

Written against v1.4.1.

A news post is an ordinary WordPress post with a *Newsroom Features* box under the editor. This guide covers the fields in that box and what each one changes on the published post.

## Steps

1. **Write the post** in the editor as usual, and give it a featured image if it has one. On the single post page the featured image appears above the title, links to the full-size file, and shows its caption and, where the attachment has a media credit, `Image by <credit>`.
2. **Choose its categories.** The first category is the one the breadcrumb shows, unless the post has a primary category set (the `pricat_get_categories` meta). When the first category is the one with term id `13` (*Featured* on the site the code was written for), the breadcrumb shows the second category instead.
3. **Include the boilerplate** by ticking *Include Boilerplate*. The boilerplate set on [News Settings](News-Settings.md) is then added after the post's content where `content.php` renders it, and in the `fullcontent` REST field. `content-post.php` prints a fixed paragraph instead (see [Templates](Templates.md#content-postphp)).
4. **Attach the release PDF** in *News Release PDF* when the post is an official news release. The sidebar then shows a *View News Release* button linking to it.
5. **Pick the media contacts** in *Media Contact*. The sidebar of the single post shows the contact card for that group: each person's name, office and mobile numbers, and email.
6. **Publish.** The published date is printed in Associated Press style (`Sept. 4, 2026`) at the start of the first paragraph.

**Note:** *Media Contact* offers a fixed set of groups, and each group's card is written into `sidebar.php`. Adding a group or changing a person's details is a code change; see [Templates](Templates.md#sidebarphp).

## Reference

- [Post and category fields](Post-and-category-fields.md)
- [News Settings](News-Settings.md)
- [REST API fields](REST-API-fields.md)
