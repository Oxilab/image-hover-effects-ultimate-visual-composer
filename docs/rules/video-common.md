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

| Video | Hook length | What the hook does |
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

## Layout and type

- **No text, counter or graphic on top of the UI.** Headlines beside or above the screen.
- No clipped glyphs (descenders g, y, p), also in gradient text.
- One camera matrix drives both the screenshot transform and the cursor.

## Output

| Item | Spec |
| --- | --- |
| Video | 1920 x 1080 (and 1080 x 1920 for vertical cuts), 60 fps, H.264 High |
| Audio | AAC 48 kHz, mastered to -14 LUFS (`master.mjs`) |
| Captions | `.srt` timed to the spoken words |
| Thumbnail | 1280 x 720, under 2 MB, real UI, one short line of text, readable at 320 x 180, no text on top of the UI |
| Upload pack | `youtube.md`: title plus 2 alternatives, description with chapters and links, tags, pinned comment |
| QA | `QA.md`: every check and its result |

Links for descriptions: docs `https://oxilab.dev/docs/flipbox/`, WordPress.org
`https://wordpress.org/plugins/image-hover-effects-ultimate-visual-composer/`, demos
`https://demos.oxilab.dev/flipbox/template/`. Channel `https://www.youtube.com/@oxilabdev`.
