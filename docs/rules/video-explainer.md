# Explainer and launch videos ("what it is, why you want it")

No Flipbox explainer exists yet. The method is the Shop Manager launch video (1:16, 16:9 and 9:16, delivered
2026-10-04) and its feature tour (3:10), both approved by Richard. Read [video-common.md](video-common.md) first.
Pipeline to copy: `/Users/richard/Local Sites/oxilab/shop-manager-launch-video/` (its `README.md` has every command).

## Two formats

| Format | Length | Shape |
| --- | --- | --- |
| Launch / explainer | 60 to 90 s (under 1:30 so the Reels cut fits), every platform format ([video-common.md](video-common.md), Platforms and formats) | Problem, reveal, 3 to 5 feature beats, outcome, call to action |
| Feature tour | 2:30 to 3:10, 16:9 | The main features with their use, calmer pace, chapters |

**Hard limit (Richard, 2026-10-16): every explainer video is under 5 minutes**, the final render included (end card
and outro). The lengths above are the targets; if the script grows, cut beats or tighten gaps until the timeline is
4:59 or less. Check the length after building the timeline and again on the final file; never speed the voice above
1.1 to make it fit.

## Story for Flipbox (draft to adapt)

1. **Hook and problem** (hook in 5 s, problem about 10 s): info boxes show everything at once and get skipped.
   Open on plain boxes, not on a logo.
2. **Reveal**: Flipbox; a card turning over to its back with a button.
3. **Beats** (real UI, a few seconds each): 29 templates with live previews; front image or icon, back text and a
   button; flip on hover or on click; colors, fonts and spacing in the editor; the Flipbox block, Elementor widget
   and shortcode; works on phones (tap to flip).
4. **Outcome**: a services or team section that invites a hover.
5. **Call to action**: "Get Flipbox" plus the WordPress.org page, on the closing screen.

Every beat is checked in the code first. Pro templates and controls appear as plain features.

## How it is built (Shop Manager pipeline)

- Script lines in `content/script.json`; Richard approves the script first.
- `tts.mjs` → `vo-measure.mjs` → `build-timeline.mjs` (with `content/pacing.json`) → `content/timeline.json`.
- Music: an ElevenLabs Music take shaped in post by `shape-music.mjs`.
- Sound effects from ElevenLabs; a click sound on every click.
- Captures with every write request **blocked**; never save data while recording.
- Flips filmed for real with the frame-stepped capture of `flipbox-tutorial-video/scripts/rec/`; never fake a flip
  in Remotion.
- Headlines: words between `*stars*` get the brand highlight (`#0D817C`); text never on top of the UI.
- Render 16:9 and 9:16 from one timeline, -14 LUFS, plus an `.srt`.
- Review storyboard stills and click-check stills before the final render.

## Richard's corrections from the Shop Manager videos

No Free/Pro or prices; don't read out page addresses; real data only (never the templates' Lorem copy); privacy
scan; slower pace for the tour; no clipped descenders; one accent color only; no decorative dash before a label;
every wp-admin notice and count badge removed.

## Deliverables

`out/final/`: `flipbox-launch.mp4` (1920 x 1080), `flipbox-launch-9x16.mp4` (1080 x 1920), `.srt`, `voiceover/`;
for a tour `out/tour/final/`. YouTube pack in `youtube/youtube-upload.md` (uploads: tour, launch, Short; titles with
2 alternatives; descriptions with chapters and links; tags; pinned comment; thumbnails 1280 x 720).
