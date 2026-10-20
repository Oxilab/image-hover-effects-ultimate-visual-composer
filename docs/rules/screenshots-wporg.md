# WordPress.org screenshots

The screenshot set on `wordpress.org/plugins/image-hover-effects-ultimate-visual-composer/`.

**Done 2026-10-20 (Richard approved shot 1, then "make all and replace", then asked for Elementor, Gutenberg and Use Shortcode
shots):** 12 square shots in `.wordpress-org/`, readme `== Screenshots ==` rewritten to match. Order: 1 editor of #23
with a card hovered, 2 Flip Box list, 3 front end (temporary page with the WPBakery element), 4 Create New with the
naming dialog (typed, never submitted), 5 the card dialog, 6 Flip Trigger (ring), 7 Import Templates at Style 20 and
21, 8 Use the Shortcode Anywhere (Shortcode panel ringed after its Copy button; the Name panel shares the `oxi-addons-shortcode` class, so match by the text "Shortcode for posts"), 9 Works With Gutenberg (block inserter searched "flip", "Available to install" hidden because it lists other
plugins' blocks), 10 Works With WPBakery (Add Element with the Flipbox logo), 11 Works With Elementor (colored widget icon since 3.1.1) (Elements panel
searched "flip"; Structure panel, promotions and section handles hidden), 12 Settings without the License card and
version. The 4 old April 2026 shots were backed up to the session scratchpad only.

Seen while capturing the Elementor shot (not fixed, for Richard): in the Elementor editor preview the card's back text renders in a
serif font instead of the design's Open Sans, so the flip box's Google Font seems not to load in Elementor's preview. Getting Started was left out: it repeats the
"50+ effects" claim (00-general rule 3).

Data changed on the local site for the set (kept, undo files in
`flipbox-tutorial-video/content/data-undo/`): flip box #23 cards made square (Height 330 to 282, Style 5 stretches
each photo to the card) and the city card uses a square crop, attachment 136044 (`scripts/db/fb23-square.php
restore`); 8 generic names on the list ("Style 1", "Style 2" ...) renamed to realistic ones (`scripts/db/fb-names.php
restore`). Temporary page and post: `scripts/db/wporg-pages.php make|remove` (removed after the build).

## Richard's corrections on the look (2026-10-20)

- Images must never look stretched: check every photo's shape against the card it sits in before capturing.
- The first stage teal was too dark: use the lighter teal now in `frame.mjs`.
- The image is **square**, with the **same space left, right and below** the window, 140 px (200 was too much). A tall
  shot is captured wider until that fits (`w = ABOVE + h - SIDE`); a short page is captured narrower with a shorter
  title (shots 3 and 10: 1124 x 1000; 9 and 11: 1300 x 1176) so it still fills the square.

## Approval flow

1. Build **one** screenshot, show it (open it in Preview), wait for Richard's word.
2. Then build the rest and show the full set.
3. Only after his go: copy into `.wordpress-org/`, rewrite the readme captions, remove temporary data.
Nothing touches the plugin folder before step 3. Back up the old screenshots to the scratchpad first.

## The look (approved 2026-10-20)

| Part | Spec |
| --- | --- |
| Capture | Playwright, device scale 2; 1500 x 1376 for a normal admin shot (that makes the square), wider for a taller shot (editor 1556 x 1432), 1124 x 1000 for a short page |
| Output | Square PNG, about 2800 to 3700 px, 2 to 4.5 MB |
| Stage | Lighter teal: `linear-gradient(148deg,#16928b,#0f7b75 45%,#0b625d)`, teal glows `rgba(52,214,205,.85)` bottom right and `rgba(70,222,212,.6)` top left, soft amber glow `rgba(255,190,90,.45)` at the bottom; no grid or pattern (see Background below) |
| Space | Top padding 64; left, right and bottom 140 each (`SIDE` in `frame.mjs`); the square is computed in the frame |
| Window | macOS window: 42 px bar with traffic lights, 15 px corners, soft shadow |
| Logo pill | White pill top left with the real wordmark `image/flipbox-logo.svg` ("Flip" black, "box" teal) at 200 x 44 |
| Title | Centred, 800 weight, 62 px (the frame shrinks it to fit), white, no dashes; the build fails if the title comes within 24 px of the logo |
| Highlight | Ring in amber `#ffb020`; no arrow in this set (arrows crossed content on 6 and 9) |

## Content rules

- **Show the flip**: in shots with flip boxes, one box is hovered (or clicked for On Click) so its back face shows.
- Real data only ([00-general.md](00-general.md) rule 10): the kept example flip box #23 "Travel Tours"; never the
  templates' Lorem copy on an editor or front-end shot.
- Clean wp-admin: no admin bar, no notices, no menu count badges, only core menus plus Flip Box.
- Privacy: the build fails a shot on a non example.com email or a user's identity string (whole words). The Settings
  shot starts below the License card.
- Captions in `readme.txt` (`== Screenshots ==`) match the screenshot numbers and say only what the image shows.

Suggested set (check each screen first): the flip box list; Create New with live previews; the "Create flip box"
dialog; the editor with a flip box mid flip; the item dialog (front and back fields); General tab with Flip Trigger;
the template library; the Flipbox block in the block editor; the Elementor widget; Settings.

## Pipeline

`/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/scripts/wporg/`: `frame.mjs` (stage, square, title fit),
`build.mjs` (the `SHOTS` list; privacy, identity and version scan before every write), plus helpers `measure.mjs`,
`fullpage.mjs`, `probe.mjs`, `probe-panels.mjs` for planning a shot. Output `out/wporg/screenshot-N.png`.
Run: `IH_COOKIES=<cookies> IH_BANNED=<identity json> node scripts/wporg/build.mjs [n ...]`. Shots 3, 9, 10 and 11 need
`scripts/db/wporg-pages.php make` first (build.mjs reads the new ids from `content/data-undo/wporg-pages.json`) and
`remove` after. Login cookies from `scripts/db/auth-cookies.php`; the identity list and cookies live in the
scratchpad only.

Gotchas: the block's `flipboxId` attribute is a **string** (`{"flipboxId":"23"}`, a number shows the placeholder);
the block editor needs the "Top toolbar" preference so the block toolbar does not cover text; WPBakery filters its
Add Element list on key events (`pressSequentially`, not `fill`); `content/data-undo/` must exist before a data
script runs (both scripts now stop if they cannot write their record).

## Background: no grid, no squares (Richard, 2026-10-17)

The stage, frame or canvas behind a screenshot is a plain color or a smooth gradient with soft glows only: **no grid,
squares, lines, dots or any repeated pattern**, not even faint. The same rule covers videos, thumbnails and blog
images. On 2026-10-17 the pipelines `image-hover-tutorial-video/scripts/wporg/frame.mjs` and
`wporg-screenshots/product-layouts/frame.mjs` still draw a 58 px grid ("so the flat areas of the gradient are not
dead space"): remove that layer before the next build, and never copy it into a new pipeline. The installed
WordPress.org sets of Shop Manager, Image Hover and Product Layouts still show a grid until they are rebuilt.

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
