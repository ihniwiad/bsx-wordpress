<?php get_header(); ?>

<main id="main" data-id="page-php-test">

    <section>

    <?php
      if ( have_posts() ) : while ( have_posts() ) : the_post();

        get_template_part( 'template-parts/content/content-page', get_post_format() );

      endwhile; endif;
    ?>

    <div class="container my-5">
        <?php
            // include 'src/libs/form/example.php';
            if ( class_exists( 'Bsx_Mail_Form' ) && method_exists( 'Bsx_Mail_Form', 'make_form_from_template' ) ) {
                ( new Bsx_Mail_Form )->make_form_from_template( 1 );
            }
        ?>
    </div>

    </section>
    <!-- /section (h1) -->

</main>

<?php get_footer(); ?>