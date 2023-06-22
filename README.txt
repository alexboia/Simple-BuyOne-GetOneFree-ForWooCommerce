=== Simple Buy-One-Get-One-Free For WooCommerce ===
Contributors: alexandruboia
Donate link: https://ko-fi.com/alexandruboia
Tags: php, wordpress, wordpress-plugin, woocommerce, woocommerce-plugin, woocommerce-extension, buy-one-get-one-free, buy-any-get-one-free
Requires at least: 6.0.0
Tested up to: 6.2.2
Stable tag: 0.1.0
Requires PHP: 7.4.0
License: BSD New License
License URI: https://opensource.org/licenses/BSD-3-Clause

Simple buy one-get one free (and buy any get one free) plug-in for WooCommerce

== Description ==

Simple buy one-get one free (and buy any get one free) plug-in for WooCommerce, that allows, as the name implies, two things:
- for a given product, configure another one to be granted for free (regardless of the quantity the trigger product the customer has bought);
- configure a given product to be granted for free (regardless of what the customer has placed in his or her cart);
- combine these two behaviors.

Additionally, the plug-in allows one to configure the messages displayed in the products that:
- serve as trigger for free products;
- are granted for free when any other product is bought.

The plug-in is translated in both Romanian (ro_RO) and English (en_US, default).
That's it, and nothing more for now.

Configuring the plug-in
------------------------
Configuration is possible, but not required. 
The following options can be changed via `WooCommerce - Settings - Products - Buy one get one free`:

- "Buy-one-get-one-free product promo message banner format" - This message will be displayed if a product is assigned a free product. Use `%s` to specify where the free product name and link will be inserted. Defaults to `Buy this and you get %s for free!`;
- "Buy-any-get-one-free product promo message banner format" - This message will be displayed whenever a product is granted for free on any order. Defaults to `This product is free for any order you place!`.

Requirements
------------
- WordPress 6.0.0 or greater;
- WooCommerce 5.0.0 or greater;
- PHP 7.4.0 or greater.

== Screenshots ==

1. Configuring the plug-in
2. Product configuration
3. Product page

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/abp01-wp-trip-summary` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure the plug-in via `WooCommerce - Settings - Products - Buy one get one free`.

== Changelog ==
= 0.1.0 =
Use this version as the first officially distributed version.