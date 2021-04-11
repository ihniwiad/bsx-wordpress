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
            include 'src/libs/form/example.php';
        ?>
    </div>

    </section>
    <!-- /section (h1) -->

</main>

<?php get_footer(); ?>