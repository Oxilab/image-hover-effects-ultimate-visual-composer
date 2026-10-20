# Videos: rules for every video

Applies to tutorials ([video-tutorial.md](video-tutorial.md)), explainers ([video-explainer.md](video-explainer.md))
and ads ([video-ads.md](video-ads.md)). Built from the Flipbox tutorial (4:55, 2026-10-09), the Image Hover tutorial
(v3, 2026-10-14) and the Shop Manager launch video and feature tour (2026-10-03/04), where Richard set each rule.

## Where the project lives

- One Remotion project per video at `/Users/richard/Local Sites/oxilab/flipbox-<kind>-video/` (never inside the
  plugin, never only in the scratchpad). The tutorial project exists:
  `/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/` (a copy of the Image Hover pipeline with Flipbox
  capture scripts).
- Start from the closest pipeline: that tutorial project for tutorials,
  `/Users/richard/Local Sites/oxilab/shop-manager-launch-video/` for explainers and launch videos. Read the project's
  `README.md` first.
- Write a `README.md` in a new project: deliverables, how to rebuild, every change made to the site and its undo.

## Order of work

1. **Brief**: purpose, length, audience, platform, aspect ratios. Ask Richard only what the brief and these rules
   don't answer.
2. **Script**: every line checked against the code and the UI ([00-general.md](00-general.md) rule 3). Show it to
   Richard and get approval before recording.
3. **Site data**: real, realistic data on every screen; record every change with an undo file.
4. **Capture**, **voice**, **timeline**, **checks**, **render**, **master**, **captions**, **thumbnail**, **upload
   pack**, in that order.
5. Show Richard review stills or a draft before the final render; after his corrections, update these rules.

## Words on screen and in the voice

- English, international. No dashes anywhere (also no decorative line before a label).
- **No Free/Pro, plans or prices** in voice, captions, on-screen text or thumbnails. Pro templates and the color,
  font and Custom CSS controls appear as plain features.
- Don't read out admin URLs or page parameters; name the menu and the button.
- Every sentence is true of the plugin today; no "50+ effects" ([00-general.md](00-general.md) rule 3).
- It is the **Flipbox** plugin; never show or name Image Hover Effects Ultimate in a Flipbox video.

## Every video opens with a hook (Richard, 2026-10-15)

| Video | Hook length | What the hook does; every platform and its format: see Platforms and formats below |
| --- | --- | --- |
| Ad | 0 to 2 s | Movement plus one short line; a card flipping over in the first second ([video-ads.md](video-ads.md)) |
| Explainer or launch | 0 to 5 s | The viewer's problem in one line over real output (flat info boxes that say everything at once), then the reveal ([video-explainer.md](video-explainer.md)) |
| Tutorial | 0 to 8 s | The finished result first (a row of flip boxes on a live page, the mouse turning them over) and one sentence promising it, then "here's how" ([video-tutorial.md](video-tutorial.md)) |

Rules for the hook: real UI and a real result, never a mock-up; it promises only what the video delivers; the first
frame already shows a flip box working; the spoken hook and the on-screen line say the same thing, readable in 2 s.

## Brand (Flipbox), from `flipbox-tutorial-video/src/theme.ts`

| Part | Value |
| --- | --- |
| Main color | Teal `#1ABCB5` (accents), strong `#0D817C` (text, filled buttons, highlighted words on light scenes), soft `#E2F6F4`, light background `#F4FBFA` |
| Text | Ink `#101828`, muted `#475467` |
| Logo | `.wordpress-org/icon-256x256.png`, `image/logo.png`, wordmark `image/flipbox-logo.svg` |

White text on `#1ABCB5` is only 2.4:1: put white text on `#0D817C` instead. One brand accent only (teal), never a
second "pain" color for problem scenes.

## Capture (real UI only)

- Playwright, 1920 x 1080 at device scale 2, logged in with a signed cookie
  (`flipbox-tutorial-video/scripts/db/auth-cookies.php`, no password).
- Clean wp-admin at capture time: no admin bar identity, no notices, no third-party menus or Dashboard widgets, no
  Site Health, no update count badges.
- A scanner checks every captured state and **fails** on: any non example.com email, phone numbers, `sk_`/`pk_`
  keys, any user identity string (whole words), license keys, placeholder copy (lorem, ipsum, "Far far away"), and
  the words free, pro, premium, upgrade, license, pricing, buy, purchase. Template pages may show their sample copy
  with a written reason. A flip card's hidden back face counts as visible text for a DOM scan, so the scanner skips it
  with an `elementFromPoint` hit test (`scripts/rec/lib.mjs`).
