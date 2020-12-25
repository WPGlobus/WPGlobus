# TIVWP Updater - Changelog #

## 2020-12-25 - version 1.0.10

* Fix: removed outdated migration code.
* Fix: Update status after (de)activation.
* Tweak: general code cleanup.

## 2018-03-23 - version 1.0.9

* Fix: Invalid requests when `php.ini` or `.htaccess` has the `arg_separator.output=&amp;` setting.

## 2017-12-06 - version 1.0.8

* Fix: The `JSON_OBJECT_AS_ARRAY` constant is replaced with `true` to be compatible with the `PHP 5.3`.

## 2017-01-21 - version 1.0.7

* Fix: If a deactivation request returns status `"inactive"`, reset the local status and allow keys editing.

## 2017-01-17 - version 1.0.6
* Added: `tivwp-updater-ok-to-run` filter to continue running even if there is a `.git` folder.

> **Warning:** do not run the actual update or it will destroy the Git! Use it for activation tests only!

## 2016-09-28 - version 1.0.5
* Added: Translations for the active/inactive status texts.
* Fix: Fixed a license activation bug that occurred when plugin "slug" did not match the plugin folder.
* Fix: Do not compact JS and CSS in `SCRIPT_DEBUG` mode.
* Fix: Simplified CSS rules.
* Fix: Print the library version in CSS and JS.
