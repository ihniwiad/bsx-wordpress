<?php 

// CHANGE LOCAL LANGUAGE
// must be called before load_theme_textdomain()
 
add_filter( 'locale', 'bsx_theme_localized' );

/**
 * Switch to locale given as query parameter l, if present
 */
function bsx_theme_localized( $locale ) {
    if ( isset( $_GET[ 'l' ] ) ) {
        return sanitize_key( $_GET[ 'l' ] );
    }
    return $locale;
}

/**
 * Load theme translation from bsx-wordpress-example/languages/ directory
 */
load_theme_textdomain( 'bsx-wordpress', get_template_directory() . '/languages' );

/**
 * helper
 */

$file = dirname( __FILE__ ) . '/inc/helper/helper-functions.php';
if ( file_exists( $file ) ) {
    require $file;
}

/**
 * variables
 */

// paths
$functions_file_basename = getcwd(); // e.g. /var/www/some_user_123/html/

$serverName = $_SERVER[ 'SERVER_NAME' ];
$homeUrl = get_bloginfo( 'url' ) . '/';

$rootPath = get_bloginfo( 'template_directory' ).'/';
$resourcesPath = 'resources/';

$relativeAssetsPath = 'assets/';
$assetsPath = $rootPath . $relativeAssetsPath;

// make equal protocol
$rootRelatedAssetsPath = explode( str_replace( 'https://', 'http://', $homeUrl ), str_replace( 'https://', 'http://', $assetsPath ) )[ 1 ];

// get css file version using absolute file path
$cssFileName = 'css/style.min.css';
$cssFilePath = $rootRelatedAssetsPath . $cssFileName;
$cssVersion = file_exists( $cssFilePath ) ? filemtime( $cssFilePath ) : 'null';

// get js file versions
$vendorJsFileName = 'js/vendor.min.js';
$vendorJsFilePath = $rootRelatedAssetsPath . $vendorJsFileName;
$vendorJsVersion = file_exists( $vendorJsFilePath ) ? filemtime( $vendorJsFilePath ) : 'null';

$scriptsJsFileName = 'js/scripts.min.js';
$scriptsJsFilePath = $rootRelatedAssetsPath . $scriptsJsFileName;
$scriptsJsVersion = file_exists( $scriptsJsFilePath ) ? filemtime( $scriptsJsFilePath ) : 'null';


// logo path
$logoPath = $assetsPath . 'img/ci/logo/logo.svg';


// dev mode
$isDevMode = false;
if ( isset( $_GET[ 'dev' ] ) && $_GET[ 'dev' ] == '1' ) {
    $isDevMode = true;
}


// patterns
$phoneHrefRemovePatterns = array( '/ /i', '/\./i', '/\//i', '/-/i' );



/**
 * include required files
 */

// classes
require get_template_directory() . '/src/libs/nav/classes/class-bsx-walker-page.php';
require get_template_directory() . '/src/libs/nav/classes/class-bsx-walker-nav-menu.php';

require_once( __DIR__ . '/src/libs/data-processing-consent/class-consent-popup-manager.php' );
require_once( __DIR__ . '/src/libs/img-gallery/class-bsx-photoswipe.php' );
require_once( __DIR__ . '/src/libs/lazy-img/class-lazy-img.php' );


/**
 * WordPress titles
 */
add_theme_support( 'title-tag' );


/**
 * navigations
 */

function register_my_menus() {
    register_nav_menus(
        array(
            'primary-menu' => __( 'Primary Menu', 'bsx-wordpress' ),
            'footer-column-1-menu' => __( 'Footer Column 1 Menu', 'bsx-wordpress' ),
            'footer-column-2-menu' => __( 'Footer Column 2 Menu', 'bsx-wordpress' ),
            'footer-column-3-menu' => __( 'Footer Column 3 Menu', 'bsx-wordpress' ),
            'footer-column-4-menu' => __( 'Footer Column 4 Menu', 'bsx-wordpress' ),
            'footer-column-5-menu' => __( 'Footer Column 5 Menu', 'bsx-wordpress' ),
            'footer-bottom-menu' => __( 'Footer Bottom Menu', 'bsx-wordpress' ),
            'consent-popup-menu' => __( 'Consent Popup Menu', 'bsx-wordpress' )
        )
    );
}
add_action( 'init', 'register_my_menus' );

