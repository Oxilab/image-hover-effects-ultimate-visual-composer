# Docs: writing, publishing and SEO

Flipbox docs live on the live site `oxilab.dev` as the `docs` post type, under
`https://oxilab.dev/docs/flipbox/<section>/<slug>/`. The method comes from the 34 Shop Manager docs and their SEO
pass (2026-07 to 2026-09), where Richard set every rule below.

## Workflow (strict, from Richard)

1. **Understand** the feature from the code and the real UI ([00-general.md](00-general.md) rule 3). Stop and report.
2. **Plan**: the list of docs (or the one doc) with section, title, focus keyword. Stop for approval.
3. **One doc at a time**: show the brief (title, slug, focus keyword, SEO title, meta description, outline,
   screenshots planned) and wait for a literal "YES".
4. Write the full doc, with screenshots ([screenshots-docs.md](screenshots-docs.md)).
5. **Publish only when Richard says so.** Then set the SEO fields and verify the live page.

Never skip ahead or batch several docs without his go.

## Audience and voice

- Beginners: site owners, not developers. Plain English, short paragraphs (under 120 words), numbered steps for
  anything done in order, the exact menu path and button labels as they appear on screen ("Use this design",
  "Create and edit", "Add a flip box", "Save changes", "Add to Create New").
- Every claim verified in the code or the UI. Every screenshot matches its step.
- No dashes ([00-general.md](00-general.md) rule 4). Pro features are labelled "(Pro)" in the doc.

## House HTML (Gutenberg block markup, from the live docs)

```
<p class="wp-block-paragraph">Intro with the <strong>focus keyword</strong> early.</p>
<h2 class="wp-block-heading">Step 1: ...</h2>
<ol class="wp-block-list"><li>...</li></ol>
<figure class="wp-block-image size-large"><img src="https://cdn.oxilab.dev/flipbox/docs/..." alt="..."/></figure>
<blockquote class="wp-block-quote is-layout-flow wp-block-quote-is-layout-flow"><p><strong>Tip:</strong> ...</p></blockquote>
```

The table of contents and the image lightbox are added by the theme (`themes/oxilab/inc/oxilab-docs/`); don't paste a
TOC into the content.

## Flipbox docs now (live, checked 2026-10-16)

Category ids (`docs_category`): parent **FlipBox = 10**; Getting Started = 20, Creating & Managing = 21,
Customization & Styling = 22, Animations & Effects = 23, Page Builder Integration = 24, Settings & Configuration = 25,
Troubleshooting & FAQ = 26. Assign a doc to the parent **and** its section, for example `[10, 22]`.

| Section (slug) | Docs (slug, id where known) |
| --- | --- |
| getting-started | what-is-flipbox-introduction-features, how-to-install-activate-flipbox-free, quick-start-create-your-first-flip-box-in-5-minutes (335), system-requirements-for-flipbox-plugin |
| creating-managing | how-to-create-a-new-flip-box, understanding-the-flipbox-editor, adding-content-to-the-front-face, adding-content-to-the-back-face (340), using-images-in-your-flip-box (341), using-icons-in-your-flip-box (342), adding-buttons-and-links-to-your-flip-box (343), how-to-delete-or-deactivate-a-flip-box (344) |
| customization-styling | overview-of-the-29-flip-box-layout-styles (345), customizing-colors-in-your-flip-box (346), typography-settings-fonts-size-style (347), spacing-sizing-width-height-padding-margin (348), box-shadow-settings (349), button-styling-options (350), adding-custom-css-to-your-flip-box (351) |
| animations-effects | overview-of-flip-box-animation-effects (352), how-to-set-an-animation-effect-on-your-flip-box (353), complete-list-of-available-flip-box-animations (354) |
| page-builder-integration | how-to-use-flipbox-with-elementor (355), how-to-use-flipbox-with-wpbakery-visual-composer (356), how-to-use-flipbox-as-a-wordpress-widget (357), using-the-flipbox-shortcode-in-posts-pages (358) |
| settings-configuration | plugin-settings-overview (359), managing-user-permissions-for-flipbox (360), font-awesome-google-fonts-settings (361) |
| troubleshooting-faq | flip-box-not-showing-on-frontend-troubleshooting (362), flip-box-animation-not-working-troubleshooting (363), flip-box-looks-wrong-on-mobile-troubleshooting (364), frequently-asked-questions-flipbox-plugin (365) |

