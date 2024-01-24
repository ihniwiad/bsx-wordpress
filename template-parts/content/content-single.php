<article class="" data-id="content-single">

    <div class="container-fluid container-lg of-hidden px-0">
        <div class="row justify-content-around">
            <div class="col-12 col-lg-10">
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
        </div>
    </div>

    <div class="container-fluid">
        <div class="text-column">

            <h1 class="blog-post-title my-4"><?php the_title(); ?></h1>

            <header class="d-flex align-items-center my-4">
                
                <?php
                    // get_avatar_url( mixed $id_or_email, array $args = null )
                    $user_id = get_the_author_meta( 'ID' );
                    $author_nickname = get_the_author_meta( 'nickname' );

                    if ( is_numeric( $author_nickname ) ) :

                        // load lazy
                        $attachment_id = $author_nickname; 
                        $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                        $image_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail' ); // returns array( $url, $width, $height )

                        if ( $image_attributes ) {
                            $img_data = array(
                              'img' => array(
                                'url' => $image_attributes[ 0 ],
                                'width' => 50, // intval( $image_attributes[ 1 ] ) / 2,
                                'height' => 50, // intval( $image_attributes[ 2 ] ) / 2,
                                'alt' => $alt,
                                'class_name' => 'rounded-circle'
                              ),
                              'figure' => array(
                                'class_name' => 'mb-0 mr-2'
                              )
                            );

                            if ( class_exists( 'LazyImg' ) && method_exists( 'LazyImg', 'print' ) ) {
                                ( new LazyImg( $img_data ) )->print();
                            }
                        }
                    ?>
                <?php endif; ?>
                <div>
                    <?php echo esc_html__( 'from', 'bsx-wordpress' ); ?> <strong><?php the_author(); ?></strong> – <?php the_date(); ?>
                </div>
            </header>

            <?php the_content(); ?>

        </div>
        <!-- /.text-column -->
    </div>

    <div class="container">
        <div class="line-label mt-5 mb-2">
            <span><?php echo esc_html__( 'Share', 'bsx-wordpress' ); ?></span>
        </div>

        <?php
            $encoded_blog_url = rawurlencode( get_the_permalink() );
            $encoded_blog_title = rawurlencode( get_the_title() );
        ?>

        <ul class="list-inline text-center mb-5">

            <?php
                $title = esc_html__( 'Share on', 'bsx-wordpress' ) . ' X (Twitter)';
                $url = '//twitter.com/share?url=' . $encoded_blog_url . '&amp;text=' . $encoded_blog_title;
            ?>
            <li class="list-inline-item mx-0">
                <a class="fa-stack fa-2x hover-text-twitter" href="<?php echo $url; ?>" rel="nofollow" target="_blank" aria-label="<?php echo $title; ?>" title="<?php echo $title; ?>">
                    <i class="fa fa-circle fa-stack-2x" aria-hidden="true"></i>
                    <i class="fab fa-x-twitter fa-stack-1x fa-inverse" aria-hidden="true"></i>
                </a>
            </li>
            <?php
                $title = esc_html__( 'Share on', 'bsx-wordpress' ) . ' Facebook';
                $url = '//facebook.com/sharer.php?u=' . $encoded_blog_url;
            ?>
            <li class="list-inline-item mx-0">
                <a class="fa-stack fa-2x hover-text-facebook" href="<?php echo $url; ?>" rel="nofollow" target="_blank" aria-label="<?php echo $title; ?>" title="<?php echo $title; ?>">
                    <i class="fa fa-circle fa-stack-2x" aria-hidden="true"></i>
                    <i class="fab fa-facebook fa-stack-1x fa-inverse" aria-hidden="true"></i>
                </a>
            </li>
            <?php
                $title = esc_html__( 'Share via Email', 'bsx-wordpress' );
                $url = 'mailto:?subject=' . $encoded_blog_title . '&amp;body=' . $encoded_blog_url;
            ?>
            <li class="list-inline-item mx-0">
                <a class="fa-stack fa-2x hover-text-dark" href="<?php echo $url; ?>" aria-label="<?php echo $title; ?>" title="<?php echo $title; ?>">
                    <i class="fa fa-circle fa-stack-2x" aria-hidden="true"></i>
                    <i class="fas fa-envelope fa-stack-1x fa-inverse" aria-hidden="true"></i>
                </a>
            </li>
                        
        </ul>


    </div>



</article>
<!-- /[data-id="content-single"] -->

<?php /*
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
*/