=== WPGlobus - Multilingual Everything! ===
Contributors: tivnetinc, alexgff, tivnet
Donate link: http://www.wpglobus.com/
Tags: bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, multilingual SEO, language switcher, translate, translation, TIVWP, WPGlobus
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Multilingual / Globalization: URL-based multilanguage; easy translation interface, compatible with WP SEO by Yoast, All in One SEO Pack and ACF!

== Description ==

**WPGlobus** is a plugin for making bilingual / multilingual WordPress blogs and sites.

> **Versions 1.0.x are released to the general public as Beta Software. During the Beta stage, the software is tested for bugs, crashes, errors and inconsistencies. If you see some obscure errors that the development team might not yet discovered, please let us know! Your reports are appreciated!**

With the WPGlobus Free Core plugin, you can:

* Add one or several languages to your WP blog/site, so that the users with the required capabilities can:
	* translate Posts and Pages to multiple languages using an easy tabbed interface;
	* translate major taxonomies: categories and tags;
	* translate menus and widgets;
* Enable multilingual SEO features of:
    * WP SEO by Yoast;
    * All in One SEO Pack by Michael Torbert.
* Translate Custom Fields:
	* standard;
	* created with the Advanced Custom Fields plugin.
* Switch the languages at the Front using:
	* a drop-down menu extension;
	* a customizable widget with various display options;
* Switch the Administrator interface language using a top bar selector.

The WPGlobus option panel allows for selecting active languages as well as defining custom combinations of country flag and language abbreviation.

= Demos =

