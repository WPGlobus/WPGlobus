# WPGlobus - Multilingual Everything! #

## Changelog ##

2019-11-14 - version 2.2.27
* Added: (Vendor/Acf) If editor is hidden by ACF, we hide WPGlobus, too..

2019-11-11 - version 2.2.26
* Fixed: (Core/Builders) Don't initialize the Builders class when initial attributes are empty.
* Internal: (Builders/Block Editor) Changed the admin bar builder label to `Block Editor`.

2019-11-09 - version 2.2.25
* Fixed: (Vendor/Yoast) Don't start JS if support module was not defined.
* Fixed: (Vendor/Yoast) Start the support module while the corresponding option is missing.

2019-11-08 - version 2.2.24
* Added: (Core/Builders) Check if block editor is used with specific post types.

2019-11-06 - version 2.2.23
* Internal: (Options) Moved theme info to `Customize` section.
* Internal: (Core) Get hidden post types from the corresponding class.

2019-10-31 - version 2.2.22
* Added: (Core/Media) Media files support for the standard and Builder modes.
* Added: (Vendor/Acf) Filter for the `Url` field type.

2019-10-29 - version 2.2.21
* Fixed: (Core) Uncaught `TypeError: WPGlobusYoastSeo.init is not a function`.
* Internal: (Builders/Gutenberg) Updated save post message.

2019-10-16 - version 2.2.20
* Added: (Core/Post Types) Hide Gutenberg's post types.
* Added: (Core/Recommendation) A link to the "Recommendations" tab from the `plugins.php` page.
* Added: (Admin/HelpDesk) Get subject from `$_GET` array.
* Added: (Vendor/Yoast) Support Yoast SEO from v.12.
* Added: (Vendor/Yoast) Support Yoast SEO Premium from v.12.(Beta stage).
* Added: (Core/WPGlobusDialogApp) `afterSave` callback.

2019-10-02 - version 2.2.16
* Added: (Vendor/Yoast) Filters for `SEO Title`, `Meta Desc` on `edit.php` page.

2019-09-18 - version 2.2.15
* Fixed: (Builders/Gutenberg) TypeError `Cannot read property 'PluginSidebarMoreMenuItem' of undefined`.

2019-09-07 - version 2.2.14
* Added: (Options/Builders) Pinned button type option for builder mode.
* Added: (Flag) `serbska_malka.png`.

2019-08-12 - version 2.2.13
* Fixed: `extract_text()` regex to support line breaks in strings.

2019-08-06 - version 2.2.12
* Fixed: (Config) PHP warnings for clean install.

2019-08-05 - version 2.2.11
* Fixed: Default locale and flag for Taiwan.
* Fixed: (Vendor/All In One SEO Pack) Do not set the keywords field in according to the option.
* Added: (WPGlobus Admin interface) More translated strings in `de_DE` and `sv_SE`.
* Added: (Config) (Options/Сompatibility) `builder_post_types` option.
* Added: (Config/Builder) `post_types` property.
* Added: (Options/Welcome) Notices to `Welcome` tab.
* Added: (Vendor/All In One SEO Pack) `wpglobus-translatable` class for multilingual fields.

2019-06-07 - version 2.2.10
* Fixed: (Vendor/All In One SEO Pack) The detection of version less than 3.0.

2019-06-04 - version 2.2.9
* Added: (Vendor/All In One SEO Pack) Support `All In One SEO Pack` 3.

2019-06-03 - version 2.2.8
* Internal: (Core/Admin bar menu) Moved JS script to footer.
* Fixed: (Builders/Yoast) Don't run builder mode for undefined post type, e.g. `slides` from Bridge theme.

2019-06-01 - version 2.2.7
* Internal: (Core/Admin bar menu) Revised WPGlobus language menu in admin bar.

2019-05-31 - version 2.2.6
* Fixed: (Builders/Elementor) Revised the language switcher JS script.
* Added: (Builders/Elementor) Localized permalink for the `View Page` button.
* Internal: (Core/Config Builder] Added `is_default_language` function.

