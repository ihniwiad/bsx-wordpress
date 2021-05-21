<?php get_header(); ?>

<main id="main" data-id="page">

	<?php
		if ( have_posts() ) : while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content/content-page', get_post_format() );

		endwhile; endif;
	?>

</main>

<?php get_footer(); ?>