- Real hovers and flips, frame-stepped (`scripts/rec/shot.mjs`). Native select lists don't render headless: draw the
  real options under the field (`fblib.openList()`; inside the block editor iframe, close the list in that frame).
- Writes: explainers never write data while recording. Tutorials may create one flip box on camera, with a cleanup
  script (`scripts/db/fb-cleanup.php`) that restores the site; touch only `type='flip'` rows.

## Voice, sync and clicks

- ElevenLabs, `eleven_multilingual_v2`. The key is read at runtime by the project's `el-key.mjs`; switch it to the
  `.env` value first ([00-general.md](00-general.md) rule 14).
- Tutorial voice: **Jerry B.** `tCgAUbeV0tdD1S2yFoCx`, speed 1.0 (used for the Flipbox tutorial). Explainer voice:
  **Jesse** `sXcl1NoQHcU7NRh6Na5D`, speed 1.0 to 1.1. Richard was given 19 alternatives on 2026-10-16
  (`/Users/richard/Local Sites/oxilab/voice-samples/previews/`); if he picks others, write them here.
- **The screen shows what the voice says, when it says it.** Every action anchored to its word; accept only a worst
  lag of about 0.6 s and no silent tail over 2 s.
- Every click lands on the real button (DOM-measured target, `click-check.mjs`), with a click sound.
- Pace: calm. Gaps of about 0.4 to 0.55 s, about 1 s before a new section, holds of 0.4 to 0.9 s after a click.

## Backgrounds: no grid, no squares (Richard, 2026-10-17)

Richard, pointing at the ThankRedirect explainer: no square shapes or marks in the background. So, in every video
(every scene, light and dark backdrops, intro, outro, thumbnails, ad frames):

- **No grid, no squares, no lines, no dots, no checkerboard, no tile or repeated pattern** behind the UI or the text,
  not even faint or faded out with a mask.
- Allowed: a plain color, smooth gradients and soft blurred glows in the brand colors, and the real UI itself.
- Check a still of every scene type at full size before rendering: the background must be clean.
- In code a grid looks like `linear-gradient(<color> 1px, transparent 1px)` with a `backgroundSize` in px,
  `repeating-linear-gradient`, `repeating-radial-gradient` or a pattern SVG: remove the layer, don't just fade it.
- On 2026-10-17 these still draw one: `src/components/Backdrop.tsx` (light and dark backdrop) in
  `thankredirect-explainer-video` and `shop-manager-launch-video`. Remove it before their next render, and never copy
  it into a new project.

## Layout and type

- **No text, counter or graphic on top of the UI.** Headlines beside or above the screen.
- No clipped glyphs (descenders g, y, p), also in gradient text.
- One camera matrix drives both the screenshot transform and the cursor.

## Output

| Item | Spec |
| --- | --- |
| Video | 1920 x 1080 (and 1080 x 1920 for vertical cuts), 60 fps, H.264 High; every platform and its format: see Platforms and formats below |
| Audio | AAC 48 kHz, mastered to -14 LUFS (`master.mjs`) |
| Captions | `.srt` timed to the spoken words |
| Thumbnail | 1280 x 720, under 2 MB, real UI, one short line of text, readable at 320 x 180, no text on top of the UI |
| Upload pack | `youtube.md`: title plus 2 alternatives, description with chapters and links, tags (see YouTube tags below), pinned comment |
| QA | `QA.md`: every check and its result |

Links for descriptions: docs `https://oxilab.dev/docs/flipbox/`, WordPress.org
`https://wordpress.org/plugins/image-hover-effects-ultimate-visual-composer/`, demos
`https://demos.oxilab.dev/flipbox/template/`. Channel `https://www.youtube.com/@oxilabdev`.

## YouTube tags (Richard, 2026-10-17)

Richard, on the ThankRedirect explainer: the tags came to 278 characters, too few; they must use the 500 character
limit. So, for every YouTube upload (tutorial, explainer, Short):

- **Fill the tags to 480 to 495 characters** of YouTube's 500, counted the way YouTube counts: each tag's length,
  **plus 2 for a tag with a space** (YouTube wraps it in quotes), plus 1 for each comma. Plain "comma space" text
  comes out shorter, so it is not the measure. Check it before writing the pack:
  `node -e 'const t=["tag one","tag2"];console.log(t.reduce((a,x)=>a+x.length+(x.includes(" ")?2:0),0)+t.length-1)'`
