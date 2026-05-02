<?php
/**
 * Setup tripgo Child Theme's textdomain.
 *
 * Declare textdomain for this child theme.
 * Translations can be filed in the /languages/ directory.
 */
function tripgo_child_theme_setup() {
	load_child_theme_textdomain( 'tripgo-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'tripgo_child_theme_setup' );


add_action( 'wp_enqueue_scripts', 'tripgo_enqueue_styles' );
function tripgo_enqueue_styles() {
    $parenthandle = 'tripgo-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}

add_filter( 'wp_mail_smtp_core_wp_mail_function_incorrect_location_notice', '__return_false' );

// Export Custom Taxonomy and Custom Checkout Fields
add_action( 'rss2_head', function() {
    if ( is_admin() ) {
        // Custom Taxonomies
        $custom_taxonomies = recursive_array_replace( '\\', '', get_option( 'ovabrw_custom_taxonomy', [] ) );

        if ( ! empty( $custom_taxonomies ) && is_array( $custom_taxonomies ) ) {
            foreach ( $custom_taxonomies as $slug => $items ) {
                echo "<ovabrw_custom_taxonomies>\n";
                    if ( $slug ) echo "\t<slug>".$slug."</slug>\n";
                    if ( $items['name'] ) echo "\t<name>".$items['name']."</name>\n";
                    if ( $items['singular_name'] ) echo "\t<singular_name>".$items['singular_name']."</singular_name>\n";
                    if ( $items['label_frontend'] ) echo "\t<label_frontend>".$items['label_frontend']."</label_frontend>\n";
                    if ( $items['enabled'] ) echo "\t<enabled>".$items['enabled']."</enabled>\n";
                    if ( $items['show_listing'] ) echo "\t<show_listing>".$items['show_listing']."</show_listing>\n";
                echo "</ovabrw_custom_taxonomies>\n";
            }
        }

        // Custom Checkout Fields
        $checkout_fields = recursive_array_replace( '\\', '', get_option( 'ovabrw_booking_form', [] ) );

        if ( ! empty( $checkout_fields ) && is_array( $checkout_fields ) ) {
            foreach ( $checkout_fields as $slug => $items ) {
                // Select
                $options_key    = isset( $items['ova_options_key'] ) && $items['ova_options_key'] ? $items['ova_options_key'] : '';
                $options_text   = isset( $items['ova_options_text'] ) && $items['ova_options_text'] ? $items['ova_options_text'] : '';
                $options_price  = isset( $items['ova_options_price'] ) && $items['ova_options_price'] ? $items['ova_options_price'] : '';

                // Radio
                $radio_values   = isset( $items['ova_radio_values'] ) && $items['ova_radio_values'] ? $items['ova_radio_values'] : '';
                $radio_prices   = isset( $items['ova_radio_prices'] ) && $items['ova_radio_prices'] ? $items['ova_radio_prices'] : '';

                // Checkbox
                $checkbox_key   = isset( $items['ova_checkbox_key'] ) && $items['ova_checkbox_key'] ? $items['ova_checkbox_key'] : '';
                $checkbox_text  = isset( $items['ova_checkbox_text'] ) && $items['ova_checkbox_text'] ? $items['ova_checkbox_text'] : '';
                $checkbox_price = isset( $items['ova_checkbox_price'] ) && $items['ova_checkbox_price'] ? $items['ova_checkbox_price'] : '';

                // File
                $max_file_size  = isset( $items['max_file_size'] ) && $items['max_file_size'] ? $items['max_file_size'] : '';

                echo "<ovabrw_custom_checkout_fields>\n";
                    if ( $slug ) echo "\t<slug>".$slug."</slug>\n";
                    if ( $items['type'] ) echo "\t<type>".$items['type']."</type>\n";
                    if ( $items['label'] ) echo "\t<label>".$items['label']."</label>\n";
                    if ( $items['default'] ) echo "\t<default>".$items['default']."</default>\n";
                    if ( $items['placeholder'] ) echo "\t<placeholder>".$items['placeholder']."</placeholder>\n";
                    if ( $items['class'] ) echo "\t<class>".$items['class']."</class>\n";
                    if ( $items['required'] ) echo "\t<required>".$items['required']."</required>\n";
                    if ( $items['enabled'] ) echo "\t<enabled>".$items['enabled']."</enabled>\n";
                    
                    // Select Keys
                    if ( ! empty( $options_key ) && is_array( $options_key ) ) {
                        echo "\t<select_keys>".implode( '|', $options_key )."</select_keys>\n";
                    }
                    // Select Texts
                    if ( ! empty( $options_text ) && is_array( $options_text ) ) {
                        echo "\t<select_texts>".implode( '|', $options_text )."</select_texts>\n";
                    }
                    // Select Prices
                    if ( ! empty( $options_price ) && is_array( $options_price ) ) {
                        echo "\t<select_prices>".implode( '|', $options_price )."</select_prices>\n";
                    }
                    // Radio Values
                    if ( ! empty( $radio_values ) && is_array( $radio_values ) ) {
                        echo "\t<radio_values>".implode( '|', $radio_values )."</radio_values>\n";
                    }
                    // Radio Prices
                    if ( ! empty( $radio_prices ) && is_array( $radio_prices ) ) {
                        echo "\t<radio_prices>".implode( '|', $radio_prices )."</radio_prices>\n";
                    }
                    // Checkbox Keys
                    if ( ! empty( $checkbox_key ) && is_array( $checkbox_key ) ) {
                        echo "\t<checkbox_keys>".implode( '|', $checkbox_key )."</checkbox_keys>\n";
                    }
                    // Checkbox Texts
                    if ( ! empty( $checkbox_text ) && is_array( $checkbox_text ) ) {
                        echo "\t<checkbox_texts>".implode( '|', $checkbox_text )."</checkbox_texts>\n";
                    }
                    // Checkbox Prices
                    if ( ! empty( $checkbox_price ) && is_array( $checkbox_price ) ) {
                        echo "\t<checkbox_prices>".implode( '|', $checkbox_price )."</checkbox_prices>\n";
                    }
                    // Max File Size
                    if ( $max_file_size ) {
                        echo "\t<max_file_size>".$max_file_size."</max_file_size>\n";
                    }
                echo "</ovabrw_custom_checkout_fields>\n";
            }
        }
    }
});


/**
 * ========================================
 * HM TOUR & TRAVEL - CUSTOM FUNCTIONS
 * ========================================
 */

/**
 * Add Google Fonts for Hero Section
 */
function hmtravel_add_google_fonts() {
    wp_enqueue_style( 'hmtravel-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'hmtravel_add_google_fonts' );

/**
 * Custom Favicon for HM Travel
 * Note: Upload favicon-hmtravel.png to the child theme directory
 */
function hmtravel_custom_favicon() {
    $favicon_path = get_stylesheet_directory_uri() . '/favicon-hmtravel.png';
    
    // Check if custom favicon exists
    if ( file_exists( get_stylesheet_directory() . '/favicon-hmtravel.png' ) ) {
        echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $favicon_path ) . '">';
        echo '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url( $favicon_path ) . '">';
        echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $favicon_path ) . '">';
    }
}
add_action( 'wp_head', 'hmtravel_custom_favicon', 5 );

/**
 * Add custom body class for HM Travel branding
 */
function hmtravel_body_class( $classes ) {
    $classes[] = 'hmtravel-website';
    return $classes;
}
add_filter( 'body_class', 'hmtravel_body_class' );

/**
 * Enqueue custom JavaScript for dropdown menu fix
 */
function hmtravel_custom_scripts() {
    wp_enqueue_script( 
        'hmtravel-menu-fix', 
        get_stylesheet_directory_uri() . '/js/menu-fix.js', 
        array('jquery'), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'hmtravel_custom_scripts' );

/**
 * Add custom meta tags for SEO
 */
function hmtravel_custom_meta_tags() {
    if ( is_front_page() ) {
        echo '<meta name="description" content="HM Tour & Travel - Hajj & Umroh With Sunnah Ways. Akreditasi A dari KEMENAG. Lebih dari 13 tahun pengalaman melayani jemaah umroh dan haji.">';
        echo '<meta name="keywords" content="umroh, haji, travel umroh, travel haji, HM Tour, PT Hikami Mandiri Indonesia, umroh murah, haji plus, SAPUHI">';
        echo '<meta property="og:title" content="HM Tour & Travel - Hajj & Umroh With Sunnah Ways">';
        echo '<meta property="og:description" content="Travel Amanah, Sesuai Sunnah, Pelayanan Ramah, Harga Murah, Proses Mudah, Fasilitas Mewah, Semoga Berkah">';
        echo '<meta property="og:type" content="website">';
    }
}
add_action( 'wp_head', 'hmtravel_custom_meta_tags' );

/**
 * Custom excerpt length for blog posts
 */
function hmtravel_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'hmtravel_excerpt_length' );

/**
 * Add custom image sizes for team photos
 */
function hmtravel_custom_image_sizes() {
    add_image_size( 'team-photo', 400, 400, true ); // Square crop for team photos
    add_image_size( 'team-photo-large', 600, 600, true );
}
add_action( 'after_setup_theme', 'hmtravel_custom_image_sizes' );

/**
 * Disable WordPress emoji scripts (performance optimization)
 */
function hmtravel_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'hmtravel_disable_emojis' );

/**
 * Add custom widget area for footer
 */
function hmtravel_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'HM Travel Footer Info', 'tripgo-child' ),
        'id'            => 'hmtravel-footer-info',
        'description'   => __( 'Widget area for HM Travel company information', 'tripgo-child' ),
        'before_widget' => '<div class="widget hmtravel-footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'hmtravel_widgets_init' );

/**
 * Add custom shortcode for company tagline
 */
function hmtravel_tagline_shortcode() {
    return '<span class="tagline-text">Hajj & Umroh With Sunnah Ways</span>';
}
add_shortcode( 'hmtravel_tagline', 'hmtravel_tagline_shortcode' );

/**
 * Add custom shortcode for company moto
 */
function hmtravel_moto_shortcode() {
    return '<div class="hmtravel-moto">Travel Amanah, Sesuai Sunnah, Pelayanan Ramah, Harga Murah, Proses Mudah, Fasilitas Mewah, Semoga Berkah</div>';
}
add_shortcode( 'hmtravel_moto', 'hmtravel_moto_shortcode' );

/**
 * Add preload for critical fonts
 */
function hmtravel_preload_fonts() {
    echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap" as="style">';
}
add_action( 'wp_head', 'hmtravel_preload_fonts', 1 );

/**
 * Optimize WordPress performance
 */
function hmtravel_optimize_performance() {
    // Remove query strings from static resources
    if ( ! is_admin() ) {
        add_filter( 'script_loader_src', 'hmtravel_remove_query_strings', 15, 1 );
        add_filter( 'style_loader_src', 'hmtravel_remove_query_strings', 15, 1 );
    }
}
add_action( 'init', 'hmtravel_optimize_performance' );

function hmtravel_remove_query_strings( $src ) {
    if ( strpos( $src, '?ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}

/**
 * Add custom CSS class to menu items
 */
function hmtravel_menu_item_class( $classes, $item, $args ) {
    if ( $args->theme_location == 'primary' ) {
        $classes[] = 'hmtravel-menu-item';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'hmtravel_menu_item_class', 10, 3 );

/**
 * Custom walker for navigation menu (dropdown fix)
 */
class HMTravel_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"sub-menu hmtravel-dropdown\">\n";
    }
}

/**
 * Add schema markup for organization
 */
function hmtravel_schema_markup() {
    if ( is_front_page() ) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            'name' => 'HM Tour & Travel',
            'alternateName' => 'PT Hikami Mandiri Indonesia',
            'url' => home_url(),
            'logo' => get_stylesheet_directory_uri() . '/favicon-hmtravel.png',
            'description' => 'Travel Amanah, Sesuai Sunnah, Pelayanan Ramah, Harga Murah, Proses Mudah, Fasilitas Mewah, Semoga Berkah',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'Jl. A.H. Nasution No.98, Sukamiskin',
                'addressLocality' => 'Arcamanik',
                'addressRegion' => 'Kota Bandung',
                'postalCode' => '40293',
                'addressCountry' => 'ID'
            ),
            'foundingDate' => '2012',
            'slogan' => 'Hajj & Umroh With Sunnah Ways'
        );
        
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
    }
}
add_action( 'wp_head', 'hmtravel_schema_markup' );
