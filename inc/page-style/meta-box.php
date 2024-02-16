<?php

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
    global $functions_file_basename;
    $meta = get_post_meta( $post->ID, 'page_style', true ); 
    ?>
        <input type="hidden" name="page_style_meta_box_nonce" value="<?php echo wp_create_nonce( $functions_file_basename ); ?>">
        <p>
            <label>
                <input type="checkbox" name="page_style[add_page_top_space]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'add_page_top_space' ] ) && $meta[ 'add_page_top_space' ] == 1 ) echo 'checked' ?>><?php _e( 'Add space on Page top', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="page_style[wrap_page_with_container]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'wrap_page_with_container' ] ) && $meta[ 'wrap_page_with_container' ] == 1 ) echo 'checked' ?>><?php _e( 'Wrap Page with Container', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="page_style[show_breadcrumb]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'show_breadcrumb' ] ) && $meta[ 'show_breadcrumb' ] == 1 ) echo 'checked' ?>><?php _e( 'Show Breadcrumb', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="page_style[not_show_bottom_banner]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'not_show_bottom_banner' ] ) && $meta[ 'not_show_bottom_banner' ] == 1 ) echo 'checked' ?>><?php _e( 'Do not show bottom banner', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="page_style[page_is_gallery_parent]" value="1" <?php if ( is_array( $meta ) && isset( $meta[ 'page_is_gallery_parent' ] ) && $meta[ 'page_is_gallery_parent' ] == 1 ) echo 'checked' ?>><?php _e( 'Page is gallery parent', 'bsx-wordpress' ); ?>
            </label>
        </p>
        <p>
            <label for="page_style[cpt_slug]">
                <?php _e( 'Assign Custom Post Type (optional)', 'bsx-wordpress' ); ?>
            </label>
            <select name="page_style[cpt_slug]" id="page_style[cpt_slug]">
                <option value=""><?php _e( '– unset –', 'bsx-wordpress' ); ?></option>
                <?php
                    $custom_post_types = get_post_types( [ 
                        '_builtin' => false,
                        'show_ui' => true,
                        'public' => true,
                    ] );
                    // echo '<pre style="width: 100%; overflow: auto;">';
                    // print_r( $custom_post_types );
                    // echo '</pre>';
                    foreach ( $custom_post_types as $key => $value ) {
                        echo '<option value="' . $value . '" ' . ( ( isset( $meta[ 'cpt_slug' ] ) ) ? selected( $meta[ 'cpt_slug' ], $value ) : '' ) . ' >' . $value . '</option>';
                    }
                ?>
            </select>
        </p>
    <?php 
}
function save_page_style_meta( $post_id ) {
    global $functions_file_basename;
    // verify nonce
    if ( isset( $_POST[ 'page_style_meta_box_nonce' ] ) && ! wp_verify_nonce( $_POST[ 'page_style_meta_box_nonce' ], $functions_file_basename ) ) {
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