=== WPGlobus - Multilingual Everything! ===
Contributors: tivnetinc, alexgff, tivnet
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN
Tags: bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, multilingual SEO, language switcher, translate, translation, TIVWP, WPGlobus
Requires at least: 4.0
Tested up to: 4.3
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Multilingual / Globalization: URL-based multilanguage; easy translation interface, compatible with Yoast SEO, All in One SEO Pack and ACF!

== Description ==

**WPGlobus** is a family of WordPress plugins assisting you in making bilingual / multilingual WordPress blogs and sites.

The **WPGlobus Free Core plugin** provides you with the main multilingual tools. After you install it, you will be able to:

* Add one or several languages to your WP blog/site, so that the users with the required capabilities can:
	* manually translate Posts and Pages to multiple languages using an easy tabbed interface;
	* translate major taxonomies (categories and tags);
	* translate menus and widgets;
* Enable multilingual SEO features of:
	* Yoast SEO;
	* All in One SEO Pack by Michael Torbert;
	* WooCommerce (paid add-on).
* Translate Custom Fields:
	* standard;
	* created with the Advanced Custom Fields plugin.
* Switch the languages at the Front using:
	* a drop-down menu extension;
	* a customizable widget with various display options;
* Switch the Administrator interface language using a top bar selector;
* Use the WPGlobus option panel to select active languages and define custom combinations of country flag and language abbreviation.

> **NOTE: WPGlobus does NOT translate texts automatically! To see how it works, please read the [Quick Start Guide](http://www.wpglobus.com/quick-start/).**

The WPGlobus plugin serves as the **foundation** to other plugins in the family:

* [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/):
	* This is a paid WPGlobus extension, which adds multilingual capabilities to WooCommerce-based online stores.

* [WPGlobus Featured Images](https://wordpress.org/plugins/wpglobus-featured-images/):
	* This plugin allows setting featured images separately for each language.

* [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/):
	* This plugin enables selective translation of the `wp_options` table strings. You need to use it when your theme or a 3rd party plugin (a slider, for example) has its own option panel, where you enter texts.

* [WPGlobus for WPBakery Visual Composer](https://wordpress.org/plugins/wpglobus-for-wpbakery-visual-composer/):
	* This extension enables WPGlobus on certain themes that use WPBakery's Composer. Please note that Visual Composer is a commercial product, and therefore our support is limited.

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

Then please read the [Quick Start Guide](http://www.wpglobus.com/quick-start/).

== Frequently Asked Questions ==

= Please read these first: =

* [The Quick Start Guide](http://www.wpglobus.com/quick-start/)
* [Before contacting Support...](http://www.wpglobus.com/before-contacting-wpglobus-support/)

From the [WPGlobus FAQ Archives](http://www.wpglobus.com/faq/):

* [Do you support PHP 5.x?](http://www.wpglobus.com/faq/support-php-5-2/)
* [Do you support MSIE / Opera / Safari / Chrome / Firefox - Version x.x?](http://www.wpglobus.com/faq/support-msie-opera-safari-chrome-firefox/)
* [Do you plan to support subdomains and URL query parameters?](http://www.wpglobus.com/faq/subdomains-and-url-query-parameters/)
* [I am using WPML (qTranslate, Polylang, Multilingual Press, etc.). Can I switch to WPGlobus?](http://www.wpglobus.com/faq/i-am-using-wpml-qtranslate-polylang-multilingual-press-etc-can-i-switch-to-wpglobus/)
* [Do you plan to support WooCommerce, EDD, other e-Commerce plugins?](http://www.wpglobus.com/faq/support-woocommerce-edd/)
* [Is it possible to set the user's language automatically based on IP and/or browser language?](http://www.wpglobus.com/faq/set-language-by-ip/)
* [How do I contribute to WPGlobus?](http://www.wpglobus.com/faq/how-do-i-contribute-to-wpglobus/)

== Screenshots ==

1. Welcome screen.
2. Settings panel.
3. Languages setup.
4. Attaching language switcher to a menu.
5. Editing post in multiple languages.
6. Multilingual Yoast SEO and Featured Images.
7. Language Switcher widget and Multilingual Editor dialog.
8. Multilingual WooCommerce store powered by [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/).

== Upgrade Notice ==

No known backward incompatibility issues.

== Changelog ==

= 1.2.2 =

* ADDED:
	* New extension, [WPGlobus for WPBakery Visual Composer](https://wordpress.org/plugins/wpglobus-for-wpbakery-visual-composer/) is referenced on the add-ons page.
	* Support for the [The Events Calendar plugin](https://wordpress.org/plugins/the-events-calendar/).
	* Support hidden ACF groups.
* FIXED:
	* Correct Yoast SEO Page Analysis for the default language.
	* Compatibility with ReduxFramework-based theme options.

= 1.2.1 =

* FIXED:
	* Correct handling of Yoast SEO entries containing special HTML characters.
	* Correct handling of title, description and keywords for All In One SEO Pack 2.2.7
	* Incorrect behavior of the menus created from custom type posts.
	* Multilingual strings in Customizer (site name and description).
* ADDED:
	* Support for the [Whistles plugin](https://wordpress.org/plugins/whistles/).
	* Partial support of the All-in-one SEO Pack-PRO.
	* Added full name language without flag for Language Selector Mode option.
* COMPATIBILITY:
	* Yoast SEO 2.3 ( former WordPress SEO )
	
= 1.2.0 =

* ADDED:
	* Handling the hash part of the URLs.
	* New extension, [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/) is referenced on the add-ons page.
	* Filter 'wpglobus_enabled_pages'
* FIXED:
	* Center the flag icons vertically. Thanks to Nicolaus Sommer for the suggestion.
	* Correct language detection with no trailing slash on home url, i.e. `example.com/fr` works the same as `example.com/fr/`

= 1.1.1.3 =

* FIXED:
	* Bug with switching languages when WordPress is in a subfolder of the main site.

= 1.1.1.2 =

* FIXED:
	* js script for WPSEO 2.2

= Earlier versions =

* [See the complete changelog here](https://github.com/WPGlobus/WPGlobus/blob/master/changelog.md)

= WooCommerce-WPGlobus =

* [See the changelog here](http://www.wpglobus.com/extensions/woocommerce-wpglobus/woocommerce-wpglobus-changelog/)

== Demo Sites ==

* [WPGlobus.com](http://www.wpglobus.com/):
	* Bilingual site using a variety of posts, pages, custom post types, forms, a slider and a WooCommerce store with Subscription and API extensions.
* [Site in subfolder](http://demo-subfolder.wpglobus.com/):
	* Demonstration of two WPGlobus-powered sites, one of which is installed in a subfolder of another. Shows the correct behavior of WPGlobus with URLs like `example.com/folder/wordpress`.
* [WooCommerce Multilingual](http://demo-store.wpglobus.com/):
	* A **multilingual WooCommerce** site powered by the `woocommerce-wpglobus` plugin.

