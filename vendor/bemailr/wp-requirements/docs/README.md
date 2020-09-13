# WP Requirements

A helpful library for checking the prerequisites when activating / running a WordPress plugin.

## About

`WP Requirements` is a library that helps WordPress developers to check whether the environment meets their plugins' requirements.

Currently, you can use the library to verify:

* Versions of PHP, MySQL, and WordPress;
* Enabled PHP extensions;
* Version of the activated WordPress theme;
* Whether the required plugins are active or not, and optionally - their versions.

## Installation

The WP Requirements Library can be included in your project by simply downloading to a folder, by adding as a Git submodule, or with the help of Composer.

### Composer

The library is available on [Packagist](https://packagist.org/packages/bemailr/wp-requirements).

When you run `composer require bemailr/wp-requirements`, Composer normally installs the library into the `/vendor/bemailr/wp-requirements/` folder. We will assume this path in all examples of the documentation.

#### Example of composer.json for your plugin

```
{
  "name": "me/my-plugin",
  "description": "My Plugin",
  "type": "wordpress-plugin",
  "require": {
    "bemailr/wp-requirements": "^2.0.0"
  }
}
```

## Using the Library

### The basic usage schema

> This is only a schema. The real code should use WordPress hooks and do some additional checking, as shown in [this example](./sample-plugin-loader.php).

```php
require_once dirname( __FILE__ ) . '/vendor/bemailr/wp-requirements/wpr-loader.php';
if ( ! WP_Requirements::validate( __FILE__ ) ) {
    return;
}
```

A slightly more advanced example:

```php
require_once dirname( __FILE__ ) . '/vendor/bemailr/wp-requirements/wpr-loader.php';
$requirements = new WP_Requirements( __FILE__ );
if ( ! $requirements->valid() ) {
    // Here you can do some additional actions.
    // ...

    // Default action.
    $requirements->process_failure();
}
```

### Defining the requirements

There are two methods to define your plugin's prerequisites:

* By passing a PHP array to the class constructor;
* By creating a `wp-requirements.json` file.

Both methods work the same way, so use the one you like.

#### PHP array

The second parameter of the constructor is an array of requirements:

```php
$requirements = new WP_Requirements( __FILE__, array(...) );
```

The [plugin loader example](./sample-plugin-loader.php) shows a sample of such array passed to the class constructor.

#### JSON configuration file

If the array of prerequisites was not passed to the constructor, the WP Requirements Library looks for a file named `wp-requirements.json` in the plugin folder, eg. `/wp-content/plugins/your-plugin/wp-requirements.json`;

> You can change this location with the `wp_requirements_configuration_folders` filter.

The configuration file example can be found [here](./sample-wp-requirements.json). Copy it to your plugin's folder and modify as necessary.

### Verifying required plugins

The configuration snippets below tell WP Requirements that:

* WooCommerce plugin must be active and its version must be 2.6 or later;
* WPGlobus plugin must be active (any version);
* Polylang plugin must NOT be active.

#### PHP

```php
'plugins' => array(
  'woocommerce/woocommerce.php' => '2.6',
  'wpglobus/wpglobus.php'       => true,
  'polylang/polylang.php'       => false,
),
```

#### JSON

```
"plugins": {
  "woocommerce/woocommerce.php": "2.6",
  "wpglobus/wpglobus.php": true,
  "polylang/polylang.php": false
}
```

## The global configuration parameters

| Name | Default Value | Description |
| --- | --- | --- |
| `version_compare_operator`  | `>=` | Change the default comparison operator to any value supported by the [version_compare()](http://php.net/manual/en/function.version-compare.php) function. |
| `not_valid_actions` | `array( 'deactivate', 'admin_notice' )` | Define the actions to be performed if the requirements are **not** met. Depending on your code, you may want to deactivate the plugin, or keep it active doing something limited. |
| `show_valid_results` | `false` | In the admin notice, show all the results, whether requirements are met or not. |
| `requirements_details_url`  | (empty) | The URL that will be displayed as a link instead of listing the unmet prerequisites. Useful if the list can be long and/or if you want to provide the detailed information on a separate page. |
