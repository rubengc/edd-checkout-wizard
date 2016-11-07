<?php
/**
 * Plugin Name:     EDD Checkout Wizard
 * Plugin URI:      https://wordpress.org/plugins/edd-checkout-wizard/
 * Description:     Adds a form wizard with validation to your checkout page
 * Version:         1.0.1
 * Author:          rubengc
 * Author URI:      http://rubengc.com
 * Text Domain:     edd-checkout-wizard
 *
 * @package         EDD\Checkout_Wizard
 * @author          rubengc
 * @copyright       Copyright (c) rubengc
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Checkout_Wizard' ) ) {

    /**
     * Main EDD_Checkout_Wizard class
     *
     * @since       1.0.0
     */
    class EDD_Checkout_Wizard {

        /**
         * @var         EDD_Checkout_Wizard $instance The one true EDD_Checkout_Wizard
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Checkout_Wizard
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Checkout_Wizard();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_CHECKOUT_WIZARD_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_CHECKOUT_WIZARD_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_CHECKOUT_WIZARD_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            require_once EDD_CHECKOUT_WIZARD_DIR . 'includes/scripts.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            add_action( 'edd_before_checkout_cart', array( $this, 'render_html_tabs' ) );
            add_action( 'edd_after_purchase_form', array( $this, 'render_html_buttons' ) );
            // Register settings
            add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_CHECKOUT_WIZARD_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_checkout_wizard_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-checkout-wizard' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-checkout-wizard', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-checkout-wizard/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-checkout-wizard/ folder
                load_textdomain( 'edd-checkout-wizard', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-checkout-wizard/languages/ folder
                load_textdomain( 'edd-checkout-wizard', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-checkout-wizard', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'edd_checkout_wizard_settings',
                    'name'  => '<strong>' . __( 'Plugin Name Settings', 'edd-checkout-wizard' ) . '</strong>',
                    'desc'  => __( 'Configure Plugin Name Settings', 'edd-checkout-wizard' ),
                    'type'  => 'header',
                )
            );

            return array_merge( $settings, $new_settings );
        }

        public function render_html_tabs() {
            if( isset($_POST['action']) && $_POST['action'] == 'edd_recalculate_taxes' ) {
                return;
            }

            $tabs = array(
                'overview' => array(
                    'label' => __( 'Overview' ),
                    'selectors' => array(
                        '#edd_checkout_cart_form',
                        '#edd-rp-checkout-wrapper', // Support for EDD Recommended Products
                    )
                ),
                'payment_method' => array(
                    'label' => __( 'Payment Method' ),
                    'selectors' => array(
                        '.edd-payment-icons',
                        '#edd_payment_mode_select',
                    )
                ),
                'account' => array(
                    'label' => __( 'Account' ),
                    'selectors' => array(
                        '#edd_checkout_login_register',
                        '#edd_checkout_user_info',
                    )
                ),
                'address' => array(
                    'label' => __( 'Billing Address' ),
                    'selectors' => array(
                        '#edd_cc_address',
                        '#edd_vat_info_show', // Support for EDD VAT
                    )
                ),
                'payment' => array(
                    'label' => __( 'Payment' ),
                    'selectors' => array(
                        '#edd_purchase_submit',
                    )
                ),
            );

            if( ! edd_show_gateways() ) {
                unset($tabs['payment_method']);
            }

            $tabs = apply_filters( 'edd_checkout_wizard_checkout_tabs', $tabs );

            $current_tab = 'tab-' . array_keys( $tabs )[0];
            $current_step = 1;

            ?>
            <div class="edd-checkout-wizard-nav-tabs">
            <?php
                foreach($tabs as $tab_id => $tab_args) {
                    ?>
                    <a id="tab-<?php echo $tab_id; ?>"
                       href="#"
                       class="edd-checkout-wizard-nav-tab nav-tab <?php echo ($current_tab == 'tab-' . $tab_id) ? 'nav-tab-active' : ''; ?>"
                       data-selector="<?php echo implode( ', ', $tab_args['selectors'] ); ?>"
                       data-validated="false"
                       data-current="<?php echo ($current_tab == 'tab-' . $tab_id) ? 'true' : 'false'; ?>"
                    >
                        <span class="edd-checkout-wizard-nav-tab-number"><?php echo $current_step; ?></span>
                        <span class="edd-checkout-wizard-nav-tab-label"><?php echo $tab_args['label']; ?></span>
                    </a>
                    <?php

                    $current_step++;
                }
            ?>
            </div>
            <?php
        }

        public function render_html_buttons() {
            $args = array(
                'style'       => edd_get_option( 'button_style', 'button' ),
                'color'       => edd_get_option( 'checkout_color', 'blue' ),
            );

            $style = edd_get_option( 'button_style', 'button' );
            $color = edd_get_option( 'checkout_color', 'blue' );

            $color = ( $color == 'inherit' ) ? '' : $color;
            ?>
            <div class="edd-checkout-wizard-buttons">
                <button
                    type="button"
                    id="edd-checkout-wizard-prev-button"
                    class="edd-checkout-wizard-button <?php echo $style; ?> <?php echo $color; ?>"
                    aria-hidden="true"
                >
                    <span class="edd-checkout-wizard-button-label"><?php echo __( 'Previous' ); ?></span>
                </button>
                <button
                    type="button"
                    id="edd-checkout-wizard-next-button"
                    class="edd-checkout-wizard-button <?php echo $style; ?> <?php echo $color; ?>"
                    aria-hidden="true"
                >
                    <span class="edd-checkout-wizard-button-label"><?php echo __( 'Next' ); ?></span>
                </button>
            </div>
            <?php
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_Checkout_Wizard
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Checkout_Wizard The one true EDD_Checkout_Wizard
 */
function edd_checkout_wizard() {
    return EDD_Checkout_Wizard::instance();
}
add_action( 'plugins_loaded', 'edd_checkout_wizard' );


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function edd_checkout_wizard_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'edd_checkout_wizard_activation' );
