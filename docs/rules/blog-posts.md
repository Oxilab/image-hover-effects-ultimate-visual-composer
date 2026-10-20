# Blog posts (SEO, AEO and GEO)

Rules for Flipbox blog posts. Richard set them on 2026-10-15 for Product Layouts, and they apply
to every Oxilab plugin: every post opens with a hook, is SEO, AEO and GEO friendly, scores **Rank Math 80+ honestly**, reads 100% like a person wrote it, carries tags for its keywords, and has
a featured image plus images inside the post made for its topic. Read [00-general.md](00-general.md) first; the
Rank Math facts in [docs-publishing-seo.md](docs-publishing-seo.md) apply here too.

## Where the post goes (decided by Richard, 2026-10-15)

**oxilab.dev blog**, through the `wordpress` MCP (`/Users/richard/Local Sites/oxilab/oxilab-mcp-server`). On
2026-10-16 it held two Shop Manager drafts (564, 544) and no Flipbox posts.

| Tool | Use |
| --- | --- |
| `get_posts`, `get_post` | List posts; one post with raw content, categories, tags and featured image id |
| `create_post`, `update_post` | Title, content, slug, status, categories, tags, `featured_media`, excerpt |
| `get_categories`, `create_category` | Post categories (search first; `create_category` reuses an existing name) |
| `get_tags`, `create_tag` | Tags (search first; `create_tag` reuses an existing name instead of making a duplicate) |
| `update_seo` | Rank Math focus keyword, SEO title, meta description, slug |

| `upload_media`, `get_media`, `update_media` | **Only for the featured image** (see Images below); every other blog or doc image goes to Cloudflare R2 |

Categories on 2026-10-16: Shop Manager (45), WooCommerce Guides (46), Uncategorized (1). None fits a Flipbox
post (most are not about WooCommerce): propose a new category in the brief, for example "WordPress Guides" or
"Flipbox", and use what Richard approves. Tags on 2026-10-15: 47 Shop Manager for WooCommerce,
48 WooCommerce dashboard, 49 wp-admin alternative, 50 WooCommerce store management, 51 WordPress plugin (the last one
fits). Check with `get_tags` before every post; the list grows.

The MCP tools load when a Claude Code session starts: after the server changes, a new session is needed before the
new tools appear.

## Workflow

1. **Keyword research first** ([keyword-research.md](keyword-research.md)): measured search volume, traffic
   potential, difficulty, CPC, intent, trend and competitors for every candidate, from free tools (Keyword Planner, Bing, Semrush free),
   never guessed. Pick one focus keyword, 2 to 4 secondary keywords and **at least one money keyword**, and check the
   keyword map so no other page already targets them.
2. **Search the results page** for the focus keyword (web search): what the top 5 results cover, their format
   (guide, list, comparison), the "People also ask" questions, and what they miss. The post must answer the intent
   at least as fully and add something they don't have (real screenshots, a tested setup, a clear table).
3. **Brief** to Richard: the keyword table (volume, traffic potential, KD, CPC, intent, trend, source and date),
   title, slug, focus, secondary and money keywords, intent, what the top results cover and the gap
   this post fills, the hook (first 2 or 3 sentences), outline (H2s and H3s), FAQ questions, images planned (featured
   plus inside), internal and external links, tags, category. Wait for a literal **YES**.
4. Write the post from scratch. Check every claim about the plugin in the code ([00-general.md](00-general.md)
   rule 3).
5. Make the images (below).
6. **Originality and copyright test** (below). Fix anything it finds before Richard sees the draft.
7. Show Richard the draft with images and the test results.
8. Create it as a **draft** (`create_post` with `status: draft`), set the SEO fields (`update_seo`), then publish
   only when Richard says so. Then the **after publishing** steps (below).

## The hook (first 2 or 3 sentences)