- **Every tag true and relevant to the video**: the main keyword first, then its close variations, then the features
  the video shows, then broad ones (woocommerce plugin, wordpress plugin), then the plugin name. No unrelated or
  trending words (YouTube treats misleading tags as spam), no plan or price words.
- In the upload pack: the tags on **one line, ready to paste**, with the count written next to it
  (example: `thankredirect-explainer-video/out/final/upload-pack.md`, 16 tags, 489).

## Platforms and formats (Richard, 2026-10-17)

Richard posts every explainer and launch video on **YouTube, YouTube Shorts, Facebook Reels, X, LinkedIn and
Reddit**, so it is rendered for all six from one timeline. Paid ads follow [video-ads.md](video-ads.md); tutorials
are YouTube only (16:9) unless Richard asks for more.

| Platform | Who sees it there | File | Size | Length limit | Also deliver |
| --- | --- | --- | --- | --- | --- |
| YouTube | Store owners searching how to do something | `youtube/<name>.mp4` | 1920 x 1080 (16:9) | none that matters | Thumbnail 1280 x 720, `.srt`, chapters, tags (see YouTube tags) |
| YouTube Shorts | Phone viewers scrolling | `youtube-shorts/<name>-shorts.mp4` | 1080 x 1920 (9:16) | under 3:00 | Title ends with #Shorts; the link goes in a pinned comment (description links are not clickable everywhere) |
| Facebook Reels | Store owners on Facebook, on phones | `facebook-reels/<name>-reels.mp4` | 1080 x 1920 (9:16) | **under 1:30** | The link goes in the first comment |
| X | The WordPress and developer crowd | `x/<name>-x.mp4` and `x/<name>-x-square.mp4` | 1920 x 1080, plus 1080 x 1080 for the phone feed | under 2:20, under 512 MB | Post text under 280 characters |
| LinkedIn | Agencies, freelancers, store owners at work | `linkedin/<name>-linkedin.mp4` | 1080 x 1080 (1:1) | under 10:00 | `.srt` (LinkedIn plays muted) |
| Reddit | WordPress and WooCommerce communities | `reddit/<name>-reddit.mp4` | 1080 x 1350 (4:5) | under 15:00, under 1 GB | Title and one paragraph body, the subreddit's title tag (below) |

How to make them:

1. **One timeline, four renders**: the 16:9 master (`Launch`) and three social cuts (`Social-9x16`, `Social-1x1`,
   `Social-4x5`), H.264 yuv420p with AAC, each mastered to -14 LUFS. Copy them into the six folders above
   (`out/final/<platform>/`, APFS clones so they take no extra space).
2. **Length**: keep an explainer **under 1:30**, so the same cut fits Reels and every other platform. A longer video
   (a feature tour) goes to YouTube, LinkedIn and X (if under 2:20) only, unless Richard asks for a short cut.
3. **Social cut layout**: the 16:9 video as a big rounded card, the logo and the WordPress.org link above it, big
   word synced captions burned in below it (feeds play muted), on the clean brand background. In 9:16 keep every text
   out of the top 14% and the bottom 35% (Reels and Shorts put their buttons there); 1:1 and 4:5 use the same layout,
   tighter.
4. **Check before delivery**: a still of each format, the length on the final files, file sizes under each limit,
   -14 LUFS on each.
5. **Upload pack** (`out/final/upload-pack.md`): per platform the title, the post text and where the link goes; every
   text on one line so a copy carries no hard line breaks; every claim checked in the code.
6. **Reddit title tags**: r/WordpressPlugins removes posts without one of [FREE], [PREMIUM], [FREEMIUM], [PROMOTION],
   [REVIEW], [DISCUSSION], [REQUEST] or [HELP] at the start of the title. All five Oxilab plugins are free on
   WordPress.org with a Pro license, so use **[FREEMIUM]** (and the Freemium flair). Read every subreddit's rules
   before posting; r/woocommerce and r/Wordpress limit self promotion.

Worked example and template: `/Users/richard/Local Sites/oxilab/thankredirect-explainer-video/` (`src/Vertical.tsx`
LAYOUTS, `scripts/render-all.sh`, `scripts/platforms.sh`, `out/final/upload-pack.md`).

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
