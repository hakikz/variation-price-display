<?php 

if ( !function_exists( 'vpd_get_price_html' ) ){

	add_filter('woocommerce_variable_price_html', 'vpd_get_price_html', 10, 2);

	function vpd_get_price_html( $price, $product ){

		// $product = wc_get_product( get_the_ID() );

		// Disable VPD if same price for all variations

		$variation_prices = $product->get_variation_prices( true );

	    $count  = (int) count( array_unique( $variation_prices['price'] ));

		if( $count === apply_filters('vpd_variation_same_price_count', 1) ){

			return $price;

		}

		// Disable VPD filter

		if( apply_filters( 'disable_vpd_price_format', false, $price, $product ) ){

			return $price;

		}
		
		if($product->is_type( 'variable' )):
		
			$price_display_option = VPD_Common::get_options()->price_display_option;
			$display_from_before_min_price = VPD_Common::get_options()->display_from_before_min_price;
			$display_up_to_before_max_price = VPD_Common::get_options()->display_up_to_before_max_price;
			$format_sale_price = VPD_Common::get_options()->format_sale_price;

			switch ($price_display_option) {

			  case "min":

			  	$before_min_price = ( $display_from_before_min_price === 'yes' ) ? __('From ', 'variation-price-display') : '';

			  	// $min_price = wc_price( $product->get_variation_price( 'min', true ) );
			  	$min_price = vpd_format_price( $format_sale_price, 'min', $product );

				$prices = apply_filters( 'vpd_prefix_min_price', $before_min_price ) . $min_price;
				
			    break;

			  case "max":

			  	$before_max_price = ( $display_up_to_before_max_price === 'yes' ) ? __('Up To ', 'variation-price-display') : '';

			  	$max_price = vpd_format_price( $format_sale_price, 'max', $product );

				$prices = apply_filters( 'vpd_prefix_max_price', $before_max_price ) . $max_price;

			    break;

			  case "max_to_min":

			  	if( $product->get_variation_price( 'max', true ) === $product->get_variation_price( 'min', true ) ){

			  		$prices = wc_price( $product->get_variation_price( 'max', true ) );

			  	}
			  	else{

			  		$prices = wc_format_price_range($product->get_variation_price( 'max', true ) , $product->get_variation_price( 'min', true ) );

			  	}

			    break;

			  default:

			  	if( $product->get_variation_price( 'max', true ) === $product->get_variation_price( 'min', true ) ){

			  		$prices = wc_price( $product->get_variation_price( 'min', true ) );

			  	}
			  	else{

			  		$prices = wc_format_price_range($product->get_variation_price( 'min', true ) , $product->get_variation_price( 'max', true ) );
			  		
			  	}

			}

			$vpd_price = apply_filters( 'vpd_woocommerce_variable_price_html', $prices . $product->get_price_suffix(), $product, $price, $price_display_option );

			return $vpd_price;
		
		else:

			return $price;

		endif;
	}
}

// Reset "Clear" link control

if ( ! function_exists( 'vpd_remove_reset_link' ) ){

	add_filter( 'woocommerce_reset_variations_link', 'vpd_remove_reset_link', 20, 1 );

	function vpd_remove_reset_link( $link ){

		if ( VPD_Common::get_options()->hide_reset_link == 'no' ){
			return $link;
		}

		return false;

	}

}

// Format Price function
if ( ! function_exists( 'vpd_format_price' ) ){

	function vpd_format_price( $format, $type, $product  ){

		switch ( $format ) {

		  case "yes":

		  	if( $product->get_variation_regular_price( $type, true ) !== $product->get_variation_sale_price( $type, true ) ){

				$formatted_price =  wc_format_sale_price( wc_price( $product->get_variation_regular_price( $type, true ) ), wc_price( $product->get_variation_sale_price( $type, true ) ) );
			}
			else{

				$formatted_price = wc_price( $product->get_variation_price( $type ) );

			}

			$price = apply_filters( 'vpd_formatted_price', $formatted_price, $type, $product );

			break;

		  default:

		  	$formatted_price = wc_price( $product->get_variation_price( $type ) );

			$price = apply_filters( 'vpd_non_formatted_price', $formatted_price, $type, $product );

		}

		return apply_filters('vpd_format_price_fiter', $price, $type, $product);
	}
	
}