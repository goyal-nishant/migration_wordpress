    <?php

    /**
     * Cart Page
     *
     * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
     *
     * HOWEVER, on occasion WooCommerce will need to update template files and you
     * (the theme developer) will need to copy the new files to your theme to
     * maintain compatibility. We try to do this as little as possible, but it does
     * happen. When this occurs the version of the template file will be bumped and
     * the readme will list any important changes.
     *
     * @see     https://docs.woocommerce.com/document/template-structure/
     * @package WooCommerce\Templates
     * @version 7.9.0
     */

    defined('ABSPATH') || exit;
    wp_head();
    ?>

    <section class="woo-cart-container">
        <?php do_action('woocommerce_before_cart'); ?>
        <div class="woo-form-container">
            <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                <?php do_action('woocommerce_before_cart_table'); ?>

                <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span></th>
                            <th class="product-name"><?php esc_html_e('Class', 'woocommerce'); ?></th>
                            <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                            <th class="product-quantity"><?php esc_html_e('Qty', 'woocommerce'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php do_action('woocommerce_before_cart_contents'); ?>

                        <?php
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        ?>
                                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                                    <td class="product-remove">
                                        <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                esc_html__('Remove this item', 'woocommerce'),
                                                esc_attr($product_id),
                                                esc_attr($_product->get_sku())
                                            ),
                                            $cart_item_key
                                        );
                                        ?>
                                    </td>

                                    <td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                        <div class="woo-product-cart-container">
                                            <div class="woo-product_thumbnail">
                                                <?php
                                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                                if (!$product_permalink) {
                                                    echo $thumbnail; // PHPCS: XSS ok.
                                                } else {
                                                    printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                                                }
                                                ?>
                                            </div>
                                            <div class="product-name">
                                                <?php
                                                if (!$product_permalink) {
                                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                                } else {
                                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                                }

                                            //this is used to display custom category in cart page which take value of custom price by display_custom_cart_items() this function
                                               // echo isset($cart_item['custom_category']) ? '<br>' . esc_html($cart_item['custom_category']) : '';


                                                do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                                // Meta data.
                                                echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                                // Backorder notification.
                                                if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                    echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                        <?php
                                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            
                                            //this is used to display custom price in cart page which take value of custom price by display_custom_cart_items() this function
                                            //echo isset($cart_item['custom_price']) ? wc_price($cart_item['custom_price']) : wc_price($_product->get_price());

                                        ?>
                                    </td>

                                    <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                        <?php woocommerce_quantity_input(); ?>

                                    </td>




                                </tr>
                        <?php
                            }
                        }
                        ?>

                      <?php do_action('woocommerce_cart_contents'); ?>

                        <tr>
                            <td colspan="6" class="actions">

                                <?php if (wc_coupons_enabled()) { ?>
                                    <div class="coupon">
                                        <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" /> <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="cust-update-btn button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>


                                <?php //do_action('woocommerce_cart_actions'); 
                                ?>

                                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                            </td>
                        </tr>

                        <?php do_action('woocommerce_after_cart_contents'); ?>
                    </tbody>
                </table>
                <?php do_action('woocommerce_after_cart_table'); ?>
            </form>
        </div>

        <?php do_action('woocommerce_before_cart_collaterals'); ?>
        <div class="cart-collaterals">
            <?php
            /**
             * Cart collaterals hook.
             *
             * @hooked woocommerce_cross_sell_display
             * @hooked woocommerce_cart_totals - 10
             */
            do_action('woocommerce_cart_collaterals');
            ?>
        </div>


        <div class="container bundles">
            <div class="bundle-outer">
                <?php
                $heading = get_field('heading');
                $product_name = get_field('product_name');
                $product_image = get_field('product_image');
                $deal_content = get_field('deal_content');
                $product_link = get_field('product_link');
                $deal_date = get_field('deal_date');
                ?>
                <h2><?php echo $heading; ?></h2>
                <div class="bundle-inner">
                    <div class="bundle-img">
                        <div class="plus-icon">
                            <svg class="svg-inline--fa fa-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M240 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H176V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H384c17.7 0 32-14.3 32-32s-14.3-32-32-32H240V80z"></path>
                            </svg>
                        </div>
                        <div class="product-image">
                            <a href="<?php echo $product_link['url'] ?>"><img src="<?php echo $product_image['url'] ?>" alt="<?php echo $product_image['title'] ?>"></a>
                        </div>
                    </div>
                    <div class="product-name">
                        <h4><?php echo $product_name;
                            echo $deal_content; ?></h4>
                    </div>
                    <div class="counter">
                        <?php
                        // Step 1: Retrieve ACF Field Value
                        $countdown_value = get_field('deal_date');

                        // Step 2: Convert Date Format
                        $countdown_date = DateTime::createFromFormat('d/m/Y g:i a', $countdown_value);

                        // Step 3: Calculate Countdown
                        $current_date = new DateTime();
                        $difference = $current_date->diff($countdown_date);

                        // Step 4: Display Countdown
                        echo 'Hours: ' . $difference->format('%h') . '<br>';
                        echo 'Seconds: ' . $difference->format('%s') . '<br>';


                        ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="product-info"></div>
<?php do_action('woocommerce_after_cart'); ?>

<?php
// function display_custom_cart_items() {
//     $category_groups = array();

//     foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
//         if (isset($cart_item['custom_category'])) {
//             $category = $cart_item['custom_category'];
//         } else {
//             $category = 'Uncategorized'; 
//         }

//         if (!isset($category_groups[$category])) {
//             $category_groups[$category] = array();
//         }

//         $category_groups[$category][] = $cart_item;
//     }

//     ob_start();
    
//     foreach ($category_groups as $category => $cart_items) {
        ?>
         <p> <?php //echo esc_html($category); ?></strong></p>
         <ul>
             <?php
//             foreach ($cart_items as $cart_item_key => $cart_item) {
//                 $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

//                 if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
//                     ?>
                     <li>
                        <?php // echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key); ?>
                       <?php
//                         if (isset($cart_item['custom_price']) && $cart_item['custom_price'] > 0) {
//                             echo '<br><strong>Custom Price:</strong> ' . wc_price($cart_item['custom_price']);
//                         } else {
//                             // If no custom price is set or it's zero, display the regular price
//                             echo '<br><strong>Regular Price:</strong> ' . wc_price($_product->get_price());
//                         }
//                         ?>
                   </li>
                    <?php
//                 }
//             }
//             ?>
        </ul>
        <?php
//     }

//     $output = ob_get_clean();

//     return $output;
// }

//echo display_custom_cart_items();

?>
