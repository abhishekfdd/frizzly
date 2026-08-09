# Frizzly

WordPress plugin, closed on wordpress.org since 2025-03-11 for
CVE-2025-30554, an unauthenticated reflected XSS. Goal is a
security-only 1.1.1 release that gets the plugin reinstated.

## Prior attempt
An earlier remediation attempt exists on branch archive/may-2026-attempt
and in ~/Sites/frizzly-may-2026.patch. It was built on a stale base and
targeted a stored XSS, not the reported unauthenticated reflected XSS.
Do not apply it. You may read it for reference only after completing
your own independent analysis.

## Scope rules
- Security fixes only. No refactoring, no modernisation, no
  reformatting, no renaming, no "while I'm here" changes.
- If you spot something worth changing that is not a security
  fix, add it to TODO-1.2.0.md instead of changing it.
- One concern per change. Show the diff and stop.
- Never touch ../frizzly-svn. Releases are manual.

## Verification
- `npx wp-env run cli wp plugin check frizzly` must be clean.
- `composer exec phpcs` must be clean.
- Tested on WP latest with PHP 8.3 and 8.4.

## Escaping
Use the context-correct WP function (esc_html, esc_attr, esc_url,
esc_textarea, wp_kses_post). Never htmlspecialchars.
Sanitise on input with wp_unslash plus the right sanitize_* call.