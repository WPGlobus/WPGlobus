=== WPGlobus - Multilingual Everything! ===
Contributors: tivnetinc, alexgff, tivnet
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN
Tags: bilingual, globalization, i18n, international, l10n, localization, multilanguage, multilingual, language switcher, translate, translation, WPGlobus
Requires at least: 4.2
Tested up to: 4.5
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Multilingual / Globalization: URL-based multilanguage; easy translation interface, compatible with Yoast SEO, All in One SEO Pack and ACF!

== Description ==

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

* [WooCommerce WPGlobus](http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/): adds multilingual capabilities to WooCommerce-based online stores.
* [WPGlobus Plus](http://www.wpglobus.com/shop/extensions/wpglobus-plus/): adds URL fine-tuning, publishing status per translation, multilingual Yoast SEO analysis and more.

= Compatibility with Themes =

WPGlobus works correctly with all themes that apply proper filtering before outputting content.
Some themes incorporate 3rd party plugins (e.g., sliders, forms, composers) - not all of them are 100% multilingual-ready. When you see elements that cannot be translated, please let the theme / plugin authors know. We are ready to help them.

= Permalinks =

**IMPORTANT:** WPGlobus will not work if your URLs look like `example.com?p=123` or `example.com/index.php/category/post/`.

Please go to `Settings->Permalinks` and change the permalink structure to non-default and with no `index.php` in it. If you are unable to do that for some reason, please talk to your hosting provider / systems administrator.

= Developing on `localhost` or custom ports =

WPGlobus may not work correctly on development servers having URLs like `//localhost/mysite` or on custom ports like `//myserver.dev:3000`. Please use a proper domain name (a fake one from `/etc/hosts` is OK) and port 80.

= More info and ways to contact the WPGlobus Development Team =

* [WPGlobus.com website](http://www.wpglobus.com/).
* [Open source code on GitHub](https://github.com/WPGlobus).
* WPGlobus on social networks: [Facebook](https://www.facebook.com/WPGlobus), [Twitter](https://twitter.com/WPGlobus), [Google Plus](https://plus.google.com/+Wpglobus), [LinkedIn](https://www.linkedin.com/company/wpglobus).

= Admin interface translations: =

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

= 1.5.3 =

* ADDED:
	* Localize RSS feed URL.
* FIXED:
	* Disappearing translated headline and other Yoast 3.2 - related issues.

= 1.5.2 =

* FIXED:
	* Several issues related to Yoast SEO 3.2.

= 1.5.1 =

* ADDED:
	* Support for Yoast SEO Version 3.2;
	* Filter 'wpglobus_nav_menu_objects' - allows to modify the localized URLs in menu, if any tweaking is required;
	* `uk` (Ukrainian) admin interface translation.

= 1.5.0 =

* ADDED:
	* WordPress 4.5 compatibility;
	* Customizer improvements related to WordPress 4.5;
	* Customizer filter `wpglobus_customize_disabled_sections`;
	* Filter `wpglobus_disabled_acf_fields` to disable ACF and ACF Pro field translation;
	* `page` as the 3rd parameter to `wpglobus_localize_custom_data` filter;
	* Support for layers in Slider Revolution plugin;
	* `wpglobus-translatable` CSS class to post excerpt.
* FIXED:
	* Prevent adding element to itself in `WPGlobusDialogApp`;
	* Customizer section for [Easy Google Fonts plugin](https://wordpress.org/plugins/easy-google-fonts/);
	* No fatal error in `WPGlobus_Core::translate_wp_post` when not a `WP_Post` passed (Ticket 6390)

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
	* A multilingual WooCommerce site powered by the `woocommerce-wpglobus` plugin.
