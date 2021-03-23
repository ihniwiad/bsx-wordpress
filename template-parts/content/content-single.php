<div class="container below-navbar-content" data-id="content-single">

    <h2 class="blog-post-title"><?php the_title(); ?></h2>

    <p class="blog-post-meta"><?php the_date(); ?> by <a href="#"><?php the_author(); ?></a></p>

    <div class="p">
        <?php if ( has_post_thumbnail() ) {
            the_post_thumbnail( 'large', [ 'class' => 'img-fluid' ] );
        } ?>
    </div>

    <?php the_content(); ?>

</div>
<!-- /[data-id="content-single"] -->