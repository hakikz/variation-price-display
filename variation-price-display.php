<?php
/**
 * Plugin Name: Variation Product Price Display for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/variation-price-display
 * Description: Adds lots of advanced options to control how you display the price for your WooCommerce variable products.
 * Author: Hakik Zaman
 * Version: 1.0.3
 * Domain Path: /languages
 * Requires at least: 5.5
 * Tested up to: 5.9
 * Requires PHP: 7.0
 * WC requires at least: 5.5
 * WC tested up to: 6.2.0
 * Text Domain: variation-price-display
 * Author URI: https://github.com/hakikz
 */

defined( 'ABSPATH' ) or die( 'Keep Quit' );

/**
 * All display conditions of pricing
 * Type of display options
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/conditions.php';

/**
 * Main Class Start
 * Plugin class to initialize all the settings
 */

if( !class_exists( 'Variation_Price_Display' ) ):
 
    class Variation_Price_Display {

        /*
         * Construct of the Class.
         *
         */
        public function __construct(){
            if( self::is_woo_active() ){
                self::init();
            }
            else{
                add_action( 'admin_notices', __CLASS__ . '::admin_notice__error' );
            }
        }

        /*
         * Checking WooCommerce is installed and activated or not.
         *
         */

        public static function is_woo_active(){

            $woo_exists = false;

            // Check if `is_plugin_active_for_network` function is not exist then requre plugin.php
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
              require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }

            if ( is_multisite() ) {
                // this plugin is network activated - Woo must be network activated 
                if ( is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
                    $woo_exists = is_plugin_active_for_network('woocommerce/woocommerce.php') ? true : false; 
                // this plugin is locally activated - Woo can be network or locally activated 
                } 
                else {
                    $woo_exists = is_plugin_active( 'woocommerce/woocommerce.php')  ? true : false;   
                }
            }
            else {
              $woo_exists =  is_plugin_active( 'woocommerce/woocommerce.php') ? true : false;     
            }

            return $woo_exists;
                
        }

        /*
         * Notice if WooCommerce is not activated.
         *
         */

        public static function admin_notice__error() {
            $class = 'notice notice-error';
            $text = esc_html__( 'WooCommerce', 'variation-price-display' );

            $link_args = array(
                'tab'       => 'plugin-information',
                'plugin'    => 'woocommerce',
                'TB_iframe' => 'true',
                'width'     => '640',
                'height'    => '500',
            );

            $link    = esc_url( add_query_arg( $link_args, admin_url( 'plugin-install.php' ) ) );

            $message = wp_kses( __( "<strong>Variation Product Price Display for WooCommerce</strong> is an add-on of ", 'variation-price-display' ), array( 'strong' => array() ) );

            printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
        }


        /*
         * Bootstraps the class and hooks required actions & filters.
         *
         */
        public static function init() {
            add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
            add_action( 'woocommerce_settings_tabs_variation_price_display', __CLASS__ . '::settings_tab' );
            add_action( 'woocommerce_update_options_variation_price_display', __CLASS__ . '::update_settings' );
            add_filter( 'plugin_action_links_variation-price-display/variation-price-display.php', __CLASS__ . '::settings_link' );
        } 
        
        
        /*
         * Add a new settings tab to the WooCommerce settings tabs array.
         *
         * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
         * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
         */
        public static function add_settings_tab( $settings_tabs ) {
            $settings_tabs['variation_price_display'] = __( 'Variation Price Display', 'variation-price-display' );
            return $settings_tabs;
        }


        /*
         * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
         *
         * @uses woocommerce_admin_fields()
         * @uses self::get_settings()
         */
        public static function settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
        }


        /*
         * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
         *
         * @uses woocommerce_update_options()
         * @uses self::get_settings()
         */
        public static function update_settings() {
            woocommerce_update_options( self::get_settings() );
        }


        /*
         * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
         *
         * @return array Array of settings for @see woocommerce_admin_fields() function.
         */
        public static function get_settings() {

            $price_display_option = get_option('vpd_price_types', 'min');

            $settings = array(
                array(
                    'name'     => __( 'Variation Pricing Layout', 'variation-price-display' ),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'vpd_price_display_section_title'
                ),
                array(
                    'name' => __( 'Price types ', 'variation-price-display' ),
                    'type' => 'radio',
                    'id'   => 'vpd_price_types',
                    'required' => true,
                    'options' => array(
                        'min' => __( 'Minimum Price ', 'variation-price-display' ), 
                        'max' => __( 'Maximum Price ', 'variation-price-display' ), 
                        'min_to_max' => __( 'Minimum to Maximum Price ', 'variation-price-display' ), 
                        'max_to_min' => __( 'Maximum to Minimum Price ', 'variation-price-display' ), 
                    ),
                    'default' => $price_display_option,
                ),

                array(
                    'name'     => __( 'Add From', 'variation-price-display' ),
                    'id'       => 'vpd_from_before_min_price',
                    'type'     => 'checkbox',
                    'desc'     => __( 'Enable it to display <b><u>From</u></b> before Minimum Price', 'variation-price-display' ),
                    'class'    => 'vpd-from-text',
                    'default' => 'yes',
                ),   

                array(
                    'name'     => __( 'Add Up To', 'variation-price-display' ),
                    'id'       => 'vpd_up_to_before_max_price',
                    'type'     => 'checkbox',
                    'desc'     => __( 'Enable it to display <b><u>Up To</u></b> before Maximum Price', 'variation-price-display' ),
                    'class'    => 'vpd-up-to-text',
                ),              

                array(
                     'type' => 'sectionend',
                     'id' => 'vpd_setting_section_end'
                )
            );

            return apply_filters( 'vpd_settings', $settings );
        }


        /*
         * Add setting link to the plugin.
         *
         * @return setting link html.
         */
        public static function settings_link( $links ) {

            $parameters = array(
                'page'  => 'wc-settings',
                'tab'   => 'variation_price_display',
            );

            // Build and escape the URL.
            $url = esc_url( add_query_arg(
                $parameters,
                get_admin_url() . 'admin.php'
            ) );

            // Create the link.
            $settings_link = "<a href='$url'>" . __( 'Settings', 'variation-price-display' ) . '</a>';
            
            // Adds the link to the start of the array.
            array_unshift(
                $links,
                $settings_link
            );

            return $links;
        }

    }

    new Variation_Price_Display();

endif;