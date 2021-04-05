<?php
get_header();
?>

<main id="main">

  <section class="container below-navbar-content mb-5">

    <div class="display-1 text-primary font-weight-bold">404</div>

    <h1><?php echo __( 'Not found', 'bsx-wordpress' ); ?></h1>

    <div class="lead my-4">
      <p><?php echo __( 'It looks like nothing was found at this location. Maybe try a search?', 'bsx-wordpress' ); ?></p>
    </div>

    <div>
      <?php get_search_form(); ?>
    </div>

  </section>
  <!-- /section (h1) -->

</main>

<?php
get_footer();
