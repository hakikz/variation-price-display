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

        	do_action( 'vpd_setting_section_start' );

        	if ( ! empty( $value['title'] ) ) {
				echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
			}
			if ( ! empty( $value['desc'] ) ) {
				echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
				echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
				echo '</div>';
			}
			echo '<table class="form-table '.esc_html( $value['table_class'] ).'">' . "\n\n";
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

		// Add markup before settings page starts
		public function before_table_start(){
			echo "
				<div class=\"vpd-setting\">

					<aside class=\"vpd-setting-sidebar\">

						<div class=\"sideblock\">

							<h3><span class=\"dashicons dashicons-text-page\"></span> " . __('Documentation', 'variation-price-display') . "</h3>
							<p>" . __('To know more about settings, Please check our <a href="https://wpxtension.com/doc-category/variation-price-display-range-for-woocommerce/" target="_blank">documentation</a>', 'variation-price-display') . "</p>

						</div>

						<div class=\"sideblock\">

							<h3><span class=\"dashicons dashicons-editor-help\"></span> " . __('Help & Support', 'variation-price-display') . "</h3>
							<p>" . __('Still facing issues with Variation Price Range Display? Please <a href="https://wpxtension.com/submit-a-ticket/" target="_blank">open a ticket.</a>', 'variation-price-display') . "</p>

						</div>

						<div class=\"sideblock\">

							<h3><span class=\"dashicons dashicons-star-filled\"></span> " . __('Love Our Plugin?', 'variation-price-display') . "</h3>
							<p>" . __('We feel honored when you use our plugin on your site. If you have found our plugin useful and makes you smile, please consider giving us a <a href="https://wordpress.org/support/plugin/variation-price-display/reviews/" target="_blank">5-star rating on WordPress.org</a>. It will inspire us a lot.', 'variation-price-display') . "</p>

						</div>

					</aside>

					<div class=\"vpd-setting-content\">";
		}

		// Add markup after settings page end
		public function after_table_end(){
			echo "</div></div>";
		}

        /*
         * Register all hooks.
         *
         */

        public function register(){

        	// Registering VPD Select Custom Field
        	add_action( 'woocommerce_admin_field_vpd_select' , array( $this, 'add_admin_field_vpd_select' ), 99, 1 );
        	add_action( 'woocommerce_admin_field_vpd_title' , array( $this, 'add_admin_field_vpd_title' ), 99, 1 );

        	// Add markup before table starts
        	add_action( 'woocommerce_settings_variation-price-display', array( $this, 'before_table_start' ) );
        	add_action( 'woocommerce_after_settings__variation-price-display', array( $this, 'after_table_end' ) );

        }

    }

    new VPD_Custom_Woo_Fields();

endif;