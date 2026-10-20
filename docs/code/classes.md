# Classes and traits

Checked against the code on 2026-10-16. Templates are in [templates.md](templates.md).

## Classes\Admin_Ajax

Created by `Admin_helper::data_process()` (action `wp_ajax_oxi_flip_box_data`, nonce `oxi-flip-box-editor`, GET or
POST) as `new Admin_Ajax($functionname, $rawdata, $styleid, $childid)`; the constructor checks the plugin role, sets
the table names and calls `$this->$functionname(...)`. Any method name can be requested, so every method checks
permission itself.

| Method | Does | Permission |
| --- | --- | --- |
| `create_flip` | With a `styleid`: clone that flip box under the new name. Without: create from a design JSON (`plugin` must be `flipbox`). Echoes the editor URL | Plugin role |
| `shortcode_delete` | Delete a flip box and its items | Plugin role |
| `get_shortcode_export` | Stream `flipbox-template-<id>.json` (`style`, `child`, `plugin: flipbox`) | Plugin role |
| `post_json_import` | Insert an imported file as a new flip box (called from the Flip Box page's upload form) | `manage_options` |
| `shortcode_active` / `shortcode_deactive` | Add a template to / remove it from the Create New list (`oxi_div_import`, `type='flip'` only) | Plugin role |
| `addons_rearrange` | Reorder items by re-inserting rows | Plugin role |
| `oxi_addons_user_permission`, `oxi_addons_font_awesome`, `oxi_addons_google_font`, `oxi_flipbox_support_massage`, `oxi_addons_pre_loader` | Save one setting | `manage_options` |
| `oxi_flipbox_delete_data_on_uninstall` | Danger zone switch | `manage_options` |
| `oxi_flipbox_delete_all_data` | Delete all data, needs `{"confirm":"DELETE"}` | `manage_options` |
| `check_user_permission()` | The role's first capability, default `manage_options` (no cache) | |

## Classes\Data_Cleaner

`UNINSTALL_OPTION` (`oxi_flipbox_delete_data_on_uninstall`), `IMPORT_TYPES` (`flip`, `oxi-addons-flip`), `SETTINGS`,
`OPTIONS`, `SHARED_OPTIONS`, `LICENSE_OPTIONS`, `TRANSIENTS`. `counts()`, `delete_now()` (deletes the rows, then
`Installation::Flip_Datatase()` seeds the five default templates again), `uninstall()` (per site, only with the
opt in). Tables are never dropped: they are shared with older Oxilab plugins. The shared options stay while another
Oxilab plugin is installed.

## Classes\Docs, Support_Reviews, Support_Upgrade

- `Docs::links()`, `nav_item()`: the "How to use?" dropdown in the plugin menu bar (`howto-menu.css`).
- `Support_Reviews`: shown 7 days after `oxilab_flip_box_activation_date` until `oxilab_flip_box_nobug`; AJAX
  `oxilab_flip_notice_dissmiss` (nonce only).
- `Support_Upgrade`: free sites, 10 days after `oxilab_flip_box_upgrade_date`, only on the plugin's pages; AJAX
  `oxilab_flip_upgrade_dissmiss` (nonce and `manage_options`). Never together with the review notice.

## Inc_Helper\Admin_helper (trait of the main class)

`data_process()`, `verify_request_nonce()`, `User_Reviews()`, `admin_notice()`, `admin_upgrade_notice()`,
`has_pro_access()`, `upgrade_notice_date()`, `installation_date()`, `oxilab_admin_menu()` (menu bar: Flip Box,
Create New, Import Templates, "How to use?"; Upgrade on free sites, Demos, Docs, Support, Settings icon),
`check_current_tabs()` (Pro), `fixed_data()` (hex), `welcome_remove_menus()`.

## Inc_Helper\Public_Helper, CSS_JS_Loader, Sanitization

- `Public_Helper::shortcode_render($id, $user)`: loads the row and items, runs `Public_Render\StyleN`; prints
  nothing for a missing id.
- `CSS_JS_Loader`: `admin_css()` (Bootstrap, Font Awesome, `admin.css`, Bree Serif and Source Sans Pro from Google),
  `admin_js()` (Popper, Bootstrap), `admin_home()` (jQuery UI, DataTables), `admin_elements_frontend_loader()`
  (minicolors, icon picker, fontselect, growl, `vendor.js`, media).
- `Sanitization`: the editor's form controls; the shared General tab fields (Flip Trigger, Flip Type, Flip Effects).

## Includes\Assets, Admin, Admin\Menu, Admin\Account

`Assets`: admin CSS by `$_GET['page']`, `oxi-flip-editor-page` body class, Elementor editor styles and a Backbone
patch. `Menu::Flip_Create()` routes Create New: with a `styleid` it runs `Inc\StyleN` for that row (no type check),
otherwise `Pages\Create`. `Account` wraps the Freemius Account template.

## Includes\Installation

`Flip_Datatase()` creates `oxi_div_style`, `oxi_div_list`, `oxi_div_import` (`dbDelta`), adds
`oxilab_flip_box_version`, and seeds Create New with Style 1 to 5 if no `flip` row exists.

## Page\Admin_Render

Constructor: tables, `oxiid` from `$_GET['styleid']`, the nonce from the request, then `saving()` (each POST form:
`style_data()`, `child_save()`, `child_edit()`, `rename_shortcode()`, `Clone_child_data()`, `Delete_child_data()`),
`hooks()` (assets, CodeMirror for Custom CSS as `window.oxiFlipCssEditor`, the row and items, style array) and
`render()` (header, tabs, right panel, preview, item dialog, edit templates for instant editing, toast, delete
dialog). `import_font_family()` also builds the font picker and the free plan gate script.

## Page\Public_Render

`parse_styledata()`, `num()` and `percent()` (safe math on saved values, see [templates.md](templates.md)),
`text_render()`, `font_familly()`, `font_awesome_render()`, `hooks()` (assets plus inline CSS
and JS), `is_builder_context()`, `public_loader()`, `render()`, `boot_script()` (load guard), `flip_trigger()`,
`flip_trigger_script()`, `admin_edit_panel()`. Static `$block_preview` is set by the block's editor preview.

## Modules

| Class | Details |
| --- | --- |
| `Gutenberg` | Registers `asset/blocks/flipbox` with `render_block()`; front end returns the raw shortcode inside the block wrapper when `do_shortcode` runs later in the filter (avoids `wptexturize` breaking the load guard script); editor preview renders inline with stylesheet links; `window.oxiFlipBlockData` (flip boxes, edit and create URLs); a deleted id shows a notice in the editor only |
| `Elementor` | Widget `wpkin_flipbox` "Flipbox", control "Shortcode" (select, default the newest flip box), icon from `admin-icon.svg` |
| `Visual_Composer` | `vc_map` "Flip Boxes and Image Overlay" (Content), text field "ID", shortcode `oxilab_flip_box_VC` |
| `Widget` | `oxi_flip_box_widget`, one ID field stored as `title` (its label doesn't print) |
| `Divi` | Off. Module `oxilab_flip_box_divi`, front-end fallback shortcode, builder asset fixes, `oxiflip_design_css` AJAX (also `nopriv`), `force_style_type()`, placeholder in the builder |
