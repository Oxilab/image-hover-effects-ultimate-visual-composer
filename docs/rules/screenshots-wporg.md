# WordPress.org screenshots

The screenshot set on `wordpress.org/plugins/image-hover-effects-ultimate-visual-composer/`. **Not redone yet:**
`.wordpress-org/` still holds 4 old screenshots (April 2026) with the captions "Template Demo", "Admin Panel.",
"Live Preview.", "Popup Data Input form.", from before the 3.1.0 redesign. When Richard asks for a new set, make it
in the approved format of the Image Hover set (2026-10-14) and the Product Layouts set (2026-10-15), in Flipbox
colors.

## Approval flow

1. Build **one** screenshot, show it (open it in Preview), wait for Richard's word.
2. Then build the rest and show the full set.
3. Only after his go: copy into `.wordpress-org/`, rewrite the readme captions, remove temporary data.
Nothing touches the plugin folder before step 3. Back up the old screenshots to the scratchpad first.

## The look (proposed from the approved sets; confirm on the first shot)

| Part | Spec |
| --- | --- |
| Capture | Playwright, 1500 px wide, device scale 2, height per shot (about 860 to 990) |
| Output | 3256 px wide PNG, the Shop Manager width |
| Stage | A deep teal gradient from the Flipbox banner and logo (for example `#063b3a` → `#0d817c`), teal glow `rgba(23,188,181,.8)`, a warm accent glow taken from `.wordpress-org/banner-1544x500.png`, faint 58 px grid, padding 64. Pick the exact colors from the banner, as was done for Image Hover |
| Window | macOS window: 42 px bar with traffic lights, 15 px corners, deep shadow |
| Logo pill | Top left: the mark `.wordpress-org/icon-256x256.png` plus the wordmark "Flipbox" |
| Title | Centred, 800 weight, 62 px, white, no dashes, about 30 characters; the build fails if the title comes within 24 px of the logo |
| Highlight | Ring and curved arrow in a color that stands out from teal (amber `#ffb020`); `arrow: false` where an arrow would cross the content |

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

Build it next to the tutorial: `/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/scripts/wporg/` (copy
`frame.mjs` and `build.mjs` from `image-hover-tutorial-video/scripts/wporg/`, change colors, logo and `SHOTS`).
Output `out/wporg/screenshot-N.png`. Login cookies from `scripts/db/auth-cookies.php`; the identity list and cookies
live in the scratchpad only.
