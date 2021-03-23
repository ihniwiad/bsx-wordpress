<?php get_header(); ?>

<div data-id="single">

	<?php
		if ( have_posts() ) : while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content/content-single', get_post_format() );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; endif;
	?>

</div>
<!-- /[data-id="single"] -->

<?php get_footer(); ?>