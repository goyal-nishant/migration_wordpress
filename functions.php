<?php

use PhpParser\Node\Stmt\Echo_;

use function WooCommerce\PayPalCommerce\OrderTracking\tr;

/**
 * transformationtechtraining functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *  
 * @package transformationtechtraining
 */

 

 //Save custom data into order_item table while place order on checkout page
 add_action('woocommerce_checkout_create_order_line_item', 'save_custom_category_to_order', 10, 4);

 function save_custom_category_to_order($item, $cart_item_key, $values, $order) {
     // Check if custom category is set in the checkout page data    
     $custom_category = $values['custom_category'];
     $item->add_meta_data('Custom Category', $custom_category, true);
 }


 // Override wc_display_item_meta function using a filter hook
function custom_wc_display_item_meta( $html, $item, $args ) {
    // Check if the meta data we want to display exists
    $custom_category = $item->get_meta('Custom Category');

    // If custom category exists, display it instead of the default meta data
    if ( ! empty( $custom_category ) ) {
         $html = '<ul class="custom-wc-item-meta"><li>' . 
                wp_kses_post( $custom_category ) . '</li></ul>';
    }
    return $html;
}


// Hook into 'woocommerce_display_item_meta' filter
add_filter( 'woocommerce_display_item_meta', 'custom_wc_display_item_meta', 10, 3 );
 // Display custom meta key on backend orders
 add_filter('woocommerce_order_item_display_meta_key', 'filter_order_item_display_meta_key', 10, 4);

function filter_order_item_display_meta_key($display_key, $meta, $item) {
    // Check if the meta key is 'Custom Category'
    if ($meta->key === 'Custom Category') {
        // Return null to prevent displaying the meta key and colon separator
        return " ";
    }
    return $display_key;
}

 // Add custom column header
function custom_column_header( $columns ) {
    $columns['custom_column'] = ' Price variations';
    return $columns;
}
add_filter( 'manage_product_posts_columns', 'custom_column_header', 10 );

// Populate custom column
function custom_column_content( $column, $post_id ) {
    if ( $column == 'custom_column' ) {
        // Retrieve custom field data
        $repeater_fields = get_field( 'custom_price_repeater', $post_id );

        if ( $repeater_fields ) {
            foreach ( $repeater_fields as $index => $repeater_field ) {
                $category = $repeater_field['custom_category'];
                $price = $repeater_field['custom_price'];
                echo $category . "  $" . $price . "<br>";
            }
        }
    }
}
add_action( 'manage_product_posts_custom_column', 'custom_column_content', 10, 2 );

// Add custom category name below product name in cart
add_filter('woocommerce_cart_item_name', 'custom_cart_item_name_display', 10, 3);
function custom_cart_item_name_display($product_name, $cart_item, $cart_item_key) {
    // Check if custom category is set
    if (isset($cart_item['custom_category'])) {
        $custom_category = $cart_item['custom_category'];
        // Display custom category name below product name
        $product_name .= '<br><small>' . $custom_category . '</small>';
    }
    return $product_name;
}

 //replace the default price by our custom price
 function replace_default_price_with_custom($product_price, $cart_item, $cart_item_key ) {

    $custom_price = isset($cart_item['custom_price']) ? (float)$cart_item['custom_price'] : null;
    
    if (!empty($custom_price)) {
        $product_price = wc_price($custom_price);         
    }
    return  $product_price;
}
add_filter('woocommerce_cart_item_price', 'replace_default_price_with_custom', 10, 3);

 //order summary in cart page
 add_filter( 'woocommerce_cart_subtotal', 'custom_cart_subtotal', 10, 3 );
 add_filter( 'woocommerce_cart_totals_order_total_html', 'custom_order_total', 10, 1 );
 
 
 function custom_cart_subtotal( $subtotal, $compound, $cart ) {
     $category_groups = array();
     $total = 0;
 
     // Group cart items by category
     foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
         if ( isset( $cart_item['custom_category'] ) ) {
             $category = $cart_item['custom_category'];
         } else {
             $category = 'Uncategorized'; // Default category if custom category is not set
         }
 
         if ( ! isset( $category_groups[ $category ] ) ) {
             $category_groups[ $category ] = array();
         }
 
         $category_groups[ $category ][] = $cart_item;
     }
 
     // Calculate total based on custom prices
     foreach ( $category_groups as $category => $cart_items ) {
         foreach ( $cart_items as $cart_item_key => $cart_item ) {
             $custom_price = isset( $cart_item['custom_price'] ) ? $cart_item['custom_price'] : $cart_item['data']->get_price();
             $total += $custom_price * $cart_item['quantity'];
         }
     }
 
     // Format and return custom subtotal
     return wc_price( $total );
 }
 
 function custom_order_total( $order_total_html ) {
     // Get the cart instance
     $cart = WC()->cart;
 
     // Calculate the total based on custom prices
     $total = 0;
     foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
         $custom_price = isset( $cart_item['custom_price'] ) ? $cart_item['custom_price'] : $cart_item['data']->get_price();
         $total += $custom_price * $cart_item['quantity'];
     }
 
     // Format and return custom order total HTML
     return sprintf( '<strong>%s</strong> %s', __( 'Total:', 'woocommerce' ), wc_price( $total ) );
 }
 
