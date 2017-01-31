/**
 * @link https://www.npmjs.com/package/grunt-text-replace
 */

/**
 * There is no support for Grunt templates in the `from:` replacement.
 * Therefore, we need to get the config variable ourselves.
 */
var cfgJson = require("./cfg.json");

module.exports = {
    version: {
        overwrite: true,
        src: ["<%= package.name %>.php"],
        replacements: [
            {
                from: new RegExp(" \\* Version: .+"),
                to: " * Version: <%= package.version %>"
            },
            {
                from: new RegExp("define\\( '(" + cfgJson.version.define + ")'.+"),
                to: "define( '$1', '<%= package.version %>' );"
            }
        ]
    },
    wpi18n: {
        overwrite: true,
        src: ["node_modules/grunt-wp-i18n/vendor/wp-i18n-tools/extract.php"],
        replacements: [
            {
                from: "public function entry_from_call( $call, $file_name ) {",
                to: "public function entry_from_call( $call, $file_name ) { if ( $call['args'][ count( $call['args'] ) - 1 ] !== '<%= cfg.text_domain %>' ) { return null; }"
            }
        ]
    },
    readme_md: {
        overwrite: true,
        src: ["README.md"],
        replacements: [
            {
                from: "![Multilingual WooCommerce store powered by [WooCommerce WPGlobus](https://wpglobus.com/product/woocommerce-wpglobus/).](https://ps.w.org/wpglobus/assets/screenshot-8.png)",
                to: "![Multilingual WooCommerce store powered by WooCommerce WPGlobus.](https://ps.w.org/wpglobus/assets/screenshot-8.png)"
            },
            {
                from: "# WPGlobus - Multilingual Everything! #",
                to: "[![Latest Stable Version](https://poser.pugx.org/wpglobus/wpglobus/v/stable)](https://packagist.org/packages/wpglobus/wpglobus) [![Total Downloads](https://poser.pugx.org/wpglobus/wpglobus/downloads)](https://packagist.org/packages/wpglobus/wpglobus) [![Latest Unstable Version](https://poser.pugx.org/wpglobus/wpglobus/v/unstable)](https://packagist.org/packages/wpglobus/wpglobus) [![License](https://poser.pugx.org/wpglobus/wpglobus/license)](https://packagist.org/packages/wpglobus/wpglobus) [![Project Stats](https://www.openhub.net/p/WPGlobus/widgets/project_thin_badge.gif)](https://www.openhub.net/p/WPGlobus)\n\n# WPGlobus - Multilingual Everything! #"
            }
        ]
    }
};
