<?php
/**
 * BSX WordPress Theme
 *
 * A Bootstrap 4 based Theme
 *
 * @package BSX WordPress
 * @since 1.0.0
 */


get_header(); ?>

<section>

    <div class="container below-navbar-content">

        <h1>Blog</h1>

        <div class="row" data-id="index">
            <main class="col-sm-8 blog-main" id="main">

                <?php
                    if ( have_posts() ) : 

                        while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content/content', get_post_format() );
                        endwhile;

                        get_template_part( 'template-parts/pagination/post-pagination' );

                    else:

                        get_template_part( 'template-parts/content/content-none' );

                    endif;
                ?>

            </main>
            <!-- /.blog-main -->

            <?php get_sidebar(); ?>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

</section>
<!-- /section (h1) -->

<?php get_footer();

