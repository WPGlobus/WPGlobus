# TIVWP Updater
## Change log

All notable changes to this project will be documented in this file.

## [1.0.9] 2018-03-23
### Fixed

Invalid requests when `php.ini` or `.htaccess` has the `arg_separator.output=&amp;` setting.

## [1.0.8] 2017-12-06
### Fixed

The `JSON_OBJECT_AS_ARRAY` constant is replaced with `true` to be compatible with the `PHP 5.3`.

## [1.0.7] 2017-01-21
### Fixed

If a deactivation request returns status `"inactive"`, reset the local status and allow keys editing.

## [1.0.6] 2017-01-17
### Added
`tivwp-updater-ok-to-run` filter to continue running even if there is a `.git` folder.

> **Warning:** do not run the actual update or it will destroy the Git! Use it for activation tests only!

## [1.0.5] 2016-09-28
### Added
- Translations for the active/inactive status texts.

### Changed
- Fixed a license activation bug that occurred when plugin "slug" did not match the plugin folder.
- Do not compact JS and CSS in `SCRIPT_DEBUG` mode.
- Simplified CSS rules.
- Print the library version in CSS and JS.
