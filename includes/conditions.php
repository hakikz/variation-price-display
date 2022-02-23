<?php 

if ( !function_exists( 'vpd_get_price_html' ) ){

	// add_filter('woocommerce_get_price_html', 'vpd_get_price_html');

	add_filter('woocommerce_variable_price_html', 'vpd_get_price_html', 10, 2);

	function vpd_get_price_html( $price, $product ){

		// $product = wc_get_product( get_the_ID() );
		
		if($product->is_type( 'variable' )):
		
			$price_display_option = VPD_Common::get_options()->price_display_option;
			$display_from_before_min_price = VPD_Common::get_options()->display_from_before_min_price;
			$display_up_to_before_max_price = VPD_Common::get_options()->display_up_to_before_max_price;

			switch ($price_display_option) {

			  case "min":

				if ( $display_from_before_min_price === 'yes' ){

					$prices = __('From ', 'variation-price-display') . wc_price( $product->get_variation_price( 'min' ) );

				}
				else{
					
					$prices = wc_price( $product->get_variation_price( 'min' ) );

				}
				

			    break;

			  case "max":

				if ( $display_up_to_before_max_price === 'yes' ){

					$prices = __('Up To ', 'variation-price-display') . wc_price( $product->get_variation_price( 'max' ) );
					
				}
				else{

			    	$prices = wc_price( $product->get_variation_price( 'max' ) );
				}

			    break;

			  case "max_to_min":

			  	if( $product->get_variation_price( 'max' ) === $product->get_variation_price( 'min' ) ){

			  		$prices = wc_price( $product->get_variation_price( 'max' ) );

			  	}
			  	else{

			  		$prices = wc_format_price_range($product->get_variation_price( 'max' ) , $product->get_variation_price( 'min' ) );

			  	}

			    break;

			  default:

			  	if( $product->get_variation_price( 'max' ) === $product->get_variation_price( 'min' ) ){

			  		$prices = wc_price( $product->get_variation_price( 'min' ) );

			  	}
			  	else{

			  		$prices = wc_format_price_range($product->get_variation_price( 'min' ) , $product->get_variation_price( 'max' ) );
			  		
			  	}

			}

			$vpd_price = apply_filters( 'vpd_woocommerce_variable_price_html', $prices . $product->get_price_suffix(), $product );

			return $vpd_price;
		
		else:

			return $price;

		endif;
	}
}