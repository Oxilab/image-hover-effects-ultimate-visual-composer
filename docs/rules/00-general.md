# General rules (read first, every task)

These come from Richard's own corrections across the Flipbox, Image Hover, Product Layouts, Shop Manager and
ThankRedirect work. They apply to every job: code, docs, screenshots, videos and replies.

## 1. Never break a site

Flipbox runs on many live sites, and its shortcodes sit inside saved pages built with every page builder. Richard:
"if you break anything, every user loses money in their business." So:

- Change only what the task needs. Keep markup, CSS class names, field names, option names, AJAX function names and
  saved data formats as they are unless the task is to change them.
- **The style string is positional.** Each flip box's settings are one string read by position
  (`explode('|')`, `$styledata[N]`). Never insert or reorder a value: append new settings at the end and read them
  by name, as Flip Trigger does ([../code/templates.md](../code/templates.md)).
- Prove nothing broke: record the behaviour **before** the change, make the change, record it **after**, compare.
  The methods are in [coding.md](coding.md) (Testing).
- If a test is red, say so with the output. Never hide or work around a failing check (see rule 11).

## 2. Security in every change, tested every time

Every change keeps or improves security, and **every code change is security tested before it is called done**
(Richard, 2026-10-16: no security break of any kind). The checklist and the mandatory test are in
[coding.md](coding.md) (Security checklist, Security test); the history and open items are in
[../code/security.md](../code/security.md). Report any security issue you notice, even when it is not the task.

## 3. Verify before you state anything

Before writing **any** claim about how the plugin behaves (in a doc, a video script, a caption, a support reply, a
changelog line), check it in the code or by driving the real UI. Never infer it from an old doc, from the readme's
marketing lines, from how WordPress usually works, or from what seems likely. Inherited text is not verified text.
If it can't be verified, leave it out and say so. A wrong claim "breaks the plugin for the user and destroys trust".

Numbers: the code has 29 templates (Style 1 to 29) with 87 ready designs ([../code/templates.md](../code/templates.md)).
The readme's "more than 50 effects" and the Getting Started page's "50+ Hover & Flip Effects" are not counted in the
code; don't repeat them.

## 4. No dashes

Never write the em dash or the en dash anywhere: replies, docs, readme, captions, video text, code comments, commit
messages, these files. Use a colon for "label: explanation" and a comma for an aside. In videos and graphics, no
decorative dash or short line before a label. Hyphens inside words (`wp-admin`, `flip-box`) and arrows in paths
(`Flip Box → Create New`) are fine. Real UI in a screenshot stays as it is, and so does the plugin's official name
on WordPress.org.

## 5. English, international

Every output (docs, videos, thumbnails, ads, YouTube copy, readme) is in English for a global audience. Richard
chats in Banglish; that says nothing about the audience. Never offer Bangla or Bangladesh-specific versions.

## 6. Versions and changelog

- Never bump the version (plugin header and `const VERSION` in `index.php`, readme `Stable tag`) unless Richard asks.
- Add changelog lines to the **top** version section in `readme.txt` only while that version is unreleased. A version
  is released when its git tag exists (`git -C <plugin> tag`); 3.1.1 is released. If the top section is released,
  ask Richard before starting a new one.
- Changelog line format of this plugin (see the 3.1.0 section): `*` followed directly by one or two sentences a site
  owner understands, starting with the change ("*New Flip Trigger option ...", "*Fixed flip boxes flashing ...",
  "*Redesigned the Settings page ..."), no dashes.
- `@since` tags match the version that will ship the change.

## 7. One step at a time, Richard approves

- For any set (screenshots, docs, videos): make **one**, show it, wait for Richard's word, then do the rest.
- Docs: understand, plan, then one page at a time: brief, Richard says YES, write, publish only when told
  ([docs-publishing-seo.md](docs-publishing-seo.md)).
- Nothing reaches the plugin's `.wordpress-org/` folder, the live site, WordPress.org or YouTube before he approves.

## 8. Outward actions need a clear go

Publishing a doc, writing through the Rank Math or WordPress MCP to the live site, pushing to git, uploading
anything, sending anything: only when Richard asked for that action in this task. Show the diff or the content first.
Reading the live site is fine.

## 9. Privacy in every visual

No admin name, email, avatar, phone number, license key (not even the masked part on the Settings page), API key or
real customer data in any screenshot, video or doc image. Capture scripts scan every frame and **fail** on a match
(identity list from the WordPress users, matched as whole words). Remove personal lines at capture. The Media
Library on the test site holds personal photos: show only the tutorial images.

## 10. Real data only

Before any capture, check every screen for test data (WTEST, DEMO, "delete me", load-test names) and placeholder
copy, and replace it with realistic data first. Tell Richard what changed and keep an undo file. Every template's
sample content is placeholder copy (Lorem Ipsum, "Far far away", "Vokalia", "Consectetuer", "WooCommerce" titles,
"Buy Now" on Style 2 design 3): on template pages it may show with a written reason; in a flip box you capture,
replace it. The kept example flip box is #23 "Travel Tours" (the newest, so the Elementor widget with no id on page
405 shows it); check it still exists.

