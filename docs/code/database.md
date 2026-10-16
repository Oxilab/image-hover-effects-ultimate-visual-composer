# Database

Checked against the code on 2026-10-16. Tables are created by `Includes/Installation.php` (`dbDelta`) on
activation. They are **shared** with older Oxilab plugins (the `oxi_div_*` family), so every query filters by type.

## Tables

### `{prefix}oxi_div_style`: one row per flip box

| Column | Content |
| --- | --- |
| `id` | The shortcode id |
| `name` | The name (varchar 50) |
| `type` | `flip` for Flipbox rows (the design JSON files say `Flip`) |
| `style_name` | `style1` to `style29` |
| `css` | The positional style string ([templates.md](templates.md)), plus `flip-trigger` at the end since 3.1.0 |

### `{prefix}oxi_div_list`: one row per item (card)

| Column | Content |
| --- | --- |
| `id` | Item id (order = id order; Reorder re-inserts rows) |
| `styleid` | The flip box |
| `type` | `flip` (or empty on items saved by the editor) |
| `files` | The positional item string (`{#}|{#}` separated) |
| `css` | Empty in current designs |

### `{prefix}oxi_div_import`: shared with Image Hover

| Rows | Meaning |
| --- | --- |
| `type = 'flip'`, `name = N` | Template N is in the Create New list |
| `type = 'oxi-addons-flip'` | Custom fonts for the font picker (`name = 'custom'` or a Google font in `font`) |

This table is also used by Image Hover (types `<module>-ultimate`). Never touch other types; `Data_Cleaner` deletes
only `flip` and `oxi-addons-flip`.

## Options

| Option | Values | Read by | Removed by |
| --- | --- | --- | --- |
| `oxi_addons_user_permission` | A role key; empty = admins | Menu, `Admin_Ajax::check_user_permission()` | Delete all data, uninstall |
| `oxi_addons_pre_loader` | Saved by its AJAX method; no field on the Settings page | nothing in this version | Delete all data, uninstall |
| `oxi_flipbox_support_massage` | `no` = off | Settings | Delete all data, uninstall |
| `oxi_addons_font_awesome` | `no` = don't load Font Awesome | renders | Only if no other Oxilab plugin is installed (Image Hover reads it too) |
| `oxi_addons_google_font` | `no` = don't load Google Fonts | renders | Same as above |
| `oxi_addons_font_awesome_version` | `version||url` | `Public_Helper::icon_font_selector()` | not by Flipbox |
| `oxi_flipbox_delete_data_on_uninstall` | `yes` / `no` | uninstall | uninstall |
| `oxilab_flip_box_version` | Version at install | nothing | uninstall |
| `oxilab_flip_box_activation_date`, `oxilab_flip_box_nobug` | Review notice clock and dismissal | Review notice | uninstall |
| `oxilab_flip_box_upgrade_date`, `oxilab_flip_box_upgrade_nobug` | Upgrade notice clock and dismissal | Upgrade notice | uninstall |
| `oxilab_flip_box_recommended` | Old recommendations dismissal | nothing now | uninstall |
| `widget_oxi_flip_box_widget` | Widget instances | WordPress | uninstall |
| `oxilab_flip_box_license_status`, `oxilab_flip_box_license_key` | Legacy license | Pro check, Settings | uninstall |

Transient `oxi_flip_box_activation_redirect` (30 s after activation; Freemius' first path does the redirect).
Freemius keeps its own `fs_*` data.

## Data flow

```
Create:  design JSON (Page/JSON.php) → INSERT style (name, 'flip', style_name, css) → INSERT its items
Clone:   copy the row and its items (create_flip with a styleid)
Save:    POST → register_style() + flip-trigger → UPDATE css
Items:   POST → register_child() → INSERT / UPDATE files; Clone and Delete by item id; Reorder re-inserts
Render:  SELECT style + items by id → Public_Render\StyleN → HTML + design CSS
Export:  {"style": row, "child": items, "plugin": "flipbox"};  Import: post_json_import() (type must be 'flip')
Delete:  DELETE the style row and its items
```
