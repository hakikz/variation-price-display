<?php
/**
 * Plugin Name: Variation Price Display Range for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/variation-price-display
 * Description: Adds lots of advanced options to control how you display the price for your WooCommerce variable products.
 * Author: WPXtension
 * Version: 1.2.3
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.0
 * Requires PHP: 7.0
 * WC requires at least: 5.5
 * WC tested up to: 6.5.1
 * Text Domain: variation-price-display
 * Author URI: https://wpxtension.com/
 */

defined( 'ABSPATH' ) or die( 'Keep Quit' );


/**
 * Main Class Start
 * Plugin class to initialize all the settings
 */

if( !class_exists( 'Variation_Price_Display' ) ):
 
    class Variation_Price_Display {


        /*
         * Version of Plugin.
         *
         */

        protected $_version = '1.2.3';

        /*
         * Construct of the Class.
         *
         */

        public function __construct(){
            if( self::is_woo_active() ){
                $this->constants();
                $this->includes();
                $this->init();
            }
            else{
                add_action( 'admin_notices', __CLASS__ . '::admin_notice__error' );
            }
        }

        /*
         * Version function of VPD.
         *
         */
        public function version() {
            return esc_attr( $this->_version );
        }


        /*
         * Bootstraps the class and hooks required actions & filters.
         *
         */
        public function init() {

            // Load TextDomain
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

            // Current Screen
            add_action('current_screen', array( $this, 'get_screen' ) );

            // Backend Scripts
            add_action('admin_enqueue_scripts', array( $this, 'backend_scripts' ) );

            // Frontend Scripts
            add_action('wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

            add_filter( 'plugin_action_links_variation-price-display/variation-price-display.php', array( $this, 'settings_link') );

            // Plugin row meta link
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            // Admin settings page
            add_action( 'woocommerce_init', array( $this, 'init_great_admin' ) );
        } 

        /**
         *
         * Load Text Domain Folder
         *
         */
        public function load_textdomain() {
            load_plugin_textdomain( "variation-price-display", false, basename( dirname( __FILE__ ) )."/languages" );
        }

        /*
         * Get screen object.
         *
         */
        public function get_screen(){

            $screen = get_current_screen();
            return $screen;
        }


        /*
         * Checking WooCommerce is installed and activated or not.
         *
         */

        public static function is_woo_active(){

            $woo_exists = false;

            // Check if `is_plugin_active_for_network` function is not exist then require plugin.php
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
         * VPD Frontend Scripts.
         *
         */
        public function frontend_scripts(){

            // Check if it's the single product page. If not, just emtry return before executing the following scripts.
            if( !is_product() ){
                return;
            }

            wp_enqueue_style('vpd-public-style', plugins_url('public/css/vpd-public-style.min.css', __FILE__), array(), VPD_VERSION, false );
            wp_enqueue_script( 'vpd-public-script', plugins_url('public/js/vpd-public-script.min.js', __FILE__), array('jquery'), VPD_VERSION, true );
            wp_localize_script( 'vpd-public-script', 'vpd_public_object',
                array( 
                    'changeVariationPrice' => VPD_Common::get_options()->change_variation_price,
                    'hideDefaultPrice' => VPD_Common::get_options()->hide_default_price,
                )
            );

        }

        /*
         * VPD backend Scripts.
         *
         */
        public function backend_scripts(){

            // Check if it's the ?page=woocommerce_page_wc-settings. If not, just empty return before executing the folowing scripts. 
            if( $this->get_screen()->id != 'woocommerce_page_wc-settings') {
                return;
            }
            wp_enqueue_style( 'vpd-admin-style', plugins_url('admin/css/vpd-admin-style.min.css', __FILE__), array(), VPD_VERSION, false );
            wp_enqueue_script( 'vpd-admin-script', plugins_url('admin/js/vpd-admin-script.min.js', __FILE__), array('jquery'), VPD_VERSION, true );
            wp_localize_script( 'vpd-admin-script', 'vpd_admin_object',
                array( 
                    'priceType' => VPD_Common::get_options()->price_display_option,
                )
            );
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

            $message = wp_kses( __( "<strong>Variation Price Display Range for WooCommerce</strong> is an add-on of ", 'variation-price-display' ), array( 'strong' => array() ) );

            printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
        }


        /*
         * Define function of VPD.
         *
         */

        public function define( $name, $value, $case_insensitive = false ) {
            if ( ! defined( $name ) ) {
                define( $name, $value, $case_insensitive );
            }
        }


        /*
         * Constants of VPD.
         *
         */

        public function constants() {
            $this->define( 'VPD_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
            $this->define( 'VPD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
            $this->define( 'VPD_VERSION', $this->version() );
            $this->define( 'VPD_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
            $this->define( 'VPD_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
            $this->define( 'VPD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'VPD_PLUGIN_FILE', __FILE__ );
        }

        /*
         * Includes of files.
         *
         */

        public function includes() {

            /**
             * All display conditions of pricing
             * Type of display options
             */
            require_once $this->include_path( 'class-vpd-common.php' );
            require_once $this->include_path( 'conditions.php' );

            /**
             * Plugin settings fields 
             */
            if ( is_admin() ) {
                require_once $this->include_path( 'class-vpd-custom-fields.php' );
                // require_once $this->include_path( 'class-vpd-admin-settings.php' );
            }

        }


        /*
         * Function of include path.
         *
         */
        public function include_path( $file = '' ) {
            $file = ltrim( $file, '/' );

            return VPD_PLUGIN_INCLUDE_PATH . $file;
        }
        


        /*
         * Add setting link to the plugin.
         *
         * @return setting link html.
         */
        public function settings_link( $links ) {

            $parameters = array(
                'page'  => 'wc-settings',
                'tab'   => 'variation-price-display',
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

            if( !VPD_Common::check_plugin_state('variation-price-display-pro') ){
                $pro_link = "<a style='font-weight: bold; color: #8012f9;' href='https://wpxtension.com/product/variation-price-display-for-woocommerce/' target='_blank'>" . __( 'Go Premium' ) . '</a>';
                array_push( $links, $pro_link );
            }

            return $links;
        }


        /**
        * ====================================================
        * Plugin row link for plugin listing page
        * ====================================================
        **/

        function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
 
            if ( strpos( $plugin_file, 'variation-price-display.php' ) !== false ) {

                $new_links = array(
                    'ticket' => '<a href="https://wpxtension.com/submit-a-ticket/" target="_blank" style="font-weight: bold; color: #8012f9;">Help & Support</a>',
                    'doc' => '<a href="https://wpxtension.com/doc-category/variation-price-display-range-for-woocommerce/" target="_blank">Documentation</a>'
                );
                 
                $plugin_meta = array_merge( $plugin_meta, $new_links );

            }
             
            return $plugin_meta;
        }


        public function init_great_admin() {

            // other great admin stuff

            // this is probably in a class constructor or something similar
            add_filter( 'woocommerce_get_settings_pages', function( $pages ) {

                $pages[] = include( 'includes/class-vpd-admin-settings.php' );

                return $pages;

            } );
        }
        

    }

    new Variation_Price_Display();

endif;

