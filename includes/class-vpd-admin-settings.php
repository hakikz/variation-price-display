<?php

defined( 'ABSPATH' ) or die( 'Keep Quit' );

// if ( !class_exists( 'VPD_Admin_Settings' ) ):

	class VPD_Admin_Settings extends \WC_Settings_Page{

		/*
         * Construct of the Class.
         *
         */
		public function __construct(){

			// Checking WooCommerce is activated or not in plugin main php file
            if( Variation_Price_Display::is_woo_active() ){

                $this->id               = 'variation-price-display';
                $this->label            = 'Variation Price Display';

                parent::__construct();

                $this->init();
            }

        }

        /*
         * Bootstraps the class and hooks required actions & filters.
         *
         */
		public function init() {
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
            add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
            
            // We need to add sections for our settings tab thats why we used this hook
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		}


        public function get_sections() {
                
            $sections = array(
                ''          => esc_html__( 'General', 'woo-variation-gallery' ),
                'advanced'  => esc_html__( 'Advanced', 'woo-variation-gallery' )
            );
            
            return apply_filters( 'woocommerce_get_sections_variation_price_display', $sections );
        }

		/*
         * Add a new settings tab to the WooCommerce settings tabs array.
         *
         * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
         * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
         */
        // public static function add_settings_tab( $settings_tabs ) {
        //     $settings_tabs['variation_price_display'] = __( 'Variation Price Display', 'variation-price-display' );
        //     return $settings_tabs;
        // }


        /*
         * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
         *
         * @uses woocommerce_admin_fields()
         * @uses self::get_settings()
         */
        // public static function settings_tab() {
        //     woocommerce_admin_fields( self::get_settings() );
        // }


        /*
         * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
         *
         * @uses woocommerce_update_options()
         * @uses self::get_settings()
         */
        // public static function update_settings() {
        //     woocommerce_update_options( self::get_settings() );
        // }


        /*
         * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
         *
         * @return array Array of settings for @see woocommerce_admin_fields() function.
         */
        public function get_settings( $current_section = '' ) {

            $price_display_option = VPD_Common::get_options()->price_display_option;

            switch ( $current_section ):

                case 'advanced':
                    $settings = apply_filters( 'vpd_advanced_settings', $current_section );
                    break;
                default:
                    $settings = array(
                        array(
                            'title'     => __( 'Variation Prices', 'variation-price-display' ),
                            'type'     => 'vpd_title',
                            'desc'     => 'Replace the WooCommerce variation price range with any other format',
                            'table_class' => 'vpd-table vpd-table--price-display',
                            'id'       => 'vpd_price_display_section_title'
                        ),
                        array(
                            'name' => __( 'Price types ', 'variation-price-display' ),
                            'type' => 'vpd_select',
                            'id'   => 'vpd_price_types',
                            'required' => true,
                            'options' => apply_filters( 'vpd_price_type_option_list', array(
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
                            'class'     => 'vpd-from-text vpd-checkbox',
                            'default'   => 'yes',
                        ),   

                        array(
                            'name'     => __( 'Add Up To', 'variation-price-display' ),
                            'id'       => 'vpd_up_to_before_max_price',
                            'type'     => 'checkbox',
                            'desc'     => __( 'Enable it to display <b><u>Up To</u></b> before Maximum Price', 'variation-price-display' ),
                            'class'    => 'vpd-up-to-text vpd-checkbox',
                        ), 

                        array(
                            'name'     => __( 'Custom Text', 'variation-price-display-pro' ),
                            'id'       => 'vpd_custom_price_text',
                            'type'     => 'text',
                            'desc'     => __( '<b>Some Examples:</b> <code>Starts at %min_price%</code>, <code>Starts %min_price% to %max_price%</code>' ),
                            'desc_tip'      => __( 'Display price format as you want, between two prices. <b>Note:</b> Display <b>Minimum Price</b> as <u>%min_price%</u> and <b>Maximum Price</b> as <u>%max_price%</u>. ' ),
                            'class'    => 'vpd-upto-textbox',
                            'default'   => 'Starts at %min_price%',
                        ),

                        array(
                            'name'     => __( 'Variation Price', 'variation-price-display' ),
                            'id'       => 'vpd_change_price',
                            'type'     => 'checkbox',
                            'desc'     => __( 'Change price, based on selected variation(s)', 'variation-price-display' ),
                            'class'    => 'vpd-up-to-text vpd-checkbox',
                            'default'   => 'yes',
                        ), 

                        array(
                            'name'     => __( 'Hide Default Price', 'variation-price-display' ),
                            'id'       => 'vpd_hide_default_price',
                            'type'     => 'checkbox',
                            'desc'     => __( 'Don\'t display default variation price', 'variation-price-display' ),
                            'class'    => 'vpd-up-to-text vpd-checkbox',
                            'default'   => 'yes',
                        ),

                        array(
                            'name'     => __( 'Hide Reset Link', 'variation-price-display' ),
                            'id'       => 'vpd_hide_reset_link',
                            'type'     => 'checkbox',
                            'desc'     => __( 'Remove "Clear" link on single product page', 'variation-price-display' ),
                            'class'    => 'vpd-up-to-text vpd-checkbox',
                            'default'   => 'no',
                        ),    

                        array(
                            'name'     => __( 'Format Sale Price', 'variation-price-display' ),
                            'id'       => 'vpd_format_sale_price',
                            'type'     => 'checkbox',
                            'desc'     => __( 'Show Regular Price and Sale Price Format.' ),
                            'desc_tip'     => __( ' <b>For Example:</b> <code>From <del>$40</del> $38 </code>' ),
                            'class'    => 'vpd-up-to-text vpd-checkbox',
                            'default'   => 'no',
                        ),            

                        array(
                             'type' => 'sectionend',
                             'id' => 'vpd_setting_section_end'
                        )
                    );
                    break;

            endswitch;


            return apply_filters( 'vpd_settings', $settings, $current_section );
        }

        public function output() {
                
            global $current_section, $hide_save_button;

            do_action( 'vpd_settings_start' );
            
            if ( $current_section === 'advanced' && !VPD_Common::check_plugin_state('variation-price-display-pro') ) {
                    $hide_save_button = true;
                    echo "<a href='https://wpxtension.com/product/variation-price-display-for-woocommerce/' target='_blank'><img src='https://ps.w.org/variation-price-display/assets/advanced_tab.png' width='100%' /></a>";
                    // $this->tutorial_section();
            } 
            else {
                $settings = $this->get_settings( $current_section );
                WC_Admin_Settings::output_fields( $settings );
            }

            do_action( 'vpd_settings_end' );
        }

        public function save() {
                
            global $current_section;
            
            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::save_fields( $settings );
        }

	}

	new VPD_Admin_Settings();

// endif;