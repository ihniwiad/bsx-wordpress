<?php
  
    $meta = get_post_meta( get_the_ID(), 'page_style', true );
    $add_page_top_space = isset( $meta[ 'add_page_top_space' ] ) && $meta[ 'add_page_top_space' ];
    $wrap_page_with_container = isset( $meta[ 'wrap_page_with_container' ] ) && $meta[ 'wrap_page_with_container' ];
    $page_is_gallery_parent = isset( $meta[ 'page_is_gallery_parent' ] ) && $meta[ 'page_is_gallery_parent' ];

    $class_names = array();
    if ( $add_page_top_space ) {
        $class_names[] = 'below-navbar-content';
    }
    if ( $wrap_page_with_container ) {
        $class_names[] = 'container';
    }

    $atts = array();
    if ( $page_is_gallery_parent ) {
        $atts[] = 'data-fn="photoswipe"';
    }
?>
<div class="<?php echo join( ' ', $class_names ); ?>"<?php echo ! empty( $atts ) ? ' ' . join( ' ', $atts ) : ''; ?> data-id="content-page">
 
    <?php the_content(); ?>

</div>
<!-- /[data-id="content-page"| -->