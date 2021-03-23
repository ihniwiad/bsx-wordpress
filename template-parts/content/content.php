<div class="blog-post" data-id="content">

    <h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

    <p class="blog-post-meta">
        <?php the_date(); ?> by 
        <a href="#"><?php the_author(); ?></a> 
        &bull; 
        <a href="<?php comments_link(); ?>">
            <?php 
                printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'bxs-wordpress' ), number_format_i18n( get_comments_number() ) ); 
            ?>
        </a>
    </p>

    <?php if ( has_post_thumbnail() ) {?>

        <div class="row">
            <div class="col-3 col-md-2">
                <?php the_post_thumbnail( 'thumbnail', [ 'class' => 'img-fluid' ] ); ?>
            </div>
            <div class="col-9 col-md-10">
                <?php the_excerpt(); ?>
            </div>
        </div>
        
    <?php } else { ?>

        <?php the_excerpt(); ?>

    <?php } ?>

</div>
<!-- /[data-id="content"] -->