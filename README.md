# WPGlobus - Multilingual Everything! #
**Contributors:** tivnetinc, alexgff, tivnet  
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN  
**Tags:** bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, multilingual SEO, language switcher, translate, translation, TIVWP, WPGlobus  
**Requires at least:** 4.0  
**Tested up to:** 4.2.2  
**Stable tag:** trunk  
**License:** GPLv2  
**License URI:** https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE  

**Multilingual / Globalization:** URL-based multilanguage; easy translation interface, compatible with WP SEO by Yoast, All in One SEO Pack and ACF!  

## Description ##

**WPGlobus** is a family of WordPress plugins assisting you in making bilingual / multilingual WordPress blogs and sites.

The **WPGlobus Free Core plugin** provides you with the main multilingual tools. After you install it, you will be able to:

* Add one or several languages to your WP blog/site, so that the users with the required capabilities can:
	* translate Posts and Pages to multiple languages using an easy tabbed interface;
**	* translate major taxonomies:** categories and tags;  
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
* Switch the Administrator interface language using a top bar selector;
* Use the WPGlobus option panel to select active languages and define custom combinations of country flag and language abbreviation.

The WPGlobus plugin serves as the **foundation** to other plugins in the family:

* [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/):
	* This is a paid WPGlobus extension, which adds multilingual capabilities to WooCommerce-based online stores.

* [WPGlobus Featured Images](https://wordpress.org/plugins/wpglobus-featured-images/):
	* This plugin allows setting featured images separately for each language.

* [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/):
	* This plugin enables selective translation of the `wp_options` table strings. You need to use it when your theme or a 3rd party plugin (a slider, for example) has its own option panel, where you enter texts.

### Demos ###

* [WPGlobus.com](http://www.wpglobus.com/):
	* Bilingual site using a variety of posts, pages, custom post types, forms, a slider and a WooCommerce store with Subscription and API extensions.
* [Site in subfolder](http://demo-subfolder.wpglobus.com/):
	* Demonstration of two WPGlobus-powered sites, one of which is installed in a subfolder of another. Shows the correct behavior of WPGlobus with URLs like `example.com/folder/wordpress`.
* [WooCommerce Multilingual](http://demo-store.wpglobus.com/):
	* A **multilingual WooCommerce** site powered by the `woocommerce-wpglobus` plugin.

### WPGlobus is compatible with many popular plugins, including: ###

* [WordPress SEO](https://yoast.com/wordpress/plugins/seo/) by [Joost de Valk](https://profiles.wordpress.org/joostdevalk/);
* [All in One SEO Pack](https://wordpress.org/plugins/all-in-one-seo-pack/) by [Michael Torbert](https://profiles.wordpress.org/hallsofmontezuma/);
* [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) by [Elliot Condon](https://profiles.wordpress.org/elliotcondon/);
* [Sidebar Login](https://wordpress.org/plugins/sidebar-login/) by [Mike Jolley](https://profiles.wordpress.org/mikejolley/).

### 3rd Party Software and Files Used ###

* [ReduxFramework](http://reduxframework.com/) core is embedded in the plugin to provide a nice admin interface. If the [ReduxFramework Plugin](https://wordpress.org/plugins/redux-framework/) is already installed, it will be used instead of the embedded version.
* Most of the flag images are downloaded from the [Flags of the World](http://www.crwflags.com/FOTW/FLAGS/index.html) website.

### More info and ways to contact the WPGlobus Development Team ###

* [WPGlobus.com website](http://www.wpglobus.com/).
* [Open source code on GitHub](https://github.com/WPGlobus).
* WPGlobus on social networks:
	* [Facebook](https://www.facebook.com/WPGlobus)
	* [Twitter](https://twitter.com/WPGlobus)
	* [Google Plus](https://plus.google.com/+Wpglobus)
	* [LinkedIn](https://www.linkedin.com/company/wpglobus)

> **If you see some obscure errors that the development team might not yet discovered, please let us know! Your reports are appreciated!**

## Installation ##

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the WPGlobus plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

## Frequently Asked Questions ##

From the [FAQ Archives on the WPGlobus Website](http://www.wpglobus.com/faq/):

* [Do you support PHP 5.x?](http://www.wpglobus.com/faq/support-php-5-2/)
* [Do you support MSIE / Opera / Safari / Chrome / Firefox - Version x.x?](http://www.wpglobus.com/faq/support-msie-opera-safari-chrome-firefox/)
* [Do you plan to support subdomains and URL query parameters?](http://www.wpglobus.com/faq/subdomains-and-url-query-parameters/)
* [I am using WPML (qTranslate, Polylang, Multilingual Press, etc.). Can I switch to WPGlobus?](http://www.wpglobus.com/faq/i-am-using-wpml-qtranslate-polylang-multilingual-press-etc-can-i-switch-to-wpglobus/)
* [Do you plan to support WooCommerce, EDD, other e-Commerce plugins?](http://www.wpglobus.com/faq/support-woocommerce-edd/)
* [Is it possible to set the user's language automatically based on IP and/or browser language?](http://www.wpglobus.com/faq/set-language-by-ip/)
* [How do I contribute to WPGlobus?](http://www.wpglobus.com/faq/how-do-i-contribute-to-wpglobus/)

## Screenshots ##

**1. Admin interface:** languages setup.  
**2. Front:** language switcher in the twentyfourteen theme menu.  

## Upgrade Notice ##

No known backward incompatibility issues.

## Changelog ##

### 1.2.0 ###

* ADDED:
	* Handling the hash part of the URLs.
	* New extension, [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/) is referenced on the add-ons page.
	* Filter 'wpglobus_enabled_pages'
* FIXED:
	* Center the flag icons vertically. Thanks to Nicolaus Sommer for the suggestion.
	* Correct language detection with no trailing slash on home url, i.e. `example.com/fr` works the same as `example.com/fr/`

### 1.1.1.3 ###

* FIXED:
	* Bug with switching languages when WordPress is in a subfolder of the main site.

### 1.1.1.2 ###

* FIXED:
	* js script for WPSEO 2.2

### 1.1.1.1 ###

* FIXED:
	* Wrong behavior when the main language is not English. Thanks to [Klaus Feurich](https://wordpress.org/support/profile/lunymarmusic) for reporting the bug.
	* Restored default path to ReduxCore because of silly side effects.

### 1.1.1 ###

* ADDED:
	* Handling attribute "maxlength" in custom fields for all languages.
	* Support of the WP-SEO 2.2.
	* Compatibility with Redux Framework 3.5.
* FIXED:
	* Language tabs in admin editor styled according to the WP standards.
	* Correct creation of the post title and description for extra languages in AIOSEOP.
	* Enabled translation of the WPGlobus option panel.

### Earlier versions ###

* [See the complete changelog here](https://github.com/WPGlobus/WPGlobus/blob/master/changelog.md)

### WooCommerce-WPGlobus ###

* [See the changelog here](http://www.wpglobus.com/extensions/woocommerce-wpglobus/woocommerce-wpglobus-changelog/)
