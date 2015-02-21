=== WPGlobus ===
Contributors: tivnet, alexgff
Donate link: http://www.wpglobus.com/
Tags: bilingual, globalization, i18n, international, l10n, language, localization, multilanguage, multilingual, multilingual SEO, SEO, switcher, translate, translation, WPGlobus, TIVWP
Requires at least: 4.0
Tested up to: 4.1.1
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Multilingual / Globalization: URL-based multilanguage; easy translation interface with WordPress SEO by Yoast supported!

== Description ==

**WPGlobus** is a plugin for bilingual / multilingual WordPress sites.

The current version of the plugin:

* Provides admin interface to translate posts, pages, menus, categories and tags to multiple languages.
* Adds a drop-down menu to a navigation menu, thus allowing to switch between languages, by changing the URL (`/{language}/page/`)

> **Note:** the `/page/?lang={language}` URLs or subdomains are not supported.

The administrator interface allows for selecting active languages as well as defining custom combinations of country flag and language abbreviation.

= More info =

* [WPGlobus.com website](http://www.wpglobus.com/).
* [WPGlobus code on GitHub](https://github.com/WPGlobus).
* WPGlobus on:
	* [LinkedIn](https://www.linkedin.com/company/wpglobus),
	* [Facebook](https://www.facebook.com/WPGlobus),
	* [Twitter](https://twitter.com/WPGlobus) and
	* [Google Plus](https://plus.google.com/111657854098133499126).
* The [WordPress SEO Plugin](https://yoast.com/wordpress/plugins/seo/).

= 3rd Party Software and Files Used =

* [ReduxFramework](http://reduxframework.com/) core is embedded in the plugin to provide a nice admin interface. If the [ReduxFramework Plugin](https://wordpress.org/plugins/redux-framework/) is already installed, it will be used instead of the embedded version.
* Most of the flag images are downloaded from the [Flags of the World](http://www.crwflags.com/FOTW/FLAGS/index.html) website.

== Installation ==

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the WPGlobus plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

Please check out the [WPGlobus Website](http://www.wpglobus.com/) for additional information.

== Screenshots ==

1. Admin interface: languages setup.
2. Front: language switcher in the twentyfourteen theme menu.

== Upgrade Notice ==

= 1.0.0 =
The plugin is currently in BETA stage! WPGlobus only supports the localization URLs in the form of example.com/xx/page/. We do not plan to support subdomains (xx.example.com) and language queries (example.com?lang=xx).

== Changelog ==

= 1.0.4 =
* FIXED:
	* Disabled WPGlobus admin interface on ACF screens - until we support them properly.
* ADDED:
	* Frontend filter acf/load_value/type=text(area): works if the fields were manually formatted {:en}...{:}

= 1.0.3 =
* FIXED:
	* PHP notice on plugin activation hook when a theme is upgraded.
	* Language selector drop-down applied to all menus instead of the selected one.
	* Correct display of the default category name on the edit-tags.php?taxonomy=category page.

= 1.0.2 =
* FIXED:
	* Save posts correctly if no default language title entered
	* Preserve languages for trashed, and later restored posts
	* Save languages correctly at heartbeat for pending and drafts
* ADDED:
	* Filter to translate title attributes in nav menus

= 1.0.1 =
* FIXED:
	* Line breaks disappear in visual mode during autosave
	* Correct display of slug in WP-SEO panel

= 1.0.0 =
* Beta-version of the plugin.
* Can translate all basic elements of WordPress
* WP-SEO by Yoast is supported
* ?lang= URLs dropped

= 0.1.1 =
* FIX: Notice 'walker_nav_menu_start_el' filter in functions.php twentyfifteen theme

= 0.1.0 =
* Initial release (language switcher)