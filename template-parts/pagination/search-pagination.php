<?php

$pagination = get_the_posts_pagination( array(
    'mid_size'           => 5,
    'prev_text'          => '<span class="fas fa-arrow-left" aria-hidden="true"></span><span class="sr-only">' . __( 'Previous Page', 'bsx-wordpress' ) . '</span>',
    'next_text'          => '<span class="fas fa-arrow-right" aria-hidden="true"></span><span class="sr-only">' . __( 'Next Page', 'bsx-wordpress' ) . '</span>',
    'screen_reader_text' => __( 'Search Pages Navigation', 'bsx-wordpress' ),
    'aria_label'         => __( 'Search Pages Navigation', 'bsx-wordpress' ),
    'class'              => 'mb-5',
) );
// use bootstrap class names
$pagination = str_replace( '<h2 ', '<header ', $pagination );
$pagination = str_replace( '</h2>', '</header>', $pagination );
$pagination = str_replace( ' role="navigation"', '', $pagination ); // remove since elem is nav, no need for role="navigation"
$pagination = str_replace( 'nav-links', 'text-center', $pagination );
$pagination = str_replace( 'page-numbers', 'btn btn-outline-primary', $pagination );
$pagination = str_replace( ' current"', ' active"', $pagination ); // do not destroy aria-active="page"
$pagination = str_replace( 'screen-reader-text', 'sr-only', $pagination );
echo $pagination;

// $next_link = get_previous_posts_link( __( 'Previous Page', 'bsx-wordpress' ) );
// if ( $next_link ) {
//     echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $next_link );
// }
// $prev_link = get_next_posts_link( __( 'Next Page', 'bsx-wordpress' ) );
// if ( $prev_link ) {
//     echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $prev_link );
// }