33 docs, written 2026-03 with H2 steps and **no screenshots**. They describe the interface before 3.1.0 (the old
Create New and Import pages, page reloads in the editor, the old Settings page). Rewriting them means checking every
step and adding screenshots.

Plugin features with no doc of their own: the **Flipbox block** for the block editor (3.1.0; the "How to use?" menu
has no block entry because no guide exists), **Flip Trigger** (hover or click, 3.1.0), the template library (Import
Templates, "Add to Create New", "Remove from list"), import and export of a flip box, item Clone, the Settings License
card and Danger zone, the Custom CSS code editor, Divi (the shortcode works there).

**Links into these docs from the plugin.** `Classes/Docs.php` (the "How to use?" menu) links to exact doc URLs
(quick start, shortcode, Elementor, WPBakery, widget). Never change the slug of a linked doc; if it must change, add a
redirect and update the plugin in the next release.

Find a doc's id: the live page's body class `postid-<id>`. `get_docs` returns only the newest 100 docs, so use
`get_doc` with the id. The authoritative URL list is `https://oxilab.dev/docs-sitemap.xml`.

## Tools: the `wordpress` MCP only

Use the `mcp__wordpress__*` tools (server `/Users/richard/Local Sites/oxilab/oxilab-mcp-server`), never node or curl
scripts that write.

| Tool | Use |
| --- | --- |
| `get_docs_categories` | Category ids |
| `get_doc` | One doc with raw content (needed before editing) |
| `create_doc` / `update_doc` | Content, title, status, slug, categories |
| `update_seo` | Rank Math focus keyword, SEO title, meta description, canonical, robots, slug (through `rankmath/v1/updateMeta`) |
| (no upload tool) | Doc images go to Cloudflare R2, which Richard uploads ([screenshots-docs.md](screenshots-docs.md)) |

Blog posts use the post tools of the same server: [blog-posts.md](blog-posts.md).

Gotchas: the MCP sometimes answers "fetch failed"; retry, then check no duplicate was created. Write internal links
absolute and check each slug in the sitemap first.

The **Rank Math MCP** (`rankmath`) has write access to the live site's SEO settings, schema, redirects and sitemap.
Start with read-only audits; show the diff and get Richard's go before any write. It is OAuth: if it needs
authorizing, Richard does it in an interactive `/mcp` session.

## SEO recipe per doc (scores land around 80)

1. Focus keyword: chosen with measured data ([keyword-research.md](keyword-research.md)) and checked in the keyword
   map, 2 or 3 words the doc uses naturally. Examples: "flip box", "flip box Elementor", "flip card".
2. SEO title up to 60 characters, keyword first; a number and a power word only where both are true of the doc.
3. Meta description up to 158 characters, keyword early.
4. Keyword in 3 to 5 of the H2s, only as a pure rename; never reword a step or a fact for SEO.
5. Keyword in at least one image alt that describes the screenshot honestly.
6. 5 or more internal links (to other Flipbox docs, checked in the sitemap) and 1 external link to a real authority.
7. Density just over 1.0% (Rank Math's best band is 1.0 to 2.5%). Length bands: 600 to 999 words = 2, 1000 to
   1499 = 3, 1500 to 1999 = 4; don't pad.
8. `update_doc` then `update_seo`. The score only updates when the doc is opened and saved in the editor; never
   write a score through the API.

Tests you leave alone, and say so: permalink length (`https://oxilab.dev/docs/flipbox/` is 32 characters plus a
section of 12 to 26), the table of contents (built by the theme), Content AI (Rank Math Pro).

Never raise a score by writing to what the score measures ([00-general.md](00-general.md) rule 11).
