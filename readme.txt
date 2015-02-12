=== WPGlobus ===
Contributors: tivnet, alexgff
Tags: bilingual, globalization, i18n, international, l10n, language, localization, multilanguage, multilingual, switcher, translate, translation, WPGlobus, TIVWP
Requires at least: 3.8
Tested up to: 4.1
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Adds a language switcher drop-down to navigation menus.

== Description ==

**WPGlobus** is a plugin for bilingual / multilingual WordPress sites.

The current version of the plugin:

* Provides admin interface to translate posts, pages, menus, categories and tags to multiple languages.
* Adds a drop-down menu to a navigation menu, thus allowing to switch between languages, by changing the URL (`/{language}/page/`)

**Note:** the `/page/?lang={language}` URLs or subdomains are not supported.

The administrator interface allows for selecting active languages as well as defining custom combinations of country flag and language abbreviation.

= 3rd Party Software and Files Used =

[ReduxFramework](http://reduxframework.com/) core is embedded in the plugin to provide a nice admin interface. If the [ReduxFramework Plugin](https://wordpress.org/plugins/redux-framework/) is already installed, it will be used instead of the embedded version.

Most of the flag images are downloaded from the [Flags of the World](http://www.crwflags.com/FOTW/FLAGS/index.html) website.

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

= 1.0.0 =
* Beta-version of the plugin.
* Can translate all basic elements of WordPress
* WP-SEO by Yoast is supported
* ?lang= URLs dropped

= 0.1.1 =
* FIX: Notice 'walker_nav_menu_start_el' filter in functions.php twentyfifteen theme

= 0.1.0 =
* Initial release (language switcher)