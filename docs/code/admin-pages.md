# Admin pages

Every screen, checked 2026-10-16. Menu: **Flip Box** (icon `image/admin-icon.svg`), registered in
`Includes/Admin/Menu.php` with the first capability of the role chosen in Settings → Access (default
`manage_options`).

| Screen | URL (`admin.php?page=...`) | Class | CSS | JS | AJAX (`oxi_flip_box_data`) |
| --- | --- | --- | --- | --- | --- |
| Flip Box (list) | `oxi-flip-box-ultimate` | `Pages\Home` | bootstrap, admin, settings, home, howto-menu | `home.js` (DataTables) | `create_flip` (clone), `shortcode_delete`, export link; import is a form post to the page |
| Create New | `oxi-flip-box-ultimate-new` | `Pages\Create` | bootstrap, admin, settings, templates, howto-menu | `create.js` | `create_flip`, `shortcode_deactive` |
| Editor | `oxi-flip-box-ultimate-new&styleid=<id>` | `Inc\StyleN` (extends `Page\Admin_Render`) | bootstrap, admin, editor, howto-menu, libraries, code editor | `editor.js`, `js-files/styleN.js`, `vendor.js`, `flip-trigger.js`, libraries | `addons_rearrange`; everything else posts the page's own forms |
| Import Templates | `oxi-flip-box-ultimate-import` | `Pages\Import` | bootstrap, admin, settings, templates, howto-menu | `import.js` | `shortcode_active` |
| Getting Started | `flipbox-getting-started` | `Pages\GettingStarted` | getting-started, howto-menu | `getting-started.js` | none |
| Settings | `oxi-flip-box-ultimate-settings` | `Pages\Settings` | bootstrap, admin, settings, howto-menu | `settings.js` | one per field, `oxi_flipbox_delete_data_on_uninstall`, `oxi_flipbox_delete_all_data` |
| Account (Freemius) | `oxi-flip-box-ultimate-account` | Freemius template wrapped by `Includes\Admin\Account` | settings, account, admin-menu | Freemius | Freemius |

Getting Started is also the page Freemius opens after activation.

## Flip Box (list)

Hero "Flip boxes" with the count, **Import** and **Create new**. Empty state "No flip boxes yet" with "Create your
first flip box" and "Import a JSON file". Table, newest first: Name (#id, template "Style N", item count),
Shortcode (Copy, Copy PHP code), actions Edit, Clone (dialog "Clone flip box", name for the copy), Export, Delete
(dialog "Delete flip box"). Import: the "Import a flip box" dialog posts a `.json` file to the page (nonce
`oxilab-flipbox-import`, needs `upload_files`), handled by `Home::manual_import_json()` →
`Admin_Ajax::post_json_import()`.

## Create New

Hero "Create a flip box" with "Browse more templates". One card per template in the Create New list ("Style 6, 3
designs", "Remove from list"), each design rendered live; **Use this design** opens "Create flip box" (Name, max 50,
placeholder "For example: Team members", Cancel, **Create and edit**). A "Want more designs?" card links to Import
Templates.

## Import Templates (template library)

Hero "Template library" with the number of templates not yet added and "Back to Create New". Each template with its
designs and **Add to Create New**, or "Pro only" for Style 11 to 29 on free sites. Empty state "Every template is
already added".

## Editor

Header: "All flip boxes" back link, the flip box name, "Style N", `#id`. Left: tabs **General**, **Front**,
**Backend**, **Custom CSS**, **Support**, then Reload and **Save changes**. Right panel: "Add a flip box", Name with
**Rename**, Shortcode (copy buttons for the shortcode and the PHP code), "Reorder flip boxes". Preview with a
background color picker; each item has Edit, Clone and Delete. Saving happens in the background with a toast
("Changes saved", "Flip box saved", "Name updated", "Flip box deleted", "Flip box cloned, the copy is added at the
end"); the preview is patched in place.

## Settings

Hero "Settings" with "Changes save automatically". Cards:

| Card | Fields (option) |
| --- | --- |
| License | Status pill, plan, expiry, masked key, Activate or Change license (Freemius dialog), Get Pro or Renew, Manage account |
| Access | Who can edit (`oxi_addons_user_permission`) |
| Assets and performance | Font Awesome (`oxi_addons_font_awesome`), Google Fonts (`oxi_addons_google_font`); off = `no` |
| Advanced | Support message (`oxi_flipbox_support_massage`) |
| Danger zone (admins only) | Delete data when the plugin is deleted (`oxi_flipbox_delete_data_on_uninstall`), Delete all data (type DELETE) |

Side card "Need a hand?" with Getting started, Documentation, demos and support.

## Getting Started

Logo, a thank you line, tabs: **Introduction** (the YouTube tutorial `Jj_8eL8nHPI`, feature blocks), **Basic Usage**,
**Help** (docs, support, license help), **Changelog**, **Get PRO** (feature comparison, pricing link).

## Plugin menu bar

`Admin_helper::oxilab_admin_menu()` through the filter `oxi-flip-box-plugin/admin_menu`: logo, Flip Box, Create New,
Import Templates, "How to use?"; right side Upgrade (free sites), Demos, Docs, Support, Settings icon.
