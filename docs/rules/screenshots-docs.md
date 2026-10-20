# Screenshots for docs (oxilab.dev)

Screenshots inside the Flipbox docs at `oxilab.dev/docs/flipbox/`. The rules come from the Shop Manager docs
(Richard's corrections on docs 503 and 514) and the Image Hover and Product Layouts WordPress.org sets.

## The rules

1. **The mark matches the text exactly.** A highlight box covers every element the paragraph names, built as the
   union of their boxes, not just the first one.
2. **No mark beats a wrong mark.** A screenshot that shows a whole screen for an overview has no mark at all.
3. **Arrows only for one small control** the reader has to hunt for (a single button or icon).
4. **The screenshot proves the UI, the code proves the behaviour.** Check both before writing the step
   ([00-general.md](00-general.md) rule 3).
5. **Current UI only.** The 33 live Flipbox docs have no screenshots at all (checked 2026-10-16); every doc that is
   rewritten gets new ones of the 3.1.0 interface.
6. **Show the flip.** When the step is about how a flip box looks, capture it mid flip or with the back face showing
   (hover, or click for "On Click" boxes), so the reader sees both sides.
7. **Privacy and real data** exactly as in [00-general.md](00-general.md) rules 9 and 10. No license key, not even
   masked. No placeholder copy in a flip box you capture.
8. **No dashes** in alt text, captions or marks.

## Format

| Part | Spec |
| --- | --- |
| Capture | Playwright at device scale 2, logged in with `auth-cookies.php`, wp-admin cleaned (no admin bar, no notices, no other plugins' menus, no count badges) |
| Crop | Only the part the step is about (an element screenshot of the card, dialog or panel plus 24 px of context). Show the wp-admin sidebar only for "find the menu" steps |
| Mark | Amber ring `#ffb020`, 4 px, 10 px outside the elements, 12 px corners, light glow; arrow in the same amber (stands out from the teal UI). **Ask Richard** on the first Flipbox doc whether he wants amber or another color, then record it here |
| File | WebP, sharp at 2x, lower case, named `flipbox-<topic>-<step>.webp` (example `flipbox-flip-trigger-2.webp`) |
| Alt text | What the image shows, in plain words; the doc's focus keyword in at least one alt |
| Caption | Optional; when used, it says what to look at, in one short sentence |

## Where the images go: Cloudflare R2

Doc images live on **Cloudflare R2** and load from the CDN, never from the WordPress Media Library.

| Part | Rule |
| --- | --- |
| Public URL | `https://cdn.oxilab.dev/flipbox/docs/<file>.webp` (the `flipbox/` folder already holds the template sample images such as `flipbox/dog.jpg`, so doc images go in `docs/` below it) |
| Local mirror | `/Users/richard/Local Sites/oxilab/r2-uploads/flipbox/docs/` (same file name as the R2 key) |
| Who uploads | **Richard uploads to R2 himself.** Save the file in the mirror folder, write the CDN URL into the doc, and tell him which files to upload |
| Never | Upload doc images with `upload_media` or any other way into WP Media |
| Replace an image | Save the new file under the **same** name and tell Richard; no doc edit is needed |

## Pipeline

Reuse the capture code of `/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/` (`scripts/rec/`: logged-in
context, admin cleanup, privacy scan with the back-face hit test, `fblib.openList()` for native select lists). Add a
`scripts/docs/docs.mjs` there that captures element crops, draws the union mark, and writes WebP to
`out/docs/<doc-slug>/`. Keep it in that project, never only in the scratchpad.

## Checklist per doc

1. List the steps of the doc and decide which ones need a picture (one per step where the screen changes).
2. Capture, mark, export; look at every image next to its paragraph.
3. Show Richard the set with the doc draft.
4. Save the files in `r2-uploads/flipbox/docs/`, put their CDN URLs and alt text in the doc, and give Richard the
   list of files to upload to R2.

## Background: no grid, no squares (Richard, 2026-10-17)

The stage, frame or canvas behind a screenshot is a plain color or a smooth gradient with soft glows only: **no grid,
squares, lines, dots or any repeated pattern**, not even faint. The same rule covers videos, thumbnails and blog
images. On 2026-10-17 the pipelines `image-hover-tutorial-video/scripts/wporg/frame.mjs` and
`wporg-screenshots/product-layouts/frame.mjs` still draw a 58 px grid ("so the flat areas of the gradient are not
dead space"): remove that layer before the next build, and never copy it into a new pipeline. The installed
WordPress.org sets of Shop Manager, Image Hover and Product Layouts still show a grid until they are rebuilt.

- **No version numbers** (Richard, 2026-10-17): hide every plugin and WordPress version on screen and never say one; see [00-general.md](00-general.md) rule 18.
