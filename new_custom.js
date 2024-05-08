jQuery(document).ready(function($) {
    $('.add-to-cart-ajax').on('click', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var formData = $form.serialize();

        $.ajax({
            type: 'POST',
            url:ajax_url,
            data: formData + '&action=add_to_cart_ajax',
            success: function(response) {
                console.log('AJAX Success:', response.status);
                console.log('Product ID:', response.product_id);
                console.log('Custom Price:', response.custom_price);
                console.log('Custom Category:', response.custom_category); 

                var productId = response.product_id;
                var customPrice = response.custom_price;
                var customCategory = response.custom_category; 

                otherScriptFunction(productId, customPrice, customCategory); 
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log('AJAX Error:', textStatus, errorThrown);
                console.log(xhr.responseText);
            }
        });
    });
});