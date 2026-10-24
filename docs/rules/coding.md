# Coding rules

How code in Flipbox is organised, how to change it safely, how to prove nothing broke, how admin screens look, and
how a release goes out. Read [00-general.md](00-general.md) first. The code reference is in [../code/](../code/).

## Structure

| Rule | Detail |
| --- | --- |
| Namespace | `OXI_FLIP_BOX_PLUGINS\` maps to the plugin root (Composer PSR-4, `vendor/autoload.php`). Folder and class names match: `Includes/Admin/Pages/Settings.php` is `OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages\Settings`. |
| Main file | `index.php`: Freemius init (`wpkin_fb_v()`), final class `Oxilab_Flipbox` (singleton `oxilab_flipbox()`, aliases `wpkin_flipbox()` and `WPKin_Flipbox` for old code), boot on `plugins_loaded`. |
| Admin pages | `Includes/Admin/Pages/` (Home = the Flip Box list, Create, Import, Settings, GettingStarted with its `Tabs/`). `Addons.php` is not in the menu. |
| Templates | One editor class `Inc/StyleN.php` (extends `Page\Admin_Render`) and one render class `Public_Render/StyleN.php` (extends `Page\Public_Render`) per template, N = 1 to 29; front CSS `asset/frontend/css/StyleN.css`; editor JS `asset/backend/js-files/styleN.js`. Ready designs live in `Page/JSON.php`. See [../code/templates.md](../code/templates.md). |
| Editor saves | The editor posts its forms to its own page (`Admin_Render::saving()`: style, item save, item edit, rename, clone, delete), each with its own nonce; `editor.js` sends those posts in the background and patches the preview. |
| AJAX | `wp_ajax_oxi_flip_box_data` → `Admin_helper::data_process()` (nonce `oxi-flip-box-editor`) → `new Classes\Admin_Ajax($functionname, ...)`, which calls the method of that name. A new action is a new public method there, with its own capability check inside. |
| Style data | A positional string in `oxi_div_style.css` ([00-general.md](00-general.md) rule 1). New settings are appended after the design's string and read by name (`Public_Render::flip_trigger()` is the pattern). Writers of `css`: the editor save, create, clone and import (copied verbatim, so appended keys travel). |
| Data | Tables `oxi_div_style`, `oxi_div_list`, `oxi_div_import`, shared with older Oxilab plugins, so every query filters `type = 'flip'` (fonts: `oxi-addons-flip`). A new option goes into `Classes/Data_Cleaner.php` (SETTINGS or OPTIONS). |
| Assets | Plain CSS and JS, no build step. Admin: `asset/backend/`; front end: `asset/frontend/`. Admin CSS per page in `Includes/Assets.php`; the editor's libraries in `Inc_Helper/CSS_JS_Loader.php`; front assets enqueued by `Public_Render::public_loader()` while a flip box renders. |
| Pro check | The filter `oxi-flip-box-plugin/pro_version` → `Admin_helper::check_current_tabs()` (legacy option `oxilab_flip_box_license_status` = `valid`, or `wpkin_fb_v()->can_use_premium_code()`); registered only in admin. Notices use `has_pro_access()` (filterable with `oxilab_flip_box_has_pro_access`). |
| Hex strings | `Admin_Filters()` and the license checks name hooks and options as hex (`fixed_data()`); decoded in [../code/hooks-shortcodes.md](../code/hooks-shortcodes.md). Write plain strings in new code. |

## Style

- Every new or changed line follows the WordPress Coding Standards in `phpcs.xml` and passes PHPCS (see PHPCS and
  WPCS validation below), also in older files written as `func($a)` without inner spaces: the lines you touch are
  written `func( $a )`, the lines you don't touch stay as they are (no mass reformatting).
- Comment density like the surrounding code: 3.1.0 code explains the "why" in short docblocks (see `Public_Render`'s
  load guard). No dashes in comments.
- New methods carry `@since` with the version that will ship them.
- Text domain `oxi-flip-box-plugin` (the widget still uses `image-hover-effects-ultimate-visual-composer`, leave it).

## Security checklist (every change)

Admin side:
- Capability: the plugin's role from Settings → Access (`oxi_addons_user_permission`, resolved to that role's first
  capability, default `manage_options`), checked in every `Admin_Ajax` method; settings and Danger zone methods need
  `manage_options`.
- Nonces: AJAX `oxi-flip-box-editor`; editor forms `oxiflipstylecss`, `oxiflipchildnonce`, `oxiflipeditdata`,
  `oxiflipclonedata`, `oxiflipdeletedata`, `oxi-addons-name-change`; import `oxilab-flipbox-import`; notices
  `oxilab_flip_notice_dissmiss`.
- Destructive actions confirm on the server too (Delete all data needs `confirm=DELETE`).

Every request:
- Sanitize input by type. Style values: `sanitize_text_field` / `sanitize_hex_color` per field, Custom CSS
  `sanitize_textarea_field`; item fields `sanitize_text_field`, URLs `sanitize_url`.
- Escape output at the point of output. Render classes read the style through `Public_Render::parse_styledata()`
  (`esc_attr` on every value) and item text through `text_render()` (`wp_kses_post`). Keep both.
- SQL only through `$wpdb->prepare` or `$wpdb->update`; table names from `$wpdb->prefix`.
- Never let a missing row or class cause a fatal error: check before `new $cls`.
- Never calculate with a raw saved value: use `$this->num()` and `$this->percent()` from `Public_Render`. On PHP 8
  one empty setting in plain math stopped a customer's whole page (2026-10-20, [../code/templates.md](../code/templates.md)).

Public (logged out):
- Shortcodes read a flip box by integer id; there is no public AJAX while the Divi module stays off (its
  `oxiflip_design_css` endpoint is `nopriv`; review it before switching Divi on).

Then record anything new or still open in [../code/security.md](../code/security.md).

## PHPCS and WPCS validation (mandatory, every code change)

Richard, 2026-10-16: "phpcs and wpcs validation korte hobe". Every PHP change passes PHP_CodeSniffer with the
WordPress Coding Standards before it is called done, together with the security test below.

**Tools.** PHPCS 3.13, WPCS 3.4 and PHPCompatibilityWP are installed once in
`/Users/richard/Local Sites/oxilab/tools/wpcs/`, outside the plugin: the plugin's `vendor/` folder is tracked in git
and ships, so never run `composer install` or `composer require` inside the plugin for dev tools. The checks use
this plugin's own `phpcs.xml` (WordPress, WordPress-Core, PHPCompatibilityWP for PHP 7.4+, the text domain, and the
sniffs the project turned off).

| Command | Use |
| --- | --- |
| `node "/Users/richard/Local Sites/oxilab/tools/wpcs/changed.mjs" "<plugin folder>"` | **The gate.** Checks only the lines changed since the last commit plus new PHP files in full. Must end with 0 errors and 0 warnings |
| `node ".../tools/wpcs/changed.mjs" "<plugin folder>" <tag>` | The same against a release tag, to check everything since that release |
| `".../tools/wpcs/check.sh" "<plugin folder>" <files>` | Full report for whole files |
| `".../tools/wpcs/check.sh" "<plugin folder>" --summary` | Totals for the whole plugin, to compare with the baseline |
| `".../tools/wpcs/check.sh" "<plugin folder>" --fix <files>` | phpcbf auto fix; only on files you are changing anyway, and review the diff line by line after (it may touch lines outside the task) |

**Rules.**
1. **Changed lines: 0 errors, 0 warnings** in `changed.mjs`. Fix the code; don't silence the sniff.
2. A `// phpcs:ignore <Sniff.Name> -- <reason>` only where the code is right and the sniff can't see it (for
   example a table name from `$wpdb->prefix`, or JSON written for a file download), always naming the sniff and
   the reason. Never a bare `phpcs:ignore` or `phpcs:disable` for a whole file, never one to hide a security sniff
   (`WordPress.Security.*`, `WordPress.DB.PreparedSQL*`) without the security test proving the line safe.
