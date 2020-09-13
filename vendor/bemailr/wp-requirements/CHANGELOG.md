# WP Requirements
## Change log

All notable changes to this project will be documented in this file.

## [2.0.3] 2016-10-18

### Changed
- Exclude development files from distribution (Composer "dist").

### Internal
- Grunt cleanup.

## [2.0.2] 2016-09-21
### Added
- String translations (PO/MO) and a Grunt script to generate them.

### Changed
- `$locale` is not a parameter anymore. Translations use the `'wp-requirements'` text domain.
- Theme name is now printed correctly if validation failed.
- No more bailing out if the plugin file does not exist - to support a "must not be active" requirement.

## [2.0.1] 2016-09-07
### Added
- Can specify a boolean `true/false` instead of the plugin version.

## [2.0.0] 2016-09-01
New major release, not backward-compatible with the previous versions.
The versions `1.*` are considered obsolete and should not be used.

