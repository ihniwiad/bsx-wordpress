<?php

function bsx_theme_forms_add_menu() {
 
    global $bsx_theme_forms_page;
    global $theme_forms_list_table;

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


    // hook screen options
    add_action( 'load-' . $bsx_theme_forms_page, 'bsx_add_custom_screen_option' );

}
add_action( 'admin_menu', 'bsx_theme_forms_add_menu' );


function bsx_theme_form_show_entries() { 
    global $functions_file_basename;
    global $theme_forms_list_table;

    ?>
        <div class="wrap">
            <?php

                // TODO: check if list page, prepare items & show screen options only if list page

                $theme_forms_list_table->prepare_items();

                Theme_Forms_Admin_Pages::init();
            ?>
        </div>
    <?php
}


// add screen options to page
function bsx_add_custom_screen_option() {
    global $theme_forms_list_table;
    $option = 'per_page';
    $args = array(
        'label' => esc_html__( 'Form Entries', 'bsx-wordpress' ),
        'default' => 10,
        'option' => 'theme_forms_entries_per_page'
    );
    add_screen_option( $option, $args );
    // add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );

    $theme_forms_list_table = new Theme_Forms_List_Table();
}


// save screen options
function bsx_save_custom_screen_option( $status, $option, $value ) {
    return $value;
}
add_filter( 'set-screen-option', 'bsx_save_custom_screen_option', 10, 3 );



