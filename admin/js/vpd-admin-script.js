( function ( $ ) {

    var $minTR, $maxTR, $customTR, $salePriceTR, $vpdAdminObject;

    // Getting VPD Admin Object
    $vpdAdminObject = vpd_admin_object;

    // Getting <tr> using fields
    $minTR = $( '#vpd_from_before_min_price' ).closest('tr');
    $maxTR = $( '#vpd_up_to_before_max_price' ).closest('tr');
    $customTR = $( '#vpd_custom_price_text' ).closest('tr');
    $salePriceTR = $( '#vpd_format_sale_price' ).closest('tr');

    // Callback function to display <tr> based on data
    minMaxConditions( $vpdAdminObject.priceType );
    

    // Expressions indented
    $( '.vpd-select-wrap #vpd_price_types' ).on('change', function(){
        
        // Getting value on change dropdown data
        var $priceType = $(this).val();

        // Callback function to display <tr> based on data
        minMaxConditions( $priceType )

    });


    // Condition Function for Minimum Maximum <tr>
    function minMaxConditions($priceType){

        // Initially hiding both <tr> for minimum and maximum checkbox
        switch( $priceType ) {

            case 'min':
                $minTR.show();
                $maxTR.hide();
                $customTR.hide();
                $salePriceTR.show();
                break;

            case 'max':
                $minTR.hide();
                $customTR.hide();
                $maxTR.show();
                $salePriceTR.show();
                break;

            case 'custom':
                $minTR.hide();
                $customTR.show();
                $maxTR.hide();
                $salePriceTR.show();
                break;

            case 'list_variations':
                $minTR.hide();
                $customTR.hide();
                $maxTR.hide();
                $salePriceTR.show();
                break;

            default:
                $minTR.hide();
                $maxTR.hide();
                $customTR.hide();
                $salePriceTR.hide();

        }

    }

 
} )( jQuery );