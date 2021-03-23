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


// variables


// dev mode
// $isDevMode = false;
// if ( isset( $_GET[ 'dev' ] ) && $_GET[ 'dev' ] == '1' ) {
//   $isDevMode = true;
// }


// paths

// $serverName = $_SERVER[ 'SERVER_NAME' ];
// $homeUrl = get_bloginfo( 'url' ) . '/';

// $rootPath = get_bloginfo( 'template_directory' ).'/';
// $resourcesPath = 'resources/';

// $relativeAssetsPath = 'assets/';
// $assetsPath = $rootPath . $relativeAssetsPath;

// // make equal protocol
// $rootRelatedAssetsPath = explode( str_replace( 'https://', 'http://', $homeUrl ), str_replace( 'https://', 'http://', $assetsPath ) )[ 1 ];

// // get css file version using absolute file path
// $cssFileName = 'css/style.min.css';
// $cssFilePath = $rootRelatedAssetsPath . $cssFileName;
// $cssVersion = file_exists( $cssFilePath ) ? filemtime( $cssFilePath ) : 'null';

// // get js file versions
// $vendorJsFileName = 'js/vendor.min.js';
// $vendorJsFilePath = $rootRelatedAssetsPath . $vendorJsFileName;
// $vendorJsVersion = file_exists( $vendorJsFilePath ) ? filemtime( $vendorJsFilePath ) : 'null';

// $scriptsJsFileName = 'js/scripts.min.js';
// $scriptsJsFilePath = $rootRelatedAssetsPath . $scriptsJsFileName;
// $scriptsJsVersion = file_exists( $scriptsJsFilePath ) ? filemtime( $scriptsJsFilePath ) : 'null';
    

/**
 * REQUIRED FILES
 * Include required files.
 */

// Custom page walker.
require get_template_directory() . '/classes/class-bsx-walker-page.php';
// require get_template_directory() . '/classes/include-classes.php';


/**
 * WordPress titles
 */
add_theme_support( 'title-tag' );


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



// TODO: add custom global options


// TODO: add page/post meta boxes



