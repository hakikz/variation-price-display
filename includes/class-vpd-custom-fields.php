<?php

defined( 'ABSPATH' ) or die( 'Keep Quit' );

/**
 * Main Class Start
 * Plugin class to initialize all the settings
 */

if( !class_exists( 'VPD_Custom_Woo_Fields' ) ):
 
    class VPD_Custom_Woo_Fields {

    	/*
         * Construct of the Class.
         *
         */
        public function __construct(){

			// Calling Register function to run the hooks
			$this->register();

        }

        /*
         * VPD Title custom field.
         *
         */
        public function add_admin_field_vpd_title( $value ){
        	if ( ! empty( $value['title'] ) ) {
				echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
			}
			if ( ! empty( $value['desc'] ) ) {
				echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
				echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
				echo '</div>';
			}
			echo '<table class="form-table vpd-table">' . "\n\n";
			if ( ! empty( $value['id'] ) ) {
				do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
			}
        }

        /*
         * VPD Select custom field.
         *
         */

        public function add_admin_field_vpd_select( $value ){

        	// Description handling.
            $option_value 		= (array) WC_Admin_Settings::get_option( $value['id'] );
            $field_description 	= WC_Admin_Settings::get_field_description( $value );
            $description       	= $field_description['description'];
            $tooltip_html      	= $field_description['tooltip_html'];

            // Custom attribute handling.
			$this->custom_attributes 	= array();
    
		    ?>
		   
		    <tr valign="top" class="<?php echo esc_attr( $value['tr_class'] ); ?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<select
						name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						<?php echo implode( ' ', $this->custom_attributes ); // WPCS: XSS ok. ?>
						<?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?>
						>
						<?php
						foreach ( $value['options'] as $key => $val ) {
							?>
							<option value="<?php echo esc_attr( $key ); ?>"
								<?php

								if ( is_array( $option_value ) ) {
									selected( in_array( (string) $key, $option_value, true ), true );
								} else {
									selected( $option_value, (string) $key );
								}

								?>
							><?php echo esc_html( $val ); ?></option>
							<?php
						}
						?>
					</select> <?php echo $description ; // WPCS: XSS ok. ?>
				</td>
			</tr>

		<?php       
		}

		// Add markup after table starts (for further use)
		// public function before_table_start(){
		// 	echo "<thead><tr><td>Test</td></tr></thead>";
		// }


        /*
         * Register all hooks.
         *
         */

        public function register(){

        	// Registering VPD Select Custom Field
        	add_action( 'woocommerce_admin_field_vpd_select' , array( $this, 'add_admin_field_vpd_select' ), 99, 1 );
        	add_action( 'woocommerce_admin_field_vpd_title' , array( $this, 'add_admin_field_vpd_title' ), 99, 1 );

        	// Add markup before table starts (for further use)
        	// add_action( 'woocommerce_settings_vpd_price_display_section_title', array( $this, 'before_table_start' ) );

        }

    }

    new VPD_Custom_Woo_Fields();

endif;