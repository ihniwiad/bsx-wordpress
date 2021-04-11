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


// paths

$rootPath = get_template_directory().'/';
$resourcesPath = 'resources/';
$assetsPath = $rootPath.'assets/';


/**
 * variables
 */

// paths

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
            'footer-bottom-menu' => __( 'Footer Bottom Menu', 'bsx-wordpress' )
        )
    );
}
add_action( 'init', 'register_my_menus' );

// add filter to add class name to li
function add_additional_class_on_li( $classes, $item, $args ) {
    if ( isset( $args->add_li_class ) ) {
        $classes[] = $args->add_li_class;
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
}, PHP_INT_MAX - 1 );


/**
 * add Open Graph Meta Tags
 */

// TODO: add meta boxes for title & discription, use title & excerpt as fallback

function meta_og() {
    global $post;

    if ( is_single() || is_page() ) {

        $meta = get_post_meta( $post->ID, 'meta_tag', true );

        if ( isset( $meta[ 'meta_title' ] ) && $meta[ 'meta_title' ] != '' ) {
            $title = $meta[ 'meta_title' ];
        }
        else {
            $title = get_the_title();
        }
        
        if ( isset( $meta[ 'meta_description' ] ) && $meta[ 'meta_description' ] != '' ) {
            $description = $meta[ 'meta_description' ];
        }
        else {
            $excerpt = strip_tags( $post->post_content );
            $excerpt_more = '';
            if ( strlen($excerpt ) > 155) {
                $excerpt = substr( $excerpt, 0, 155 );
                $excerpt_more = ' ...';
            }
            $excerpt = str_replace( '"', '', $excerpt );
            $excerpt = str_replace( "'", '', $excerpt );
            $excerptwords = preg_split( '/[\n\r\t ]+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY );
            array_pop( $excerptwords );
            $excerpt = implode( ' ', $excerptwords ) . $excerpt_more;

            $description = $excerpt;
        }

        ?>
<meta name="description" content="<?php echo $description; ?>">
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url" content="<?php echo the_permalink(); ?>">
<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>">

        <?php

        if ( has_post_thumbnail( $post->ID ) ) {
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
            ?>
<meta property="og:image" content="<?php echo $img_src[0]; ?>">
            <?php 
        } 

        if ( is_single() ) {
            ?>
<meta name="author" content="<?php echo get_the_author(); ?>">
<meta property="og:type" content="article">
            <?php
        }
    } 
    else {
        return;
    }
}
add_action( 'wp_head', 'meta_og', 5 );


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
                <input class="input-group-append btn btn-primary" type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
            </div>
        </div>
    </form>';
 
    return $form;
}
add_filter( 'get_search_form', 'custom_search_form' );


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

    // register each field
    register_setting(
        'custom-settings-contact', // option group
        'owner-name' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'street' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'address-additional' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'zip' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'city' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'country' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'phone' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'mail' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'service-phone' // option name
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
    add_settings_field(
        'twitter', // id
        __( 'Twitter', 'bsx-wordpress' ), // title
        'render_custom_input_field', // callback, use unique function name
        'custom_options_social_media', // page
        'custom-settings-section-social-media', // section = 'default'
        array(
            'twitter',
            'label_for' => 'twitter'
        ) // args = array()
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

    // register each field
    register_setting(
        'custom-settings-social-media', // option group
        'facebook' // option name
    );
    register_setting(
        'custom-settings-social-media', // option group
        'twitter' // option name
    );
    register_setting(
        'custom-settings-social-media', // option group
        'instagram' // option name
    );
    register_setting(
        'custom-settings-social-media', // option group
        'googleplus' // option name
    );
    register_setting(
        'custom-settings-social-media', // option group
        'xing' // option name
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

    // register each field
    register_setting(
        'custom-settings-layout', // option group
        'footer_columns_count' // option_name
    );
    register_setting(
        'custom-settings-layout', // option group
        'footer_phone_mail_show' // option_name
    );

}
// Shared  across sections
// modified from https://wordpress.stackexchange.com/questions/129180/add-multiple-custom-fields-to-the-general-settings-page
function render_custom_input_field( $args ) {
    $options = get_option( $args[ 0 ] );
    echo '<input type="text" id="'  . $args[ 0 ] . '" name="'  . $args[ 0 ] . '" value="' . $options . '"></input>';
}
function render_custom_checkbox( $args ) {
    $options = get_option( $args[ 0 ] );
    echo '<label><input type="checkbox" id="'  . $args[ 0 ] . '" name="' . $args[ 0 ] . '" value="1"' . ( ( $options ) ? 'checked' : '' ) . ' />' . __( 'Yes', 'bsx-wordpress' ) . '</label>';
}
add_action( 'admin_init', 'custom_settings_page_setup' );


/**
 * meta boxes
 */

// page style
function add_page_style_meta_box() {
    $screen = 'page'; // choose 'post' or 'page'
    add_meta_box( 
        'page_style_meta_box', // $id
        __( 'Page Style', 'bsx-wordpress' ), // $title
        'show_page_style_meta_box', // $callback
        $screen, // $screen
        'side', // $context, choose 'normal' or 'side')
        'default', // $priority
        null 
    );
}
add_action( 'add_meta_boxes', 'add_page_style_meta_box' );

function show_page_style_meta_box() {
    global $post;
    $meta = get_post_meta( $post->ID, 'page_style', true ); 
    ?>
        <input type="hidden" name="page_style_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
        <p>
            <label>
                <input type="checkbox" name="page_style[add_page_top_space]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'add_page_top_space' ] ) && $meta[ 'add_page_top_space' ] == 1 ) echo 'checked' ?>><?php echo __( 'Add space on Page top', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="page_style[wrap_page_with_container]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'wrap_page_with_container' ] ) && $meta[ 'wrap_page_with_container' ] == 1 ) echo 'checked' ?>><?php echo  __( 'Wrap Page with Container', 'bsx-wordpress' ); ?>
            </label>
        </p>
    <?php 
}
function save_page_style_meta( $post_id ) {
    // verify nonce
    if ( isset( $_POST[ 'page_style_meta_box_nonce' ] ) && ! wp_verify_nonce( $_POST[ 'page_style_meta_box_nonce' ], basename(__FILE__) ) ) {
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // check permissions
    if ( isset( $_POST[ 'post_type' ] ) && 'page' === $_POST[ 'post_type' ] ) {
        if ( !current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        } 
        elseif ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }
    // cannot check for `isset( $_POST[ 'page_style' ] )` since empty checkboxes would never be saved
    if ( isset( $_POST[ 'page_style_meta_box_nonce' ] ) ) {
        // $old = get_post_meta( $post_id, 'page_style', true );
        $new = $_POST[ 'page_style' ];
        // if ( isset( $new ) && $new !== $old ) {
            update_post_meta( $post_id, 'page_style', $new );
        // } 
        // elseif ( '' === $new && $old ) {
        //     delete_post_meta( $post_id, 'page_style', $old );
        // }
    }
}
add_action( 'save_post', 'save_page_style_meta' );

// meta tag
function add_meta_tag_meta_box() {
    $screen = [ 'page', 'post' ]; // choose 'post' or 'page'
    add_meta_box( 
        'meta_tag_meta_box', // $id
        __( 'Meta Data', 'bsx-wordpress' ), // $title
        'show_meta_tag_meta_box', // $callback
        $screen, // $screen
        'side', // $context, choose 'normal' or 'side')
        'high', // $priority
        null 
    );
}
add_action( 'add_meta_boxes', 'add_meta_tag_meta_box' );

function show_meta_tag_meta_box() {
    global $post;
    $meta = get_post_meta( $post->ID, 'meta_tag', true ); 
    ?>
        <input type="hidden" name="meta_tag_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

        <p>
            <label for="meta_tag[meta_title]"><?php echo __( 'Title', 'bsx-wordpress' ); ?> (<b data-bsxui="char-counter">0</b> / 40&hellip;60)</label>
            <br>
            <textarea data-bsxui="counting-input" name="meta_tag[meta_title]" id="meta_tag[meta_title]" rows="2" cols="30" style="width:100%;"><?php if ( isset( $meta['meta_title'] ) ) { echo $meta['meta_title']; } ?></textarea>
        </p>
        <p>
            <label for="meta_tag[meta_title]"><?php echo __( 'Description', 'bsx-wordpress' ); ?> (<b data-bsxui="char-counter">0</b> / 150&hellip;160)</label>
            <br>
            <textarea data-bsxui="counting-input" name="meta_tag[meta_description]" id="meta_tag[meta_description]" rows="5" cols="30" style="width:100%;"><?php if ( isset( $meta['meta_description'] ) ) { echo $meta['meta_description']; } ?></textarea>
        </p>

        <script>
( function( $ ) {
    $( document.currentScript ).parent().find( '[ data-bsxui="counting-input"]' ).each( function() {
        $input = $( this );
        $.fn.updateCount = function() {
            $input = $( this );
            $counter = $input.parent().find( '[data-bsxui="char-counter"]' );
            var charCount = $input.val().length;
            $counter.html( charCount );
        }
        $input.updateCount();
        $input.on( 'change input paste keyup', function() {
            $( this ).updateCount();
        } );
    } );
} )( jQuery );
        </script>
    <?php 
}
function save_meta_tag_meta( $post_id ) {
    // verify nonce
    if ( isset( $_POST[ 'meta_tag_meta_box_nonce' ] ) && ! wp_verify_nonce( $_POST[ 'meta_tag_meta_box_nonce' ], basename(__FILE__) ) ) {
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // check permissions
    if ( isset( $_POST[ 'post_type' ] ) && 'page' === $_POST[ 'post_type' ] ) {
        if ( !current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        } 
        elseif ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }
    if ( isset( $_POST[ 'meta_tag' ] ) ) {
        $old = get_post_meta( $post_id, 'meta_tag', true );
        $new = $_POST[ 'meta_tag' ];
        if ( $new && $new !== $old ) {
            update_post_meta( $post_id, 'meta_tag', $new );
        } 
        elseif ( '' === $new && $old ) {
            delete_post_meta( $post_id, 'meta_tag', $old );
        }
    }
}
add_action( 'save_post', 'save_meta_tag_meta' );


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



// REST ROUTES

/**
 * callback function for routes endpoint
 */
function bsx_example_get_endpoint( $request ) {
    $products = array(
        '1' => 'I am product 1',
        '2' => 'I am product 2',
        '3' => 'I am product 3',
    );

    $id = ( string ) $request[ 'id' ];
 
    if ( isset( $products[ $id ] ) ) {
        $product = $products[ $id ];
        return rest_ensure_response( $product );
    } 
    else {
        // error 404
        return new WP_Error( 'rest_example_invalid', esc_html__( 'The product does not exist.', 'bsx-wordpress' ), array( 'status' => 404 ) );
    }
 
    // error 500
    return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'bsx-wordpress' ), array( 'status' => 500 ) );
}