3. **The plugin total never goes up.** Run `--summary` before and after; compare with the baseline in
   `/Users/richard/Local Sites/oxilab/tools/wpcs/baseline-image-hover-effects-ultimate-visual-composer.txt`. Baseline on 2026-10-16: 7288 errors and 733 warnings in 86 files.
   Re-record the baseline after a release or a cleanup.
4. **PHPCompatibility errors are never accepted**: the plugin must run on PHP 7.4 to 8.4.
5. **No mass reformatting.** Old violations on lines you don't change stay until Richard asks for a cleanup; a
   cleanup is its own task with its own before and after proof (behavior identical, output byte for byte).
6. **JavaScript** has no linter set up: `node --check` every changed file and follow the WordPress JavaScript
   style of the file.
7. **Report** the `changed.mjs` result (files checked, 0 errors, 0 warnings) and the before and after totals in
   the reply, next to the security test results.

## Security test (mandatory, every code change)

Richard, 2026-10-16: "code lekhar shomoi obboshoi security maintain korte hobe and security test korte hobe, jate kore
kono doroner security break na hoi." Every change to PHP or JS, however small, keeps security and is **security
tested before it is called done**. No exceptions, also not for "just a UI change". A change that fails a check is not
finished: fix it first. If the fix is outside the task, stop and tell Richard; never ship a known hole.

