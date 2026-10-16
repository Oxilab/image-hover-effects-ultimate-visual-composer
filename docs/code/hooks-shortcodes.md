# Hooks, AJAX, shortcodes, block, widgets, JS objects

Checked against the code on 2026-10-16.

## Shortcodes

| Shortcode | Registered in | Output |
| --- | --- | --- |
| `[oxilab_flip_box id="N"]` | `index.php` `Shortcode_loader()` | Flip box N |
| `[oxilab_flip_box_VC id="N"]` | `Modules/Visual_Composer.php` | The same, for the WPBakery element |
| `[oxilab_flip_box_divi id="N"]` | `Modules/Divi.php` | Only when the Divi module is switched on |

PHP use: `<?php echo do_shortcode('[oxilab_flip_box id="N"]'); ?>` (the editor's Shortcode panel and the list copy
it). `widget_text` runs `do_shortcode`.

## Block and widgets

| Integration | Name in the UI | Details |
| --- | --- | --- |
| Gutenberg block | "Flipbox" (`oxi-flip-box/flipbox`, category Design, icon image-flip-horizontal) | `asset/blocks/flipbox/block.json` (apiVersion 3, attribute `flipboxId`, align wide and full); editor script `asset/backend/js/gutenberg-block.js` (plain ES5) with "Choose a flip box" and a ServerSideRender preview; `window.oxiFlipBlockData` |
| Elementor widget | "Flipbox" (`wpkin_flipbox`, General) | Control "Shortcode" (select); style and script dependencies registered by the render |
| WPBakery element | "Flip Boxes and Image Overlay" (`oxilab_flip_box_VC`, Content) | Text field "ID" |
| Classic widget | "Flipbox - Awesomes Flip Boxes Image Overlay" (`oxi_flip_box_widget`) | One ID field |
| Divi module | `oxilab_flip_box_divi` | Switched off |

## AJAX actions

| Action | Handler | Who |
| --- | --- | --- |
| `oxi_flip_box_data` | `Admin_helper::data_process()` → `Admin_Ajax::<functionname>()` ([classes.md](classes.md)) | Nonce `oxi-flip-box-editor`, then the method's own capability check |
| `oxilab_flip_notice_dissmiss` | `Support_Reviews::notice_dissmiss()` | Nonce only |
| `oxilab_flip_upgrade_dissmiss` | `Support_Upgrade::ajax_action()` | Nonce plus `manage_options` |
| `oxiflip_design_css` (also `nopriv`) | `Divi::output_design_css()` | Only registered when the Divi module is on |

Functions called by the admin JS: `create_flip` (create.js, home.js clone), `shortcode_delete` (home.js),
`shortcode_deactive` (create.js), `shortcode_active` (import.js), `addons_rearrange` (editor inline script), the
setting names and `oxi_flipbox_delete_all_data` (settings.js), `get_shortcode_export` (export link).

## Editor form posts (same page, not AJAX)

| Form field | Nonce | Handler |
| --- | --- | --- |
| `oxi-addons-flip-templates-submit=Submit` | `oxiflipstylecss` | `style_data()` |
| `oxi-flip-template-modal-submit=Submit` | `oxiflipchildnonce` | `child_save()` (new item or update by `item-id`) |
| `edit` + `item-id` | `oxiflipeditdata` | `child_edit()` (fallback; `editor.js` fills the dialog from templates) |
| `clone` + `item-id` | `oxiflipclonedata` | `Clone_child_data()` (only items of this flip box) |
| `delete` + `item-id` | `oxiflipdeletedata` | `Delete_child_data()` |
| `addonsstylenamechange=Save` | `oxi-addons-name-change` | `rename_shortcode()` |

## Hooks the plugin adds

| Hook | Where | For |
| --- | --- | --- |
| `plugins_loaded` | `index.php` | `init_plugin()` |
| `init` | `index.php`, `Modules/Gutenberg.php` | Textdomain, block registration |
| `upgrader_process_complete` | `index.php` | `plugin_upgrade_hook()` (never matches) |
| `admin_menu`, `admin_head` | `Menu.php`, `index.php` | Menu; hide an old dashboard page |
| `admin_enqueue_scripts`, `admin_body_class` | `Includes/Assets.php`, `Account.php` | Admin CSS, body classes |
| `elementor/editor/*`, `elementor/frontend/*`, `elementor/preview/*`, `elementor/widgets/register` | `Assets.php`, `Modules/Elementor.php` | Elementor |
| `enqueue_block_editor_assets` | `Modules/Gutenberg.php` | Block data |
| `vc_before_init` | `Modules/Visual_Composer.php` | `vc_map` |
| `widgets_init`, `widget_text` | `index.php` | Widget |
| `admin_notices`, `admin_footer` | `Support_*`, `Settings.php` | Notices, Freemius license dialog |
| Freemius `after_uninstall`, `hide_account_tabs`, `templates/account.php` | `index.php`, `Account.php` | Cleanup, Account page |

## Hooks the plugin fires or offers

| Hook | Type | Use |
| --- | --- | --- |
| `oxi-flip-box-plugin/before_init` | action | Before the init hooks |
| `oxilab_flipbox_loaded` | action | Freemius ready |
| `oxi-flip-box-plugin/pro_version` | filter (hex `6f78692d666c69702d626f782d706c7567696e2f70726f5f76657273696f6e`) | `true` when Pro is active (admin only) |
| `oxi-flip-box-plugin/admin_menu` | filter (hex `...2f61646d696e5f6d656e75`) | Prints the plugin menu bar |
| `oxi-flip-box-support-and-comments` | filter | Called by pages; nothing registers it in this version |
| `oxilab_flip_box_has_pro_access` | filter | QA override for the upgrade notice only |

Hex decodes: `oxilab_flip_box_license_status` (legacy license option), `valid`, `check_current_tabs`,
`oxilab_admin_menu`.

## Localized JS objects

| Object | Script | Contents |
| --- | --- | --- |
| `oxi_flip_box_editor` | `create.js`, `home.js`, `import.js` | `ajaxurl`, `nonce` (`oxi-flip-box-editor`) |
| `oxi_flip_box_settings` | `settings.js` | `ajaxurl`, `nonce` (`oxi-flip-box-editor`) |
| `window.oxiFlipCssEditor` | `editor.js` | `wp_enqueue_code_editor()` settings for Custom CSS |
| `window.oxiFlipBlockData` | `gutenberg-block.js` | Flip boxes, edit and create URLs |
