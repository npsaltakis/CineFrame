# CineFrame — Joomla Video Library (component + content plugin)

Reusable video system for **Joomla 5 & 6**. Manage a library of YouTube / Vimeo / raw-embed
videos in the admin and drop them into any article with a shortcode.

## Shortcode

```
{cineframe videoid=44 width=540}        ← from the library (recommended)
{cineframe url=https://youtu.be/xxxx}   ← direct URL (YouTube/Vimeo auto-detected)
{cineframe youtube=VIDEO_ID}
{cineframe vimeo=VIDEO_ID}
{cineframe embed=<iframe ...>}          ← raw embed
```

Legacy `{avsplayer videoid=N ...}` is also supported (drop-in for the old plugin).

## Contents

```
cineframe/
  com_cineframe/              → administrator/components/com_cineframe   (admin manager)
  plg_content_cineframe/      → plugins/content/cineframe                (shortcode renderer)
  media/plg_content_cineframe → media/plg_content_cineframe              (self-contained player CSS)
```

## Database

Table `#__cineframe_videos` (id, title, type, source, width, published, ordering, created, …).
Created automatically by the component installer (`com_cineframe/sql/install.mysql.utf8.sql`).

## Install options

**A. As-is (copy):** copy the three folders to the paths shown above, then register the
extensions (component + plugin) via Joomla's *Extensions → Discover*, or import the SQL +
add the `#__extensions` rows manually.

**B. Packaged (recommended for distribution):** zip each part and (optionally) wrap them in a
package manifest `pkg_cineframe.xml`, then install through *Extensions → Install*. The installer
creates the table, the admin menu item and the language entries on every J5/J6 site.

## Notes

- Namespaces: `Webtech\Component\Cineframe`, `Webtech\Plugin\Content\Cineframe`.
- The player CSS travels with the plugin (`media/plg_content_cineframe/css/cineframe.css`),
  so the embed is styled (responsive 16:9) on any site, independent of the template.
- Languages: en-GB + el-GR included.

© 2026 WebtechSolutions — GNU GPL v2 or later.
