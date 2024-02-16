<?php

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