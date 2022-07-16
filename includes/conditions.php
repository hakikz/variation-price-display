<?php 

if ( !function_exists( 'vpd_get_price_html' ) ){

	// add_filter('woocommerce_get_price_html', 'vpd_get_price_html');

	add_filter('woocommerce_variable_price_html', 'vpd_get_price_html', 10, 2);

	function vpd_get_price_html( $price, $product ){

		// $product = wc_get_product( get_the_ID() );

		// $prices = $product->get_variation_prices( true );
		
		if($product->is_type( 'variable' )):
		
			$price_display_option = VPD_Common::get_options()->price_display_option;
			$display_from_before_min_price = VPD_Common::get_options()->display_from_before_min_price;
			$display_up_to_before_max_price = VPD_Common::get_options()->display_up_to_before_max_price;
			$format_sale_price = VPD_Common::get_options()->format_sale_price;

			switch ($price_display_option) {

			  case "min":

			  	$before_min_price = ( $display_from_before_min_price === 'yes' ) ? __('From ', 'variation-price-display') : '';
			  	$after_min_price =  '';

			  	// $min_price = wc_price( $product->get_variation_price( 'min' ) );
			  	$min_price = vpd_format_price( $format_sale_price, 'min', $product );

				$prices = apply_filters( 'vpd_prefix_min_price', $before_min_price ) . $min_price . apply_filters( 'vpd_suffix_min_price', $after_min_price );
				
			    break;

			  case "max":

			  	$before_max_price = ( $display_up_to_before_max_price === 'yes' ) ? __('Up To ', 'variation-price-display') : '';
			  	$after_max_price = '';

			  	$max_price = vpd_format_price( $format_sale_price, 'max', $product );

				$prices = apply_filters( 'vpd_prefix_max_price', $before_max_price ) . $max_price . apply_filters( 'vpd_suffix_max_price', $after_max_price );

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

			$vpd_price = apply_filters( 'vpd_woocommerce_variable_price_html', $prices . $product->get_price_suffix(), $product, $price );

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

		  	if( $product->get_variation_regular_price( $type ) !== $product->get_variation_sale_price( $type ) ){

				$formatted_price =  wc_format_sale_price( wc_price( $product->get_variation_regular_price( $type ) ), wc_price( $product->get_variation_sale_price( $type ) ) );
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