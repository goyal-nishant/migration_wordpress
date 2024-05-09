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
    <?php $inner_banner_bg = get_field('inner_banner_bg'); ?>
    <section class="inner_banner">
        <div class="container">
            <div class="inner_banner-container">
                <div class="inner_banner-info" style="background-image: url('<?php echo $inner_banner_bg; ?>');">
                    <?php
                    // Get the ID of the current post or page
                    $post_id = get_the_ID();

                    // Get the featured image URL
                    $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');

                    // Display the featured image
                    if ($featured_image_url) {
                        echo '<span><img src="' . esc_url($featured_image_url) . '" alt="' . esc_attr(get_the_title()) . '"></span>';
                    }
                    ?>

                </div>
                <div class="inner_banner-content">
                    <h1><?php echo get_the_title(); ?></h1>
                    <?php the_sub_field('inner_banner_discription'); ?>
                    <?php
                    // Get the content of the current post or page
                    $page_content = get_the_content();

                    // Limit the content to 50 words
                    $trimmed_content = wp_trim_words($page_content, 50, '...');

                    // Display the trimmed content
                    echo  wpautop($trimmed_content);

                    ?>

                    <?php $link = get_sub_field('button_text'); ?>
                    <?php if ($link) :
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                    ?>
                        <a class="btn-grey cm-button" href="<?php echo esc_url($link_url); ?>"><?php echo esc_html($link_title); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- End Inner_banner Section -->



    <!-- Start Overview Section -->
    <div class="overview-section">

        <div class="overview-section-top">
            <div class="container">
                <div class="overview-inner">
                    <div class="product-image">
                        <?php
                        // Output featured image
                        // if (has_post_thumbnail()) {
                        //     $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                        //     echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title()) . '">';
                        // }
                        the_custom_logo();
                        ?>
                    </div>
                    <div class="overview-internal">
                        <ul class="listing">
                            <?php
                            $overview_top_heading = get_field('overview_top_heading');
                            $skills_top_heading = get_field('skills_top_heading');
                            $carrers_top_heading = get_field('carrers_top_heading');
                            ?>
                            <li>
                                <a href="#overview"><?php echo $overview_top_heading; ?></a>
                            </li>
                            <li>
                                <a href="#skills"><?php echo $skills_top_heading; ?></a>
                            </li>
                            <li>
                                <a href="#carrers"><?php echo $carrers_top_heading; ?></a>
                            </li>
                            <li class="shop-link">
                                <?php

                                // Inside single-product.php
                                // global $product;
                                // echo '<a href="#" id="custom-add-to-cart-btn" data-product-id="' . $product->get_id() . '">Add to Cart</a>';
                                ?>
                                <!-- <a href="" id="custom-add-to-cart-btn"  data-product-id="<?php //echo $post_id 
                                                                                                ?>">Register Now</a> -->
                                <?php
                                $product_type = wc_get_product()->get_type();

                                if ($product_type === 'simple') {
                                    // echo $post_id;
                                ?>
                                    <a href="<?php echo esc_url(home_url('/')); ?>/home/?add-to-cart=<?php echo $post_id; ?>" class="add-to-cart-btn">Register Now</a>

                                <?php
                                }
                                ?>

                                <?php
                                $product_type = wc_get_product()->get_type();

                                if ($product_type === 'variable') {
                                ?>
                                    <a href="#buy-now" id="" data-product-id="<?php echo $post_id ?>">Register Now</a>
                                <?php
                                }
                                ?>
                                <script>
                                    jQuery('#custom-add-to-cart-btn').on('click', function(e) {
                                        e.preventDefault();

                                        // Get the product ID from data attribute or any other method
                                        var product_id = jQuery(this).data('product-id');

                                        // Make an AJAX request to add the product to the cart
                                        jQuery.ajax({
                                            type: 'POST',
                                            url: wc_add_to_cart_params.ajax_url,
                                            data: {
                                                action: 'add_to_cart',
                                                product_id: product_id,
                                            },
                                            success: function(response) {
                                                // Redirect to the cart page after successful addition
                                                window.location.href = wc_add_to_cart_params.cart_url;
                                            },
                                        });
                                    });
                                </script>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <section class="overview-content" id="overview">
            <div class="container">
                <?php
                $overview_heading = get_field('overview_heading');
                $overview_subheading = get_field('overview_subheading');
                $overview_details = get_field('overview_details');
                $left_right_content = get_field('left_right_content');
                ?>
                <h4><?php echo $overview_heading; ?></h4>
                <h3><?php echo $overview_subheading; ?></h3>

                <?php if (have_rows('overview_details')) : ?>
                    <ul class="overview-details">
                        <?php while (have_rows('overview_details')) : the_row();
                            $overview_details_heading = get_sub_field('overview_details_heading');
                            $overview_content = get_sub_field('overview_content');
                        ?>
                            <li>
                                <h5><?php echo $overview_details_heading; ?></h5>
                                <?php echo $overview_content; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>

                <?php if (have_rows('left_right_content')) : ?>
                    <div class="left-right-content">
                        <?php while (have_rows('left_right_content')) : the_row();
                            $image = get_sub_field('image');
                            $content = get_sub_field('content');
                        ?>

                            <div class="left-right-internal">
                                <div class="image">
                                    <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['title'] ?>">
                                </div>
                                <div class="content-inner">
                                    <div class="img-head">
                                        <div class="product-image">
                                            <?php
                                            // Output featured image
                                            if (has_post_thumbnail()) {
                                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                                echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title()) . '">';
                                            }
                                            ?>
                                        </div>
                                        <div class="heading">
                                            <h2><?php echo get_the_title(); ?></h2>
                                        </div>
                                    </div>
                                    <div class="content-internal">
                                        <?php echo $content; ?>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Start Bundle Section -->
        <section class="bundle-classes">
            <div class="container">
                <?php echo do_shortcode('[easy_product_bundle]'); ?>
            </div>
        </section>
        <!-- End Bundle Section -->

        <!-- Start Client Section -->
        <section class="client">
            <div class="container">
                <div class="client-container splide" id="splide">
                    <div class="client-content splide__track">
                        <div class="client-list splide__list">
                            <?php
                            if (have_rows('client_item')) :
                                while (have_rows('client_item')) : the_row();
                            ?>
                                    <div class="client-item splide__slide">
                                        <?php $image = get_sub_field('client_image');
                                        if (!empty($image)) : ?>
                                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                                        <?php endif; ?>
                                    </div>
                            <?php
                                endwhile;
                            endif;
                            ?>
                        </div>
                    </div>
                    <p><?php the_field('client_heading'); ?></p>
                </div>
            </div>
        </section>
        <!-- End Client Section -->

        <section class="skills-content" id="skills">
            <div class="container">
                <?php
                $skills_content = get_field('skills_content');
                $skills_heading = get_field('skills_heading');
                ?>
                <?php echo $skills_content; ?>
                <h2><?php echo $skills_heading; ?></h2>
                <?php if (have_rows('skills_details')) : ?>
                    <ul class="skill-details">
                        <?php while (have_rows('skills_details')) : the_row();
                            $skills_image = get_sub_field('skills_image');
                            $skills_sub_heading = get_sub_field('skills_sub_heading');
                            $skills_sub_content = get_sub_field('skills_sub_content');
                        ?>
                            <li>
                                <div class="skills-heading-inner">
                                    <img src="<?php echo $skills_image['url'] ?>" alt="<?php echo $skills_image['title'] ?>">
                                    <h4><?php echo $skills_sub_heading; ?></h4>
                                </div>
                                <?php echo $skills_sub_content;  ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

        <section class="carrers-content" id="carrers">
            <div class="container">
                <?php $careers_heading = get_field('careers_heading'); ?>
                <h2><?php echo $careers_heading; ?></h2>
                <?php if (have_rows('carrer_options_list')) : ?>
                    <ul class="carrer-options">
                        <?php while (have_rows('carrer_options_list')) : the_row();
                            $carrer_option = get_sub_field('carrer_option');
                        ?>
                            <li>
                                <h5> <?php echo $carrer_option; ?></h5>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

    </div>
    <!-- End Overview Section -->

    <!-- Start Newsletter Section -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-wrap">
                <div class="heading">
                    <h2><?php the_field('newsletter_heading'); ?></h2>
                </div>
                <div class="newsletter-custom">
                    <?php the_field('newsletter_shortcode'); ?>
                </div>
            </div>
        </div>
    </section>
    <!-- End Newsletter Section -->