### 1. Review the diff before testing

For every changed or added line, write down in the test notes:
- **Inputs**: `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_FILES`, AJAX and REST parameters, shortcode and block
  attributes, imported files, and database values that a lower role or a visitor can write.
- **Who can reach it**: logged-out visitor, subscriber, the plugin's own role (it can be set to Editor or Author),
  administrator.
- **Where it goes**: HTML, attribute, URL, JavaScript, CSS inside `<style>`, SQL, a file path, a class or method name,
  a redirect, an outgoing request.

### 2. Check every item (WordPress threat list)

| Threat | Rule |
| --- | --- |
| Missing permission | Every handler checks the capability; destructive or site-wide actions need `manage_options` |
| CSRF | A nonce on every state change, checked on the server |
| IDOR | A posted id must belong to the parent being edited and to this plugin (its own table, its own type) |
| SQL injection | Only `$wpdb->prepare`, `insert`, `update`, `delete`; never a variable inside SQL |
| Stored and reflected XSS | Escape at output for the context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses`); never echo raw input |
| DOM XSS | No `innerHTML`, `.html()` or `insertAdjacentHTML` with data that came from a user; use `textContent` or escape |
| CSS breakout | CSS printed in `<style>` writes every `</` as `<\/`; entity-decoded values are checked after decoding |
| Path traversal and files | No user value in a file path without `realpath` inside the plugin; uploads read from `tmp_name` after `is_uploaded_file`; never write into the plugin folder |
| Dynamic calls | A class or method name from a request only through an allow list |
| Object injection, code execution | No `unserialize`, `eval`, `assert`, `create_function` or variable `include` on user data |
| Open redirect, SSRF | `wp_safe_redirect`; outgoing requests only with `wp_safe_remote_*` to known hosts |
| Privilege escalation | A setting that grants access (the plugin's role) can only be changed by an administrator |
| Information leak | No paths, SQL errors, debug output or other users' data in responses; drafts, private and password-protected content stays hidden |
| Denial of service | Every count and page size from a request is clamped; no unbounded query |
| Bad input crash | Wrong type (array instead of string), missing key, unknown id, `-1`, huge numbers: a clean error, never a fatal 500 |
| Secrets | No key, token or license in code, logs, responses or commits |

### 3. Run real attacks against the local site

Write the test as a script in `/Users/richard/Local Sites/oxilab/security-tests/<plugin>/` (not only in the
scratchpad) and run it on `https://oxilab.test`:
- **As every role**: logged out, a subscriber, the plugin's role, an administrator. Create temporary users named
  "security test (delete me)" and delete them after (by id and name).
- **Without and with a wrong nonce**, and with another user's nonce.
- **Payloads** in every changed input: `<script>alert(1)</script>`, `"><img src=x onerror=alert(1)>`,
  `javascript:alert(1)`, `</style ><script>alert(1)</script>`, `&lt;/style &gt;&lt;script&gt;`, `' OR 1=1 -- `,
  `../../wp-config.php`, `-1`, `0`, `99999999`, an array where a string is expected, an unknown id, an id that
  belongs to another plugin or another parent.
- **Expected**: refused (403 or a clear error) or stored and printed safely escaped; check the saved row and the
  rendered page source, not only the response.

### 4. Static checks

