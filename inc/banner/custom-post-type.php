<?php

/**
 * custom post type banner
 */

function create_banner_custom_post() {
    register_post_type( 'banner-cpt', // my-cpt
        array(
        'labels' => array(
            'name' => __( 'Banner', 'bsx-wordpress' ),
            'singular_name' => __( 'Banner', 'bsx-wordpress' ),
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array( 'slug' => 'banner' ),
        'show_in_rest' => true,
        'supports' => array(
            'title',
            'editor',
            'custom-fields'
        ),
        'menu_position' => 35,
        'menu_icon' => 'dashicons-button',
    ) );
}
add_action( 'init', 'create_banner_custom_post' );





