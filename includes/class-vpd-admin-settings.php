<?php

defined( 'ABSPATH' ) or die( 'Keep Quit' );

if ( !class_exists( 'VPD_Admin_Settings' ) ):

	class VPD_Admin_Settings{

		/*
         * Construct of the Class.
         *
         */
		public function __construct(){

			// Checking WooCommerce is activated or not in plugin main php file
            if( Variation_Price_Display::is_woo_active() ){
                self::init();
            }

        }

        /*
         * Bootstraps the class and hooks required actions & filters.
         *
         */
		public static function init() {
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
            add_action( 'woocommerce_settings_tabs_variation_price_display', __CLASS__ . '::settings_tab' );
            add_action( 'woocommerce_update_options_variation_price_display', __CLASS__ . '::update_settings' );
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

            $price_display_option = VPD_Common::get_options()->price_display_option;

            $settings = array(
                array(
                    'name'     => __( 'Variation Price Range Settings', 'variation-price-display' ),
                    'type'     => 'vpd_title',
                    'desc'     => '',
                    'id'       => 'vpd_price_display_section_title'
                ),
                array(
                    'name' => __( 'Price types ', 'variation-price-display' ),
                    'type' => 'vpd_select',
                    'id'   => 'vpd_price_types',
                    'required' => true,
                    'options' => apply_filters( 'vpd_option_list_html', array(
                        'min' => __( 'Minimum Price ', 'variation-price-display' ), 
                        'max' => __( 'Maximum Price ', 'variation-price-display' ), 
                        'min_to_max' => __( 'Minimum to Maximum Price ', 'variation-price-display' ), 
                        'max_to_min' => __( 'Maximum to Minimum Price ', 'variation-price-display' ), 
                    ) ),
                    'desc_tip'      => __( 'Select a price type to change the default WooCommerce price range', 'variation-price-display' ),
                    'class'   => 'vpd-dropdown',
                    'tr_class'   => 'vpd-select-wrap',
                    'default' => $price_display_option,
                ),

                array(
                    'title'      => __( 'Add From', 'variation-price-display' ),
                    'id'        => 'vpd_from_before_min_price',
                    'type'      => 'checkbox',
                    'desc'     => __( 'Enable it to display <b><u>From</u></b> before Minimum Price', 'variation-price-display' ),
                    'class'     => 'vpd-from-text',
                    'default'   => 'yes',
                ),   

                array(
                    'name'     => __( 'Add Up To', 'variation-price-display' ),
                    'id'       => 'vpd_up_to_before_max_price',
                    'type'     => 'checkbox',
                    'desc'     => __( 'Enable it to display <b><u>Up To</u></b> before Maximum Price', 'variation-price-display' ),
                    'class'    => 'vpd-up-to-text',
                ), 

                array(
                    'name'     => __( 'Variation Price', 'variation-price-display' ),
                    'id'       => 'vpd_change_price',
                    'type'     => 'checkbox',
                    'desc'     => __( 'Change price, based on selected variation(s)', 'variation-price-display' ),
                    'class'    => 'vpd-up-to-text',
                    'default'   => 'yes',
                ), 

                array(
                    'name'     => __( 'Hide Default Price', 'variation-price-display' ),
                    'id'       => 'vpd_hide_default_price',
                    'type'     => 'checkbox',
                    'desc'     => __( 'Don\'t display default variation price', 'variation-price-display' ),
                    'class'    => 'vpd-up-to-text',
                    'default'   => 'yes',
                ),            

                array(
                     'type' => 'sectionend',
                     'id' => 'vpd_setting_section_end'
                )
            );

            return apply_filters( 'vpd_settings', $settings );
        }

	}

	new VPD_Admin_Settings();

endif;