# Overview

Checked against the code on 2026-10-16 (version 3.1.0).

## Identity

| Field | Value |
| --- | --- |
| Plugin name (header) | Flipbox - Awesomes Flip Boxes Image Overlay |
| Folder and WordPress.org slug | `image-hover-effects-ultimate-visual-composer` (historical; it is Flipbox) |
| Main file | `index.php` (final class `Oxilab_Flipbox`, singleton `oxilab_flipbox()`; uses the traits `Inc_Helper\Public_Helper` and `Inc_Helper\Admin_helper`; aliases `wpkin_flipbox()` and `WPKin_Flipbox`) |
| Version | `3.1.0` (header, `Oxilab_Flipbox::VERSION`, readme `Stable tag`); released, tag `v3.1.0` |
| Text domain | `oxi-flip-box-plugin` |
| Namespace | `OXI_FLIP_BOX_PLUGINS\` → plugin root (Composer PSR-4) |
| Minimums | Code constants: PHP 7.4, WordPress 6.2 (the readme still says 7.0 and 4.4) |
| Freemius | `wpkin_fb_v()`, id 20098, slug `oxi-flip-box-ultimate`, first path `admin.php?page=flipbox-getting-started`, paid plans, `is_premium` false (Pro is a license on the same code) |
| Pro check | filter `oxi-flip-box-plugin/pro_version` → `Admin_helper::check_current_tabs()` (legacy `oxilab_flip_box_license_status` = `valid`, or `can_use_premium_code()`); admin only |
| License | GPLv2 or later |

## What it does

Flip boxes: a card with a front (image or icon and a title) that turns over on hover, or on click (3.1.0), to a
back with text and a button. The site owner picks one of 29 templates in Create New, names it, adds items (each item
is one card) in the editor, styles it (General, Front, Backend, Custom CSS tabs) and places it with
`[oxilab_flip_box id="N"]`, the Flipbox block, the Elementor widget, the WPBakery element or a classic widget.

## Constants (`define_constance()`)

| Constant | Value |
| --- | --- |
| `OXI_FLIP_BOX_FILE`, `OXI_FLIP_BOX_BASENAME` | Main file, `image-hover-effects-ultimate-visual-composer/index.php` |
| `OXI_FLIP_BOX_PATH` | Plugin folder path, trailing slash |
| `OXI_FLIP_BOX_URL` | Plugin URL, trailing slash |
| `OXI_FLIP_BOX_PLUGIN_VERSION` | `3.1.0` |
| `OXI_FLIP_BOX_MINIMUM_PHP_VERSION`, `OXI_FLIP_BOX_MINIMUM_WP_VERSION` | `7.4`, `6.2` (not enforced anywhere) |

## Boot sequence

1. `index.php`: Composer autoload, Freemius init (fires `oxilab_flipbox_loaded`), Freemius `after_uninstall` →
   `Classes\Data_Cleaner::uninstall`.
2. `Oxilab_Flipbox::__construct()`: constants, activation hook (`Includes\Installation::plugin_activation_hook()`:
   tables, the five default templates in Create New, transient `oxi_flip_box_activation_redirect`), empty
   deactivation hook, `upgrader_process_complete` → `plugin_upgrade_hook()` (never matches, see
   [../rules/coding.md](../rules/coding.md)), fires `oxi-flip-box-plugin/before_init`, `init` → textdomain,
   `plugins_loaded` → `init_plugin()`.
3. `init_plugin()`:
   - `new Includes\Assets()` (admin CSS per page, Elementor editor assets)
   - `Shortcode_loader()`: `[oxilab_flip_box]`, `Modules\Visual_Composer`, `Modules\Elementor`, `Modules\Gutenberg`,
     the classic widget, `widget_text` → `do_shortcode` (`Modules\Divi` is commented out)
   - `Public_loader()`: table names
   - when `is_admin()`: `new Includes\Admin()` (Menu, an empty Notice, Account), `Admin_Filters()` (the Pro filter and
     the menu bar filter, hex), `User_Admin()` (AJAX `oxi_flip_box_data`, hides an old dashboard page),
     `User_Reviews()` (review notice after 7 days, else the upgrade notice after 10 days on free sites)

## Request flow

```
Admin: Flip Box → Create New (templates in the Create New list, live previews)
       → "Use this design" → name dialog → AJAX oxi_flip_box_data, functionname create_flip (design JSON)
       → editor (?page=oxi-flip-box-ultimate-new&styleid=<id>) → Inc\StyleN
       → Save changes: POST to the same page (nonce oxiflipstylecss) → Admin_Render::style_data()
         → oxi_div_style.css = "key |value| key |value| ... custom-css |...| flip-trigger |hover|"
       → items: POST (nonce oxiflipchildnonce) → oxi_div_list.files = "key {#}|{#}value{#}|{#}..."

Front: [oxilab_flip_box id="N"] / block / Elementor / WPBakery / widget
       → Public_Helper::shortcode_render(N) → row, style_name "style6" → Public_Render\Style6
       → HTML now (with the load guard script), style.css + animation.css + waypoints + jquery.js enqueued,
         the design CSS added with wp_add_inline_style('flip-box-addons-style') (inline in builder previews)
```

## Links

| What | URL |
| --- | --- |
| WordPress.org | https://wordpress.org/plugins/image-hover-effects-ultimate-visual-composer/ |
| Docs | https://oxilab.dev/docs/flipbox/ |
| Demos | https://demos.oxilab.dev/flipbox/template/ |
| Pricing | https://oxilab.dev/flipbox/pricing/ |
| Support | https://wordpress.org/support/plugin/image-hover-effects-ultimate-visual-composer/ |

## Code docs in this folder

| File | Contents |
| --- | --- |
| [file-map.md](file-map.md) | Every folder and file, and where to make a change |
| [classes.md](classes.md) | Every class and trait |
| [admin-pages.md](admin-pages.md) | Every admin screen: URL, class, CSS, JS, AJAX |
| [templates.md](templates.md) | The 29 templates, the style string, items, Free and Pro, how a flip box renders |
| [hooks-shortcodes.md](hooks-shortcodes.md) | Hooks, AJAX functions, shortcodes, block, widgets, JS objects |
| [database.md](database.md) | Tables, options, data flow |
| [assets.md](assets.md) | CSS, JS, what loads where |
| [security.md](security.md) | Security model, history, open items |
