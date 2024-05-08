<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woo.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop'); ?>
<div class="single-product-outer">
    <div class="inner_banner single-bg">
        <div class="container">
            <?php
            /**
             * woocommerce_before_main_content hook.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action('woocommerce_before_main_content');
            ?>
            <div class="flex-single">
                <div class="title-single">
                    <h2><?php echo the_title(); ?></h2>
                    <a href="#">Learn more about it</a>
                </div>
                <div class="includes-single">
                    <?php
                    $product_id = get_the_ID();
                    $includes_heading = get_field('includes_heading', $product_id);
                    ?>
                    <h5><?php echo  $includes_heading; ?></h5>
                    <?php

                    if (have_rows('includes_repeater', $product_id)) { ?>
                        <ul class="includes-repeater">
                            <?php while (have_rows('includes_repeater', $product_id)) {
                                the_row();
                                $includes_item = get_sub_field('includes_item');
                            ?>
                                <li>
                                    <?php echo $includes_item; ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php
                    }
                    ?>

                </div>
            </div>
            <div class="product-info">
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>

                    <?php wc_get_template_part('content', 'single-product'); ?>

                <?php endwhile; // end of the loop. 
                ?>
            </div>
        </div>
    </div>
</div>
<div class="custom-structure">
    <?php
    if (have_rows('custom_price_repeater')) {
        while (have_rows('custom_price_repeater')) {
            the_row();
            $custom_category = get_sub_field('custom_category');
            $custom_price = get_sub_field('custom_price');
            $product_id = $product->get_id();
    ?>
            <div class="cat-outer-single">
                <h2><?php echo $custom_price; ?></h2>
                <h3><?php echo $custom_category; ?></h3>
                <form class="cart" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post" enctype="multipart/form-data">
                    <button class="add-to-cart-ajax" type="submit" class="button alt" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>">
                        <?php echo esc_html__('Add to cart', 'woocommerce'); ?>
                    </button>
                    <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>">
                    <input type="hidden" name="custom_price" value="<?php echo esc_attr($custom_price); ?>">
                    <input type="hidden" name="custom_category" value="<?php echo esc_attr($custom_category); ?>"> <!-- Add this line -->
                    <?php wp_nonce_field('add_to_cart', 'woocommerce-add-to-cart-nonce'); ?>
                </form>
            </div>
    <?php
        }
    }
    ?>
</div>
<script src="<?php echo get_template_directory_uri(); ?>/woocommerce/cart/custom_script.js"></script>
<script>
    jQuery(document).ready(function($) {
    $('.add-to-cart-ajax').on('click', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var formData = $form.serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
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

</script>

<?php
get_footer('shop');
