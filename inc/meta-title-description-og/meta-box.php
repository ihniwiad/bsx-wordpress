<?php

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

// Include jQuery to admin only (used in `show_meta_tag_meta_box()`)
add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_script( 'jquery' );
} );

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
if ( window.jQuery ) {  
    ( function( $ ) {
        $( document.currentScript ).parent().find( '[data-bsxui="counting-input"]' ).each( function() {
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
}
else {
    console.error( 'Missing jQuery plugin.' );
}
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


// add meta boxes for title & discription, use title & excerpt as fallback

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