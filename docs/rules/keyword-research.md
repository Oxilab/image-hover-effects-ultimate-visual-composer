# Keyword research (before every blog post and doc rewrite)

Richard, 2026-10-16: before choosing keywords or tags, analyse them properly: how many people search them, how hard
they are to rank for, who the competitors are, and always include **money keywords**, so the post brings a large
amount of real visitors who can become customers. No post is written before this analysis is approved.

## Where the numbers come from (never guessed)

Search volume, difficulty and cost per click are **measured data**. Never estimate, invent or "round" a number; if a
number is missing, write "no data" and say why.

### The workflow Richard chose (2026-10-16): Google Keyword Planner

No paid tool for now (Ahrefs too expensive; DataForSEO postponed, see the end of this section). Every post runs like
this:

1. **Claude: seed list.** From the topic, the plugin's features, Google autocomplete, "People also ask", related
   searches, WordPress.org plugin search, the support forum and competitors' pages, Claude writes 20 to 60 candidate
   keywords (informational, commercial and money keywords) into
   `docs/marketing/data/<YYYY-MM-DD>-<post-slug>-seeds.txt`, **one keyword per line**, and gives Richard the file
   path plus the list in the reply so he can paste it.
2. **Richard: Keyword Planner.** In Google Ads → Tools → Keyword Planner → **Get search volume and forecasts**, paste
   or upload the list. Location: **All locations** (the audience is international); language English; date range the
   last 12 months. Download the **Historical metrics** as CSV. Optional, same session: **Discover new keywords** with
   the 3 to 5 main seeds and download those ideas too (often finds keywords Claude didn't think of).
3. **Richard: hand over the file(s).** Save them as `docs/marketing/data/<YYYY-MM-DD>-<post-slug>-kp.csv` (and
   `-kp-ideas.csv`), or send the path.
4. **Claude: everything else.** Read the CSV, add the free checks below, score and choose the keywords, fill the
   keyword map, and put the keyword table in the brief.

Reading the export: Keyword Planner CSVs are usually **UTF-16 and tab separated**, with 2 title lines before the
header. Columns used: `Keyword`, `Avg. monthly searches`, `Three month change`, `YoY change`, `Competition`,
`Competition (indexed value)`, `Top of page bid (low range)`, `Top of page bid (high range)`, and the 12 monthly
columns (the trend). Keep the raw file unchanged in `data/`.

What the export gives and what it doesn't:
- **Volume**: Google's own number, a rounded 12-month average; without ad spend often a range (such as 100 to 1K).
  Good for comparing keywords, not an exact visitor count.
- **Money value**: `Top of page bid` (what advertisers pay per click) and `Competition` (how many advertisers bid).
  A real bid means people searching it buy: those are the **money keywords**. Note: `Competition` is advertiser
  competition, not ranking difficulty.
- **Trend**: the monthly columns and the 3-month and YoY change.
- **Ranking difficulty is not in it.** Claude judges it from the live results page (step below) and says so in the
  brief ("difficulty: judged from the top 10, no KD score").

Free checks Claude adds for every post:

| Check | Source | Gives |
| --- | --- | --- |
| Ranking difficulty | The live top 10 for each shortlisted keyword (web search) | Who ranks (big brands, forums, thin pages, WordPress.org), page types, how well they answer: easy, medium or hard |
| Questions | "People also ask", autocomplete, the support forum | H2 questions and FAQ |
| AI answers | Whether a Google AI Overview appears and which sites it cites | The AEO target |
| Our coverage | The keyword map, our docs and posts | No two pages on one keyword |

Optional, only if Richard wants more: Semrush free (about 10 lookups a day) for a KD score on the shortlist, and
Google Search Console for real impressions once posts are live.

**DataForSEO** (pay as you go, $50 minimum, no subscription; its volume and CPC come from Google Ads, the same source
as Keyword Planner) would let Claude do steps 1 to 4 alone. Postponed by Richard on 2026-10-16; if he takes it later,
run the free comparison first (15 to 20 keywords from DataForSEO against Keyword Planner) before adding money.

Without a Keyword Planner export the brief says "no volume data" and nothing is written until Richard sends it.

Every number in a brief carries its source and date ("Keyword Planner, all locations, 2026-10-16").

## What to measure for every candidate keyword

| Metric | Why |
| --- | --- |
| Search volume (Keyword Planner, all locations) | How many people search it per month. The audience is international |
| Traffic potential | What a top page can get from all the related searches, often more than the single keyword: the sum of the Keyword Planner volumes of the related keywords the post can rank for, labelled as that |
| Difficulty | How hard the top 10 is to break into: **easy, medium or hard**, judged from the live results page (a KD score only if Richard adds Semrush or a paid tool) |
| CPC (Top of page bid) | What advertisers pay per click: a real bid means the searchers buy, so it marks a **money keyword** |
| Search intent | Informational (how to), commercial (best, vs, review, alternatives), transactional (plugin, download, pricing) |
| Trend | Growing, flat or falling over 12 months |
| SERP features | AI Overview, "People also ask", video, featured snippet: what format wins |
| Who ranks | The top 10 domains, their strength and their content type, and whether a page like ours can beat them |
| Our coverage | Does an Oxilab doc or post already target it (to avoid two pages competing) |

## Money keywords (required in every post)

Money keywords are searches by people ready to choose or buy a plugin: commercial and transactional intent, usually
with a real CPC. Examples of the shape (check each one's data before use): "flip box plugin",
"wordpress flip box plugin", "flip box elementor", "flip card wordpress plugin", "team member flip box plugin",
"flipbox pro".

Rules:
- Every post targets at least one money keyword: as the focus keyword (comparison or "how to choose" posts) or as a
  secondary keyword used where it fits naturally (how-to posts), with a clear path to the pricing page or
  WordPress.org.
- Informational posts still earn their place: they bring the large volume, then link to the money pages.
- Only honest use: no claim that Flipbox is "the best" without proof, no competitor names in a post unless
  Richard approves a comparison post.

## Choosing the keywords

1. **Seed list** from the post topic, the plugin's features ([../code/templates.md](../code/templates.md)), competitors'
   pages and real questions (support forum, "People also ask").
2. **Pull the data**: Richard's Keyword Planner export plus Claude's free checks (above).
3. **Filter**: right intent for the post, volume worth writing for, a difficulty the site can win, a trend that
   isn't dying. oxilab.dev is a young domain (launched about April 2026), so start with "easy" keywords where the top
   10 has weak, old or off-topic pages; grow into harder keywords as the site gains links.
4. **Score** to rank the candidates: `traffic potential × intent value ÷ difficulty`, with intent value 3 for
   transactional, 2 for commercial, 1 for informational, and difficulty 1 easy, 2 medium, 4 hard. The score orders
   the list; judgement and the results-page check decide.
5. **Assign** in the keyword map: one focus keyword per page, 2 to 4 secondary keywords, at least one money keyword.
6. **Tags** come from the analysed list (focus and secondary keywords with real volume), never invented.

## Competitor analysis

- The top 10 for the focus keyword: domain, page type, word count, headings, what each covers and misses.
- For competitors that rank for many of our keywords (other WordPress flip box, flip card and info box plugins and page builder addons): the topics
  their blogs and docs cover that we don't (read from their sites and the results pages; a Semrush free lookup of a
  competitor domain shows a few of its top keywords), recorded as ideas in the keyword map.
- Their names stay in the research notes; they go into a post only if Richard approves a comparison post.

## The keyword map

`docs/marketing/keyword-map.md` lists every keyword we target, with its data, intent, the one page it belongs to and
the date the data was pulled. Check it before every post (no two pages on one keyword) and update it after.

## In the brief to Richard

A table for the focus keyword, the secondary keywords and the money keyword(s): volume, traffic potential,
difficulty (easy, medium or hard, and why), top of page bid, competition, intent, trend, top competitors, source and
date, plus why this combination was chosen and the tags it gives.
Richard approves the keywords together with the brief.