2019-05-27 - version 2.2.5
* Fixed: (Core/JS) Fixed first buttons group alignment issue in tinyMCE editor (standard mode).

2019-05-25 - version 2.2.4
* Fixed: (Builders/Gutenberg) Don't start JS script on disabled post type.

2019-05-22 - version 2.2.3
* Added: (Builders/Gutenberg) New language switcher.
* Internal: (Builders/JS_Composer) Start `js_composer` as a builder with WP 5.

2019-05-15 - version 2.2.2
* Fixed: (Vendor/Acf) Changed attribute to 'height' for text elements.

2019-05-14 - version 2.2.1
* Fixed: (Vendor/Acf) Fixed hidden WPGlobus dialog start icon with ACF Pro from v.5.8
* Fixed: (Customizer) Fixed PHP Warning `Invalid argument supplied for foreach`.

2019-05-12 - version 2.2.0
* Compatibility: WordPress 5.2
* Fixed: (Customizer) WPGlobus language selector alignment for WordPress 5.2.

2019-05-04 - version 2.1.15
* Internal: (Builders/Elementor) Beta-3 version of the `Elementor` support.

2019-04-29 - version 2.1.14
* Fixed: (Core/JS) Issue with an incorrect behavior of the dialog form with `hidden` class.
* Added: (Core/Builders) Element `admin_bar_builder_label` to the WP admin bar.
* Internal: (Flags) Added `purple.globe` icon.

2019-04-19 - version 2.1.13
* Internal: (Builders/Elementor) Beta-2 version for Elementor support.

2019-04-06 - version 2.1.12
* Internal: (Core/Options) Updated the `Compatibility` tab on the `WPGlobus Options` page.

2019-03-09 - version 2.1.11
* Fixed: (Vendor/ACF) Do not reset the `table` field of the `ACF:Table Field` plugin.
* Fixed: Do not filter meta and multilingual fields for no-builder pages.

2019-02-23 - version 2.1.10
* Compatibility: new JS for Admin and Builders support, to work with WordPress 5.1.*

---
### 2.1.9 ###

* COMPATIBILITY:
    * Vendor/Acf: ACF v.5.7.12
* INTERNAL:
	* Core: Fixed PHP Notice: `undefined index menuItems`.

### 2.1.8 ###

* INTERNAL:
	* Core: Synchronize Config.

### 2.1.7 ###

* COMPATIBILITY:
    * WordPress 5.1
* INTERNAL:
	* Core/Meta: Serialize meta value if it is an array.

### 2.1.6 ###

* FIXED:
	* Builders/Gutenberg: don't show our metabox when editing post types where WPGlobus is disabled.
* INTERNAL:
	* Core/Builders: use the `$_POST['post_type']` value to define post type on the `post.php` page (WordPress 5).
	* Core/Builders: added the `$post_type` parameter to the `get_3rd_party_status_for_gutenberg()` function.
	* Builders/WooCommerce: revised `get_3rd_party_status_for_gutenberg()` algorithm when WooCommerce is active.
	
### 2.1.5 ###

* REVISED:
	* Vendor/ACF: `get_post_meta_fields` function.
* INTERNAL:
	* `WPGlobus::add_locale_marks` refactored to ignore arrays and objects if passed as the first parameter.	
	
### 2.1.4 ###

* FIXED:
	* Builders: prevent to filter disabled post types.
	
### 2.1.3 ###

* ADDED:
    * Vendor/ACF: support ACF repeater field in builder mode.
* REVISED:
	* Vendor/Yoast: `get_post_metadata` filter.
	
### 2.1.2 ###

* FIXED:
    * Builders/Gutenberg: the CPT support when the `Classic Editor` plugin is not active.
	
### 2.1.1 ###

* FIXED:
    * Builders/Core: use the default language when creating a new post (on the `post-new.php` admin page).
* TWEAK:
	* Builders/Core: update admin bar label for WordPress 5.
	* Helpdesk: prevent duplicate clicks on the `Send` button.
	
### 2.1.0 ###

