<?php

function bsx_theme_forms_add_menu() {
 
    global $bsx_theme_forms_page;

    // item level 1
    $bsx_theme_forms_page = add_menu_page( 
        esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // page title
        esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // menu title
        'manage_options', // capability
        'theme-form-entries', // menu_slug
        'bsx_theme_form_show_entries', // function to show related content
        'dashicons-email-alt2', // icon url
        4 // position
    );

	// add as subitem to custom post type menu (level 2)
    // add_submenu_page( 
    //     'edit.php?post_type=theme-forms-cpt', // parent_slug
    //     esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // page_title
    //     esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // menu_title
    //     'manage_options', // capability
    //     'theme-form-entries', // menu_slug, 
    //     'bsx_theme_form_show_entries', // function = '', 
    //     10 // position = null
    // );

    // function myFilterScreenOption( $keep, $option, $value ) {
    //     if ( $option === 'entries_per_page' ) {
    //         if ( $value < 0 ) {
    //             $value = 0;
    //         } elseif ( $value > 100 ) {
    //             $value = 100;
    //         }
    //     }
    //     return $value;
    // }
    // add_filter( 'set-screen-option', 'myFilterScreenOption', 11, 3 );


    add_action( 'load-' . $bsx_theme_forms_page, 'bsx_add_custom_screen_option' );

    // function test_table_set_option( $status, $option, $value ) {
    //     return $value;
    // }
    // add_filter( 'set-screen-option', 'test_table_set_option', 10, 3 );


}
add_action( 'admin_menu', 'bsx_theme_forms_add_menu' );

function bsx_theme_form_show_entries() { 
    global $functions_file_basename;
    ?>
        <div class="wrap">
            <?php
                ( new Theme_Forms_Admin_Pages )->init();
            ?>
        </div>
    <?php
}


// add screen options to page
function bsx_add_custom_screen_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Form Entries',
        'default' => 10,
        'option' => 'entries_per_page'
    );
    add_screen_option( $option, $args );
    // add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );
}






// save screen options
function bsx_save_custom_screen_option( $status, $option, $value ) {
    // if ( $option === 'entries_per_page' ) {
    //     update_option( 'entries_per_page', $value );
    // }
    // return $status;
    return $value;
}
add_filter( 'set_screen_option', 'bsx_save_custom_screen_option', 10, 3 );
