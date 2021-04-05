<?php
?>
<nav>
    <ul class="list-unstyled row my-5">
        <li class="col">
            <?php 
                $next_link = get_previous_posts_link( __( 'Previous Page', 'bsx-wordpress' ) );
                if ( $next_link ) {
                    echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $next_link );
                }
            ?>
        </li>
        <li class="col text-right">
            <?php
                $prev_link = get_next_posts_link( __( 'Next Page', 'bsx-wordpress' ) );
                if ( $prev_link ) {
                    echo str_replace ( 'a href', 'a class="btn btn-outline-primary" href', $prev_link );
                }
            ?>
        </li>
    </ul>
</nav>