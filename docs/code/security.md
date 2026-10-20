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
| 3.1.1 (2026-10-20, not tagged yet) | Bad input crash: an empty, zero or text value in a calculated setting (width, height, animation duration; Style 1 also border and icon size) was a fatal 500 on PHP 8 for every visitor of the page. All 64 calculations in the 29 templates now go through `Public_Render::num()` / `percent()`. Tested with `security-tests/image-hover-effects-ultimate-visual-composer/empty-values-render.php`: before 87 of 87 renders fatal, after 0; payload `</style ><script>alert(1)</script>"><img src=x onerror=alert(1)>` in those settings never printed raw (the helpers return numbers only); the 16 saved local flip boxes byte identical (HTML and CSS hashes). No new input, handler, capability or nonce, so no role attacks apply |

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