* ADDED:
    * Builders/Core: added Yoast SEO Premium to the list of supported add-ons.
    * Builders/Gutenberg: added the Custom Post Type support.
* TWEAK:
	* Builders/Gutenberg: visual improvements.

### 2.0.1 ###

* FIXED:
    * Builders/Gutenberg: saving posts correctly in the WordPress 5 'classic-editor' mode.

### 2.0.0 ###

* COMPATIBILITY:
    * WordPress 5.0 (with Gutenberg in the WP Core)
* ADDED:
    * Vendor/Yoast SEO: multilingual taxonomy support  (`term.php` page). 

### 1.9.30 ###

* ADDED:
    * Builders/Gutenberg: Checking 3rd party add-ons status for Gutenberg.
* FIXED:
	* Options Panel: CSS tweaks.
	* Options Panel: invalid call to `ob_get_clear()` resulted in duplicate sidebar menu under certain conditions.

### 1.9.29 ###

* FIXED:
	* Builders/Gutenberg: CSS tweaks.
	
### 1.9.28 ###

* FIXED:
	* Builders/Gutenberg: enabled WPGlobus metabox for posts and pages.
	
### 1.9.27 ###

* COMPATIBILITY:
    * Gutenberg 4.3.0
* INTERNAL:
	* Core/Builders: add builder label to admin bar.
	
### 1.9.26 ###

* COMPATIBILITY:
    * Gutenberg 4.2.0

### 1.9.25 ###

* ADDED:
	* Core/Meta: filter to enable/disable meta.
	* Vendor/ACF: New function to get ACF fields for post.
 
### 1.9.24 ###

* FIXED:
	* Vendor: load config file of All in One SEO Pack for builder page only.
* INTERNAL:
    * Helpdesk page refactored.

### 1.9.23 ###

* FIXED:
    * Gutenberg: saving languages correctly when editing pages.
	* ACF: Check the existence of the `acf_maybe_get_field` function to prevent fatal error in older versions.
	
### 1.9.22 ###

* FIXED:
	* Gutenberg: Correctly define language of the current post for REST API requests.
* ADDED:
    * Notice about builders in Beta stage.
	
### 1.9.21 ###

* FIXED:
	* All in One SEO Pack: correct saving empty value (keyword, description) for extra languages.
	* Yoast SEO: correct filter multilingual title on front-end.

### 1.9.20 ###

* FIXED:
    * Missing some `*.min.js` files.

### 1.9.19 ###

* FIXED:
	* Elementor: correct path to the file.
	* Yoast SEO: extract title for the default language.

### 1.9.18 ###

* COMPATIBILITY:
	* Elementor 2.2.6
* FIXED:
	* Incorrect handling of the `_yoast_wpseo_title` field for Yoast SEO.

### 1.9.17 ###

* COMPATIBILITY:
    * ACF 5.7.7
    * Yoast SEO 8.4
    * Gutenberg 4.0.0
    * WPBakery Page Builder 5.5.5
* INTERNAL:
	* Correct setting description value on the `nav-menu.php` page.

### 1.9.16 ###

* ADDED:
    * Special "flag" icon: `"easy-to-read"`.
* INTERNAL:
    * Initialization of the `WPGlobusDialogApp` JS object on the `nav-menu.php` page.

### 1.9.15 ###

* ADDED:
    * Filter `wpglobus_use_admin_wplang` to support upcoming advanced email localization features. 

### 1.9.14 ###

* ADDED:
    * New action `wpglobus_after_load_textdomain`. Can use this action to load additional translations.
    * Change the current language and reload translations when `switch_locale()` or `restore_previous_locale()` functions are called.
	* Options Panel: Info section.
* COMPATIBILITY:
	* Yoast SEO 7.3
* INTERNAL:
    * Call `unload_textdomain` function instead of accessing the `$l10n` global directly.
	
### 1.9.13 ###

* FIXED:
    * Correct language detection when doing WooCommerce AJAX calls. 
	* Options: correctly initialize Language Selector Menu dropdown ("None" option was missing).
* TWEAK:
    * Added a clarifying message to the clean-up procedure.