* [Site in subfolder](http://demo-subfolder.wpglobus.com/):
	* Demonstration of two WPGlobus-powered sites, one of which is installed in a subfolder of another. Shows the correct behavior of WPGlobus with URLs like `example.com/folder/wordpress`.
* [WooCommerce Multilingual](http://demo-store.wpglobus.com/):
	* A **multilingual WooCommerce** site powered by the `woocommerce-wpglobus` plugin (work in progress, will be released soon).

= Free Add-ons =

* [WPGlobus Featured Images](https://wordpress.org/plugins/wpglobus-featured-images/):
	* Set featured image separately for each language.

* [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/):
	* Enable translate of the option values stored in the `wp_options` table.

* More extensions are planned:
	* Stay tuned, search the repository for [WPGlobus](https://wordpress.org/plugins/search.php?q=WPGlobus).

= WPGlobus is compatible with these popular plugins: =

* [WordPress SEO](https://yoast.com/wordpress/plugins/seo/) by [Joost de Valk](https://profiles.wordpress.org/joostdevalk/);
* [All in One SEO Pack](https://wordpress.org/plugins/all-in-one-seo-pack/) by [Michael Torbert](https://profiles.wordpress.org/hallsofmontezuma/);
* [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) by [Elliot Condon](https://profiles.wordpress.org/elliotcondon/);
* [Sidebar Login](https://wordpress.org/plugins/sidebar-login/) by [Mike Jolley](https://profiles.wordpress.org/mikejolley/).

= 3rd Party Software and Files Used =

* [ReduxFramework](http://reduxframework.com/) core is embedded in the plugin to provide a nice admin interface. If the [ReduxFramework Plugin](https://wordpress.org/plugins/redux-framework/) is already installed, it will be used instead of the embedded version.
* Most of the flag images are downloaded from the [Flags of the World](http://www.crwflags.com/FOTW/FLAGS/index.html) website.

= More info and ways to contact the WPGlobus Development Team =

* [WPGlobus.com website](http://www.wpglobus.com/).
* [Open source code on GitHub](https://github.com/WPGlobus).
* WPGlobus on social networks:
	* [Facebook](https://www.facebook.com/WPGlobus)
	* [Twitter](https://twitter.com/WPGlobus)
	* [Google Plus](https://plus.google.com/+Wpglobus)
	* [LinkedIn](https://www.linkedin.com/company/wpglobus)

== Installation ==

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the WPGlobus plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

From the [FAQ Archives on the WPGlobus Website](http://www.wpglobus.com/faq/):

* [Do you support PHP 5.x?](http://www.wpglobus.com/faq/support-php-5-2/)
* [Do you support MSIE / Opera / Safari / Chrome / Firefox - Version x.x?](http://www.wpglobus.com/faq/support-msie-opera-safari-chrome-firefox/)
* [Do you plan to support subdomains and URL query parameters?](http://www.wpglobus.com/faq/subdomains-and-url-query-parameters/)
* [I am using WPML (qTranslate, Polylang, Multilingual Press, etc.). Can I switch to WPGlobus?](http://www.wpglobus.com/faq/i-am-using-wpml-qtranslate-polylang-multilingual-press-etc-can-i-switch-to-wpglobus/)
* [Do you plan to support WooCommerce, EDD, other e-Commerce plugins?](http://www.wpglobus.com/faq/support-woocommerce-edd/)
* [Is it possible to set the user's language automatically based on IP and/or browser language?](http://www.wpglobus.com/faq/set-language-by-ip/)
* [How do I contribute to WPGlobus?](http://www.wpglobus.com/faq/how-do-i-contribute-to-wpglobus/)

== Screenshots ==

1. Admin interface: languages setup.
2. Front: language switcher in the twentyfourteen theme menu.

== Upgrade Notice ==

No known backward incompatibility issues.

== Changelog ==

= 1.0.14 =

* FIXED:
	* Correct display of trimmed words in admin (filter on `wp_trim_words`).
	* Correct translation of the posts with `---MORE---`.

= 1.0.13 =

* ADDED:
	* Word count in wp_editor for each language.
	* Admin notice about WPGlobus requiring "nice permalinks".
* FIXED:
	* Correct language setting for URLs like `/fr?s=aaa` with no trailing slash before `?`

= 1.0.12 =

* FIXED:
	* Language switcher in navigation menus works correctly if WordPress is installed in a subfolder.
* ADDED:
	* New extension, [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/) is referenced on the add-ons page.
	* Support for http://localhost and http://127.0.0.1 development URLs.

= 1.0.11 =
* FIXED:
	* Method of URL localization correctly parses URLs like `/rush` and `/designer`, not extracting `/ru` and `/de` from them.
	* Admin CSS corrected for the active tab in the WPGlobus dialog.
	* Admin CSS corrected for icon at widgets.php page.
* ADDED:
	* New page for the future extensions and add-ons.
	* The "Disabled entities" array added to the WPGlobus config.
* COMPATIBILITY:
	* WordPress 4.2

= 1.0.10 =
* FIXED:
	* Admin CSS corrected so it's not easily broken by themes who use their own jQueryUI styling.
	* Modified the Admin language switcher's incorrect behavior occurred in some cases.
	* Corrected pt_PT and pt_BR names, locales and flags.
* COMPATIBILITY:
	* WordPress 4.2-beta3
	* WordPress SEO 2.0.1
	
= 1.0.9 =
* ADDED:
	* Admin interface to enable/disable WPGlobus for selected metaboxes.
	* Admin interface to enable/disable WPGlobus for selected Custom Post Types.
* FIXED:
	* URL localization with or without `www`, regardless of its presence in `home_url`.
	* Admin language tabs work correctly with custom post types that don't have 'title' or 'editor'.
	* All in One SEO pack plugin works correctly on the `post-new.php` admin page.
	* Language is set correctly during AJAX calls, using `HTTP_REFERER` info.
	* Language is retrieved from the current URL before other plugins load their translations.
	
= 1.0.8.1 =
* FIXED:
	* Reset hierarchical taxonomies checkmarks after save post or update post's page.
	* Incorrect empty string returning when a non-string argument passed to the text filter.

= 1.0.8 =
* ADDED:
	* Partial support of the All in One SEO Pack plugin.
	* Change WP Admin language using an Admin bar selector.
* FIXED:
	* Changed flag to `us.png` for the `en_US` locale.
	* Some Admin interface improvements.
	* Corrected field updates at the `edit-tags.php` page.
	* Corrected post saving in WPGlobus developer's mode (toggle off).
	* Support of post types with no `editor` (content).

= 1.0.7.2 =
* FIXED:
	* URL switching when WordPress serves only part of the site, like `www.example.com/blog`. Reported by [IAmVincentLiu](https://wordpress.org/support/profile/iamvincentliu) - THANKS!

= 1.0.7.1 =
* FIXED:
	* Anonymous function call prevented installing on PHP 5.2. Related to the reports by [barques](https://wordpress.org/support/profile/barques) and [Jeff Brock](https://wordpress.org/support/profile/jeffbrockstudio) - THANKS!

= 1.0.7 =
* ADDED:
	* WPGlobus Language Selector widget.
	* Enable language selector in navigation menus created using `wp_list_pages`.
	* Frontend filter meta description for All In One SEO Pack plugin.
* FIXED:
	* CSS for WPGlobus Universal Editor buttons.

= 1.0.6 =
* ADDED:
	* Admin interface and front filter to translate widgets.
	* Deutsch (de_DE) PO / MO-Dateien für WPGlobus Administration.
* FIXED:
	* Clean subjects of the comment notification emails.

= 1.0.5 =
* ADDED:
	* Localization interface for ACF text and textarea fields; no need to format languages manually.
	* Localization interface for the standard Custom Fields.

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