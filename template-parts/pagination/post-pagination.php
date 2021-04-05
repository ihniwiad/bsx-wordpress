<?php

$pagination = get_the_posts_pagination( array(
    'mid_size'           => 5,
    'prev_text'          => __( 'Previous Page', 'bsx-wordpress' ),
    'next_text'          => __( 'Next Page', 'bsx-wordpress' ),
    'screen_reader_text' => __( 'Blog Pages Navigation', 'bsx-wordpress' ),
    'aria_label'         => __( 'Blog Pages Navigation', 'bsx-wordpress' ),
    'class'              => 'my-5',
) );
// use bootstrap class names
$pagination = str_replace( '<h2 ', '<header ', $pagination );
$pagination = str_replace( '</h2>', '</header>', $pagination );
$pagination = str_replace( 'nav-links', 'text-center', $pagination );
$pagination = str_replace( 'page-numbers', 'btn btn-outline-primary', $pagination );
$pagination = str_replace( 'current', 'active', $pagination );
$pagination = str_replace( 'screen-reader-text', 'sr-only', $pagination );
echo $pagination;

// $prev_link = get_next_posts_link( __( 'Older Posts', 'bsx-wordpress' ) );
// if ( $prev_link ) {
//     echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $prev_link );
// }

// $next_link = get_previous_posts_link( __( 'Newer Posts', 'bsx-wordpress' ) );
// if ( $next_link ) {
//     echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $next_link );
// }