# Tutorial videos (YouTube, "how to use")

The method of the Flipbox getting started tutorial (4:55, 9 chapters, 2026-10-09), built from Richard's brief
("Image Hover er moto Flipbox er getting started video", 2 to 5 minutes) and his notes on the second cut; the Image
Hover v3 tutorial later followed the same rules. Read [video-common.md](video-common.md) first. Pipeline:
`/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/` (final files in `out/final/`).

## Richard's rules for tutorials (from his Flipbox notes)

1. **Follow a brand new user's order.** Never open with the Flip Box list (a new user has none): Dashboard, the menu
   flyout, Create New first; end with where flip boxes are kept (the list).
2. **Show what you say, at the word.** Click every tab the voice mentions (Front, Custom CSS) even with no change.
   The item's Edit button: click it, show the form, change nothing. General tab: open the settings (the lists).
3. **Hover is important.** Scroll the designs and hover several.
4. **Show the template library**: hover, Add to Create New, back to Create New, Use this design.
5. **Do it the way a user would.** Pick a color by dragging in the picker, never by typing. Copy the shortcode in the
   editor's Shortcode panel, not from the list.
6. **Mention every way to use it, then show the main ones**: say it works in any builder (Elementor, Divi,
   Gutenberg, WPBakery), use the Shortcode block, mention the text editor, then the Flipbox block, publish, the
   output.
7. Professional and beginner friendly; not too long, not too slow; end on a short thanks with no tail.

## The story (current cut)

| Time | Chapter | On screen |
| --- | --- | --- |
| 0:00 | Hook and introduction | The result first, then what the video covers |
| 0:16 | Find Flip Box | Dashboard, the Flip Box menu flyout |
| 0:27 | Preview the designs | Create New with live previews, hover several |
| 0:53 | Template library and create | Import Templates, Add to Create New, back, Use this design, the name dialog, Create and edit |
| 1:30 | Add your cards | Add a flip box: front image, back title, text, button and link; Item per Rows for three side by side |
| 2:30 | Style | General tab (Flip Trigger, Flip Type, Flip Effects), Backend tab back color by dragging |
| 3:13 | Add it to a page | Copy in the Shortcode panel, the Shortcode block, the Flipbox block, publish, the live page flipping |
| 4:24 | Your flip boxes | The Flip Box list |
| 4:43 | Wrap up | Short thanks |

Verified facts (true on 2026-10-16, check again before a new script):
- Shortcode `[oxilab_flip_box id="N"]`; the editor's Shortcode panel has copy buttons for it and for the PHP code.
- Block: "Flipbox" (`oxi-flip-box/flipbox`, Design category), "Choose a flip box", a live preview that flips on
  hover. Elementor widget: "Flipbox" with a "Shortcode" select. WPBakery: "Flip Boxes and Image Overlay" with an ID
  field. Widget: "Flipbox - Awesomes Flip Boxes Image Overlay" with one ID field (its "Style ID:" label never prints, `esc_html()` without echo). The Divi module is off; the
  shortcode works in Divi.
- The editor saves in the background with a short message ("Changes saved").
- Use a **free** template (Style 1 to 10) for the walkthrough; don't say which are free.

## Flipbox capture gotchas

- Every template's back face is placeholder copy, and Style 2 design 3 has "Buy Now": pick designs that pass the
  scanner, and allow template sample words only with a reason.
- A flip card's hidden back face is visible text to a DOM scan; `lib.mjs` skips it with a hit test.
- Native select lists: `fblib.openList()`; in the block editor iframe close the list in that frame.
- The Custom CSS field is a CodeMirror code editor, not a textarea.
- The Dashboard needs third-party widgets and Site Health hidden.
- `oxi_div_import` is shared with other plugins: touch only `type='flip'` rows.
- Flip box #23 "Travel Tours" is kept and is the newest, so the Elementor widget with no id on page 405 shows it.
- Site data tools: `scripts/db/fb-stage.php`, `fb-templates.php`, `fb-labels.php`, `fb-media.php`, `fb-cleanup.php`.

## Existing videos

| Video | Where |
| --- | --- |
| Getting started tutorial (4:55) | `out/final/flipbox-tutorial.mp4`, `.srt`, `flipbox-thumbnail.png`, `youtube.md` (title "How to Add Flip Boxes in WordPress (Beginner Guide)"), `QA.md`. The Getting Started page and the readme embed `youtu.be/Jj_8eL8nHPI` |

## Deliverables (`out/final/`)

`flipbox-tutorial.mp4`, `.srt`, `flipbox-thumbnail.png`, `youtube.md`, `QA.md`.
