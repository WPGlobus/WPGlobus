[![Latest Stable Version](https://poser.pugx.org/wpglobus/wpglobus/v/stable)](https://packagist.org/packages/wpglobus/wpglobus) [![Total Downloads](https://poser.pugx.org/wpglobus/wpglobus/downloads)](https://packagist.org/packages/wpglobus/wpglobus) [![Latest Unstable Version](https://poser.pugx.org/wpglobus/wpglobus/v/unstable)](https://packagist.org/packages/wpglobus/wpglobus) [![License](https://poser.pugx.org/wpglobus/wpglobus/license)](https://packagist.org/packages/wpglobus/wpglobus)

# WPGlobus - Multilingual Everything! #
**Contributors:** tivnetinc, alexgff, tivnet  
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN  
**Tags:** bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, language switcher, translate, translation, WPGlobus  
**Requires at least:** 4.2  
**Tested up to:** 4.5  
**Stable tag:** trunk  
**License:** GPLv2  
**License URI:** https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE  

**Multilingual / Globalization:** URL-based multilanguage; easy translation interface, compatible with Yoast SEO, All in One SEO Pack and ACF!  

## Description ##

**WPGlobus** is a family of WordPress plugins assisting you in making bilingual / multilingual WordPress blogs and sites.

> **WPGlobus**

The **WPGlobus Free Core plugin** provides you with the main multilingual tools.

* **Manually translate** posts, pages, categories, tags menus and widgets; **NOTE: WPGlobus does NOT translate texts automatically!** To see how it works, please read the [Quick Start Guide](http://www.wpglobus.com/quick-start/);
* **Add one or several languages** to your WP blog/site using custom combinations of country flags, locales and language names;
* **Enable multilingual SEO features** of Yoast SEO and All in One SEO plugins;
* **Switch the languages at the front-end** using: a drop-down menu extension and/or a customizable widget with various display options;
* **Switch the Administrator interface language** using a top bar selector;

The WPGlobus plugin serves as the **foundation** to other plugins in the family.

> **Free Add-ons**

* [WPGlobus Featured Images](https://wordpress.org/plugins/wpglobus-featured-images/): allows setting featured images separately for each language.
* [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/): enables selective translation of the `wp_options` table strings. You need to use it when your theme or a 3rd party plugin (a slider, for example) has its own option panel, where you enter texts.
* [WPGlobus for WPBakery Visual Composer](https://wordpress.org/plugins/wpglobus-for-wpbakery-visual-composer/): enables WPGlobus on certain themes that use WPBakery's Composer. Please note that Visual Composer is a commercial product, and therefore our support is limited.
* [WPGlobus for Black Studio TinyMCE Widget](https://wordpress.org/plugins/wpglobus-for-black-studio-tinymce-widget/): adds multilingual editing capabilities to the visual editor widget.

> **Premium Extensions**

* [WooCommerce WPGlobus](http://www.wpglobus.com/product/woocommerce-wpglobus/): adds multilingual capabilities to WooCommerce-based online stores.
* [WPGlobus Plus](http://www.wpglobus.com/product/wpglobus-plus/): adds URL fine-tuning, publishing status per translation, multilingual Yoast SEO analysis and more.
* [Multilingual WooCommerce Nets Netaxept](http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/): with this add-on, you will be able to translate the Nets payment methods' titles and descriptions to multiple languages.
* [WPGlobus Mobile Menu](http://www.wpglobus.com/product/wpglobus-mobile-menu/): makes the WPGlobus language switcher menu compatible with mobile devices and narrow screens.

### Compatibility with Themes ###

WPGlobus works correctly with all themes that apply proper filtering before outputting content.
Some themes incorporate 3rd party plugins (e.g., sliders, forms, composers) - not all of them are 100% multilingual-ready. When you see elements that cannot be translated, please let the theme / plugin authors know. We are ready to help them.

> Special note about "Page Builders / Composers": Currently, WPGlobus is compatible with the WPBakery Visual Composer. Other builders, such as "Page Builder by SiteOrigin", "Beaver Builder", Fusion ("Avada"), Elegant ("Divi"), etc. - are **not supported at this time**.

### Plugin Compatibility ###

WPGlobus is compatible with many plugins, including but not limited to:

* Advanced Custom Fields (ACF)
* All in One SEO Pack
* Black Studio TinyMCE Widget (with our free add-on)
* MailChimp for WordPress
* Max Mega Menu
* Popups - WordPress Popup
* Sidebar Login
* Slider Revolution
* The Events Calendar
* WPBakery Visual Composer (with our free add-on)
* Whistles
* WooCommerce and some of its extensions ([WooCommerce WPGlobus](http://www.wpglobus.com/product/woocommerce-wpglobus/) premium add-on is required)
* WordPress WooCommerce Nets Netaxept Payment Plugin by sellxed/customweb - with a [premium add-on](http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/)
* Yoast SEO ([WPGlobus Plus](http://www.wpglobus.com/product/wpglobus-plus/) premium add-on is not required but highly recommended)

### Permalinks ###

**IMPORTANT:** WPGlobus will not work if your URLs look like `example.com?p=123` or `example.com/index.php/category/post/`.

Please go to `Settings->Permalinks` and change the permalink structure to non-default and with no `index.php` in it. If you are unable to do that for some reason, please talk to your hosting provider / systems administrator.

### Developing on `localhost` or custom ports ###

WPGlobus may not work correctly on development servers having URLs like `//localhost/mysite` or on custom ports like `//myserver.dev:3000`. Please use a proper domain name (a fake one from `/etc/hosts` is OK) and port 80.

### More info and ways to contact the WPGlobus Development Team ###

* [WPGlobus.com website](http://www.wpglobus.com/).
* [Open source code on GitHub](https://github.com/WPGlobus).
* WPGlobus on social networks: [Facebook](https://www.facebook.com/WPGlobus), [Twitter](https://twitter.com/WPGlobus), [Google Plus](https://plus.google.com/+Wpglobus), [LinkedIn](https://www.linkedin.com/company/wpglobus).

### Admin interface translations: ###

`de_DE` by [Tobias Hopp](http://www.tobiashopp.info/) ~ [WPGlobus ist ein Paket von mehreren WordPress-Plugins, die Möglichkeiten zur Übersetzung von Wordpress-Installationen bieten.](https://de.wordpress.org/plugins/wpglobus/).

`es_ES` by [Patricia Casado](http://mascositas.com/) ~ [WPGlobus es una familia de plugins de WordPress que ayudan en la traducción de blogs de WordPress.](https://es.wordpress.org/plugins/wpglobus/).

`fr_FR` by [FX Bénard](http://wp-translations.org/) ~ [WPGlobus fait partie des extensions WordPress qui vous aident à rendre les blogs et les sites WordPress multilingues.](https://fr.wordpress.org/plugins/wpglobus/).

`pl_PL` by [Maciej Gryniuk](http://maciej-gryniuk.tk/) ~ [WPGlobus jest rodziną wtyczek do WordPress'a pomocnych w tworzeniu wielojęzycznych blogów i stron na WordPress'ie.](https://pl.wordpress.org/plugins/wpglobus/).

`ro_RO` by [Rodica-Elena Andronache](http://themeisle.com/) ~ [WPGlobus este o familie de plugin-uri WordPress ce te asistă în realizarea de bloguri și site-uri WordPress multilingve.](https://ro.wordpress.org/plugins/wpglobus/).

`ru_RU` by [The WPGlobus Team](http://www.wpglobus.com/ru/) ~ [WPGlobus - это коллекция плагинов ВордПресс для создания мультиязычных сайтов](https://ru.wordpress.org/plugins/wpglobus/).

`sv_SE` by [Elger Lindgren](http://bilddigital.se/) ~ [WPGlobus är en familj av WordPress-tillägg som hjälper dig att göra flerspråkiga Wordpressbloggar och webbplatser.](https://sv.wordpress.org/plugins/wpglobus/).

`tr_TR` by [Borahan Conkeroglu](https://twitter.com/boracon68) ~ [WPGlobus WordPress bloglarını ve sitelerini çokdilli yapmakta size yardım eden bir WordPress eklentileri ailesidir.](https://tr.wordpress.org/plugins/wpglobus/).

`uk` by [Pavlo Novak](https://plus.google.com/u/0/114797816817149043222) ~ ["WPGlobus - це колекція ВордПресс плагінів для створення багатомовних сайтів."](https://uk.wordpress.org/plugins/wpglobus/).

**Please help us translate WPGlobus into your language!**

## Installation ##

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the WPGlobus plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

Then please read the [Quick Start Guide](http://www.wpglobus.com/quick-start/).

## Frequently Asked Questions ##

### Please read these first: ###

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

## Screenshots ##

### 1. Welcome screen. ###
![Welcome screen.](https://ps.w.org/wpglobus/assets/screenshot-1.png)

### 2. Settings panel. ###
![Settings panel.](https://ps.w.org/wpglobus/assets/screenshot-2.png)

### 3. Languages setup. ###
![Languages setup.](https://ps.w.org/wpglobus/assets/screenshot-3.png)

### 4. Attaching language switcher to a menu. ###
![Attaching language switcher to a menu.](https://ps.w.org/wpglobus/assets/screenshot-4.png)

### 5. Editing post in multiple languages. ###
![Editing post in multiple languages.](https://ps.w.org/wpglobus/assets/screenshot-5.png)

### 6. Multilingual Yoast SEO and Featured Images. ###
![Multilingual Yoast SEO and Featured Images.](https://ps.w.org/wpglobus/assets/screenshot-6.png)

### 7. Language Switcher widget and Multilingual Editor dialog. ###
![Language Switcher widget and Multilingual Editor dialog.](https://ps.w.org/wpglobus/assets/screenshot-7.png)

### 8. Multilingual WooCommerce store powered by [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/). ###
![Multilingual WooCommerce store powered by WooCommerce WPGlobus.](https://ps.w.org/wpglobus/assets/screenshot-8.png)


## Upgrade Notice ##

No known backward incompatibility issues.

## Changelog ##

### 1.5.9 ###

* ADDED:
	* Add-ons: Show a combined list of free and premium WPGlobus extensions.

### 1.5.8 ###

* FIXED:
	* Yoast: switching to "Readability" tab (WPSEO issue #5013, Ticket #8628).
* ADDED:
	* Core: flag for American Sign Language (ASL) (Ticket #8497).
	* API: new filter `wpglobus_menu_add_selector`.
	* API: new filter to modify language selector items generated from pages.

### 1.5.7 ###

* FIXED:
	* Yoast: Compatibility with 3.3.1.
	* Yoast: Correctly handle the analysis section for extra languages.
	* Yoast: Fixed progress bar in the Snippet Editor.
	* Revslider: Don't load JS when the links in slides are empty.
	* Core: No fatal error if the `mbstring` PHP extension is not loaded.

### 1.5.6 ###

* ADDED:
	* Compatibility with Yoast SEO version 3.3.0.

### 1.5.5 ###

* FIXED:
	* Taxonomy slug re-generation when the title is already multilingual.
	* Invalid Yoast SEO titles in some specific cases.
	* Flag for `uk_UA`.
* ADDED:
	* Trigger the `wpglobus_current_language_changed` jQuery event when the current language changes.

### Earlier versions ###

* [See the complete changelog here](https://github.com/WPGlobus/WPGlobus/blob/master/changelog.md)

### WooCommerce-WPGlobus ###

* [See the changelog here](http://www.wpglobus.com/extensions/woocommerce-wpglobus/woocommerce-wpglobus-changelog/)

## Demo Sites ##

* [WPGlobus.com](http://www.wpglobus.com/):
	* Bilingual site using a variety of posts, pages, custom post types, forms, a slider and a WooCommerce store with Subscription and API extensions.
* [Site in subfolder](http://demo-subfolder.wpglobus.com/):
	* Demonstration of two WPGlobus-powered sites, one of which is installed in a subfolder of another. Shows the correct behavior of WPGlobus with URLs like `example.com/folder/wordpress`.
* [WooCommerce Multilingual](http://demo-store.wpglobus.com/):
	* A multilingual WooCommerce site powered by the `woocommerce-wpglobus` plugin.
