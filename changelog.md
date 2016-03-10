# WPGlobus - Multilingual Everything! #

## Changelog ##

### 1.4.8 ###

* FIXED:
	* Post title handling in All in One SEO Pack
	* Yoast SEO tweaks
	
### 1.4.7 ###

* ADDED:
	* Support for Yoast SEO Version 3.1
	
### 1.4.6 ###

* FIXED:
	* Backslash in Quick Edit (additional fixes).
	* Do not add language marks in Quick Edit, if there is only the default language text.
* ADDED:
	* Setting WPGlobus options in Customizer (BETA).

### 1.4.5 ###

* FIXED:
	* Backslash in Quick Edit.

### 1.4.4 ###

* FIXED:
	* In Customizer JS: use `control.selector` to get the ID of parent element correctly.
	* Additional social network names elements disabled by default in Customizer.
	
### 1.4.3 ###

* ADDED:
	* Clean-up Tool to remove all languages except for the main one.

### 1.4.2 ###

* FIXED:
	* Case `data-customize-setting-link` not matching the element name in `wp.customize.control.instance`.
	* Some CSS improvements.
	
### 1.4.1 ###

* FIXED:
	* Untranslated page title with Yoast SEO.
	* Uncaught ReferenceError: WPGlobusCoreData for WooCommerce product without WooCommerce WPGlobus.
	* Adding item menu title for custom taxonomies.
	
### 1.4.0 ###

* ADDED:
	* Support for Yoast SEO Version 3.0
	* Additional flag(s).
	* 'wpglobus-current-language' CSS class for the WPGlobus Widget.
	* Any theme Customizer support.
	* Multilingual Customizer for widgets.

### 1.3.2 ###

* FIXED:
	* Removed double slashes in URLs.
	* Load minimized JS in customizer.
	* Using class for globe icon instead of id.
	* Rewriting the array of classes.
	* Minor css improvements.

### 1.3.1 ###

* FIXED:
	* Disabled Uninstall procedure. Will be refactored in the future.
	* Disable notice on non-existent [key][key] in WPML config.

### 1.3.0 ###