* INTERNAL:
    * Transition to Gulp and SCSS
    * CSS are always minimized (no `.min.css`) and mapped to the `.scss` sources.

### 1.9.12 ###

* ADDED:
    * Options Panel: Customize section.
* FIXED:
    * Custom JS code: restore some special characters after applying filters.
	
### 1.9.11 ###

* FIXED:
    * Updater: invalid requests when `php.ini` or `.htaccess` has the `arg_separator.output=&amp;` setting.
	* Customizer: fixed processing order via AJAX.
* ADDED:
	* Customizer: added the `settingType` attribute to prevent incorrect objects handling.
	* Filters: added the `_wp_attachment_image_alt` meta.
	* Core: initialize `WPGlobusDialogApp` for the `edit.php` page.

### 1.9.10 ###

* ADDED:
    * ACF: the ability to use `WPGlobusAcf.getFields()` to define which fields can be disabled (see `WPGlobusAcf.getDisabledFields()` ). ACF and ACF Pro field translation.
    * Flags: new `us-uk.png` flag.
    * POMO: Estonian translation of admin panels. Props: `Rivo Zängov`

### 1.9.9 ###

* FIXED
	* Core: Removed `devmode` switcher from the `post-new.php` page.
	* Core: Fix broken `Add Language` link. Code cleanup and cosmetics.
* ADDED:
    * Core: styling and translations for the `ON/OFF` WPGlobus switcher on the edit pages.
	
### 1.9.8.1 ###

* ADDED:
	* Core: initialize `WPGlobusDialogApp` on `options-general.php` page.
	
### 1.9.8 ###

* ADDED:
	* Core: filter `oEmbed HTML` when post has an embedded local URL in the content.

### 1.9.7.5 ###

* FIXED:
    * Options Panel: Incorrect using of `esc_html` made a link unclickable. Changed to `wp_kses`.

### 1.9.7.4 ###

* ADDED:
    * All in One SEO Pack: using a new method that was added in AIOSEOP 2.4.4.
* FIXED:
    * Customizer: do not load the class `WP_Customize_Code_Editor_Control` when running under older WordPress versions (before 4.9).

### 1.9.7.3 ###

* FIXED:
    * Customizer: saving of the options.
* ADDED:
    * Customizer: `Select Navigation Menu` option for the `Language Selector Menu` setting.

### 1.9.7.2 ###

* FIXED
	* Core: Invalid HTML tag escaping on the Edit Post screen.

### 1.9.7.1 ###

* General code clean-up, output escaping and GET/POST sanitization.

### 1.9.7 ###

* SECURITY:
    * Admin Panel: proper output escaping on the ReduxFramework-based pages. Thanks: `d4wner` (reporting), `slaFFik` (helping).
