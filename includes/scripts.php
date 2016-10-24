<?php
/**
 * Scripts
 *
 * @package     EDD\Checkout_Wizard\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_checkout_wizard_scripts( $hook ) {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    if( edd_is_checkout() ) {
        wp_enqueue_script('edd_checkout_wizard_js', EDD_CHECKOUT_WIZARD_URL . '/assets/js/edd-checkout-wizard' . $suffix . '.js', array('jquery'));
        wp_enqueue_style('edd_checkout_wizard_css', EDD_CHECKOUT_WIZARD_URL . '/assets/css/edd-checkout-wizard' . $suffix . '.css');
    }
}
add_action( 'wp_enqueue_scripts', 'edd_checkout_wizard_scripts' );