- `php -l` on every changed PHP file, `node --check` on every changed JS file.
- The PHPCS and WPCS gate (section above) with 0 errors and 0 warnings on changed lines; the security sniffs
  (`WordPress.Security.*`, `WordPress.DB.PreparedSQL*`) are part of it and are never silenced without proof.
- Grep the diff for `echo`/`print` without escaping, superglobals without sanitizing, `->query(` without
  `prepare`, `unserialize`, `eval`, `file_put_contents`, `include` with a variable, `innerHTML`, `.html(`.

### 5. Re-test what was fixed before

Run the earlier attacks from [../code/security.md](../code/security.md) again, so an old hole never comes back.

### 6. Report

In the reply and in [../code/security.md](../code/security.md): a table of each check and attack with its result
(the command and the output for anything that failed), what was not tested and why. A security fix is proven by
running the attack against the old code and the new code (see Testing).

### Where to attack in this plugin

| Surface | Reached by |
| --- | --- |
| `oxi_flip_box_data` → `Admin_Ajax::<any method>` | The plugin's role (can be Editor or Author), nonce `oxi-flip-box-editor` (open item 1: method by name) |
| Editor form posts (style, item save, edit, clone, delete, rename) | The plugin's role, one nonce each; item ids must belong to the flip box (open item 2) |
| `Create New&styleid=` editor route | The plugin's role; ids of other plugins' rows (open item 3) |
| JSON import form (Flip Box page) | Administrators with `upload_files`; the file is attacker controlled |
| Style string printed in `<style>` and HTML (`parse_styledata`, `text_render`) | Anyone who can save or import a flip box; seen by every visitor |
| `[oxilab_flip_box id]`, the block (REST preview), Elementor, WPBakery, the widget | Authors who can add shortcodes or blocks |
| `oxiflip_design_css` (`nopriv`) | Only if the Divi module is switched on again |

## Testing: prove nothing broke

| Change | Method |
| --- | --- |
| Front-end output | Render every flip box (`do_shortcode('[oxilab_flip_box id="N"]')` for every `type='flip'` id) in a PHP bootstrap and hash the HTML plus the inline CSS queued on `flip-box-addons-style`. 3.1.0 changes were proven byte-identical on all 15 flip boxes this way. |
| Editor | Playwright: open the editor of one flip box per template you touch, save, compare the saved `css` and the item `files` with a backup; collect `pageerror` events. |
| Style string | For a new setting: an old row without the new key must render exactly as before (missing = old behaviour). |
| Builders | Elementor editor, the Flipbox block (ServerSideRender preview), the Shortcode block, a classic widget; Divi only if the module is switched on. |
| Admin screens | Playwright: load the page, click the buttons, collect `pageerror` events. |
| Security fix | Run the attack against the old code (put the original file back briefly, then restore and `cmp` it) and the new code; show both results. |