## 11. Never game a score

Never raise a metric by writing to the thing it measures (an SEO score, a test, a scanner). Fix the real problem for
the reader or the user; if a check can't pass honestly, leave it red and say why.

## 12. Free and Pro

- In the plugin UI and in docs, label Pro features honestly. Free: templates Style 1 to 10; Style 11 to 29 are "Pro
  only" in the template library. In the free editor the color fields, font fields and Custom CSS are marked Pro and
  reset on save (the inline gate in `Page/Admin_Render.php`). Details in [../code/templates.md](../code/templates.md).
- In videos, thumbnails and ads, never explain Free versus Pro, plans or prices. Pro features are shown as plain
  features.
- The local site runs Pro, so nothing looks locked there. Never judge what is free from the local site; check the
  code.

## 13. Temporary data

Anything created for a test or a capture is named so it can be found ("EDITOR TEST (delete me)"), recorded in a
state file, and removed afterwards by id **and** name. Back up any real row before changing it and confirm it is
identical afterwards. The tables are shared with older Oxilab plugins: touch only `type = 'flip'` rows (and
`oxi-addons-flip` font rows). A test flip box becomes the newest, so it shows in the Elementor widget on page 405
while it exists.

## 14. Tooling habits

- One plain Bash command per call, absolute paths, no `cd ... &&` chains (they trigger permission popups). Put
  multi-step logic in a script file and run it.
- Pipelines and tools live in a project folder, never only in the session scratchpad (it gets cleared; the first Shop
  Manager screenshot pipeline was lost that way).
- Secrets live in `/Users/richard/Local Sites/oxilab/oxilab-mcp-server/.env` (moved there by Richard, 2026-10-16):
  `elevenlabdapi` (ElevenLabs), `claudeflareapi` (Cloudflare), plus the WordPress app password. Scripts read a value
  at runtime; never print, copy or commit them, and list only variable names when checking the file. The old
  `elevenlabdapi.txt` is gone, but `flipbox-tutorial-video/scripts/el-key.mjs` still reads it: point it at the `.env`
  value before the next voice job. The `claudeflareapi` token was rejected as invalid on 2026-10-16: Cloudflare
  changes go through Richard's dashboard until he adds a new token.

## 15. The local site

| What | Value |
| --- | --- |
| URL | `https://oxilab.test` (wp-admin user id 1) |
| PHP | `/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php` |
| MySQL socket | `/Users/richard/Library/Application Support/Local/run/jBC48hM3U/mysql/mysqld.sock` (exists only while the site runs in Local) |
| DB | `local` / `root` / `root`, table prefix `wp_` |
| Run a WordPress script | `php -d memory_limit=1024M -d mysqli.default_socket=<socket> script.php` (the script sets `$_SERVER['HTTP_HOST']='oxilab.test'` and requires `wp-load.php`) |
| Playwright | `/Users/richard/Local Sites/oxilab/flipbox-tutorial-video/node_modules/playwright` (load with `createRequire`) |
| Login for Playwright | `php ... /Users/richard/Local Sites/oxilab/flipbox-tutorial-video/scripts/db/auth-cookies.php <scratchpad>/cookies.json` |
| No wp-cli | use the PHP bootstrap above |

If "Error establishing a database connection" appears, the site is stopped in Local: ask Richard to start it. Front
end pages run on Astra; the site also has Divi 5 installed (see [coding.md](coding.md), Divi).

## 16. Support replies

- Read the code for the exact behaviour before answering (rule 3). Name the real menu path and button labels
  (Flip Box → Flip Box, Create New, Import Templates, Getting Started, Settings).
- Plain, friendly English; short paragraphs; sign as Richard.
- If the answer is "it's a bug", fix it first, then reply that it is fixed in the next update.
- Never promise a feature or date Richard hasn't confirmed.

## 17. Keep these rules alive

When Richard corrects something or sets a new rule, update the matching file here in the same session, with the
reason. If it applies to all Oxilab plugins, also save it to Claude's memory and to the other plugins' `docs/rules/`.

## 18. No version numbers in screenshots and videos

Richard, 2026-10-17: keep the plugin version out of every screenshot and video, so a new release never makes them
look old and nothing has to be captured again just because the version changed.

- Hide every version number on screen at capture time: the plugin's own version labels (menu footers such as
  "Version 2.1.1 · Pro", headers, about boxes, badges), the Plugins screen row, update notices and WordPress' own
  footer version. Hide them with the capture script (CSS or a DOM change in the browser), never by editing the
  plugin.
- Don't capture a changelog or "What's new" screen; it is a list of versions.
- Never write a version number in a title, caption, voice over, on-screen text, thumbnail or anything else the
  viewer sees.
- Capture scripts fail a shot when the visible text still holds the current version or "Version" followed by a
  number, the same way they fail on personal data.
- This is the one exception to "real UI stays as it is": the version is removed, everything else is shown as it is.
