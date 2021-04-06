<div class="container below-navbar-content" data-id="content-single">

    <h2 class="blog-post-title"><?php the_title(); ?></h2>

    <p class="blog-post-meta"><?php the_date(); ?> by <a href="#"><?php the_author(); ?></a></p>

    <div class="p">
        <?php
            // load lazy
            $attachment_id = get_post_thumbnail_id( $post ); 
            $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
            $image_attributes = wp_get_attachment_image_src( $attachment_id, 'large' ); // returns array( $url, $width, $height )

            if ( $image_attributes ) {
                $img_data = array(
                  'img' => array(
                    'url' => $image_attributes[ 0 ],
                    'width' => $image_attributes[ 1 ],
                    'height' => $image_attributes[ 2 ],
                    'alt' => $alt
                  )
                );

                if ( class_exists( 'LazyImg' ) && method_exists( 'LazyImg', 'print' ) ) {
                    ( new LazyImg( $img_data ) )->print();
                }
            }
        ?>
    </div>

    <?php the_content(); ?>

</div>
<!-- /[data-id="content-single"] -->