function bsx_mailer_post_endpoint( $request ) {

    $response = '';
    // $response .= var_dump( $request );

    $sanitized_values = array();
    $validation_ok = true;

    foreach ( $_POST as $key => $value ) {
        // extract type for validation from input name `mytype__myname`
        $split_key = explode( '__', $key);
        $name = $split_key[ 0 ];
        $type = $split_key[ 1 ];
        $required = ( isset( $split_key[ 2 ] ) && $split_key[ 2 ] === 'r') ? true : false;

        $value = trim( $value );

        // sanitize and validate
        if ( $type === 'email' ) {
            $value = filter_var( $value, FILTER_SANITIZE_EMAIL );
            if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
                $validation_ok = false;
            }
        }
        if ( $type === 'number' ) {
            $value = intval( $value );
            if ( ! is_numeric( $value ) ) {
                $validation_ok = false;
            }
        }

        // validate others
        if ( $required ) {
            if ( empty( $value ) ) {
                $validation_ok = false;
            }
        }

        // add to $values
        $sanitized_values[ $name ] = $value;


        // $response .= $name . ' (' . $type . ', required: ' . $required . '): ' . $value . '<br>';
    }

    $response .= 'SANITIZED OUTPUT:<br>';
    foreach ( $sanitized_values as $key => $value ) {
        $response .= $key . ': ' . $value . '<br>';
    }

 
    if ( $validation_ok ) {
        return rest_ensure_response( $response );
    } 
    else {
        // error 404
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your request does not exist.', 'bsx-wordpress' ), array( 'status' => 404 ) );
    }
 
    // error 500
    return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'bsx-wordpress' ), array( 'status' => 500 ) );
}


/**
 * register routes for endpoint
 *
 * read more here: https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/
 */

function bsx_register_rest_routes() {
    // call (1 is id): http://localhost/wordpress-testing/wp-json/bsx/v1/example/1
    register_rest_route( 'bsx/v1', '/example/(?P<id>[\d]+)', array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'bsx_example_get_endpoint',
    ) );
    // call with POST data: http://localhost/wordpress-testing/wp-json/bsx/v1/mailer/
    register_rest_route( 'bsx/v1', '/mailer/', array(
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => 'bsx_mailer_post_endpoint',
    ) );
}
 
add_action( 'rest_api_init', 'bsx_register_rest_routes' );

