<?php

if ( !class_exists( 'WPX_Menu_Class' ) ):

    class WPX_Menu_Class{

        public function __construct(){
            self::register();
        }

        /**
         * Register a custom menu page.
         */
        public static function wpx_menu_page() {
            add_menu_page(
                __( 'WPXtension', 'textdomain' ),
                'WPXtension',
                'manage_options',
                'wpx_settings_menu',
                // __CLASS__. '::wpx_menu_page_callback',
                false,
                plugins_url( 'variation-price-display/admin/images/wpx-icon.svg' ),
                76
            );
        }
        
        public static function register(){
            add_action( 'admin_menu', __CLASS__ . '::wpx_menu_page' );
        }

        public static function wpx_menu_page_callback(){
            echo "Hello World";
        }

    }

    new WPX_Menu_Class();

endif;