// add filter to add class name to li or/and class name to current li
function add_additional_class_on_li( $classes, $item, $args ) {
    if ( isset( $args->add_li_class ) ) {
        $classes[] = $args->add_li_class;
    }
    // useful e.g. for language switcher
    if ( isset( $args->add_current_li_class ) ) {
        $classes = is_array( $classes ) ? $classes : (array) $classes;
        if ( in_array( 'current_page_item', $classes ) || in_array( 'current-lang', $classes ) ) {
            $classes[] = $args->add_current_li_class;
        }
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'add_additional_class_on_li', 1, 3 );

// add filter to add class name to a
function add_additional_class_on_a( $atts, $item, $args ) {
    if ( isset( $args->add_a_class ) ) {
        $class = $args->add_a_class;
        if ( isset( $atts[ 'class' ] ) ) {
            $atts[ 'class' ] .= $class;
        }
        else {
            $atts[ 'class' ] = $class;
        }
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'add_additional_class_on_a', 10, 3 );


/**
 * disable emoji
 */

function disable_wp_emojicons() {
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
    add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'disable_wp_emojicons' );

function disable_emojicons_tinymce( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}


/**
 * remove block editor styles from frontend.
 */

function remove_editor_blocks_assets() {
    if ( ! is_admin() ) {
        wp_dequeue_style( 'editor-blocks' );
    }
}
add_action( 'enqueue_block_assets', 'remove_editor_blocks_assets' );

/**
 * remove block library css
 */

function wpassist_remove_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
} 
add_action( 'wp_enqueue_scripts', 'wpassist_remove_block_library_css' );


/**
 * remove more embed stuff (wp-embed.min.js)
 */

add_action( 'init', function() {
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    // remove global-styles-inline-css
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
    remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
    remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    wp_dequeue_style( 'global-styles' );
    wp_deregister_style( 'global-styles' );
}, PHP_INT_MAX - 1 );


/**
 * security (remove unnecessary information that could enable an attack)
 */

add_action( 'init', function() {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wlwmanifest_link' ); 
    remove_action( 'wp_head', 'rsd_link' );
}, PHP_INT_MAX - 1 );


/**
 * add Open Graph Meta Tags
 */

// you may wish to remove this if using the Yoast SEO plugin
$file = dirname( __FILE__ ) . '/inc/meta-title-description-og/meta-box.php';
if ( file_exists( $file ) ) {
    require $file;
}


/**
 * remove admin bar
 */

function remove_admin_bar() {
    //if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
        show_admin_bar( false );
    //}
}
add_action( 'after_setup_theme', 'remove_admin_bar' );


/**
 * enable featured images
 */

add_theme_support( 'post-thumbnails' );


/**
 * Generate custom search form
 *
 * @param string $form Form HTML.
 * @return string Modified form HTML.
 */
function custom_search_form( $form ) {
    $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
        <div>
            <label class="sr-only" for="s">' . __( 'Search for:' ) . '</label>
            <div class="input-group input-group-lg">
                <input class="form-control" type="text" value="' . get_search_query() . '" name="s" id="s" />
                <div class="input-group-append">
                    <input class="btn btn-primary" type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
                </div>
            </div>
        </div>
    </form>';
 
    return $form;
}
add_filter( 'get_search_form', 'custom_search_form' );

/**
 * Reduce search to posts (exclude pages)
 */
function reduce_search_only_posts( $query ) {
    if ( $query->is_search ) {
        $query->set( 'post_type', 'post' );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'reduce_search_only_posts' );


/**
 * banner custom post
 */

// helper
$file = dirname( __FILE__ ) . '/inc/banner/helper-functions.php';
if ( file_exists( $file ) ) {
    require $file;
}
// custom post
$file = dirname( __FILE__ ) . '/inc/banner/custom-post-type.php';
if ( file_exists( $file ) ) {
    require $file;
}
// meta box
$file = dirname( __FILE__ ) . '/inc/banner/meta-box.php';
if ( file_exists( $file ) ) {
    require $file;
    ( new BannerMeta() )->init();
}


/*
 * Set post views count using post meta
 */

$file = dirname( __FILE__ ) . '/inc/count-post-views/meta-box.php';
if ( file_exists( $file ) ) {
    require $file;
}


/**
 * post list shortcode
 */

$file = dirname( __FILE__ ) . '/inc/post-list/shortcode.php';
if ( file_exists( $file ) ) {
    require $file;
    ( new PostListShortcode() )->init();
}


// manage allowed block types

// function myplugin_allowed_block_types( $allowed_block_types, $post ) {     
//     if ( $post->post_type !== 'page' || $post->post_type !== 'post' ) {
//         return array( 
//             'core/paragraph', 
//             'core/heading', 
//             'core/list', 
//             'bsx-blocks/banner',
//             'bsx-blocks/buttons', 
//             'bsx-blocks/button', 
//             'bsx-blocks/button-label', 
//             'bsx-blocks/column-row', 
//             'bsx-blocks/column-rows', 
//             'bsx-blocks/container', 
//             'bsx-blocks/groups', 
//             'bsx-blocks/img-gallery', 
//             'bsx-blocks/lazy-img', 
//             'bsx-blocks/col', 
//             'bsx-blocks/row-with-cols', 
//             'bsx-blocks/section', 
//             'bsx-blocks/slider', 
//             'bsx-blocks/wrapper', 
//         );
//     }
 
//     return $allowed_block_types;
// }
 
// add_filter( 'allowed_block_types', 'myplugin_allowed_block_types', 10, 2 );


/**
 * custom global options, add menu with sublevels
 */

function custom_settings_add_menu() {
    add_menu_page( 
        __( 'Theme Settings', 'bsx-wordpress' ), // page title
        __( 'Theme Settings', 'bsx-wordpress' ), // menu title
        'manage_options', // capability
        'custom_options', // menu_slug
        'custom_settings_page', // function to show related content
        null, // icon url
        1 // position
    );
    add_submenu_page( 
        'custom_options', // parent_slug
        __( 'Social Media' ), // page_title
        __( 'Social Media' ), // menu_title
        'manage_options', // capability
        'custom-settings-social-media', // menu_slug, 
        'custom_settings_social_media', // function = '', 
        1 // position = null
    );
    add_submenu_page( 
        'custom_options', // parent_slug
        __( 'Layout' ), // page_title
        __( 'Layout' ), // menu_title
        'manage_options', // capability
        'custom-settings-layout', // menu_slug, 
        'custom_settings_layout', // function = '', 
        99 // position = null
    );
}
add_action( 'admin_menu', 'custom_settings_add_menu' );

function custom_settings_page() { ?>
    <div class="wrap">
        <h2><?php __( 'Theme Settings', 'bsx-wordpress' ); ?></h2>
        <form method="post" action="options.php">
            <?php
                do_settings_sections( 'custom_options_contact' ); // page
                settings_fields( 'custom-settings-contact' ); // option group (may have multiple sections)
                submit_button();
            ?>
        </form>
    </div>
<?php }
function custom_settings_social_media() { ?>
    <div class="wrap">
        <h2><?php __( 'Social Media', 'bsx-wordpress' ); ?></h2>
        <form method="post" action="options.php">
            <?php
                do_settings_sections( 'custom_options_social_media' ); // page
                settings_fields( 'custom-settings-social-media' ); // option group (may have multiple sections)
                submit_button();
            ?>
        </form>
    </div>
<?php }
function custom_settings_layout() { ?>
    <div class="wrap">
        <h2><?php __( 'Layout', 'bsx-wordpress' ); ?></h2>
        <form method="post" action="options.php">
            <?php
                do_settings_sections( 'custom_options_layout' ); // page
                settings_fields( 'custom-settings-layout' ); // option group (may have multiple sections)
                submit_button();
            ?>
        </form>
    </div>
<?php }


/**
 * custom settings, create pages setup
 */

function custom_settings_page_setup() {


    // section
    add_settings_section(
        'custom-settings-section-contact', // id
        __( 'Contact', 'bsx-wordpress' ), // title
        null, // callback function
        'custom_options_contact' // page
    );

    // fields for section
    add_settings_field(
        'owner-name', // id
        __( 'Owner name', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'owner-name',
            'label_for' => 'owner-name'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'owner-name' // option name
    );

    add_settings_field(
        'street', // id
        __( 'Street', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'street',
            'label_for' => 'street'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'street' // option name
    );

    add_settings_field(
        'address-additional', // id
        __( 'Adress Additional', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'address-additional',
            'label_for' => 'address-additional'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'address-additional' // option name
    );

    add_settings_field(
        'zip', // id
        __( 'Zip', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'zip',
            'label_for' => 'zip'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'zip' // option name
    );

    add_settings_field(
        'city', // id
        __( 'City', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'city',
            'label_for' => 'city'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'city' // option name
    );

    add_settings_field(
        'country', // id
        __( 'Country', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'country',
            'label_for' => 'country'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'country' // option name
    );

    add_settings_field(
        'phone', // id
        __( 'Phone', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'phone',
            'label_for' => 'phone'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'phone' // option name
    );

    add_settings_field(
        'mail', // id
        __( 'Email', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'mail',
            'label_for' => 'mail'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'mail' // option name
    );

    add_settings_field(
        'service-phone', // id
        __( 'Service Phone', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'service-phone',
            'label_for' => 'service-phone'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'service-phone' // option name
    );
    
    add_settings_field(
        'service-mail', // id
        __( 'Service Email', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_contact', // page
        'custom-settings-section-contact', // section = 'default'
        array(
            'service-mail',
            'label_for' => 'service-mail'
        ) // args = array()
    );
    register_setting(
        'custom-settings-contact', // option group
        'service-mail' // option name
    );


    // social media section
    add_settings_section(
        'custom-settings-section-social-media', // id
        __( 'Social Media', 'bsx-wordpress' ), // title
        null, // callback function
        'custom_options_social_media' // page
    );

    // fields for section
    add_settings_field(
        'facebook', // id
        __( 'Facebook', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'facebook',
            'label_for' => 'facebook'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'facebook' // option name
    );

    add_settings_field(
        'twitter', // id
        __( 'X (Twitter)', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'twitter',
            'label_for' => 'twitter'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'twitter' // option name
    );

    add_settings_field(
        'instagram', // id
        __( 'Instagram', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'instagram',
            'label_for' => 'instagram'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'instagram' // option name
    );

    add_settings_field(
        'googleplus', // id
        __( 'Google Plus', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'googleplus',
            'label_for' => 'googleplus'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'googleplus' // option name
    );

    add_settings_field(
        'xing', // id
        __( 'Xing', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'xing',
            'label_for' => 'xing'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'xing' // option name
    );

    add_settings_field(
        'linkedin', // id
        __( 'LinkedIn', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'linkedin',
            'label_for' => 'linkedin'
        ) // args = array()
    );
    register_setting(
        'custom-settings-social-media', // option group
        'linkedin' // option name
    );


    // layout section
    add_settings_section(
        'custom-settings-section-layout', // id
        __( 'Layout', 'bsx-wordpress' ), // title
        null, // callback function
        'custom_options_layout' // page
    );

    // fields for section
    add_settings_field(
        'logo', // id
        esc_html__( 'Logo', 'bsx-wordpress' ), // title
        'render_custom_textarea_field', // callback, use unique function name
        'custom_options_layout', // page
        'custom-settings-section-layout', // section = 'default'
        array(
            'logo',
            'label_for' => 'logo'
        ) // args = array()
    );
    register_setting(
        'custom-settings-layout', // option group
        'logo' // option_name
    );

    add_settings_field(
        'footer_columns_count', // id
        esc_html__( 'Footer Menu Columns Count (0...5)', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_layout', // page
        'custom-settings-section-layout', // section = 'default'
        array(
            'footer_columns_count',
            'label_for' => 'footer_columns_count'
        ) // args = array()
    );
    register_setting(
        'custom-settings-layout', // option group
        'footer_columns_count' // option_name
    );

    add_settings_field(
        'footer_phone_mail_show', // id
        esc_html__( 'Show Phone & Email in footer', 'bsx-wordpress' ), // title
        'render_custom_checkbox', // callback, use unique function name
        'custom_options_layout', // page
        'custom-settings-section-layout', // section = 'default'
        array(
            'footer_phone_mail_show',
            'label_for' => 'footer_phone_mail_show'
        ) // args = array()
    );
    register_setting(
        'custom-settings-layout', // option group
        'footer_phone_mail_show' // option_name
    );

    add_settings_field(
        'social_media_colors_use', // id
        esc_html__( 'Use Social Media Brand colors', 'bsx-wordpress' ), // title
        'render_custom_checkbox', // callback, use unique function name
        'custom_options_layout', // page
        'custom-settings-section-layout', // section = 'default'
        array(
            'social_media_colors_use',
            'label_for' => 'social_media_colors_use'
        ) // args = array()
    );
    register_setting(
        'custom-settings-layout', // option group
        'social_media_colors_use' // option_name
    );

    add_settings_field(
        'global_banner_post_id', // id
        esc_html__( 'Global Banner Post id or Banner Type', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_layout', // page
        'custom-settings-section-layout', // section = 'default'
        array(
            'global_banner_post_id',
            'label_for' => 'global_banner_post_id'
        ) // args = array()
    );
    register_setting(
        'custom-settings-layout', // option group
        'global_banner_post_id' // option_name
    );

}
// Shared  across sections
// modified from https://wordpress.stackexchange.com/questions/129180/add-multiple-custom-fields-to-the-general-settings-page
function render_custom_input_field( $args ) {
    $option_val = get_option( $args[ 0 ] );
    echo '<input type="text" id="'  . esc_attr( $args[ 0 ] ) . '" name="'  . esc_attr( $args[ 0 ] ) . '" value="' . esc_attr(  $option_val ) . '" />';
}
function render_custom_checkbox( $args ) {
    $option_val = get_option( $args[ 0 ] );
    echo '<label><input type="checkbox" id="'  . esc_attr( $args[ 0 ] ) . '" name="' . esc_attr( $args[ 0 ] ) . '" value="1"' . ( ( $option_val ) ? 'checked' : '' ) . ' />' . esc_html_e( 'Yes', 'bsx-wordpress' ) . '</label>';
}
function render_custom_textarea_field( $args ) {
    $option_val = get_option( $args[ 0 ] );
    echo '<textarea  id="'  . esc_attr( $args[ 0 ] ) . '" name="'  . esc_attr( $args[ 0 ] ) . '" rows="6" cols="80" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace;">' . esc_textarea( $option_val ) . '</textarea>';
}
add_action( 'admin_init', 'custom_settings_page_setup' );


// page style meta box

$file = dirname( __FILE__ ) . '/inc/page-style/meta-box.php';
if ( file_exists( $file ) ) {
    require $file;
}


/**
 * shortcodes
 */

// consent trigger button, use shortcode block with [consent-trigger-button]
function add_consent_button_shortcode() {
  $content = 'Missing method: Consent_Popup_Manager::popupTriggerHtml()';
  if ( class_exists( 'Consent_Popup_Manager' ) && method_exists( 'Consent_Popup_Manager', 'popupTriggerHtml' ) ) {
    $content = Consent_Popup_Manager::popupTriggerHtml();
  }
  return $content;
}
add_shortcode( 'consent-trigger-button', 'add_consent_button_shortcode' );



/**
 * include mail form
 */

// require_once( __DIR__ . '/inc/theme-forms/functions.php' );
// require_once( __DIR__ . '/inc/theme-forms/database.php' );
// $theme_forms_database_handler = new Theme_Forms_Database_Handler;
// $theme_forms_database_handler->create_table();
// require_once( __DIR__ . '/inc/theme-forms/class-theme-forms-list-table.php' );
// require_once( __DIR__ . '/inc/theme-forms/custom-post-type.php' );
// require_once( __DIR__ . '/inc/theme-forms/meta-box.php' );
// require_once( __DIR__ . '/inc/theme-forms/admin-menu.php' );
// require_once( __DIR__ . '/inc/theme-forms/admin-pages.php' );
// require_once( __DIR__ . '/inc/theme-forms/rest-route.php' );
// require_once( __DIR__ . '/inc/theme-forms/form-template.php' );
// require_once( __DIR__ . '/inc/theme-forms/shortcode.php' );

require_once( __DIR__ . '/inc/theme-forms/inc.php' );

require_once( __DIR__ . '/src/libs/form/class-bsx-mail-form.php' );
if ( class_exists( 'Bsx_Mail_Form' ) && method_exists( 'Bsx_Mail_Form' , 'init' ) ) {
    ( new Bsx_Mail_Form() )->init();
}


/*
 * Breadcrumb
 */

$file = dirname( __FILE__ ) . '/inc/breadcrumb/breadcrumb.php';
if ( file_exists( $file ) ) {
    require $file;
    ( new Bsx_Breadcrumb() )->init();
}


/**
 * Add menu link to show reusable blocks list
 */

$file = dirname( __FILE__ ) . '/inc/admin-menu/reusable-blocks-link.php';
if ( file_exists( $file ) ) {
    require $file;
}


/**
 * Yoast SEO fallback og image (for all languages)
 */

// require get_template_directory() . '/src/libs/yoast-meta/class-yoast-multilang-og-image.php';



