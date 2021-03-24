<?php
  
    $meta = get_post_meta( get_the_ID(), 'page_style', true );
    $add_page_top_space = isset( $meta[ 'add_page_top_space' ] ) && $meta[ 'add_page_top_space' ];
    $wrap_page_with_container = isset( $meta[ 'wrap_page_with_container' ] ) && $meta[ 'wrap_page_with_container' ];

    $class_names = array();
    if ( $add_page_top_space ) {
        $class_names[] = 'below-navbar-content';
    }
    if ( $wrap_page_with_container ) {
        $class_names[] = 'container';
    }
?>
<div class="<?php echo join( ' ', $class_names ); ?>" data-id="content-page">
 
    <?php the_content(); ?>

</div>
<!-- /[data-id="content-page"| -->