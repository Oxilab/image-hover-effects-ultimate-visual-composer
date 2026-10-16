# Templates, the style string, items, Free and Pro

Counted in the code on 2026-10-16. Use these numbers, never the readme's "more than 50 effects"
([../rules/00-general.md](../rules/00-general.md) rule 3).

## The numbers

| What | Count | Where |
| --- | --- | --- |
| Templates (Style 1 to 29) | 29 | `Inc/StyleN.php` (editor), `Public_Render/StyleN.php` (render), `asset/frontend/css/StyleN.css` |
| Ready designs | 87 (3 per template) | `Page/JSON.php`, an array `'StyleN' => [ '1' => '<json>', '2' => ..., '3' => ... ]` |
| Free templates | Style 1 to 10 | `Includes/Admin/Pages/Import.php`: `$id > 10` and no Pro = "Pro only" |
| In Create New on a new install | Style 1 to 5 | `Includes/Installation.php` and `Pages/Create.php` seed `oxi_div_import` rows `type='flip'`, name 1 to 5 |

A template appears in **Create New** when it has an `oxi_div_import` row (`type = 'flip'`, `name = N`). "Add to
Create New" on Import Templates adds the row (`shortcode_active`), "Remove from list" deletes it
(`shortcode_deactive`). Flip boxes already made are not affected.

## Free and Pro (what the code does)

| Gate | Where | Kind |
| --- | --- | --- |
| Style 11 to 29 "Pro only" in the template library | `Pages/Import.php` | Display only: the server's `shortcode_active` and `create_flip` don't check Pro |
| Color fields (`.oxilab-vendor-color`), font fields (`.oxilab-admin-font`), Custom CSS marked Pro | inline script in `Admin_Render::import_font_family()` | On submit the script resets colors and fonts to their saved values and empties Custom CSS (also the CodeMirror editor) |
| Upgrade link in the menu bar and notice | `Admin_helper`, `Classes/Support_Upgrade.php` | Free sites only |

The local site runs Pro, so none of this shows there.

## The style string (`oxi_div_style.css`)

One string per flip box: `" key |value| key |value| ... custom-css |X|| flip-trigger |click|"`. Every render and
editor class reads it **by position**: `explode('|', $css)` and then `$styledata[N]` / `$this->style[N]` (odd
indexes are values). Each template has its own layout of positions; the highest one each reads is its Custom CSS
(Style 1: index 149).

Rules:
- Never insert, remove or reorder a key in a template's `register_style()`: every saved flip box would shift.
- A new setting is appended after the template's string and read **by name**. Flip Trigger (3.1.0) is the pattern:
  `Admin_Render::style_data()` appends `' flip-trigger |click|'` or `|hover|`; `Public_Render::flip_trigger($css)`
  looks for `' flip-trigger |click|'`, so rows without the key stay "hover".
- Writers of `css`: the editor save, `create_flip` (design JSON or clone), and import; create, clone and import copy
  it verbatim, so appended keys travel with it.
- `Public_Render::parse_styledata()` runs `esc_attr` on every value and turns numbers into floats (PHP 8 math).

Field names in the editor form are the keys (`front-background-color`, `flip-col`, `flip-width`, ...).
Sanitizing is per field in each `Inc/StyleN::register_style()`: `sanitize_text_field`, `sanitize_hex_color` for
plain colors, `sanitize_textarea_field` for Custom CSS (keeps line breaks; 3.1.0).

## Items (`oxi_div_list.files`)

Each item (one card) is `" key {#}|{#}value{#}|{#} key {#}|{#}value{#}|{#}..."`, read by position after
`explode('{#}|{#}')`. Style 1's keys: `flip-box-front-title`, `flip-box-front-icons`, `flip-box-image-upload-url-01`
(front image), `flip-box-backend-desc`, `flip-box-backend-button-text`, `flip-box-backend-link`,
`flip-box-image-upload-url-02` (back background); others add `flip-box-font-desc`, `flip-box-backend-title`.
`register_child()` sanitizes (`sanitize_text_field`, `sanitize_url`); the render prints text through `text_render()`
(`wp_kses_post`, shortcodes, `spBac` = line break, `spTac` = non-breaking space), URLs through `esc_url`, icons
through `font_awesome_render()`.

## How a flip box renders

`shortcode_render($id)` → row → `style_name` `style6` → `new Public_Render\Style6($row, $items, 'user')`:
`loader()` pads each item string, `render()` prints
`<div class="oxi-addons-container oxi-addons-flipbox-template-style6 [oxi-flip-trigger-click] [oxi-flip-booting]">`
with the template's `default_render()`, the load guard script once per page (`boot_script()`, removes
`oxi-flip-booting` after the CSS applied, 5 s fallback) and, for click mode, `flip-trigger.js`. `hooks()` enqueues
`animation.css`, `style.css`, `waypoints.min.js`, `asset/frontend/js/jquery.js` (the animation script, not jQuery) and
adds the design CSS (`$this->inline_css`) to `flip-box-addons-style`; in builder contexts (`is_builder_context()`:
block preview, Elementor editor, Divi preview paths) the CSS and JS are echoed inline instead.

In admin (`'admin'`), each item gets Edit, Clone and Delete buttons (`admin_edit_panel()`).

## Editor tabs

`Admin_Render::render()`: tabs **General** (`#oxilab-tabs-id-5`: Flip Trigger, Flip Type, Flip Effects, Item per
Rows, sizes, animation), **Front**, **Backend**, **Custom CSS** (`#custom-css`, a CodeMirror editor when the user
allows syntax highlighting), **Support**; right panel: "Add a flip box", Name with **Rename**, Shortcode with copy
buttons (shortcode and PHP), "Reorder flip boxes"; a preview with a background color picker. Shared controls live in
`Inc_Helper/Sanitization.php` (Flip Trigger, Flip Type, Flip Effects, column, color, font and size fields).
