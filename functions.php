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
 * REQUIRED FILES
 * Include required files.
 */

// Custom page walker.
require get_template_directory() . '/src/libs/nav/classes/class-bsx-walker-page.php';
require get_template_directory() . '/src/libs/nav/classes/class-bsx-walker-nav-menu.php';
// require get_template_directory() . '/classes/include-classes.php';


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
            'footer-bottom-menu' => __( 'Footer Menu', 'bsx-wordpress' )
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
    }
    $atts[ 'class' ] .= $class;
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
 * add Open Graph Meta Tags
 */

function meta_og() {
    global $post;

    if ( is_single() ) {
        if( has_post_thumbnail( $post->ID ) ) {
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
        } 
        $excerpt = strip_tags( $post->post_content );
        $excerpt_more = '';
        if ( strlen($excerpt ) > 155) {
            $excerpt = substr( $excerpt,0,155 );
            $excerpt_more = ' ...';
        }
        $excerpt = str_replace( '"', '', $excerpt );
        $excerpt = str_replace( "'", '', $excerpt );
        $excerptwords = preg_split( '/[\n\r\t ]+/', $excerpt, -1, PREG_SPLIT_NO_EMPTY );
        array_pop( $excerptwords );
        $excerpt = implode( ' ', $excerptwords ) . $excerpt_more;
        ?>
<meta name="author" content="Your Name">
<meta name="description" content="<?php echo $excerpt; ?>">
<meta property="og:title" content="<?php echo the_title(); ?>">
<meta property="og:description" content="<?php echo $excerpt; ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?php echo the_permalink(); ?>">
<meta property="og:site_name" content="Your Site Name">
<meta property="og:image" content="<?php echo $img_src[0]; ?>">
<?php
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

    // register each field
    register_setting(
        'custom-settings-contact', // option group
        'owner-name' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'phone' // option name
    );
    register_setting(
        'custom-settings-contact', // option group
        'mail' // option name
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

    // register each field
    register_setting(
        'custom-settings-social-media', // option group
        'facebook' // option name
    );
    register_setting(
        'custom-settings-social-media', // option group
        'twitter' // option name
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
        'footer_phone_mail_show', // id
        __( 'Show Phone & Email in footer', 'bsx-wordpress' ), // title
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
    $screen = "page"; // choose 'post' or 'page'
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
    if ( isset( $_POST[ 'page_style' ] ) ) {
        $old = get_post_meta( $post_id, 'page_style', true );
        $new = $_POST[ 'page_style' ];
        if ( $new && $new !== $old ) {
            update_post_meta( $post_id, 'page_style', $new );
        } 
        elseif ( '' === $new && $old ) {
            delete_post_meta( $post_id, 'page_style', $old );
        }
    }
}
add_action( 'save_post', 'save_page_style_meta' );
