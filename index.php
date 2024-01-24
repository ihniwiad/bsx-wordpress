<?php

get_header(); ?>

<main id="main" data-id="index">

    <section>

        <?php 
            $show_breadcrumb = true;

            // check if posts page has contents
            $posts_page_id = get_option( 'page_for_posts' );
            $post_content = get_post( $posts_page_id )->post_content;
            $post_content = do_shortcode( $post_content );
            // remove Gutenberg block comments
            $post_content = preg_replace( '/<!--(.|s)*?-->/', '', $post_content );
            if ( $post_content ) :
                // show content of posts page
                echo $post_content;

                // check if show breadcrumb (of post page) since may be already contained in page content
                $meta = get_post_meta( $posts_page_id, 'page_style', true );
                $show_breadcrumb = isset( $meta[ 'show_breadcrumb' ] ) && $meta[ 'show_breadcrumb' ];
            else :
                // show h1 fallback
                ?> 
                    <div class="container below-navbar-content">
                        <h1 class="mb-4" data-id="fallback-heading"><?php echo get_the_title( $posts_page_id ) ?></h1>
                    </div>
                <?php
            endif;
        ?>

        <div class="container">

            <?php 
                // show breadcrumb if no page for posts found or if page for posts has active page_style[ 'show_breadcrumb' ] configuration
                if ( $show_breadcrumb ) : 
                    ?>
                        <div class="mb-4">
                            <?php
                                // breadcrumb
                                if ( class_exists( 'Bsx_Breadcrumb' ) && method_exists( 'Bsx_Breadcrumb', 'print' ) ) {
                                    ( new Bsx_Breadcrumb )->print();
                                }
                            ?>
                        </div>
                    <?php 
                endif; 
            ?>

            <div class="mb-5">
              <?php get_search_form(); ?>
            </div>

            <?php
                if ( have_posts() ) : 

                    ?>

                        <section>

                            <?php
                                // change heading title if not first page of posts list (page 2 ... n)
                                $paged = get_query_var( 'paged', 1 );
                            ?>

                            <h2 class="mb-4"><?php echo ( ! $paged ? esc_html__( 'Current Posts', 'bsx-wordpress' ) : sprintf( esc_html__( 'Posts (Page %s)', 'bsx-wordpress' ), $paged ) ); ?></h2>

                            <div class="row">

                                <?php

                                    while ( have_posts() ) : 
                                        the_post();
                                        get_template_part( 'template-parts/content/content', get_post_format() );
                                    endwhile;

                                ?>

                            </div>
                            <!-- /.row -->

                            <?php
                                if ( have_posts() ) {
                                    get_template_part( 'template-parts/pagination/post-pagination' );
                                }
                            ?>

                        </section>
                        <!-- /section (h2) -->

                    <?php

                else:

                    get_template_part( 'template-parts/content/content-none' );

                endif;
            ?>

        </div>
        <!-- /.container -->

        <?php

            // popular posts
            get_template_part( 'template-parts/banner/popular-posts' );



            // banner
            $args = array(
                'post_type' => 'banner-custom-post',
                'posts_per_page' => -1,
                'order' => 'DESC'
            );

            $custom_query = new WP_Query( $args );

            while ( $custom_query->have_posts() ) : 

                $custom_query->the_post();

                $post_id = get_the_ID();
                $banner_type = '';
                $meta = get_post_meta( $post_id, 'banner', true );
                if ( isset( $meta ) && isset( $meta[ 'banner_type' ] ) ) {
                    $banner_type = $meta[ 'banner_type' ];
                }

                if ( $banner_type === 'blog' ) {
                    // remove Gutenberg block comments
                    $post_content = get_the_content();
                    $post_content = do_shortcode( $post_content );
                    $post_content = preg_replace( '/<!--(.|s)*?-->/', '', $post_content );
                    ?>
                        <div class="mb-n-footer-space">
                            <?php
                                echo $post_content;
                            ?>
                        </div>
                    <?php
                }

            endwhile;

        ?>

    </section>
    <!-- /section (h1) -->

</main>

<?php get_footer();

