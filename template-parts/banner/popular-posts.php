<?php

$current_post_id = get_the_ID();
$show_posts_max = 3;

// NOTE: result will be empty as long as there is no post having meta post_views_count => 0
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'meta_key' => 'post_views_count',
    'orderby' => 'meta_value_num',
    // 'orderby' => 'title',
    'posts_per_page' => ( $show_posts_max + 1 ), // get one more to be able to exclude current post if in popupar posts
    'order' => 'DESC'
);

$custom_query = new WP_Query( $args );


if ( $custom_query->have_posts() ) :

?>

<section class="bg-light heading-inherit-text pt-5">
    <div class="container">
        <?php
            // TODO: make 2 columns, add button “Show all” right of heading 
        ?>
        <h2 class="mb-4"><?php echo esc_html__( 'Popular Posts', 'bsx-wordpress' ); ?></h2>
        <div>
            <div class="row">
                <?php

                    // TODO: exclude current post from list, get 4 posts, ignore current, stop showing after schown 3 posts
                    $shown_posts_count = 0;

                    while ( $custom_query->have_posts() && $shown_posts_count < $show_posts_max ) : 

                        $custom_query->the_post();

                        $post_id = get_the_ID();

                        // TEST
                        // echo '<!-- list post id: ' . $post_id . ' (current post id: ' . $current_post_id . ') -->';

                        $post_views_count = get_post_meta( $post_id, 'post_views_count', true );
                        // echo '<!-- $post_views_count: ' . $post_views_count . ' -->';
                        // if ( ! empty( $post_views_count ) ) {
                        //     echo '$post_views_count: ' . $post_views_count . '<br>';
                        // }
                        // else {
                        //     echo 'NO $post_views_count' . '<br>';
                        // }


                        // show only if not current post
                        if ( $post_id != $current_post_id ) {

                            get_template_part( 'template-parts/content/content', get_post_format() );

                            $shown_posts_count ++;

                        }
                        else {
                            echo '<!-- current post has been skipped in popular posts list -->';
                        }

                    endwhile;

                ?>
            </div>
        </div>
    </div>
</section>
<!-- /section (h2) -->

<?php

else :

    echo '<!-- ' . esc_html__( 'No Popular Posts available', 'bsx-wordpress' ) . ' -->';

endif;

?>