- Open with the reader's situation or problem in concrete words, then promise what the post gives them.
  Example: "Your services page lists everything in plain boxes, and visitors skim past them. A flip box shows a
  picture first and turns over to the details and a button when the mouse moves over it. In this guide you'll add
  flip boxes to a WordPress page in about ten minutes, without writing any CSS."
- The focus keyword appears naturally in the first 10% of the text.
- No clickbait: the post must deliver exactly what the hook promises.
- Never start with "In today's world", "Are you looking for", a dictionary definition or a history lesson.

## 100% human readable

- Write like an experienced store builder explaining it to one person: "you", plain words, short sentences mixed
  with longer ones, concrete examples from a real site (a services page, a team page, a pricing teaser; the "Travel
  Tours" flip box on the test site).
- One idea per paragraph, under 120 words (usually 2 to 4 sentences).
- Read it aloud. Anything that sounds like a brochure or a robot gets rewritten.
- Banned filler (it reads as machine written): delve, unlock, unleash, elevate, seamless, robust, game changer,
  supercharge, in today's fast paced world, in the realm of, it's worth noting, navigate the landscape, look no
  further, take it to the next level, in conclusion, furthermore, moreover, additionally (as a sentence opener).
- No superlatives or invented numbers (best, #1, ultimate; "boosts sales by 40%"). Facts with numbers only when they
  are true and checkable (29 templates, 87 ready designs: from [../code/templates.md](../code/templates.md)).
  Never copy the "50+ effects" figure from the readme; it is not countable in the code.
- No keyword stuffing: when density needs one more mention, rename a heading or add a genuinely useful sentence,
  never repeat a phrase for the score ([00-general.md](00-general.md) rule 11).
- No dashes. English, international.

## SEO (Rank Math 80+, honestly)

| Test | What to do |
| --- | --- |
| Focus keyword | In the SEO title (at the start), meta description, slug, first 10% of the text, 3 to 5 H2s, at least one image alt |
| SEO title | Up to 60 characters, keyword first; a number, a power word and a positive or negative sentiment word only where true of the post |
| Meta description | Up to 158 characters, keyword early, says what the reader gets |
| Slug | Short, the keyword only (blog URLs are short, so the 75 character URL test is easy here) |
| Density | Just over 1.0% (Rank Math's best band is 1.0 to 2.5%); there is nothing to gain above that |
| Length | Whatever the topic needs, usually 1,500 to 2,500 words for a guide (Rank Math bands: 1500 to 1999 = 4, 2000 to 2499 = 5, 2500+ = 8). Never pad |
| Links | 5 or more internal (Flipbox docs, other posts, the demos and pricing pages), checked live; 1 or 2 external to real authorities (WordPress.org, MDN for CSS facts) |
| Images | Featured image plus one image per main section, keyword in at least one alt |
| Paragraphs | Short (under 120 words each) |
| Table of contents | For posts over about 1,500 words, a real TOC for readers (the docs theme TOC covers docs only); ask Richard whether the theme or the Rank Math TOC block should provide it |

The score updates only when the post is opened and saved in the editor (Rank Math computes it in the browser). Never
write a score through the API. Tests that can't pass honestly (Content AI needs Rank Math Pro) stay red and are
reported.

## AEO (answer engines: featured snippets, Google AI Overviews, voice)

- Phrase H2s and H3s as the questions people ask where it reads naturally ("How do I add a flip box in
  WordPress?").
- Put a **direct answer of 40 to 60 words right under each question heading**, then the detail.
- Steps as numbered lists, comparisons as tables, definitions as one clear sentence ("X is ...").
- An FAQ section with 4 to 6 real questions (from Google's "People also ask" and support forum threads), each answered
  in 2 to 4 sentences.
- Schema: Article (Rank Math sets it), FAQPage for the FAQ (Rank Math FAQ block, added in the editor) and HowTo only
  when the post really is step by step.

## GEO (generative engines: ChatGPT, Perplexity, Gemini, Google AI Mode)

- Name things clearly and consistently: "Flipbox, a WordPress flip box plugin by Oxilab" early in the post, then
  "Flipbox".
- Write quotable, self-contained sentences with specific, true facts (counts, steps, settings names, requirements:
  PHP 7.4, WordPress 6.2).
- Show original material: real screenshots, real settings, a worked example. Generic rewrites don't get cited.
- Link to sources for any claim that isn't about the plugin itself.
- Author name and "Last updated" date visible; update the post when the plugin changes, and change the date only
  when the content changed.
- Clean structure (H2 and H3 in order, lists, tables) so the post can be quoted in parts.

## What the ranking systems reward (keep every post inside these)

Google, Bing and the AI engines read the same signals; a post that follows these ranks everywhere, a post that
games one of them risks all of them.

| System | What it rewards | What it punishes |
| --- | --- | --- |
| Google core ranking and its helpful content guidance | People-first content that fully answers the search intent; first-hand experience (E-E-A-T: Experience, Expertise, Authoritativeness, Trust): real screenshots, a setup actually tested, a named author, dates, sources | Content made mainly for search engines, thin rewrites of other pages, mass-produced pages, a title or snippet that promises more than the page gives |
| Google spam policies | Clean pages | Keyword stuffing, hidden text, doorway pages, scraped or spun content, scaled content abuse, link schemes |
| Page experience | Fast pages: WebP images with width and height set, lazy loading below the first screen, no layout shift | Huge images, layout jumps |
| Bing (and ChatGPT search, which uses Bing's index) | The same quality signals, clear headings, fresh content; IndexNow for fast indexing | The same spam |
| AI Overviews, ChatGPT, Perplexity, Gemini (AEO and GEO) | Direct answers under question headings, specific true facts, clear entity names, structure that can be quoted in parts, sources, freshness | Vague filler, unverifiable claims |

Rules that follow from it:
- **Search intent first**: match the format the top results show (a how-to gets steps, a "best way" gets a
  comparison), then do it better.
- **Experience shows**: at least one thing in every post that only someone who used Flipbox could write
  (a real screenshot, a setting's exact effect, a gotcha and its fix).
- **Topic clusters**: each post links to the matching Flipbox docs and to related posts, and the docs link
  back where it helps the reader. One post per search intent; never two posts chasing the same keyword (they compete
  with each other).
- **Freshness**: when the plugin changes, update the post and its "Last updated" date; never change the date
  without changing the content.
- **No tricks**: no keyword stuffing, hidden text, fake dates, fake reviews or invented numbers
  ([00-general.md](00-general.md) rule 11).

## Originality and copyright test (before Richard sees the draft)

Every post must be original and legally clean. Record the results in the post's `QA.md`
(`/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/out/blog/<post-slug>/QA.md`).

| Check | How | Pass |
| --- | --- | --- |
| Original text | Write from scratch; never paraphrase a competitor's post. Then pick 10 distinctive sentences (not common phrases) and run an exact-phrase web search for each (`"the sentence"`) | No match on another site. A match means rewrite that passage and search again |
| No self-duplication | Compare with our own docs and posts | No paragraph copied from a Flipbox doc or another post; summarise and link instead |
| Images | Every image is our own capture or graphic | No stock photos, no images from other sites, no third-party logos (WordPress and WooCommerce are named in text only) |
| Quotes | Any quote from another source is short, in quotation marks, credited and linked | Every quote passes |
| Trademarks | WooCommerce, WordPress and other names used as plain names ("for WordPress") | No claim or hint of being official or endorsed |
| Facts and numbers | Every plugin fact checked in the code; every outside fact linked to its source | Nothing unverified |
| Privacy | No real customer, admin name, email or license key in text or images | Clean |

If Richard has a paid plagiarism checker (Copyscape, Grammarly or similar), run the full text through it too and
record the result. **Ask Richard** once whether he has one, then note it here.

## After publishing (get it indexed everywhere)

1. Open the live post: title, meta description, images (CDN and featured), links, FAQ schema (Rank Math), no errors.
2. Check the post appears in Rank Math's sitemap (`https://oxilab.dev/sitemap_index.xml` gains a `post-sitemap.xml`
   with the first published post).
3. Ask Richard to request indexing in Google Search Console (URL Inspection) and, if Rank Math's Instant Indexing
   (IndexNow) is on, confirm it submitted to Bing; if it is off, ask Richard whether to turn it on.
4. AI crawlers must reach the site. On 2026-10-16 Cloudflare was blocking GPTBot and ClaudeBot (403); Richard allowed
   them in Cloudflare (AI Crawl Control and Security, Bots) and a retest gave 200 for GPTBot, OAI-SearchBot,
   ChatGPT-User, ClaudeBot, PerplexityBot and Google-Extended. `robots.txt` blocks no AI crawler. Re-check every few
   months, and whenever posts never show up in AI answers, by requesting a page with each crawler's user agent.
5. After about 4 weeks, review Search Console (queries, clicks, position) with Richard and update the post where a
   query shows a gap.

## Tags and category

- One main category per post.
- 4 to 8 tags, taken only from the analysed keyword list ([keyword-research.md](keyword-research.md)): the focus
  keyword, secondary and money keywords with real search volume, the product name and the platform. Reuse existing
  tags; never create near-duplicates ("flip box" and "flip boxes").
- A tag used on only one post makes a thin tag page: prefer tags that will hold several posts.
- Search with `get_tags` first; create only what is missing with `create_tag`; list the tags (existing and new) in
  the brief so Richard approves them before they are created.

## Images

Every post has a **featured image** and **images inside** the post, made for its topic. Richard set the look on
2026-10-16 with two examples (a FlexStock featured image and a plain FlexStock screenshot with a marked button).

**One size for every image in a post: 1600 x 840 px** (the 1.91 : 1 social share ratio, sharp on retina screens).
The featured image and every screenshot have exactly this size, so the post looks even.

### Featured image (like Richard's FlexStock example)

| Part | Spec |
| --- | --- |
| Background | Gradient in the Flipbox logo colors: deep teal to logo teal (`#063b3a` → `#17bcb5`, a soft lighter glow top right), a faint warm touch taken from the banner. **Ask Richard** to approve the first one, as he did for Product Layouts. No photo, no busy pattern, no grid, squares, lines or dots (Richard, 2026-10-17) |
| Left side, top to bottom | (1) a label pill in a lighter teal with white text, the post type: "How to", "Guide", "Comparison"; (2) the headline in large bold white type, 2 lines, holding the focus keyword ("Add Flip Boxes in WordPress"); (3) a subtitle in lighter white, 1 or 2 lines, the promise ("Front, back and a button. No code."); (4) an optional small wave in the warm accent under the subtitle (a decoration, never a dash before a label); (5) the logo at the bottom: a white pill with the Flipbox mark `icon-256x256.png` and the wordmark "Flipbox" |
| Right side | Real Flipbox UI or output that shows the topic: 1 or 2 crops (a row of flip boxes with one mid flip, an editor panel, the item dialog) as rounded white cards with a soft shadow, slightly tilted or overlapping, plus at most one round icon badge in the brand teal between them |
| **No browser frame** | No macOS or Apple browser window, no traffic-light dots, no address bar around the UI cards (Richard's rule) |
| Text | Short enough to read at phone size; no dashes; English; only what the post delivers |
| Truth | The UI on the cards is real (captured from the plugin), never drawn or AI generated |

### Images inside the post (like Richard's plain screenshot example)

| Part | Spec |
| --- | --- |
| Look | A normal screenshot of the real screen, rounded corners (about 16 px), on a white or very light background so every image fills the same 1600 x 840 canvas. **No browser frame**, no gradient stage |
| Crop | Only the part the step is about (with enough context to recognise where it is). Capture at device scale 2 and crop to the 1.91 : 1 shape; if the area is a different shape, center it on the light background instead of stretching |
| Mark | Only when something must be pointed at: a box around exactly the elements the text names (the union of their boxes), in amber `#ffb020` (stands out from the teal UI, as in the docs), 4 px; no mark when the image is an overview ([screenshots-docs.md](screenshots-docs.md) rules) |
| Flip | When the image shows flip boxes, one is hovered (or clicked) so its back face shows |
| Privacy | wp-admin cleaned (no admin bar name, notices, other plugins' menus), no license key, no personal data |

### Files and pipeline

| Part | Spec |
| --- | --- |
| Files | WebP, 1600 x 840, under about 250 KB each, descriptive names with the keyword where natural (`flip-box-editor.webp`); the featured image `<post-slug>-featured.webp` |
| Alt text | What the image shows, keyword in at least one |
| Build | A `scripts/blog/blog.mjs` in `/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/` (featured template plus screenshot framing; copy the Product Layouts `blog.mjs` once it exists), output to `out/blog/<post-slug>/`, inline images then copied to `r2-uploads/flipbox/blog/<post-slug>/`. Never only in the scratchpad |
| First time | Build the featured template with the first real post, show Richard the featured image and one inline image, and only then make the rest |

### Where the images go: Cloudflare R2 (Richard, 2026-10-16)

Blog images live on **Cloudflare R2** and load from the CDN, never from the WordPress Media Library.

| Part | Rule |
| --- | --- |
| Public URL | `https://cdn.oxilab.dev/flipbox/blog/<post-slug>/<file>.webp` |
| Local mirror | `/Users/richard/Local Sites/oxilab/r2-uploads/flipbox/blog/<post-slug>/` (same names as the R2 keys) |
| Who uploads | **Richard uploads to R2 himself.** Save the files in the mirror folder, use the CDN URLs in the post (`<figure class="wp-block-image size-large"><img src="https://cdn.oxilab.dev/..." alt="..."/></figure>`), and give him the list of files to upload |
| Never | `upload_media` or any other upload into WP Media for images inside a post or a doc (the featured image is the one exception, below) |
| Replace an image | Same file name in the mirror folder, tell Richard; the post needs no edit |

**The one exception: the featured image goes to the Media Library** (Richard, 2026-10-16). The theme shows it with
`the_post_thumbnail()` (`themes/oxilab/main-site/templates/single.php`, `blog.php`, `archive.php`, `author.php`),
which needs a Media Library attachment, and Rank Math uses it as the share image. So:
1. After Richard approved the draft and its images, `get_media` with a search (reuse if it is already there), else
   `upload_media` with the 1600 x 840 WebP, `alt_text`, `title` and `post_id`.
2. `update_post` with `featured_media` set to the returned id.
Only the featured image goes this way; every image inside the post stays on R2.

## Checklist before asking Richard to publish

- [ ] Keywords analysed with measured data (source and date), at least one money keyword, keyword map updated
- [ ] Results page researched; the post matches the intent and fills a gap the top results leave
- [ ] Originality and copyright test passed and recorded in `QA.md`
- [ ] Hook in the first 2 or 3 sentences, keyword in the first 10%
- [ ] Every plugin claim checked in the code
- [ ] Reads naturally aloud, no banned filler, no dashes
- [ ] Direct answers under question headings, FAQ written
- [ ] SEO title, meta, slug, keyword in H2s and an alt, density just over 1%
- [ ] 5+ internal and 1 to 2 external links, all checked live
- [ ] Featured image and section images, alt texts, WebP
- [ ] Category and tags (searched with `get_tags`, new ones approved in the brief)
- [ ] Images saved in `r2-uploads/flipbox/blog/<post-slug>/`, CDN URLs and alt text in the post, file list
      given to Richard for R2
- [ ] Featured image (1600 x 840) uploaded to the Media Library with alt text and set as `featured_media`
- [ ] Saved as draft, SEO fields set; Rank Math score read after Richard opens and saves it