// Display category below product name
add_filter('woocommerce_checkout_cart_item_quantity', 'display_category_on_checkout', 10, 3);

function display_category_on_checkout($product_quantity, $cart_item, $cart_item_key) {
    if (isset($cart_item['custom_category'])) {
        $category = $cart_item['custom_category'];
    // return $product_quantity ."-" . esc_html($category);
    }
    return $product_quantity;
}



// Change default price to custom price in subtotal
add_filter('woocommerce_checkout_cart_item_subtotal', 'change_to_custom_price_in_subtotal', 10, 3);

function change_to_custom_price_in_subtotal($product_subtotal, $cart_item, $cart_item_key) {
    if (isset($cart_item['custom_price']) && $cart_item['custom_price'] > 0) {
        return wc_price($cart_item['custom_price'] * $cart_item['quantity']);
    }
    return $product_subtotal;
}


add_action('woocommerce_before_calculate_totals', 'update_cart_item_price', 10, 1);

function update_cart_item_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        // Check if custom price is set for the cart item
        if (isset($cart_item['custom_price'])) {
            $cart_item['data']->set_price($cart_item['custom_price']);
        }
    }
}


//use ajax for add to cart button
function add_to_cart_ajax_callback() {
    error_log('AJAX Request Received');

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $custom_price = isset($_POST['custom_price']) ? floatval($_POST['custom_price']) : 0;
    $custom_category = isset($_POST['custom_category']) ? sanitize_text_field($_POST['custom_category']) : '';

    if ($product_id > 0) {
        $cart_item_data = array('custom_price' => $custom_price, 'custom_category' => $custom_category);

        WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);

        $response = array(
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'product_id' => $product_id,
            'custom_price' => $custom_price,
            'custom_category' => $custom_category 
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Invalid product ID'
        );
    }

    header('Content-Type: application/json'); 
    echo json_encode($response); 

    wp_die();
}

add_action('wp_ajax_add_to_cart_ajax', 'add_to_cart_ajax_callback');
add_action('wp_ajax_nopriv_add_to_cart_ajax', 'add_to_cart_ajax_callback');



add_action('admin_post_nopriv_custom_register_user', 'custom_register_user');
add_action('admin_post_custom_register_user', 'custom_register_user');

function custom_register_user() {
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_redirect(home_url('/register?registration_failed=true'));
        exit;
    }

    $user = new WP_User($user_id);
    $user->set_role($role);

    wp_redirect(home_url('/register?registration_success=true'));
    exit;
}


 // Redirect users to the dashboard after login
