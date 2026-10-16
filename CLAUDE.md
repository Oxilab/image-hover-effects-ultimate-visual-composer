# Flipbox

This folder (`image-hover-effects-ultimate-visual-composer`) is the **Flipbox** plugin, not Image Hover.

Before any task in this plugin, read `docs/README.md`. It sends each kind of task (code, security, release, doc
screenshots, WordPress.org screenshots, docs and SEO, blog posts, tutorial, explainer and ad videos) to its rule file
in `docs/rules/`, and the code reference is in `docs/code/`. Always read `docs/rules/00-general.md` first.

The rules that matter most:

- Never break a live site: change only what the task needs and prove it with a before and after comparison.
- The style string is positional: never insert or reorder a value; append new settings and read them by name
  (`docs/code/templates.md`).
- The tables are shared with older Oxilab plugins: touch only `type = 'flip'` rows.
- Keep or improve security on every change and security test every code change before calling it done:
  review the diff, real attacks as every role, static checks, report (`docs/rules/coding.md`, Security test).
- Every PHP change passes PHPCS with WPCS on the changed lines (0 errors, 0 warnings) with the plugin's
  `phpcs.xml` and the shared tools in `/Users/richard/Local Sites/oxilab/tools/wpcs/` (`docs/rules/coding.md`).
- Verify every claim in the code or the real UI before writing it anywhere (29 templates, 87 designs).
- Flipbox and Image Hover Effects Ultimate are separate plugins: never mix their code or data.
- No em or en dashes anywhere. English, for an international audience.
- Never bump the version unless Richard asks. A version with a git tag is released.
- One item first, Richard approves, then the rest. Nothing goes live without his go.
- No personal data or license keys in any screenshot or video; real data only.
- One plain Bash command per call; tools live in project folders, not only in the scratchpad.

When Richard corrects something, update the matching file in `docs/rules/` in the same session.

This file and `docs/` are excluded from the release in `.gitattributes`.