* FIXED:
    * Acf: fixed the saving the data inside the repeater field (issue #22).
* ADDED:
    * Customizer: additional settings. 
	
### 1.9.6 ###

* FIXED:
    * Core: do not load the WPGlobus translations from WordPress.org and always use those from the plugin's `languages` folder.
	* Customizer: fixed warning "strpos() expects parameter 1 to be string, array given".
	* Widgets: fixed handling the `keyup` event when editing in the visual mode.
* ADDED:
    * Recommend `WPGlobus Plus` to edit permalinks.

### 1.9.5 ###

* ADDED:
	* Core: added multilingual filters to the `wp_mail` and `wp_mail_from_name` WP hooks.
	
### 1.9.4 ###

* COMPATIBILITY:
	* Yoast SEO 5.9

* FIXED:
	* Yoast SEO: CSS tweaking (set `min-width`, `min-height` for the keyword tab to prevent shifting of the elements when the keyword is empty).
	
### 1.9.3 ###

* ADDED:
	* Customizer: handling of the customize changeset and theme mods URLs.
	
### 1.9.2 ###

* FIXED:
	* Core: correctly saving post tags in WordPress 4.9.

### 1.9.1 ###

* FIXED:
	* Core: convert all applicable characters to HTML entities in the category description.

### 1.9.0 ###

* COMPATIBILITY:
	* WordPress 4.9
* ADDED:
	* Customizer: new language selector.
	* Core: disable the built-in `oembed_cache` post type (added to the array of disabled post types).
* FIXED:
	* Customizer: correctly handle the case when `control.setting` is `null`.
	
### 1.8.9 ###

* TESTED WITH WordPress 4.9 and the following plugins active:
	* advanced-custom-fields 4.4.12
	* tablepress 1.8.1
	* types 2.2.18
	* woocommerce 3.2.3
	* woocommerce-api-manager 1.5.4
	* woocommerce-composite-products 3.12.2
	* woocommerce-dynamic-pricing 3.1.2
	* woocommerce-subscriptions 2.2.13
	* wordpress-seo 5.7.1
* FIXED:
	* Customizer: don't add MutationObserver when a control element does not have a parent. An example can be found in the [Ascend theme](https://wordpress.org/themes/ascend/).
	
### 1.8.8 ###

* ADDED:
	* Yoast SEO: support multiple languages in the `META KEYWORDS` tag.

### 1.8.7 ###

* COMPATIBILITY:
	* WordPress 4.8.1
* ADDED:
	* Widgets: support for the title of the image widget.
	* Core: recommend WooCommerce add-ons if not installed.

### 1.8.6 ###

* ADDED:
	* Widgets: handle multiple WYSIWYG text widgets.
	* Core: do not localize menu URLs marked with a special CSS class `wpglobus-menu-item-url-nolocalize`.
	* Core: `define( 'WPGLOBUS_CUSTOMIZE', false )` disables WPGlobus options in the Customizer.
	
### 1.8.5 ###

* FIXED:
	* Widgets: support the WYSIWYG text widgets changes introduced by WP 4.8.1.

### 1.8.4 ###

* ADDED:
	* Apply filter to the `url_to_postid()` function so it will return the correct Post ID for URLs in non-default language.
	* Allow `oembed` to handle URLs in non-default language.
	* Localize the output of the oembed's JSON.

### 1.8.3 ###

* ADDED:
	* Multilingual editor for the WYSIWYG text widgets (`arbitraryTextOrHTML`).
	* Customizer: `Customizr Pro` theme added to the list of exceptions.
	* Admin: additional translations in `ar` and `ro`.
* FIXED:
	* Yoast SEO: saving description on the `term.php` page.
	
### 1.8.2 ###

* ADDED:
	* Customizer: support TinyMCE editor in controls.
	* Customizer: preview refresh.

### 1.8.1 ###

* ADDED:
	* New filter `wpglobus_after_localize_current_url`.
	* Support for the Multilingual Taxonomy Slug module in WPGlobus Plus.

### 1.8.0 ###

* COMPATIBILITY:
	* WordPress 4.8
	* Yoast SEO 4.9
* FIXED:
	* Yoast SEO: CSS fix for the Premium version.
	* Core: No warning on the Helpdesk page if `php_uname` is disabled for security reason.
* ADDED:
	* Automatic redirect to the visitor's preferred language (first visit only). To turn it on, go to `WPGlobus -> Redirect` in the Admin menu.

### 1.7.12 ###

* FIXED:
	* Yoast SEO: the fix for the `_yst_is_cornerstone` element.
* ADDED:
	* Core: added options to the `WPGlobusDialogApp`.
	* Yoast SEO: Minor CSS improvements.

### 1.7.11 ###

* ADDED:
	* Core: added array of enabled languages to JS.
	* Core: enqueue `wpglobus.js` script for admin.
	* Customizer: improvements.
	* MailChimp: added support for `MailChimp for WordPress` 4.1.1.
	* Admin: Added Bahasa Indonesia (`id_ID`) translation.
	
### 1.7.10 ###

* ADDED:
	* Core: Basque Country flag image.
* FIXED:
	* Customizer: JS code improvements, optimization and cleanup.
	
### 1.7.9 ###

* ADDED:
	* Customizer: Changesets handling.
	* Yoast SEO: version 4.4 support.
* FIXED:
	* Customizer: Don't convert if a link was set in the default language only.
	* Yoast SEO: correct setting of keywords in versions 4.1 and 4.2.
	* Yoast SEO: correct switching Readability/Keyword tabs for extra languages in versions 4.1 and 4.2.
* INTERNAL:
	* Method to work with the strings having multiple language blocks. Required for WooCommerce 2.7.
	* Code clean-up and performance improvements.
	* Redux "Newsflash" admin notification is hidden.

### 1.7.8.2 ###

* FIXED:
	* Customizer: Handle the case of "orphaned" sections with no panel (have "undefined" type).

### 1.7.8.1 ###

* FIXED:
	* Core: JS code improvements.

### 1.7.8 ###

* FIXED:
	* Core: Initialize WordCounter for the WPGlobus TinyMCE editors only.
	* Media: Fixed PHP Notice "Undefined index while loading image in WYSIWYG editor".
	* Core: General code cleanup and testing with the latest versions of PHP and WordPress.

### 1.7.7.2 ###

* FIXED:
	* Yoast SEO: hide original section content for v.4.1.

### 1.7.7.1 ###

* FIXED:
	* Core: Casting $terms to (array) causes syntax error in PHP 5.3 and older.

### 1.7.7 ###

* FIXED:
	* Core: `WPGlobus_Utils::is_function_in_backtrace` is deprecated in favor of more advanced `WPGlobus_WP::is_function_in_backtrace` method.
	* Code: a PHP 7.1 warning cleared.
	* Customizer: JS code optimization and cleanup.
* ADDED:
	* `WPGlobus News` admin dashboard widget.
	* Customizer: exclude some incompatible themes. Example: "Experon".
	* Yoast SEO: version 4.1 support.

### 1.7.6 ###

* FIXED:
	* Customizer: exclude the `Static Front Page` section fields from WPGlobus.
	* Core: apply the `wpglobus_disabled_entities` filter on the array of entities returned for load tabs, scripts, styles.
* ADDED:
	* Settings: new section for a custom Javascript code to be embedded in the footer.

### 1.7.5 ###

* ADDED:
	* Media: Title is now multilingual.
* FIXED:
	* List of WPGlobus Plugins: display plugin info correctly if WP admin language is not one of the languages used on the `wpglobus.com` website.

### 1.7.4 ###

* FIXED:
	* Media: incorrect script filename. Reported by `@tokkonopapa`.

### 1.7.3 ###

* FIXED:
	* Various code improvements related to WP47, Yoast, and our premium add-ons. See the  detailed log on [GitHub](https://github.com/WPGlobus/WPGlobus/commits/develop).
* ADDED:
	* Media: multilingual caption, description and `ALT` text.

### 1.7.2 ###

* FIXED:
	* Ignore WP 4.7's service post types `custom_css` and `customize_changeset` in the WPGlobus Settings and Customizer.
* ADDED:
	* Yoast SEO Premium: support multiple keywords for the default language.

### 1.7.1 ###

* FIXED:
	* Incorrect handling of WordPress version 4.6.2 when loading admin JS.

### 1.7.0 ###

* FIXED:
	* Several additions and changes related to WordPress 4.7.

### 1.6.9 ###

* ADDED:
	* CSS rules for the trash icon `.wpglobus-link-trash-icon` in WPGlobus tabs in admin.
* FIXED:
	* Revised the `wpglobus_extra_languages` filter in the class `WPGlobusWidget`.
	
### 1.6.8 ###

* ADDED:
	* Support for Yoast SEO 3.8.
* FIXED:
	* Updated flag image for the `my_MM` locale (Myanmar / Burmese).

### 1.6.7 ###

* FIXED:
	* Admin language selector is displayed correctly in the "Light" color scheme.
	* Admin language selector is available on mobile (narrow) screens.
	* The premium plugin information is stored locally, so no call to the server is required.

### 1.6.6 ###

* ADDED:
    * Link to the [Professional Translation Service pages](https://wpglobus.com/translator/) from the `About WPGlobus` admin page.
    * Core: Filter `wpglobus_manage_language_items` before outputting the language items on the `edit.php` page.
	* Core: Filter `wpglobus_styles` for the frontend CSS rules (Ticket #304).
* FIXED:
	* Prevent creating duplicate terms on the `edit.php` page.
	* All-in-One SEO: Prevent duplicate keywords in `meta name="keywords"` coming from extra languages.

### 1.6.5 ###

* ADDED:
	* Helpdesk form directly in WP admin.
    * Core: Filter to enable/disable multilingual custom fields on post.php|post-new.php page.
    * Core: Filter the columns displayed in the Posts list table.
* FIXED:
    * A license activation bug that occurred when plugin "slug" did not match the plugin folder name (example: the `WPGlobus for Slider Revolution` premium add-on).

### 1.6.4 ###

* FIXED:
	* Additional checks and compatibility methods to avoid failures if `mbstring` PHP extension is not loaded.
	* Widgets: Don't setup field as multilingual if its ID is empty.
* ADDED:
	* The correct filter for the list of terms available to edit for the given post ID.

### 1.6.3 ###

* FIXED:
	* Yoast: empty focus keyword when saving a post with active extra language tab.
	* Add-ons: a better interface; get information from the server.
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
	* MailChimp: `Attempt to assign property of non-object` warning [#35](https://github.com/WPGlobus/WPGlobus/issues/35) (props `nmohanan`).
* ADDED:
	* More languages, locales and flags are configured by default (applies to "fresh" plugin activations; does not change the existing settings of a previously installed WPGlobus).

### 1.6.0 ###

* COMPATIBILITY:
	* WordPress 4.6.
* FIXED:
	* Empty Quick Edit titles in taxonomy views.
	* Warning: Non-SSL link to the image in admin (props `technima`).
* ADDED:
	* Customizer: User control on which fields can have multilingual values.
	* Customizer: In the `Language Selector Menu`, if no menus created yet, show a link to create menus.
	* Customizer: Filter to disable WPGlobus customizer for specific themes.
	* Widgets: Language-dependent conditions if the `Widget Logic` plugin is active.
	
### 1.5.10 ###

* FIXED:
	* Yoast: Compatibility with version 3.4.
* ADDED:
	* Customizer: Link to open the `Plugin Install` page.
	* Options: Show which languages are installed in the `Languages Table`.

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

### 1.5.4.1 ###

* FIXED:
	* Better error handling in the Updater module (continued).

### 1.5.4 ###

* ADDED:
	* Basic multilingual functionality for the `MailChimp for WP` plugin.
	* Support for multilingual links in the `Slider Revolution` plugin.
* FIXED:
	* Do not do "auto-paragraphing" if the `wpautop` filter has been disabled (props @emechkov).
	* Menu translations lost in some rare situations.
	* Better error handling in the Updater module.

### 1.5.3 ###

* ADDED:
	* Localize RSS feed URL.
* FIXED:
	* Disappearing translated headline and other Yoast 3.2 - related issues.

### 1.5.2 ###

* FIXED:
	* Several issues related to Yoast SEO 3.2.
	
### 1.5.1 ###

* ADDED:
	* Support for Yoast SEO Version 3.2;
	* Filter 'wpglobus_nav_menu_objects' - allows to modify the localized URLs in menu, if any tweaking is required;
	* `uk` (Ukrainian) admin interface translation.

### 1.5.0 ###

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

### 1.4.9 ###

* ADDED:
	* Support for repeater&flexible content fields for Advanced Custom Fields Pro
	* Support for Megamenu plugin
* FIXED:
	* ReduxFramework incompatibilities (in Customizer, some themes)

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
	* Load minimized JS in the Customizer.
	* Using class for globe icon instead of id.
	* Rewriting the array of classes.
	* Minor CSS improvements.

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
	* Support for [The Events Calendar plugin](https://wordpress.org/plugins/the-events-calendar/).
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
	* New extension, [WooCommerce WPGlobus](https://wpglobus.com/product/woocommerce-wpglobus/) is referenced on the add-ons page.
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
