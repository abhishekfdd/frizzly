# RESUME — Frizzly 1.1.1 security release (working state)

**Delete this file before release.** It is scratch state for resuming work after
a Claude session reset. Also add it to `.distignore` if it survives to packaging.

Branch: `security/cve-2025-3055` (base commit `a39ee20`, SVN trunk r2427847).
Plan: `~/.claude-personal/plans/plan-mode-change-nothing-twinkly-hellman.md`

## SCOPE OVERRIDE — read this first

`CLAUDE.md` says "Security fixes only. No refactoring, no modernisation, no
reformatting." **The user explicitly lifted that on 2026-08-09**, in order to
resolve a conflict: `CLAUDE.md` also sets `composer exec phpcs` clean as a
release gate, but the baseline was **1536 errors / 127 warnings across 57 files**,
unreachable without reformatting. User instruction was: *"ok you can go ahead and
refactor old code."*

So: formatting/refactor work IS in scope for this branch. `CLAUDE.md` itself has
not been amended — if a future session re-reads it and sees the old rule, this
override still stands.

The user also edited `CLAUDE.md` mid-session to add a **"Do not fix by wrapping
alone"** section. That rule is binding and shaped change 1 (see below).

## Status

| # | Change | State |
|---|---|---|
| 1 | `esc_url` + explicit base URL on nag dismiss link — **the CVE fix** | DONE |
| A | `phpcbf` auto-format pass across plugin | DONE (733 fixed) |
| 2 | `esc_attr` in `Frizzly_Meta_Elements.php` + 2 echo annotations | DONE |
| 3 | escape output + sanitise input, `Frizzly_Admin_Post_Edit_Screen.php` | DONE |
| 4 | validate `$_GET['tab']` against `get_tabs()` (both read + save paths) | DONE |
| 5 | superglobal hygiene + `get_post()` null check + SERVER_NAME + wpdb + welcome screen | DONE |
| 6 | stop compiling translator text in 5 Angular directives | DONE |
| 7 | version bump 1.1.0 → 1.1.1 + changelog + readme metadata | DONE |
| 8 | readme/header metadata for reinstatement (license, tags, Requires, Tested up to) | DONE |
| B | docblocks / method scope / file naming (bulk phpcs cleanup) | pending, needs a decision |

### Gate status

- **`wp plugin check` against a built distributable: 0 errors, 1 warning.** The
  one warning is `NonPrefixedHooknameFound` for `wp_mail_from`, which is a *core*
  filter the plugin deliberately re-applies — not a defect.
- Checking the **dev tree** instead will always show extra findings
  (`hidden_files` for `.gitignore`/`.distignore`/`.wp-env.json`,
  `application_detected` for `phpcs.xml.dist`, `unexpected_markdown_file` for
  `CLAUDE.md`/`RESUME.md`/`TODO-1.2.0.md`). All are removed by `.distignore`.
  **Always check the dist build, not the repo.** Recipe:
  ```
  rsync -a --exclude-from=.distignore --exclude=.git ./ /tmp/dist/frizzly/
  docker cp /tmp/dist/frizzly wp-env-frizzly-f8aebfa5-cli-1:/tmp/d
  docker exec wp-env-frizzly-f8aebfa5-cli-1 sh -c 'cp -r /tmp/d /var/www/html/wp-content/plugins/frizzly-dist'
  docker exec wp-env-frizzly-f8aebfa5-cli-1 wp --allow-root --path=/var/www/html plugin check frizzly-dist
  ```
  Ignore `TextDomainMismatch` in that run — it is an artifact of the `-dist`
  directory name, not a real finding.

### Environment gotchas (cost time once; don't repeat)

- `npx`/`node` are shadowed by a **broken nvm lazy-loader** in this shell; they
  recurse until `FUNCNEST`. Use absolute paths:
  `/Users/abhishekkumar/.nvm/versions/node/v24.18.0/bin/node`.
  `npx wp-env` does not work — drive wp-cli through
  `docker exec wp-env-frizzly-f8aebfa5-cli-1 wp --allow-root --path=/var/www/html ...`.
- The shell is **zsh**: unquoted `$VAR` does *not* word-split, so
  `rsync $EXCLUDES` silently does nothing. Use `--exclude-from=`.
- `timeout` is not installed.

### Verified working

- Stored XSS payloads in post social meta and `twitter_handle` render
  entity-encoded in `<meta>` on the live front end (checked with curl).
- CVE fix: with `PHP_SELF=/wp-admin/index.php/"><svg onload=alert(1)>`, the old
  code produced `/wp-admin/index.php/"><svg onload=alert(1)>?x=1&frizzly_nag...=1`
  (payload verbatim — confirms the vector); the new code produces
  `http://localhost:8888/wp-admin/index.php?frizzly_nag_no_active_modules=1`.
  Normal pages (`edit.php`, `options-general.php`) are preserved, so the "return
  you to where you were" behaviour still works.
- Front end renders share buttons; `debug.log` shows no plugin errors.

**Every `WordPress.Security.*` and `WordPress.DB.*` sniff in the plugin is now
clear.** phpcs went 1536 errors + 127 warnings → **716 errors + 28 warnings**;
all PHP files lint clean.

