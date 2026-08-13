# Deferred to 1.2.0

Items found during the 1.1.1 security release that were deliberately left out to
keep the reinstatement diff reviewable.

## Rename files to the WordPress `class-*.php` convention

`WordPress.Files.FileName` is currently excluded in `phpcs.xml.dist` (81
violations). Complying means renaming all 40 source files
(`Frizzly_Admin.php` -> `class-frizzly-admin.php`, etc.) and rewriting every
`require_once` path in `Frizzly.php`, `Frizzly_Client.php`,
`Frizzly_Admin.php`, `Frizzly_Includes.php`, `Frizzly_Ajax.php`,
`Frizzly_Client_Share_Module.php` and `Frizzly_Admin_Share_Module.php`.
Mechanical, but it touches every file in the plugin.

Remove the `<exclude name="WordPress.Files.FileName"/>` block when this is done.

## Replace the hand-maintained `js/frizzly.admin.js` bundle

There is no build source in the repo - no `package.json`, no `src/`. The 18k-line
browserify bundle (AngularJS 1.5.8, ngSanitize, angular-tooltips,
angular-drag-and-drop-lists) has to be hand-edited, which is how the 1.1.1
`$compile` hardening had to be applied. Either restore the original build
toolchain or rewrite the settings screen without AngularJS 1.x, which has been
end-of-life since January 2022.

## `wp_mail_from` filter application

`Frizzly_Share_By_Email_Ajax_Handler::send_email()` re-applies the core
`wp_mail_from` filter, which `wp plugin check` flags as a non-prefixed hook name.
It is intentional (the code sets an explicit `From:` header, so `wp_mail()` would
not fire the filter itself), but it is worth revisiting whether the plugin should
be constructing the sender address at all.

## `Requires at least: 6.5`

Set in 1.1.1 to match `minimum_wp_version` in `phpcs.xml.dist`. The true
functional minimum is 4.7 (`sanitize_textarea_field`). If supporting older
installs matters, lower the readme header and the `phpcs.xml.dist` config
together.

## `Frizzly_Client_Image_Submodule::fjarrett_get_attachment_id_by_url()`

Third-party copy-paste that runs a `RLIKE` query against `wp_posts.guid`. Now
cached and properly prepared, but `attachment_url_to_postid()` has been in core
since 4.0 and should replace it outright.

## Unused code

- `Frizzly_Client_Image_Submodule::get_attachment()` and
  `get_attachment_id_from_image_classes()` are never called.
- `js/frizzly.admin.js` `appComponent` (top of the bundle) is never registered
  and references components that no longer exist.
- `Frizzly_Admin_Settings_Screen` binds `i18n.status` in the Angular template but
  PHP never sets it; the `status` component has no producer because the
  `frizzly_settings_custom_*` filter has no listeners.