* ADDED:
	* `wpglobus-config.json` now supports Customizer.
	* [Repository of theme configuration files](https://github.com/WPGlobus/wpglobus-config-samples) (W.I.P.)
* FIXED:
	* Bug in WordPress SEO support module (was appending the site name to the SEO Title).

### 1.2.9 ###

* FIXED:
	* Correct extracting domain_tld for two-part TLDs like `.co.uk`.
	* Customizer error. Thanks to [shark0der](https://wordpress.org/support/profile/shark0der).
* ADDED:
	* `pl_PL` admin interface translation.
	* `wpglobus-config.json` configuration file for theme options, with WPML compatibility.
	* `wpglobus-current-language` CSS class to the menu.

### 1.2.8 ###

* FIXED:
	* Minor admin JS bug.

### 1.2.7 ###

* FIXED:
	* Updater bug "cannot delete old plugin files".
	* Broken Welsh flag cy.png (Thanks to Tudor Thomas).

### 1.2.6 ###

* FIXED:
	* `de_DE` admin interface properly translated.
	* Broken links to WPGlobus.com from admin pages.
* ADDED:
	* `tr_TR` admin interface translation.
	* `es` and `fr` enabled by default.
* COMPATIBILITY:
	* All In One SEO Pack 2.2.7.2
	* ACF Pro 5.3.0

### 1.2.5 ###

* FIXED:
	* Core filters refactored to better support sites with no English.
	* Multilingual Excerpt metaboxes styled to 4-lines height.
	* Several code changes related to WordPress and 3rd party plugin upgrades.
* ADDED:
	* Core support for the Black Studio TinyMCE widget.

### 1.2.4 ###

* ADDED:
	* Filter for ACF WYSIWYG fields.
	* `es_ES` admin interface translation files.

### 1.2.3 ###

* FIXED:
	* Return empty hreflangs for 404 page.
	* Duplicate title in admin bar menu.
	* Language ordering icons disappearing with some themes.
* ADDED:
	* Extended options to WPGlobus_Config class
	* 'wpglobus_id' for every option section
	
### 1.2.2 ###

* ADDED:
	* New extension, [WPGlobus for WPBakery Visual Composer](https://wordpress.org/plugins/wpglobus-for-wpbakery-visual-composer/) is referenced on the add-ons page.
	* Support for the [The Events Calendar plugin](https://wordpress.org/plugins/the-events-calendar/).
	* Support hidden ACF groups.
* FIXED:
	* Correct Yoast SEO Page Analysis for the default language.
	* Compatibility with ReduxFramework-based theme options.

### 1.2.1 ###

* FIXED:
	* Correct handling of WP SEO entries containing special HTML characters.
	* Correct handling of title,description and keywords for All In One SEO Pack 2.2.7
	* Incorrect behavior of the menus created from custom type posts.
	* Multilingual strings in Customizer (site name and description).
* ADDED:
	* Support for the [Whistles plugin](https://wordpress.org/plugins/whistles/).
	* Partial support of the All-in-one SEO Pack-PRO.
	* Added full name language without flag for Language Selector Mode option.
* COMPATIBILITY:
	* Yoast SEO 2.3 ( former WordPress SEO )
	
### 1.2.0 ###

* ADDED:
	* Handling the hash part of the URLs.
	* New extension, [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/) is referenced on the add-ons page.
	* Filter 'wpglobus_enabled_pages'
* FIXED:
	* Center the flag icons vertically. Thanks to Nicolaus Sommer for the suggestion.
	* Correct language detection with no trailing slash on home url, i.e. `example.com/fr` works the same as `example.com/fr/`

### 1.1.1 ###

* ADDED:
	* Handling attribute "maxlength" in custom fields for all languages.
	* Support of the WP-SEO 2.2.
	* Compatibility with Redux Framework 3.5.
* FIXED:
	* Language tabs in admin editor styled according to the WP standards.
	* Correct creation of the post title and description for extra languages in AIOSEOP.
	* Enabled translation of the WPGlobus option panel.
	
### 1.0.14 ###

* FIXED:
	* Correct display of trimmed words in admin (filter on `wp_trim_words`).
	* Correct translation of the posts with `---MORE---`.

### 1.0.13 ###

* ADDED:
	* Word count in wp_editor for each language.
	* Admin notice about WPGlobus requiring "nice permalinks".
* FIXED:
	* Correct language setting for URLs like `/fr?s=aaa` with no trailing slash before `?`

### 1.0.12 ###

* FIXED:
	* Language switcher in navigation menus works correctly if WordPress is installed in a subfolder.
* ADDED:
	* New extension, [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/) is referenced on the add-ons page.
	* Support for http://localhost and http://127.0.0.1 development URLs.

### 1.0.11 ###
* FIXED:
	* Method of URL localization correctly parses URLs like `/rush` and `/designer`, not extracting `/ru` and `/de` from them.
	* Admin CSS corrected for the active tab in the WPGlobus dialog.
	* Admin CSS corrected for icon at widgets.php page.
* ADDED:
	* New page for the future extensions and add-ons.
	* The "Disabled entities" array added to the WPGlobus config.
* COMPATIBILITY:
	* WordPress 4.2

### 1.0.10 ###
* FIXED:
	* Admin CSS corrected so it's not easily broken by themes who use their own jQueryUI styling.
	* Modified the Admin language switcher's incorrect behavior occurred in some cases.
	* Corrected pt_PT and pt_BR names, locales and flags.
* COMPATIBILITY:
	* WordPress 4.2-beta3
	* WordPress SEO 2.0.1
	
### 1.0.9 ###
* ADDED:
	* Admin interface to enable/disable WPGlobus for selected metaboxes.
	* Admin interface to enable/disable WPGlobus for selected Custom Post Types.
* FIXED:
	* URL localization with or without `www`, regardless of its presence in `home_url`.
	* Admin language tabs work correctly with custom post types that don't have 'title' or 'editor'.
	* All in One SEO pack plugin works correctly on the `post-new.php` admin page.
	* Language is set correctly during AJAX calls, using `HTTP_REFERER` info.
	* Language is retrieved from the current URL before other plugins load their translations.
	
### 1.0.8.1 ###
* FIXED:
	* Reset hierarchical taxonomies checkmarks after save post or update post's page.
	* Incorrect empty string returning when a non-string argument passed to the text filter.

### 1.0.8 ###
* ADDED:
	* Partial support of the All in One SEO Pack plugin.
	* Change WP Admin language using an Admin bar selector.
* FIXED:
	* Changed flag to `us.png` for the `en_US` locale.
	* Some Admin interface improvements.
	* Corrected field updates at the `edit-tags.php` page.
	* Corrected post saving in WPGlobus developer's mode (toggle off).
	* Support of post types with no `editor` (content).

### 1.0.7.2 ###
* FIXED:
	* URL switching when WordPress serves only part of the site, like `www.example.com/blog`. Reported by [IAmVincentLiu](https://wordpress.org/support/profile/iamvincentliu) - THANKS!

### 1.0.7.1 ###
* FIXED:
	* Anonymous function call prevented installing on PHP 5.2. Related to the reports by [barques](https://wordpress.org/support/profile/barques) and [Jeff Brock](https://wordpress.org/support/profile/jeffbrockstudio) - THANKS!

### 1.0.7 ###
* ADDED:
	* WPGlobus Language Selector widget.
	* Enable language selector in navigation menus created using `wp_list_pages`.
	* Frontend filter meta description for All In One SEO Pack plugin.
* FIXED:
	* CSS for WPGlobus Universal Editor buttons.

### 1.0.6 ###
* ADDED:
	* Admin interface and front filter to translate widgets.
	* Deutsch (de_DE) PO / MO-Dateien für WPGlobus Administration.
* FIXED:
	* Clean subjects of the comment notification emails.

### 1.0.5 ###
* ADDED:
	* Localization interface for ACF text and textarea fields; no need to format languages manually.
	* Localization interface for the standard Custom Fields.

### 1.0.4 ###
* FIXED:
	* Disabled WPGlobus admin interface on ACF screens - until we support them properly.
* ADDED:
	* Frontend filter acf/load_value/type=text(area): works if the fields were manually formatted {:en}...{:}

### 1.0.3 ###
* FIXED:
	* PHP notice on plugin activation hook when a theme is upgraded.
	* Language selector drop-down applied to all menus instead of the selected one.
	* Correct display of the default category name on the edit-tags.php?taxonomy=category page.

### 1.0.2 ###
* FIXED:
	* Save posts correctly if no default language title entered
	* Preserve languages for trashed, and later restored posts
	* Save languages correctly at heartbeat for pending and drafts
* ADDED:
	* Filter to translate title attributes in nav menus

### 1.0.1 ###
* FIXED:
	* Line breaks disappear in visual mode during autosave
	* Correct display of slug in WP-SEO panel

### 1.0.0 ###
* Beta-version of the plugin.
* Can translate all basic elements of WordPress
* WP-SEO by Yoast is supported
* ?lang= URLs dropped

### 0.1.1 ###
* FIX: Notice 'walker_nav_menu_start_el' filter in functions.php twentyfifteen theme

### 0.1.0 ###
* Initial release (language switcher)