Nothing is committed yet. All work is in the working tree — `git diff` is the
source of truth. Verify with the per-change checks below before assuming a change
is intact.

### What remains in phpcs (all non-security)

| Sniff | Count | Note |
|---|---|---|
| `Squiz.Commenting.*` | ~380 | missing file/class/function/variable docblocks |
| `Squiz.Scope.MethodScope.Missing` | 149 | methods lack an explicit `public`/`private` |
| `WordPress.Files.FileName.*` | 81 | **decision needed** — would rename all 40 files to `class-frizzly-*.php` and rewrite every `require_once` |
| `WordPress.WP.I18n.MissingTranslatorsComment` | 20 | remaining `printf` sites |
| everything else | ~86 | strict `in_array`, Yoda, `parse_url`, snake_case locals |

The file-renaming sniff is the one to think hard about: it is 81 violations but
touches every file in the plugin and would make the reinstatement diff nearly
unreviewable. Recommended: exclude `WordPress.Files.FileName` in
`phpcs.xml.dist` with a comment, and record the rename in `TODO-1.2.0.md`.

## Change 1 — done

`includes/admin/modules/share/Frizzly_Admin_General_Submodule.php`

```diff
 	private function show_nag_no_active_modules() {
-		global $current_user;
+		global $current_user, $pagenow;
@@
 			admin_url( 'options-general.php?page=frizzly_settings&tab=general' ),
-			add_query_arg( $this->nag_no_active_modules, '1' )
+			esc_url( add_query_arg( $this->nag_no_active_modules, '1', admin_url( $pagenow ) ) )
```

The third argument to `add_query_arg` is the actual fix — it removes the
`$_SERVER['REQUEST_URI']` fallback, so no request-derived string reaches the
`href`. `esc_url()` is defence in depth, not the fix (per the "do not fix by
wrapping alone" rule).

`admin_url( $pagenow )` was chosen over a fully static URL so that "Don't bother
me again" still returns the user to the page they were on. Verified safe against
the real core in this project's wp-env:
`~/.wp-env/wp-env-frizzly-f8aebfa5/WordPress/wp-includes/vars.php:27-49` — for
admin requests `$pagenow` is derived from `PHP_SELF`, query-stripped, then
truncated at the **first slash** and lowercased, so `/wp-admin/index.php/"><svg>`
yields `index.php`. The only shape that leaves taint in `$pagenow` (no slash, no
`?`, e.g. `/wp-admin/"><svg>`) cannot reach a running WordPress — the web server
404s before PHP runs.

Verified: `php -l` clean; phpcs on the file **34 errors / 2 warnings before and
after** (no new violations).

## Key audit facts (so a fresh session need not re-derive them)

- **No logged-out path reads `$_GET`/`$_REQUEST`/`$_COOKIE`/`REQUEST_URI`.** The
  only anonymous request-data reader is `wp_ajax_nopriv_frizzly_share_by_email`,
  which emits JSON with static strings. The CVE is an admin-page reflection
  (attacker unauthenticated, victim must be an admin) — Patchstack's PR:N/UI:R
  scoring convention.
- **The Angular admin bundle does not host the XSS and does not defeat a PHP-side
  fix.** AngularJS 1.5.8, SCE on and never weakened, ngSanitize loaded (so all 13
  `ng-bind-html` are sanitized). App code is `js/frizzly.admin.js` lines 1–1950;
  everything above is vendor.
- `$_GET['tab']` **does** reach `action="{{ }}"` at `js/frizzly.admin.js:886`, but
  is not exploitable there: `wp_json_encode` neutralises the JS-literal context,
  Angular attribute interpolation never parses HTML and never re-interpolates its
  own result, and `form[action]` maps to `$sce.RESOURCE_URL` under whitelist
  `['self']`. **`esc_url`/`esc_attr` at `Frizzly_Admin_Settings_Screen.php:81/88`
  would be a no-op** — change 4 is input validation, justified by the PHP 8 fatal
  at `Frizzly_Admin_Module.php:46/51`, not by XSS.
- Commit `0b7869d` (on `main`'s history, reverted by `a39ee20`) already contains
  correct output-escaping for changes 2 and 3's *output* half. It is **not** the
  `archive/may-2026-attempt` branch that `CLAUDE.md` warns against — safe to lift.
  It does **not** contain the input-sanitising half of change 3.
- The five `$compile` sinks for change 6 are at `js/frizzly.admin.js`
  433-435, 575-577, 616-618, 747-749, 826-828; DI/registration at 489-490,
  540-541, 787-792. All five are fed by `__()` translation strings only.

## Gates

- `npx wp-env run cli wp plugin check frizzly` — the gate that matters; it is what
  wordpress.org review runs. **Not yet run** (needs containers up).
- `composer exec phpcs` — baseline 1536 errors / 127 warnings, 741 auto-fixable.
- PHP 8.3 (primary) and 8.4 (tests), per `.wp-env.json`.

Useful: compare a file against its baseline to prove a change adds no new
violations —
`git show HEAD:<path> > /tmp/base.php && composer exec -- phpcs --report=summary /tmp/base.php`
