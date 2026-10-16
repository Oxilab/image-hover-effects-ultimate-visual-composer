# Assets

Plain CSS and JS, no build step. Checked 2026-10-16. Redesigned admin files use `filemtime()` versions; older ones
the plugin version.

## Admin CSS (`asset/backend/css/`)

| File | Loaded on | Notes |
| --- | --- | --- |
| `global-admin.css` | Every admin page | Menu item styling |
| `bootstrap.min.css`, `admin.css` | Plugin pages that call `admin_css()` | Old global styles; `admin.css` hides `.notice` and restyles links and images, so new code out-specifies it |
| `settings.css` | Settings, Flip Box list, Create, Import, Account | The `--flip-*` tokens and `oxi-flip-set-*` building blocks |
| `home.css` | Flip Box list | |
| `templates.css` | Create New, Import Templates | Template cards and previews |
| `editor.css` | Editor only (`body.oxi-flip-editor-page`) | The 3.1.0 editor look |
| `getting-started.css` | Getting Started | |
| `account.css`, `admin-menu.css` | Account | Freemius page restyle |
| `howto-menu.css` | Every plugin page | "How to use?" dropdown |
| `notice.css` | Where a notice shows | Scoped against `admin.css` |
| `gutenberg-block.css` | Block editor | Block placeholder and preview |
| Libraries | Editor | `minicolors.css`, `fontawesome-iconpicker.css`, `jquery.fontselect.css` |

## Admin JS (`asset/backend/js/`)

| File | Role |
| --- | --- |
| `editor.js` | Background saves, preview patching (`morph()`), item dialogs from templates, clone and delete, toast, CodeMirror sync, copy buttons |
| `js-files/styleN.js` | Per template live preview helpers (1 to 28) |
| `create.js` | "Use this design" dialog, "Remove from list" |
| `home.js` | DataTables, copy, clone and delete dialogs, import dialog |
| `import.js` | "Add to Create New" |
| `settings.js` | Auto-save, Danger zone dialog |
| `getting-started.js` | Tabs |
| `gutenberg-block.js` | The block's editor UI |
| `admin-notice.js` | Notice dismissal and placement below the plugin menu bar |
| `vendor.js`, `media-uploader.js` | Editor helpers, Media Library picker |
| Libraries | `bootstrap.min.js`, `popper.min.js`, `jquery.dataTables.min.js`, `dataTables.bootstrap.min.js`, `minicolors.js`, `jquery.minicolors.js`, `fontawesome-iconpicker.js`, `jquery.bootstrap-growl.js` |

## Front end

| File | When |
| --- | --- |
| `asset/frontend/css/style.css` (handle `flip-box-addons-style`) | Every page with a flip box; the design CSS is added to this handle |
| `asset/frontend/css/animation.css` (`oxi-animation`) | Every page with a flip box (scroll-in animations) |
| `asset/frontend/js/waypoints.min.js`, `jquery.js` (`flipbox-addons-jquery`) | Every page with a flip box (scroll-in animations) |
| `asset/frontend/js/flip-trigger.js` | Only for flip boxes set to "On Click" |
| `asset/frontend/css/font-awsome.min.css` | When a flip box shows icons and Font Awesome is on |
| Google Fonts | Per font, unless Google Fonts is off |

Everything is enqueued while a flip box renders, so it prints in the footer; the load guard (`oxi-flip-booting`)
keeps transitions off until the CSS has applied. In builder previews the design CSS is printed inline.
