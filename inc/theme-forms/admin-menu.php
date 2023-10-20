<?php

function bsx_theme_forms_add_menu() {

    // item level 1
    add_menu_page( 
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
