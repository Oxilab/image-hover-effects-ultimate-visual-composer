# File map

Every folder and file (without `vendor/`, `.git/` and images), and where to make a change. Checked 2026-10-16.

## Root

| File | Role |
| --- | --- |
| `index.php` | Header, Freemius init (`wpkin_fb_v()`), class `Oxilab_Flipbox`, shortcode `oxilab_flip_box`, page builder loaders, back-compat aliases |
| `readme.txt` | WordPress.org readme, FAQ, 4 old screenshot captions, changelog |
| `CLAUDE.md` | Pointer to these docs (not shipped) |
| `composer.json` / `.lock`, `phpcs.xml`, `phpunit.xml` | Autoload, Freemius SDK, dev tools; not shipped |
| `.gitattributes` | What the release leaves out |
| `.github/workflows/deploy-org.yml`, `update-assets.yml` | Deploy on a tag; assets on push to `trunk` (no such branch) |
| `.wordpress-org/` | Banners, icons, 4 old screenshots |
| `docs/` | These docs (not shipped) |

## Classes/

| File | Role |
| --- | --- |
| `Admin_Ajax.php` | Every AJAX action, called by name from `data_process()`: create, clone, delete, export, import, template list add and remove, reorder items, settings, Danger zone |
| `Data_Cleaner.php` | Danger zone and uninstall cleanup (only Flipbox rows; tables are shared) |
| `Docs.php` | The "How to use?" menu links (quick start, shortcode, Elementor, WPBakery, widget, all docs) |
| `Support_Reviews.php` | Review notice (after 7 days) and its dismissal |
| `Support_Upgrade.php` | Pro upgrade notice (free sites, after 10 days, plugin screens only) |

## Inc_Helper/ (traits)

| File | Role |
| --- | --- |
| `Admin_helper.php` | Used by the main class: `data_process()` (AJAX router), notice gates, Pro checks, the plugin menu bar `oxilab_admin_menu()`, hex decoding |
| `Public_Helper.php` | `shortcode_render()`, table names, font and icon helpers |
| `CSS_JS_Loader.php` | Admin CSS (Bootstrap, admin.css, Font Awesome, two Google Fonts) and the editor's libraries |
| `Sanitization.php` | The editor's form controls (Flip Trigger, Flip Type, Flip Effects, column, color, font, number, text, image fields) |

## Includes/

| File | Role |
| --- | --- |
| `Assets.php` | Admin CSS per page, editor body class, Elementor editor assets |
| `Admin.php` | Starts `Admin\Menu`, `Admin\Notice`, `Admin\Account` |
| `Admin/Menu.php` | The menu (Flip Box, Create New, Import Templates, Getting Started, Settings) and the editor router |
| `Admin/Account.php` | Brands the Freemius Account page |
| `Admin/Notice.php` | Empty |
| `Admin/Pages/Home.php` | The Flip Box list: copy, clone, export, delete, import |
| `Admin/Pages/Create.php` | Create New: templates in the list, live previews, "Use this design", name dialog |
| `Admin/Pages/Import.php` | Import Templates (the template library): "Add to Create New" |
| `Admin/Pages/Settings.php` | License, Access, Assets, Advanced, Danger zone |
| `Admin/Pages/GettingStarted.php` + `Tabs/` | Getting Started with tabs Introduction (video), Basic Usage, Help, Changelog, Get PRO |
| `Admin/Pages/Addons.php` | Not in the menu |
| `Installation.php` | Tables (`dbDelta`), default Create New rows |
| `Frontend.php` | Empty |

## Inc/, Public_Render/, Page/

| Path | Role |
| --- | --- |
| `Inc/StyleN.php` (1 to 29) | Editor of template N: `register_style()`, `register_child()`, `register_controls()`, `modal_form_data()`, `Rearrange()` |
| `Public_Render/StyleN.php` (1 to 29) | Front-end HTML and design CSS of template N |
| `Page/Admin_Render.php` | Editor base: saving, the editor page, item dialogs, font list, free plan gate |
| `Page/Public_Render.php` | Render base: assets, inline CSS and JS, builder contexts, load guard, Flip Trigger |
| `Page/JSON.php` | The 87 ready designs |

## Modules/

| File | Role |
| --- | --- |
| `Gutenberg.php` | The Flipbox block (`oxi-flip-box/flipbox`) |
| `Elementor.php` | The Flipbox widget (`wpkin_flipbox`) |
| `Visual_Composer.php` | The WPBakery element and `[oxilab_flip_box_VC]` |
| `Widget.php` | The classic widget |
| `Divi.php` | The Divi module and its builder fixes; **switched off** in `index.php` |

## asset/

| Path | Role |
| --- | --- |
| `asset/backend/css/` | Admin CSS ([assets.md](assets.md)) |
| `asset/backend/js/` | Admin JS: editor, create, home, import, settings, getting started, block, notices, libraries |
| `asset/backend/js-files/styleN.js` | Per template editor scripts (1 to 28; 29 is missing) |
| `asset/blocks/flipbox/block.json` | Block definition |
| `asset/frontend/css/` | `style.css` (structure, load guard, click mode), `animation.css`, `flip-boxes.css`, `StyleN.css`, `font-awsome.min.css` |
| `asset/frontend/js/` | `jquery.js` (scroll-in animations), `waypoints.min.js`, `flip-trigger.js` (click mode) |

## image/

Logos (`logo.png`, `flipbox-logo.svg`, `admin-icon.svg`, `divi-icon.svg`) and the Getting Started images.

## Where to make a change

| Change | File |
| --- | --- |
| A new AJAX action | A public method in `Classes/Admin_Ajax.php` with its own capability check, called with `functionname` |
| A setting for every template | Append it in `Admin_Render::style_data()` and read it by name in `Page/Public_Render.php` (Flip Trigger pattern), control in `Inc_Helper/Sanitization.php` |
| One template's markup or CSS | `Public_Render/StyleN.php` and `asset/frontend/css/StyleN.css` |
| One template's editor fields | `Inc/StyleN.php` (append only, never reorder) |
| A ready design | `Page/JSON.php` |
| Which templates are free | `Includes/Admin/Pages/Import.php` (`$id > 10`) |
| A doc link in the plugin | `Classes/Docs.php` |
| The plugin menu bar | `Inc_Helper/Admin_helper.php::oxilab_admin_menu()` |