function custom_login_redirect($redirect_to, $request, $user) {
    // Always redirect users to the dashboard after login
    return admin_url();
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function transformationtechtraining_setup()
{
    /*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on transformationtechtraining, use a find and replace
		* to change 'transformationtechtraining' to the name of your theme in all the template files.
		*/
    load_theme_textdomain('transformationtechtraining', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
    add_theme_support('title-tag');

    /*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'transformationtechtraining'),
        )
    );

    /*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Set up the WordPress core custom background feature.
    add_theme_support(
        'custom-background',
        apply_filters(
            'transformationtechtraining_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action('after_setup_theme', 'transformationtechtraining_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function transformationtechtraining_content_width()
{
    $GLOBALS['content_width'] = apply_filters('transformationtechtraining_content_width', 640);
}
add_action('after_setup_theme', 'transformationtechtraining_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function transformationtechtraining_widgets_init()
{
    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'transformationtechtraining'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'transformationtechtraining'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
    register_sidebar(
        array(
            'name'          => esc_html__('header-search', 'transformationtechtraining'),
            'id'            => 'header-search',
            'description'   => esc_html__('Add widgets here.', 'transformationtechtraining'),
        )
    );
    register_sidebar(
        array(
            'name'          => esc_html__('footer-contact', 'transformationtechtraining'),
            'id'            => 'footer-contact',
            'description'   => esc_html__('Add widgets here.', 'transformationtechtraining'),
        )
    );
    register_sidebar(
        array(
            'name'          => esc_html__('footer-classes', 'transformationtechtraining'),
            'id'            => 'footer-classes',
            'description'   => esc_html__('Add widgets here.', 'transformationtechtraining'),
        )
    );
    register_sidebar(
        array(
            'name'          => esc_html__('footer-menus', 'transformationtechtraining'),
            'id'            => 'footer-menus',
            'description'   => esc_html__('Add widgets here.', 'transformationtechtraining'),
        )
    );
}
add_action('widgets_init', 'transformationtechtraining_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function transformationtechtraining_scripts()
{
    wp_enqueue_style('transformationtechtraining-style', get_stylesheet_uri(), array(), _S_VERSION);
    wp_style_add_data('transformationtechtraining-style', 'rtl', 'replace');
    // Link CSS File
    wp_enqueue_style('transformationtechtraining-custom-style', get_template_directory_uri() . '/style/custom.css', ['transformationtechtraining-style'], time(), 'all');
    // Link script File
    wp_enqueue_style('owl-theme-default-style', get_stylesheet_directory_uri() . '/style/owl.theme.default.min.css');
    wp_enqueue_style('owl-carousel-min-style', get_stylesheet_directory_uri() . '/style/owl.carousel.min.css');
    wp_enqueue_script('transformationtechtraining-jquery', get_template_directory_uri() . '/js/jquery.js', array(), 1);
    wp_enqueue_script('owl-carousel-min-js', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), 2);
    wp_enqueue_script('transformationtechtraining-custom-script', get_template_directory_uri() . '/js/custom-script.js', array(), 3);
    wp_enqueue_script('transformationtechtraining-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);
    wp_enqueue_style('stylesheet-name', get_stylesheet_directory_uri() . '/custom_registration_page.css', array(), '1.0.0', 'all');


    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'transformationtechtraining_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}


// remove wp version number from scripts and styles
function remove_css_js_version($src)
{
    if (strpos($src, '?ver='))
        $src = remove_query_arg('ver', $src);
    return $src;
}
add_filter('style_loader_src', 'remove_css_js_version', 9999);
add_filter('script_loader_src', 'remove_css_js_version', 9999);

// Redirect  404 Page to Home page
add_action('template_redirect', 'redirecting_404_to_home');
function redirecting_404_to_home()
{
    if (is_404()) {
        wp_safe_redirect(site_url());
        exit();
    }
};

// Redirect  search Page to Home page

// function redirect_search_to_home()
// {
//     if (is_search()) {
//         wp_redirect(home_url());
//         exit();
//     }
// }
// add_action('template_redirect', 'redirect_search_to_home');

// Added Option Page
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => true
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'   => 'theme-general-settings',
    ));
    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Header Settings',
        'menu_title'    => 'Header',
        'parent_slug'   => 'theme-general-settings',
    ));
    acf_add_options_sub_page(array(
        'page_title'    => 'Bundle Classes Content',
        'menu_title'    => 'Bundle Classes Content',
        'parent_slug'   => 'theme-general-settings',
    ));
}

add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
    add_theme_support('woocommerce');
}

// Start Short Code of GIF
function custom_shortcode_function_gif()
{
?>
    <div class="gif">
        <div class="gif-container">
            <div class="gif-list">
                <div class="gif-item"><img src="<?php echo get_stylesheet_directory_uri() ?>/image/shield.svg" alt="shield"></div>
                <div class="gif-item"><img src="<?php echo get_stylesheet_directory_uri() ?>/image/security.svg" alt="security"></div>
                <div class="gif-item"><img src="<?php echo get_stylesheet_directory_uri() ?>/image/network.svg" alt="network"></div>
                <div class="gif-item"><img src="<?php echo get_stylesheet_directory_uri() ?>/image/aplus.svg" alt="aplus"></div>
            </div>
        </div>
    </div>
<?php
}

add_shortcode('shortcode_gif', 'custom_shortcode_function_gif');

// Register the Testimonial Custom Post Type
function register_testimonial_cpt()
{
    $labels = array(
        'name'               => 'Testimonials',
        'singular_name'      => 'Testimonial',
        'menu_name'          => 'Testimonials',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Testimonial',
        'edit_item'          => 'Edit Testimonial',
        'new_item'           => 'New Testimonial',
        'view_item'          => 'View Testimonial',
        'search_items'       => 'Search Testimonials',
        'not_found'          => 'No testimonials found',
        'not_found_in_trash' => 'No testimonials found in Trash',
        'parent_item_colon'  => 'Parent Testimonial:',
        'menu_icon'          => 'dashicons-format-quote',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'testimonials'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 20,
        'supports'            => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('testimonial', $args);
}
add_action('init', 'register_testimonial_cpt');
// End Banner Content CPT

// Start Short Code of testimonails
function cpt_query_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'posts_per_page' => 10,
        'order' => 'DESC',
        'orderby' => 'date',
        'post_type' => 'testimonial',
    ), $atts);

    $args = array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => $atts['posts_per_page'],
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );

    $query = new WP_Query($args);

    ob_start();
?>
    <div class="testimonial-list testimonial-carousel owl-carousel owl-theme">

        <?php
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
        ?>
                <div class="testimonial-item">
                    <span>
                        <?php echo get_the_post_thumbnail(); ?>
                    </span>
                    <?php echo the_content(); ?>
                    <h3><?php echo the_title(); ?></h3>
                    <h4><?php echo get_field('testimonial-email'); ?></h4>
                </div>
        <?php
            }
        } else {
            // No posts found
        }
        ?>
    </div>
<?php
    wp_reset_postdata(); // Restore global post data

    return ob_get_clean();
}
add_shortcode('cpt_testimonial', 'cpt_query_shortcode');

// code for courses slider 
function display_classes_and_products_shortcode()
{
    ob_start();
?>
    <section class="classes">
        <div class="container">
            <div class="classes-inner">
                <div class="product-categories">
                    <h2>Classes</h2>
                    <ul>
                        <?php
                        $categories = get_terms('product_cat');
                        foreach ($categories as $category) {
                            echo '<li>';
                            echo '<h5><a href="' . get_term_link($category) . '">' . $category->name . '</a></h5>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="products-list">
                    <div class="all-products-list">
                        <ul class="product-carousel owl-carousel owl-theme">
                            <?php
                            $args = array(
                                'post_type'      => 'product',
                                'posts_per_page' => -1,
                                'tax_query'      => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => 'product_type',
                                        'field'    => 'slug',
                                        'terms'    => 'easy_product_bundle',
                                        'operator' => 'NOT IN', // Exclude easy_product_bundle
                                    ),
                                ),
                            );
                            
                            $products = new WP_Query($args);
                            while ($products->have_posts()) : $products->the_post();
                                global $product;
                                $product_id    = $product->get_id();
                                $product_name  = get_the_title($product_id);
                                $product_content = substr(get_the_content($product_id), 0, 100);
                                $product_categories = wc_get_product_category_list($product_id);
                            ?>
                                <li class="slider-inner item">
                                    <?php
                                    // Get the featured image URL
                                    $image_url = get_the_post_thumbnail_url($product_id, 'full');

                                    // Output the featured image
                                    if ($image_url) {
                                        echo '<span><img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '"></span>';
                                    }
                                    ?>
                                    <h3><?php echo $product_name; ?></h3>
                                    <p><?php echo $product_content; ?></p>
                                    <div class="btn-outer">
                                        <?php echo do_shortcode('[add_to_cart_button]'); ?>
                                        <h5><a href="<?php echo get_permalink(); ?>">Learn more</a></h5>
                                    </div>
                                </li>
                            <?php
                            endwhile;
                            wp_reset_query();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    return ob_get_clean();
}
add_shortcode('display_classes_and_products', 'display_classes_and_products_shortcode');

// on click of button add product to cart and move page to cart 
function add_to_cart_button_shortcode()
{
    global $product;

    ob_start();
    echo '<form action="' . esc_url(wc_get_cart_url()) . '" method="post" class="cart">';
    echo '<input type="hidden" name="add-to-cart" value="' . esc_attr($product->get_id()) . '" />';
    echo '<button type="submit" class="button">Buy</button>';
    echo '</form>';
    return ob_get_clean();
}
add_shortcode('add_to_cart_button', 'add_to_cart_button_shortcode');

add_filter('woocommerce_debug', '__return_true');



/*---ADD plus minus sign in single product input field*/
add_action('woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus');

function bbloomer_display_quantity_plus()
{
    echo '<button type="button" class="plus"><i class="fa fa-plus" aria-hidden="true"></i></button>';
}
add_action('woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus');
function bbloomer_display_quantity_minus()
{
    echo '<button type="button" class="minus"><i class="fa fa-minus" aria-hidden="true"></i></button>';
}
// -------------

add_action('wp_footer', 'bbloomer_add_cart_quantity_plus_minus');
function bbloomer_add_cart_quantity_plus_minus()
{
    // if (!is_product() && !is_cart()) return;
    wc_enqueue_js("
      $(document).on( 'click', 'button.plus, button.minus', function() {
         var qty = $( this ).parent( '.quantity' ).find( '.qty' );
         var val = parseFloat(qty.val());
         var max = parseFloat(qty.attr( 'max' ));
         var min = parseFloat(qty.attr( 'min' ));
         var step = parseFloat(qty.attr( 'step' ));
         if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
               qty.val( max ).change();
            } else {
               qty.val( val + step ).change();
            }
         } else {
            if ( min && ( min >= val ) ) {
               qty.val( min ).change();
            } else if ( val > 1 ) {
               qty.val( val - step ).change();
            }
         }
      });
   ");
}

$args = array(
    'api_key' => 'your_default_api_key',
);

apply_filters('acf/fields/google_map/api', $args);




// Add the following code in your theme's functions.php file or a custom plugin file
// add_action('wp_ajax_add_to_cart', 'add_to_cart_ajax_handler');
// add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_ajax_handler');

// function add_to_cart_ajax_handler()
// {
//     if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
//         $product_id = absint($_POST['add-to-cart']);

//         // Add the product to the cart
//         WC()->cart->add_to_cart($product_id);

//         // Return a response if needed
//         echo json_encode(array('success' => true));

//         // Always exit to prevent further execution
//         exit;
//     }
// }


// ajax for shop now
// Inside functions.php
function add_to_cart_ajax_handler()
{
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id > 0) {
        // Add the product to the cart
        WC()->cart->add_to_cart($product_id);

        // Return a success response
        echo json_encode(array('status' => 'success'));
    } else {
        // Return an error response
        echo json_encode(array('status' => 'error', 'message' => 'Invalid product ID'));
    }

    exit();
}
add_action('wp_ajax_add_to_cart', 'add_to_cart_ajax_handler');
add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_ajax_handler');


add_action('wp_ajax_add_bundle_to_cart', 'add_bundle_to_cart_ajax_handler');
add_action('wp_ajax_nopriv_add_bundle_to_cart', 'add_bundle_to_cart_ajax_handler');

function add_bundle_to_cart_ajax_handler()
{
    if (
        isset($_POST['action']) && $_POST['action'] === 'add_bundle_to_cart'
        && isset($_POST['main_product_id']) && isset($_POST['bundle_product_id'])
    ) {

        $main_product_id = absint($_POST['main_product_id']);
        $bundle_product_id = absint($_POST['bundle_product_id']);
        $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;

        // Add the bundle product to the cart
        WC()->cart->add_to_cart($bundle_product_id, $quantity);

        // Optionally, add the main product to the cart as well
        // WC()->cart->add_to_cart($main_product_id, 1);

        // Return a response if needed
        echo json_encode(array('success' => true));

        // Always exit to prevent further execution
        exit;
    }
}

// shortcode for the bundle classes 
function easy_product_bundle_shortcode()
{
    ob_start();
?>
    <?php
    $bundle_classes_heading = get_field('bundle_classes_heading', 'options');
    $bundle_classes_content = get_field('bundle_classes_content', 'options');
    ?>

    <div class="bundle-top">
        <h2><?php echo $bundle_classes_heading; ?></h2>
        <div class="bundle-top-content">
            <?php echo $bundle_classes_content; ?>
        </div>
    </div>

    <div class="bundle-inner ">
        <div class="bundle-inside owl-carousel owl-theme owl-loaded owl-drag">
            <?php
            // Assuming you have the main product ID
            $main_product_id = get_the_ID();

            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
            );

            $all_products = wc_get_products($args);

            foreach ($all_products as $product) {
                $product_id   = $product->get_id();
                $product_type = $product->get_type();
                $product_name = $product->get_name();

                if ($product_type === 'easy_product_bundle') {
            ?>
                    <div class="product-details">
                        <?php
                        $price        = $product->get_price();
                        $categories   = wc_get_product_category_list($product_id);
                        $currency_symbol = get_woocommerce_currency_symbol();
                        ?>
                        <form class="cart" method="post" enctype="multipart/form-data">
                            <?php
                            if (is_array($categories)) {
                                // If $categories is an array, print each category
                                foreach ($categories as $category) {
                                    // echo '<p>Category: ' . esc_html($category) . '</p>';

                                }
                            } else {
                                // If $categories is a comma-separated string, explode it and print each category
                                $category_list = explode(',', $categories);
                                foreach ($category_list as $category) {
                                    // echo '<p>Category: ' . esc_html($category) . '</p>';
                            ?>
                                    <p><?php echo $category; ?></p>
                            <?php
                                }
                            }
                            ?>
                            <h4 class="bundle-name"><?php echo  $product_name; ?></h4>

                            <div class="price-section">
                                <?php
                                $limited_time_offer_field = get_field('limited_time_offer_field', $product_id);
                                $payment_plan_available = get_field('payment_plan_available', $product_id);

                                ?>
                                <h1> <?php echo wc_price($price); ?></h1>
                                <div class="sale-price">
                                    <?php
                                    //echo $product->get_sale_price();
                                    ?>
                                </div>
                                <p class="limited-offer"><?php echo $limited_time_offer_field; ?></p>
                                <div class="regular-price">
                                    <?php
                                    $reg_price = $product->get_regular_price();
                                    ?>
                                    <?php
                                    if ($reg_price) {
                                    ?>
                                        <p class="org-price"><s>Original Price: <?php echo $currency_symbol . $product->get_regular_price(); ?></s></p>
                                    <?php
                                    }
                                    ?>
                                    <p class="pay-plan"><?php echo $payment_plan_available; ?></p>
                                </div>
                            </div>

                            <div class="includes">
                                <?php
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

                            <?php
                            // Output main product ID as a hidden field
                            echo '<input type="hidden" name="main_product_id" value="' . esc_attr($main_product_id) . '">';

                            // Output bundle product ID as a hidden field
                            echo '<input type="hidden" name="add-to-cart" value="' . esc_attr($product_id) . '">';
                            ?>

                            <button type="button" class="button buy-now-button single_add_to_cart_button" onclick="addToCartAndRedirect(<?php echo esc_js($product_id); ?>)">Buy</button>
                        </form>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <script>
            function addToCartAndRedirect(bundleProductId) {
                // Get the main product ID from the hidden field
                var mainProductId = jQuery('input[name="main_product_id"]').val();

                // Send an AJAX request to add the bundle product to the cart
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                    data: {
                        action: 'add_bundle_to_cart',
                        main_product_id: mainProductId,
                        bundle_product_id: bundleProductId,
                        quantity: 1 // You can adjust the quantity if needed
                    },
                    success: function(response) {
                        // Redirect to the cart page upon successful addition to the cart
                        window.location.href = '<?php echo esc_url(wc_get_cart_url()); ?>';
                    }
                });
            }
        </script>
    </div>
<?php

    return ob_get_clean();
}

