<?php 

$reusable_blocks_menu_icon = file_get_contents( __DIR__ . '/reusable-blocks-menu-icon.svg' );


/**
 * Add menu link to show reusable blocks list
 */

function add_menu_link_reusable_blocks_list() {
    global $reusable_blocks_menu_icon;
    // add to main menu (level 1)
    add_menu_page( 
        __( 'Reusable Blocks', 'bsx-wordpress' ), // page title
        __( 'Reusable Blocks', 'bsx-wordpress' ), // menu title
        'manage_options', // capability
        get_bloginfo( 'url' ) . '/wp-admin/edit.php?post_type=wp_block', // menu_slug
        null, // function to show related content
        // 'dashicons-search',
        'data:image/svg+xml;base64,' . base64_encode( $reusable_blocks_menu_icon ), // icon url
        29 // position
    );
}
add_action( 'admin_menu', 'add_menu_link_reusable_blocks_list' );