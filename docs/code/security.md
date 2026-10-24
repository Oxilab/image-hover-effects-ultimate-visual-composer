# Security model, history and open items

The checklist for new code is in [../rules/coding.md](../rules/coding.md) (Security checklist).

## Model

| Area | Protection |
| --- | --- |
| Admin screens | Menu capability = the first capability of the role in Settings → Access (default `manage_options`) |
| Admin AJAX | Nonce `oxi-flip-box-editor`, then each `Admin_Ajax` method checks the plugin role; settings, import and Danger zone need `manage_options` |
| Editor | Form posts with one nonce per action ([hooks-shortcodes.md](hooks-shortcodes.md)) on a page that needs the plugin role |
| Saved values | Sanitized per field on save; printed through `parse_styledata()` (`esc_attr`), `text_render()` (`wp_kses_post`), `esc_url` |
| CSS output | The design CSS is built from `esc_attr`-escaped values, so `<` can't reach the `<style>` element as a tag |
| SQL | `$wpdb->prepare` / `update` |
| Front end | Shortcodes read a flip box by integer id; no public AJAX while Divi is off |
| Uninstall | Removes data only on opt in; never drops the shared tables |
| Notices | Upgrade notice only on the plugin's pages, no tracking parameters |

## History

| Version | Change |
| --- | --- |
| 3.1.0 | Danger zone with server-side `confirm=DELETE` and `manage_options`; uninstall on opt in only; "Remove from list" deletes only Flipbox's own `oxi_div_import` row; item Clone checks the item belongs to the flip box; Custom CSS saved with `sanitize_textarea_field` |
| 3.1.1 (released, tag v3.1.1) | Bad input crash: an empty, zero or text value in a calculated setting (width, height, animation duration; Style 1 also border and icon size) was a fatal 500 on PHP 8 for every visitor of the page. All 64 calculations in the 29 templates now go through `Public_Render::num()` / `percent()`. Tested with `security-tests/image-hover-effects-ultimate-visual-composer/empty-values-render.php`: before 87 of 87 renders fatal, after 0; payload `</style ><script>alert(1)</script>"><img src=x onerror=alert(1)>` in those settings never printed raw (the helpers return numbers only); the 16 saved local flip boxes byte identical (HTML and CSS hashes). No new input, handler, capability or nonce, so no role attacks apply |

| 3.1.2 (not tagged yet) | CVE-2026-109973 (Wordfence, CVSS 6.4): stored XSS through the image alt text, which Styles 18, 19, 20, 22, 25 and 29 saved with `sanitize_text_field()` (keeps `"`) and all seven alt templates (also Style 1) printed raw into `alt="..."`. Fixed on output (`esc_attr()` in `Public_Render/Style{1,18,19,20,22,25,29}.php`) and on save (`admin_special_charecter()` with `isset`/`wp_unslash`, as Style 1 already did). Also: `Admin_Render::saving()` checks `editor_capability()` itself; design CSS writes `</` as `<\/` in `Public_Render::hooks()` so a stored value (for example from `create_flip` or the JSON import, which store `files` and `css` unchanged) can not end the `<style>`; Style 25's card link uses `esc_url()`. Tests: `security-tests/image-hover-effects-ultimate-visual-composer/alt-escaping.php` (7 templates, link, CSS: 0 failures) and `alt-save-encoding.php` (7 editors: 0 failures); the 16 saved local flip boxes byte identical before and after |

## Open items (found while writing these docs, 2026-10-16, by reading the code; nothing was run or changed)

1. **`Admin_Ajax` calls any method by name.** `data_process()` passes `functionname` straight to
   `$this->$type(...)`, which can reach any method of the class, private ones included. Every method that writes
   checks permission, so nothing harmful was found, but an allow list of the AJAX method names would close it.
2. **Delete item by any id.** `Admin_Render::Delete_child_data()` deletes the posted item id without checking it
   belongs to the flip box being edited (Clone does check). A user with the plugin role could delete items of other
   flip boxes; they could do that from those flip boxes anyway, so it is low.
3. **Editor route has no type check.** `Menu::Flip_Create()` opens `Inc\StyleN` for any row id in `oxi_div_style`,
   also rows of other Oxilab plugins, and saving would rewrite them. Suggested: require `type = 'flip'`.
4. **Free plan gates are client-side** (template library "Pro only", the editor's color, font and Custom CSS reset).
   Not a security hole, a license one.
5. The review notice dismissal (`oxilab_flip_notice_dissmiss`) checks the nonce but not the capability (low).
6. If the Divi module is switched on again, review `oxiflip_design_css` (it is `nopriv` and renders any flip box's
   design CSS by id).
