[![Latest Stable Version](https://poser.pugx.org/wpglobus/wpglobus/v/stable)](https://packagist.org/packages/wpglobus/wpglobus) [![Total Downloads](https://poser.pugx.org/wpglobus/wpglobus/downloads)](https://packagist.org/packages/wpglobus/wpglobus) [![Latest Unstable Version](https://poser.pugx.org/wpglobus/wpglobus/v/unstable)](https://packagist.org/packages/wpglobus/wpglobus) [![License](https://poser.pugx.org/wpglobus/wpglobus/license)](https://packagist.org/packages/wpglobus/wpglobus) [![Project Stats](https://www.openhub.net/p/WPGlobus/widgets/project_thin_badge.gif)](https://www.openhub.net/p/WPGlobus)

# WPGlobus - Multilingual Everything! #
**Contributors:** [tivnetinc](https://profiles.wordpress.org/tivnetinc), [alexgff](https://profiles.wordpress.org/alexgff), [tivnet](https://profiles.wordpress.org/tivnet)  
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN  
**Tags:** bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, language switcher, translate, translation, WPGlobus  
**Requires at least:** 4.2  
**Tested up to:** 4.6  
**Stable tag:** trunk  
**License:** GPL-3.0  
**License URI:** https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE  

**Multilingual / Globalization:** URL-based multilanguage; easy translation interface, compatible with Yoast SEO, All in One SEO Pack and ACF!  

## Description ##

**WPGlobus** is a family of WordPress plugins assisting you in making bilingual / multilingual WordPress blogs and sites.

**Important Technical Notes**
---------------
* Some themes and 3rd party plugins are **not 100% WPGlobus-ready**. Read the "Compatibility" section below for details.
* We develop and test our software using the **latest versions of PHP and WordPress only**. While we do our best to write code compatible with older versions, we cannot be 100% sure that everything works correctly under older versions.
* The **multisite** mode (multiple virtual sites sharing a single WordPress installation) is **not tested and not supported**.

> **What is in the FREE version of WPGlobus?**

The WPGlobus plugin provides you with the main multilingual tools.

* **Manually translate** posts, pages, categories, tags menus and widgets; **NOTE: WPGlobus does NOT translate texts automatically!** To see how it works, please read the [Quick Start Guide](http://www.wpglobus.com/quick-start/);
* **Add one or several languages** to your WP blog/site using custom combinations of country flags, locales and language names;
* **Enable multilingual SEO features** of Yoast SEO and All in One SEO plugins;
* **Switch the languages at the front-end** using: a drop-down menu extension and/or a customizable widget with various display options;
* **Switch the Administrator interface language** using a top bar selector;

The WPGlobus plugin serves as the **foundation** to other plugins in the family.

> **There are several Free Add-ons:**

* [WPGlobus Featured Images](https://wordpress.org/plugins/wpglobus-featured-images/): allows setting featured images separately for each language.
* [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/): enables selective translation of the `wp_options` table strings. You need to use it when your theme or a 3rd party plugin (a slider, for example) allows you to enter some texts (headings, buttons, etc.) and stores them in the `options` table.
* [WPGlobus for WPBakery Visual Composer](https://wordpress.org/plugins/wpglobus-for-wpbakery-visual-composer/): enables WPGlobus on certain themes that use WPBakery's Composer. Please note that Visual Composer is a commercial product, and therefore our support is limited.
* [WPGlobus for Black Studio TinyMCE Widget](https://wordpress.org/plugins/wpglobus-for-black-studio-tinymce-widget/): adds multilingual editing capabilities to the visual editor widget.

> **When do I need Premium Extensions?**

* To translate URLS (`/my-page/` translates to `/fr/ma-page`, `/ru/моя-страница` and so on);
* To "postpone" translation to all languages and publish only those that are ready;
* To have completely separate menus for each language;
* To translate WooCommerce products and taxonomies;
* To have separate "focus keywords" for each language in the Yoast SEO;
* ...and more.

For more details, please check out the descriptions of each paid add-on on our website:

* [WooCommerce WPGlobus](http://www.wpglobus.com/product/woocommerce-wpglobus/): adds multilingual capabilities to WooCommerce-based online stores.
* [WPGlobus Plus](http://www.wpglobus.com/product/wpglobus-plus/): adds URL fine-tuning, publishing status per translation, multilingual Yoast SEO analysis and more.
* [WPGlobus Language Widgets](http://www.wpglobus.com/product/wpglobus-language-widget/): Multilingual widget logic: show and hide widget depending on the current language.
* [WPGlobus Header Images](http://www.wpglobus.com/product/wpglobus-header-images/): Display different header images per language. Show images depending on the settings in the Customizer.
* [WPGlobus Menu Visibility](http://www.wpglobus.com/product/wpglobus-menu-visibility/): Show or hide menu items depending on the current language.
* [WPGlobus Mobile Menu](http://www.wpglobus.com/product/wpglobus-mobile-menu/): makes the WPGlobus language switcher menu compatible with mobile devices and narrow screens.
* [WPGlobus for Slider Revolution](http://www.wpglobus.com/product/wpglobus-for-slider-revolution/): Adds multilingual capabilities to the Slider Revolution plugin.
* [Multilingual WooCommerce Nets Netaxept](http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/): with this add-on, you will be able to translate the Nets payment methods' titles and descriptions to multiple languages.

### Compatibility with Themes ###

* WPGlobus works correctly with all themes that apply proper filtering before outputting content.
* As most of the themes save their settings in the `options` table, you can use the [WPGlobus Translate Options](https://wordpress.org/plugins/wpglobus-translate-options/) plugin to process those settings correctly.
* Some themes incorporate 3rd party plugins (e.g., sliders, forms, composers) - not all of them are 100% multilingual-ready. When you see elements that cannot be translated, please **tell the theme / plugin authors**. We are ready to help them.
* Read more on the topic [here](http://www.wpglobus.com/documentation/wpglobus-compatibility-with-themes-and-plugins/).

> Special note about "Page Builders / Composers": Currently, WPGlobus is compatible with the WPBakery Visual Composer. Other builders, such as "Page Builder by SiteOrigin", "Beaver Builder", Fusion ("Avada"), Elegant ("Divi"), etc. - are **not supported at this time**.

### Plugin Compatibility ###

WPGlobus is compatible with many plugins, including but not limited to:

* ACF - Advanced Custom Fields. [WPGlobus Plus](http://www.wpglobus.com/product/wpglobus-plus/) premium add-on is required for WYSIWYG fields support,
* All in One SEO Pack,
* Black Studio TinyMCE Widget (with our free add-on),
* MailChimp for WordPress,
* Max Mega Menu,
* Popups - WordPress Popup,
* Sidebar Login,
* The Events Calendar,
* WPBakery Visual Composer (with our free add-on),
* Whistles,
* Widget Logic,
* Yoast SEO. [WPGlobus Plus](http://www.wpglobus.com/product/wpglobus-plus/) premium add-on is required for multilingual focus keyword and SEO analysis.

Some 3rd party plugins are supported with our [premium add-ons](http://www.wpglobus.com/shop/):

* [Slider Revolution](http://www.wpglobus.com/product/wpglobus-for-slider-revolution/),
* [TablePress](http://www.wpglobus.com/product/wpglobus-plus/#tablepress),
* [WooCommerce and some of its extensions](http://www.wpglobus.com/product/woocommerce-wpglobus/),
* [WooCommerce Nets Netaxept Payment Plugin](http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/),

### Permalinks ###

**IMPORTANT:** WPGlobus will not work if your URLs look like `example.com?p=123` or `example.com/index.php/category/post/`.

Please go to `Settings->Permalinks` and change the permalink structure to non-default and with no `index.php` in it. If you are unable to do that for some reason, please talk to your hosting provider / systems administrator.

**Note:** WooCommerce adds their own section to the Permalinks. It is important to fill in all the information. For example, you need to specify your Shop Base, for example `/product/`. If you leave it blank, WooCommerce will try to translate the base (eg `/produkt/` for German), which will result in a 404 error.

### Developing on `localhost` or custom ports ###

WPGlobus may not work correctly on development servers having URLs like `//localhost/mysite` or on custom ports like `//myserver.dev:3000`. Please use a proper domain name (a fake one from `/etc/hosts` is OK) and port 80.

### More info and ways to contact the WPGlobus Development Team ###

* [WPGlobus.com website](http://www.wpglobus.com/).
* [Open source code on GitHub](https://github.com/WPGlobus).
* WPGlobus on social networks: [Facebook](https://www.facebook.com/WPGlobus), [Twitter](https://twitter.com/WPGlobus), [Google Plus](https://plus.google.com/+Wpglobus), [LinkedIn](https://www.linkedin.com/company/wpglobus).

### Admin interface translations: ###

`de_DE` by [Tobias Hopp](http://www.tobiashopp.info/) ~ [WPGlobus ist ein Paket von mehreren WordPress-Plugins, die Möglichkeiten zur Übersetzung von Wordpress-Installationen bieten.](https://de.wordpress.org/plugins/wpglobus/).

`es_ES` by [FX Bénard](http://wp-translations.org/) and [Patricia Casado](http://mascositas.com/) ~ [WPGlobus es una familia de plugins de WordPress que ayudan en la traducción de blogs de WordPress.](https://es.wordpress.org/plugins/wpglobus/).

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

* [Do you support PHP 5.x? PHP 7?](http://www.wpglobus.com/faq/support-php-5-2/)
* [Do you support MSIE / Opera / Safari / Chrome / Firefox - Version x.x?](http://www.wpglobus.com/faq/support-msie-opera-safari-chrome-firefox/)
* [Do you plan to support subdomains and URL query parameters?](http://www.wpglobus.com/faq/subdomains-and-url-query-parameters/)
* [I am using WPML (qTranslate, Polylang, Multilingual Press, etc.). Can I switch to WPGlobus?](http://www.wpglobus.com/faq/i-am-using-wpml-qtranslate-polylang-multilingual-press-etc-can-i-switch-to-wpglobus/)
* [Do you support WooCommerce, EDD, other e-Commerce plugins?](http://www.wpglobus.com/faq/support-woocommerce-edd/)
* [Is it possible to set the user's language automatically based on IP and/or browser language?](http://www.wpglobus.com/faq/set-language-by-ip/)
* [How do I contribute to WPGlobus?](http://www.wpglobus.com/faq/how-do-i-contribute-to-wpglobus/)

## Screenshots ##

### 1. The Welcome screen. ###
![The Welcome screen.](https://ps.w.org/wpglobus/assets/screenshot-1.png)

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

### 1.6.3 ###

* FIXED:
	* Yoast: empty focus keyword when saving post with active extra language tab.
	* Add-ons: better interface; get information from the server.
	* Updater: do not offer upgrades if `.git` folder exists.
	* Internal: some code/comments cleanup to avoid `PHPStorm`, `WPCS`, and `php7cc` notices.

### 1.6.2 ###

* FIXED:
	* Refactored the language switch generation code for better compatibility with `BuddyPress`.
	* MailChimp: incompatibility issue [#35](https://github.com/WPGlobus/WPGlobus/issues/35) [#37](https://github.com/WPGlobus/WPGlobus/issues/37) - props [nmohanan](https://github.com/nmohan) and [tbenny](https://github.com/tbenny).
* ADDED:
	* Filter `wpglobus_user_defined_post_type` to redefine post type while check for disabled post types.

### 1.6.1 ###

* FIXED:
	* Wrong default menu title cases when taxonomy ID matches some post ID (props `mktatwp`).
	* MailChimp: `Attempt to assign property of non-object` warning [#35](https://github.com/WPGlobus/WPGlobus/issues/35)  - props [nmohanan](https://github.com/nmohan).
* ADDED:
	* More languages, locales and flags are configured by default (applies to "fresh" plugin activations; does not change the existing settings of a previously installed WPGlobus).

### 1.6.0 ###

* COMPATIBILITY:
	* WordPress 4.6.
* FIXED:
	* Empty Quick Edit titles in taxonomy views.
	* Warning: Non-SSL link to image in admin (props `technima`).
* ADDED:
	* Customizer: User control on which fields can have multilingual values.
	* Customizer: In the `Language Selector Menu`, if no menus created yet, show a link to create menus.
	* Customizer: Filter to disable WPGlobus customizer for specific themes.
	* Widgets: Language-dependent conditions if the `Widget Logic` plugin is active.

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
