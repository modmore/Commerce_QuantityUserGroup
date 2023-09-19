QuantityUserGroup for Commerce
------------------------

This is an extension for modmore's [Commerce](https://modmore.com/commerce/). It provides a product price type that extends the core-provided Quantity price type with a user group filter, so you can define usergroup-specific quantity prices.


As this is a pretty specific use case that adds a lot of power at the expense of a more complicated UI, we've opted to keep build it as an extension rather than the core.

Requirements:

- Commerce 1.3+
- PHP 7.4+

To use, install the package from the modmore package provider, and enable the module under Extras > Commerce > Configuration > Modules. After that, you can add the new price type to your products.

## Detailed usage

The best price available to a customer is automatically used by Commerce.

There is a commerce.render_quantity_usergroup_price snippet provided as part of this package, which can be used as replacement for the [commerce.render_quantity_price](https://docs.modmore.com/en/Commerce/v1/Snippets/render_quantity_price.html) snippet. It is used exactly the same way, except that it filters on the usergroup price types.

## Credits

Developed by modmore for [Brainlane](https://www.brainlane.com/).

## Support

Please open an issue with feature requests or bug reports. If you need help setting up or customising QuantityUserGroup, email support@modmore.com. As an official Commerce extension, standard support for this extension is covered by your Commerce license.