</div>
<?php
$product_type = wc_get_product()->get_type();

if ($product_type === 'variable') {
    $product = wc_get_product();
    $variations = $product->get_available_variations();

    if ($variations) {
?>
        <div class="variations bundle-classes" id="buy-now">
            <div class="container">
                <?php
                echo '<h2><label for="variation-select">' . get_the_title() . ' options:</label></h2>';
                ?>
                <div class="buy-options-cert-container">
                    <!-- <select id="variation-select">

                        <?php
                        // foreach ($variations as $variation) {
                        //     $variation_id = $variation['variation_id'];
                        //     $variation_attributes = wc_get_product_variation_attributes($variation_id);
                        //     $variation_label = implode(', ', $variation_attributes);
                        //     echo '<option value="' . esc_attr($variation_id) . '">' . esc_html($variation_label) . '</option>';
                        // }
                        ?>
                    </select> -->
                </div>
                <div class="variation-outer bundle-inside owl-carousel owl-theme owl-loaded owl-drag">
                    <?php
                    // Display information related to each variation
                    foreach ($variations as $variation) {
                        $variation_id = $variation['variation_id'];
                        $variation_attributes = wc_get_product_variation_attributes($variation_id);
                        $variation_label = implode(', ', $variation_attributes);
                        $variation_content = wc_get_formatted_variation($variation);
                        $variation_display_price = $variation['display_price'];
                        $display_regular_price = $variation['display_regular_price'];
                        $variation_description = $variation['variation_description'];
                        $is_variation_in_stock = $variation['is_in_stock'];
                        // echo "<pre>";
                        // print_r($variation_content);
                        // echo "</pre>";

                        // $display_style = $variation_id === $variations[0]['variation_id'] ? 'display: block;' : 'display: none;';
                    ?>
                        <div class="variation-content product-details bundle-inner" id="variation-info-<?php echo esc_attr($variation_id) ?>">
                            <!-- <div class="container"> -->
                                <p class="variation-name"><?php echo esc_html($variation_label); ?></p>
                                <div class="price-section">
                                    <h1>Price: <?php echo $variation_display_price; ?></h1>
                                    <?php
                                    if ($display_regular_price) {
                                    ?>
                                        <div class="regular-price">
                                            <p class="limited-offer">limited-time offer B2G1</p>
                                            <p class="org-price"><s>Original Price: <?php echo $display_regular_price; ?></s></p>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <p class="pay-plan">Payment Plan Available</p>

                                </div>
                                <?php if ($variation_description) { ?>
                                    <div class="includes">
                                        <h5>Includes</h5>
                                        <?php echo $variation_description; ?>
                                    </div>
                                <?php } ?>

                                <?php if ($is_variation_in_stock) { ?>
                                    <a href="<?php echo home_url(); ?>/home/?add-to-cart=<?php echo esc_attr($variation_id); ?>" class="buy-now-button" data-variation-id="<?php echo esc_attr($variation_id); ?>">Buy Now</a>
                                <?php } else { ?>
                                    <p class="out-of-stock-message">Out of Stock</p>
                                <?php } ?>

                            <!-- </div> -->
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

<?php
    }
}
?>



<div class="bundle-inner-org">
    <div class="container">
        <div class="bundle-inside-test ">
            <?php
            if ($product_type === 'easy_product_bundle') {
                // echo "inside bundles";
            ?>
                <div class="product-details-bundle">
                    <?php
                    echo the_title();
                    //    echo the_content();
                    echo '<div id="asnp_easy_product_bundle" class="asnp_easy_product_bundle"></div>';
                    ?>
                    <a href="<?php echo home_url(); ?>/home/?add-to-cart=<?php echo esc_attr($variation_id); ?>" class="test"></a>

                </div>
            <?php
            }
            // }
            ?>
        </div>
    </div>

</div>

<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */