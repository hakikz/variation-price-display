<?php

defined( 'ABSPATH' ) or die( 'Keep Quit' );

/**
 * Main Class Start
 * Plugin class to initialize all the settings
 */

if( !class_exists( 'VPD_Common' ) ):
 
    class VPD_Common {

        public static function get_options(){

            $vpd_options = [];

            $vpd_options['price_display_option'] = get_option('vpd_price_types', 'min');
            $vpd_options['display_from_before_min_price'] = get_option('vpd_from_before_min_price', 'yes');
            $vpd_options['display_up_to_before_max_price'] = get_option('vpd_up_to_before_max_price', '');
            $vpd_options['change_variation_price'] = get_option('vpd_change_price', 'yes');
            $vpd_options['hide_default_price'] = get_option('vpd_hide_default_price', 'yes');
            $vpd_options['hide_reset_link'] = get_option('vpd_hide_reset_link', 'no');
            $vpd_options['format_sale_price'] = get_option('vpd_format_sale_price', 'no');

            return (object) apply_filters('vpd_option_fields', $vpd_options);

        }

    }

    new VPD_Common();

endif;