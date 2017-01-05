=== EDD Checkout Wizard ===
Contributors: rubengc
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=64N6CERD8LPZN
Tags: easy digital downloads, digital, download, downloads, edd, rubengc, checkout, wizard, step, form, validation, validate, e-commerce
Requires at least: 4.0
Tested up to: 4.6.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a form wizard with validation to your checkout page.

== Description ==
This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads").

Once activated, EDD Checkout Wizard will add a form wizard to your checkout page with some features:

1. Customer can only pass to the next tab if the form is correctly completed
1. Forces to scroll to the bottom of the screen to click the next button
1. Possibility to navigate to previously validated tabs
1. Checks changes from payment method selection

Current tabs distribution:

1. Overview: Displays cart and recommended products (if EDD Recommended Products is active)
1. Payment Method: Displays available payment methods, if there is only one or none, then this tab is removed
1. Account: Displays login/register form or the account information
1. Billing Address: Displays billing address information and EU VAT information (if EDD VAT is active)
1. Payment: Last tab with cart total and purchase button

EDD Checkout Wizard has support for this third-party plugins:

1. EDD Recommended Products
1. EDD VAT

If you want support for any plugin you can send to me an email at rubengcdev@gmail.com.

There's a [GIT repository](https://github.com/rubengc/edd-checkout-wizard) too if you want to contribute a patch.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin
1. That's it!

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Frequently Asked Questions ==

= How can I customize tabs? =

First of all you need add a filter to changes how tabs are rendered, and at this point you could add, change or move tabs.

This is the structure for a tab:

``
$tab = array(
    'tab-identifier' => array(
        'label' => 'My tab',
        'selectors' => array(
            '#my-element',
            '.group-of-elements',
        )
    )
);
``

This is an example of tab customization:

``
function custom_checkout_tabs( $tabs ) {
    // Adding a meta box to general tab
    $tabs['overview']['selectors'][] = '#my-element';

    // Moving a tab
    $temp_tab = $tabs['address'];

    unset($tabs['address']);

    $tabs['address'] = $temp_tab;

    // Removing a tab
    unset($tabs['account']);

    return $tabs;
}

add_filter( 'edd_checkout_wizard_checkout_tabs', 'custom_checkout_tabs');
``

Note: Elements that are not in a tab will be kept always visible

== Screenshots ==

1. Screenshot from checkout (Theme: vendd)

1. Screenshot of form notifications (Theme: vendd)

== Upgrade Notice ==

== Changelog ==

= 1.0.2 =
* Support for credit card fields
* Improved show payment method conditional

= 1.0.1 =
* Fixed: Prevent duplicated HTML tabs When EDD recalculates taxes

= 1.0 =
* Initial release