add_shortcode('easy_product_bundle', 'easy_product_bundle_shortcode');


function redirect_shop_page()
{
    if (is_shop()) { // Check if it's the shop page
        wp_redirect('https://transformationtechtraining.com/classes/', 301);
        exit();
    }
}
add_action('template_redirect', 'redirect_shop_page');

// remove product title from the product summary on single product page 
function remove_product_title_from_summary() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
}
add_action( 'woocommerce_before_single_product', 'remove_product_title_from_summary' );

//  remove product price from the product summary on single product page
function remove_variable_product_price_from_summary() {
    global $product;

    // Check if the product is variable
    if ( $product->is_type( 'variable' ) ) {
        // Remove the price display hook
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    }
}
add_action( 'woocommerce_before_single_product', 'remove_variable_product_price_from_summary' );


// remove related products from single product page
function remove_related_products() {
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}
add_action( 'woocommerce_before_single_product', 'remove_related_products' );

// remove tabs from summary on the product page 
function remove_product_tabs( $tabs ) {
    unset( $tabs['description'] );          // Remove the description tab
    unset( $tabs['additional_information'] ); // Remove the additional information tab
    unset( $tabs['reviews'] );              // Remove the reviews tab
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'remove_product_tabs', 98 );


// remove product meta from product summary 
function remove_product_meta() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
}
add_action( 'woocommerce_before_single_product', 'remove_product_meta' );


// on click of button empty cart 
add_action('init', 'empty_cart_action');

function empty_cart_action() {
    if (isset($_POST['empty_cart'])) {
        WC()->cart->empty_cart(); // WooCommerce function to empty the cart
    }
}

