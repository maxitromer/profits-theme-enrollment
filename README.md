IMPORTANT! DONT TRY TO INSTALL THIS PLUGIN. This is a internal develop to be installed with Woocommerce and an old custom modified version of Wordpress Profits Theme.


# Profits Theme Enrollment for WooCommerce

Integrated with `Woocommerce Subscriptions`.

## How to Install it


1. Upload the plugin files to the `/wp-content/plugins/wootic/` directory, or install the plugin through the WordPress plugins screen directly. 

1.Activate through the 'Plugins' screen in WordPress.

1. Go to `Woocommerce` > `Settings` > `Profits Theme Integration` to set your Profits Theme instance information.


### How must be set the product SKU in your products?


Like this:

`BRAND_CO87-PR8-OH19-RE87-PE87-AC3-CA88_PT15`

As you can see that SKU have 3 parts divided by `_`.

`BRAND`  `_`  `CO87-PR8-OH19-RE87-PE87-AC3-CA88`  `_`  `PT15`

The first part could be used for the SKU filter of the plugin, in this case the `BRAND` part.

The second part is a series of ACTION FORMS that will be executed only when the specific status order will be created or modified.

But the third part is the important for us because is the integration with Profits Theme.

`PT15`

To enroll or downgrade a buyer you only have to add the first 2 letters (PT) as Profits Theme indicator and the number of the product you want to enroll or downgrade in the Proficts Theme instance, the 15 in this example.


### How to update?


Use this:

https://github.com/afragen/github-updater




