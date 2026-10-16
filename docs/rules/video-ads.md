# Ad videos: Facebook and Instagram, TikTok, Google Ads (YouTube)

No Flipbox ad has been made yet. This file combines Richard's video rules ([video-common.md](video-common.md), which
all apply) with each platform's format. **On the first ad, confirm the open points marked "Ask Richard", then record
his answers here.** Platform specs change: check the current safe zones and lengths in each ads manager before export.

## What an ad must do

Stop the scroll in the first seconds, show flip boxes working on real output, and send people to one place. A card
turning over is a strong first second for a vertical ad.

| Time | Beat | Notes |
| --- | --- | --- |
| 0 to 2 s | Hook | Movement plus a short line: a row of cards flipping one after another |
| 2 to 8 s | Show | Pick a design, add an image and a button, the result on a page |
| 8 to 12 s | Proof | Something true and checkable: 29 templates, works with the block editor and Elementor, the WordPress.org page |
| last 2 to 3 s | Call to action | Logo, name, one action ("Try it free on WordPress.org" or "Get Flipbox"), the site |

## Rules for every ad

- **Sound off by default**: every spoken line is also burned-in captions inside the safe zone.
- **Real UI and output only**, privacy rules as in every video. No mock-ups the plugin can't produce.
- **Claims**: only what is true and shown; no superlatives (the readme's "Best" and "most easiest" never go into an
  ad), no invented numbers, no "50+ effects", no fake reviews. Filter ad copy with the banned word list in
  `/Users/richard/Local Sites/oxilab/SKILL.md`.
- **No Free/Pro or prices** in the video. "Try it free" in the call to action only if Richard approves it for that
  campaign (Ask Richard).
- One message per ad; 2 or 3 hook variants for testing.
- Brand: teal accents, `#0D817C` behind white text, logo at the end; text never covers the flip box being shown.
- English, international, no dashes.

## Formats

| Platform | Placement | Ratio and size | Length | Notes |
| --- | --- | --- | --- | --- |
| Facebook, Instagram | Reels, Stories | 9:16, 1080 x 1920 | 15 s main, 6 s and 30 s variants | Keep text and key UI out of roughly the top 14% and bottom 35% |
| Facebook, Instagram | Feed | 4:5, 1080 x 1350 (or 1:1) | 15 s | Same edit reframed |
| TikTok | In-feed | 9:16, 1080 x 1920 | 9 to 30 s, 15 s main | Clear of the right edge and the bottom caption area |
| Google Ads | YouTube skippable in-stream | 16:9, 1920 x 1080 | 15 to 30 s | Flip and brand visible before 5 s |
| Google Ads | YouTube bumper | 16:9 | 6 s, not skippable | One idea, one flip, logo |
| Google Ads | YouTube Shorts and Demand Gen | 9:16 (and 1:1) | 15 to 30 s | Reuse the vertical cut |

## Production

- Project folder: `/Users/richard/Local Sites/oxilab/flipbox-ads-video/`, compositions per ratio from one timeline.
- Voice: Jesse `sXcl1NoQHcU7NRh6Na5D` unless Richard picks another for ads (Ask Richard). Music: an upbeat
  ElevenLabs take shaped to the cut.
- Captions burned in; also export the `.srt`.
- First frame: a card mid flip.
- Export: H.264, 30 or 60 fps, AAC, -14 LUFS, under each platform's size limit.

## Landing page and tracking

- Where the ad sends people: **Ask Richard** per campaign (the WordPress.org page,
  `https://oxilab.dev/flipbox/pricing/` or `https://demos.oxilab.dev/flipbox/template/`).
- UTM parameters on ad links are fine (the WordPress.org tracking rule applies to links inside the plugin).

## Deliverables

`out/ads/<campaign>/`: one MP4 per platform and ratio (`flipbox-<campaign>-<hook>-9x16.mp4`, `-4x5`, `-16x9`,
`-6s`), captions, `copy.md` (primary text, headline, button per platform, no superlatives) and `QA.md`.
