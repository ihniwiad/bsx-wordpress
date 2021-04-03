<?php
?>
<nav>
    <ul class="list-unstyled row">
        <li class="col">
            <?php
                $prev_link = get_next_posts_link( __( 'Older Posts', 'bsx-wordpress' ) );
                if ( $prev_link ) {
                    echo str_replace ( 'a href', 'a class="btn btn-primary" href', $prev_link );
                }
            ?>
        </li>
        <li class="col text-right">
            <?php 
                $next_link = get_previous_posts_link( __( 'Newer Posts', 'bsx-wordpress' ) );
                if ( $next_link ) {
                    echo str_replace ( 'a href', 'a class="btn btn-primary" href', $next_link );
                }
            ?>
        </li>
    </ul>
</nav>