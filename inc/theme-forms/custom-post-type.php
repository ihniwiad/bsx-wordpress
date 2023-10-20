<?php

function bsx_theme_forms_create_custom_post() {
    register_post_type( 'theme-forms-cpt', // my-custom-post
        array(
        'labels' => array(
            'name' => __( 'Theme Forms', 'bsx-wordpress' ),
            'singular_name' => __( 'Theme Form', 'bsx-wordpress' ),
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'theme-forms' ),
        'show_in_rest' => false,
        'supports' => array(
            'title',
            // 'editor',
            // 'custom-fields',
            'page-attributes' // position etc.
        ),
        'menu_position' => 3,
        'menu_icon' => 'dashicons-email',
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        // 'taxonomies'  => array( 'category' ),
    ) );
}
add_action( 'init', 'bsx_theme_forms_create_custom_post' );


