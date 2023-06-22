<p align="center">
   <img align="center" width="210" height="200" src="https://raw.githubusercontent.com/alexboia/Simple-BuyOne-GetOneFree-ForWooCommerce/main/logo.png" style="margin-bottom: 20px; margin-right: 20px;" />
</p>

# Simple-BuyOne-GetOneFree-ForWooCommerce

## About
Simple buy one-get one free (and buy any get one free) plug-in for WooCommerce, that allows, as the name implies, two things:
- for a given product, configure another one to be granted for free (regardless of the quantity the trigger product the customer has bought);
- configure a given product to be granted for free (regardless of what the customer has placed in his or her cart);
- combine these two behaviors.

Additionally, the plug-in allows one to configure the messages displayed in the products that:
- serve as trigger for free products;
- are granted for free when any other product is bought.

The plug-in is translated in both Romanian (ro_RO) and English (en_US, default).
That's it, and nothing more for now.

## Requirements
- WordPress 6.0.0 or greater;
- WooCommerce 5.0.0 or greater;
- PHP 7.4.0 or greater.

## Configuring the plug-in
Configuration is possible, but not required. 
The following options can be changed via `WooCommerce - Settings - Products - Buy one get one free`:

- "Buy-one-get-one-free product promo message banner format" - This message will be displayed if a product is assigned a free product. Use `%s` to specify where the free product name and link will be inserted. Defaults to `Buy this and you get %s for free!`;
- "Buy-any-get-one-free product promo message banner format" - This message will be displayed whenever a product is granted for free on any order. Defaults to `This product is free for any order you place!`.

## Screenshots

### Configuring the plug-in
![Configuring the plug-in](/assets/screenshots/settings.png)

### Configuring a product
![Configuring a product](/assets/screenshots/product-configuration.png)

### Product details page
![Configuring the plug-in](/assets/screenshots/product-page.png)