Rules:
- Back up any real row you will touch, from **before** any test, and diff key by key before restoring (an editor tab
  that stayed open once re-saved test CSS into #23 after a restore).
- Test on the real local site, logged in through `auth-cookies.php` (no passwords).
- `php -l` every PHP file you changed; `node --check` every JS file.
- Report results with numbers, and say plainly what was not tested.

## Admin UI design

Redesigned in 3.1.0 (Flip Box list, Create New, Import Templates, Settings, Account, the editor), same building blocks
as Image Hover with the `oxi-flip-` prefix.

| Token (CSS variable in `asset/backend/css/settings.css`, scoped to `.oxi-flip-settings`) | Value | Use |
| --- | --- | --- |
| `--flip-brand` | `#17bcb5` | Logo teal: borders, stars, tiles, focus ring. White text on it is only 2.4:1, so never use it for text or filled buttons |
| `--flip-brand-strong` | `#0d817c` | Text links and filled buttons (4.7:1) |
| `--flip-brand-soft` / `--flip-brand-ring` | `#e3f7f6` / `rgba(23,188,181,.28)` | Soft buttons, chips, focus ring |
| `--flip-success`, `--flip-danger` (+ `-soft`) | `#067647`, `#b42318` | Status, Delete and Danger zone |
| `--flip-ink`, `--flip-line`, `--flip-radius` | `#101828`, `#eaecf0`, `14px` | Text, borders, card corners |

- Pages use `oxi-flip-set-*` (hero, cards, switches, dialogs, status line); the editor's visual layer
  (`editor.css`) applies only under `body.oxi-flip-editor-page`.
- `admin.css` is old and global on the plugin pages and contains `.notice{display:none!important}` with no restoring
  rule, `.oxi-addons-wrapper a{...}` and `img{width:100%}`: scope new rules through your own class with enough
  specificity (the notices do this) instead of editing `admin.css`.
- In-page dialogs and a toast, never the browser's `confirm()`.
- Never restyle Freemius screens globally (Account is wrapped by `Includes/Admin/Account.php`).

## Divi (read before touching `Modules/Divi.php`)

The Divi module (`oxilab_flip_box_divi`) is **switched off**: `new Modules\Divi()` is commented out in `index.php`,
and the plain shortcode works inside Divi. `Modules/Divi.php` holds the full fix set from June 2026 (lazy shortcode
registration, the builder's preview paths, design CSS served as a `<link>` through `oxiflip_design_css`,
`type="text/css"` on links, a sanitized `image/divi-icon.svg`); the history is in Claude's memory
(`project_flipbox_plugin.md`). The shared part stays active: `Public_Render::is_builder_context()` prints the design
CSS inline in Elementor, Divi and block previews so it travels with the extracted markup.

## Known issues to keep in mind

- **Free plan gates run in the browser only.** Template library "Pro only" (Style 11 to 29) and the editor's color,
  font and Custom CSS reset are client-side; the server accepts them. A free user's saved Custom CSS is emptied on
  any save (pre-existing).
- **Custom CSS is HTML-escaped on output** (`parse_styledata()` runs `esc_attr`), so CSS with `>` or quotes is
  likely printed as `&gt;` / `&quot;` and breaks. Read from the code on 2026-10-16, not tested in a browser.
- `plugin_upgrade_hook()` checks `$options['plugins']['oxi-flip-box-plugin']`, a key that never exists, so the
  table update never runs on plugin updates (activation still runs it).
- `asset/backend/js-files/` has `style1.js` to `style28.js`; the editor enqueues `style29.js`, which is missing
  (404 on the Style 29 editor).
- An empty `[oxilab_flip_box]` or a deleted id prints nothing (no message for admins).
- `Includes/Frontend.php` and `Includes/Admin/Notice.php` are empty; `Pages/Addons.php` is not in the menu;
  `admin_recommended()` returns at once (the old recommendations API is gone).
- Readme says "Requires at least: 4.4" and "Requires PHP: 7.0", the code's constants say WordPress 6.2 and PHP 7.4.

## Release

1. **Never bump the version unless Richard asks** ([00-general.md](00-general.md) rule 6). Released versions have a
   git tag (`v3.1.1` is the newest, released 2026-10-21 with the PHP 8 empty value fix, the colored WPBakery and Elementor icons, the new FAQ and 12 screenshots).
2. The version lives in three places: the `Version:` header and `const VERSION` in `index.php`, and `Stable tag:` in
   `readme.txt`. `asset/blocks/flipbox/block.json` has its own `version` (3.1.0).
3. Changelog lines go in the top section of `readme.txt` (format in [00-general.md](00-general.md) rule 6).
4. Richard commits on `dev` and pushes a tag. `.github/workflows/deploy-org.yml` (10up deploy action, slug
   `image-hover-effects-ultimate-visual-composer`) ships to WordPress.org SVN, including `.wordpress-org/`.
   `update-assets.yml` listens to a `trunk` branch this repo doesn't have. Claude never commits, tags or pushes
   unless asked.
5. What ships is decided by `.gitattributes` `export-ignore` (`docs/`, `CLAUDE.md`, `.wordpress-org/`, `.github/`,
   `composer.*`, `phpcs.xml`, `phpunit.xml`). `vendor/` ships (Freemius).
6. Readme rules: the WordPress.org readme audit guide in `/Users/richard/Local Sites/oxilab/SKILL.md` (no
   superlatives; at most 5 tags; plugin name only in install step 1). The current readme has "Best Flip Box Plugins",
   "most easiest", "one of the best", and the old minimums: fix them when Richard asks for a readme pass.
7. Screenshots and their captions: [screenshots-wporg.md](screenshots-wporg.md).
