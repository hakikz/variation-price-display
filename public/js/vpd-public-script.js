( function ( $ ) {

	var singleVariation, priceContainer, initPrice, prevPrice, vpdPublicObject;

	singleVariation = $('.single-product .product-type-variable .single_variation_wrap .single_variation');
	priceContainer = $('.single-product .product-type-variable .price')
					.not('.single-product .product-type-variable .related .price, .single-product .product-type-variable .upsells .price')
					.not('.single-product .tc-price-wrap .price') //Extran Product Addons Support
					.not('.variations .price') //Variation Swatches Support;
					.not('.df-product-inner-wrap .df-product-price') //Divi Flash Support;

	initPrice = prevPrice = priceContainer.html();

	// Receiving object
	vpdPublicObject = vpd_public_object;

	// Default price hiding function
	function hideDefaultPrice(){
		// Default Price hiding condition
		switch( vpdPublicObject.hideDefaultPrice ){
			case 'no':
				$('.product-type-variable .single_variation_wrap .woocommerce-variation-price').removeClass('hide_default_price');
				break;
			default:
				$('.product-type-variable .single_variation_wrap .woocommerce-variation-price').addClass('hide_default_price');	
		}
	}

	// Function to run- on changing the variation dropdown
	function changePrice(variationPrice){

		if( vpdPublicObject.changeVariationPrice === "no" ) return;

		if (prevPrice === variationPrice) return;

		priceContainer.fadeOut(200, function () {
			priceContainer.html(variationPrice).fadeIn(200);
			prevPrice = variationPrice;
		});
	}

	// Triggering the `show_variation` event
	$(".single_variation_wrap").on("show_variation", function (event, variation) {
		// Getting variation price
		var variationPrice = $(variation.price_html).html();
		// Passing variation price to the function
		changePrice(variationPrice);
		hideDefaultPrice();

	});

	// Triggering `hide_variation` event
	$(".single_variation_wrap").on("hide_variation", function(event) {
		changePrice(initPrice);
	});


} )( jQuery );