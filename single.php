<?php 
	get_header(); 

	// increment view counter
	countPostViews( get_the_ID() );
?>

<div data-id="single">

    <div class="container-fluid bg-light below-navbar-content mb-lg-4">
    	<div class="text-column text-center pt-2 pb-3">
	        <?php
	            // breadcrumb
	        	if ( class_exists( 'Bsx_Breadcrumb' ) && method_exists( 'Bsx_Breadcrumb', 'print' ) ) {
                	( new Bsx_Breadcrumb )->print();
	        	}
	        ?>
	    </div>
    </div>

	<?php
		if ( have_posts() ) : while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content/content-single', get_post_format() );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; endif;
	?>

    <?php

        // popular posts
        get_template_part( 'template-parts/banner/popular-posts' );


        // apply banner
        ?>
            <div class="mb-n-footer-space">
                <?php
                    echo BSXWP_Banner_Helper_Fn::getBannerHtml( 'blog' );
                ?>
            </div>
        <?php

    ?>

</div>
<!-- /[data-id="single"] -->

<?php get_footer(); ?>

<?php /*

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

*/