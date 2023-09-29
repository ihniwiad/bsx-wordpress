<?php 
get_header();
?>

<main id="main" data-id="page-php-test">

    <section>

    <?php
      if ( have_posts() ) : while ( have_posts() ) : the_post();

        get_template_part( 'template-parts/content/content-page', get_post_format() );

      endwhile; endif;
    ?>

    <div class="container below-navbar-content mb-5">
        <?php

            echo '<br><b>DATABASE TEST</b>';

            // $local_time = new DateTime( 'now', new DateTimeZone( wp_timezone_string() ) );
            // echo '<br>Local time: ' . $local_time->format( 'Y-m-d H:i:s' );

            // $gmt_time = gmdate( 'Y-m-d H:i:s' );
            // echo '<br>GMT time: ' . $gmt_time;

            // global $wpdb;
            // $table = $wpdb->prefix . 'test';
            // $data = array( 
            //     'time' => current_time( 'mysql' ), 
            //     'text' => 'Use current_time( "mysql" )', 
            //     'count' => 6 );
            // $format = array( '%s', '%s','%d' );
            // $wpdb->insert( $table, $data, $format );
            // $insert_id = $wpdb->insert_id;

            // echo '<br>Succesfully inserted @ ' . $insert_id;



            /*
            FIELDS

        id BIGINT(20) AUTO_INCREMENT primary key NOT NULL,
        date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        data_gmt DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        form_id MEDIUMINT(9) NOT NULL,
        form_title TEXT NOT NULL,
        title TEXT NOT NULL,
        content LONGTEXT NOT NULL,
        status VARCHAR(30) NOT NULL,
        fields LONGTEXT NOT NULL,
        comment TEXT NOT NULL,
        ip_address VARCHAR(128) NOT NULL,
        user_agent VARCHAR(256) NOT NULL
            */




            global $wpdb;
            $table = $wpdb->prefix . 'bsx_themeforms_entries';
            $data = array( 
                'date' => current_time( 'mysql' ),
                'data_gmt' => current_time( 'mysql', 1 ),
                'form_id' => 77777,
                'form_title' => 'Test from PHP page',
                'title' => 'Some title',

                'content' => 'Some (long) content.',
                'status' => 'unread',
                'fields' => serialize( [ 'field_1' => 'foo', 'field_2' => 'bar', 'count_1' => 7 ] ),
                'comment' => '',
                'ip_address' => $_SERVER[ 'REMOTE_ADDR' ],

                'user_agent' => $_SERVER[ 'HTTP_USER_AGENT' ]
            );
            $format = array(
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',

                '%s',
                '%s',
                '%s',
                '%s',
                '%s',

                '%s'
            );
            $wpdb->insert( $table, $data, $format );
            $insert_id = $wpdb->insert_id;

            echo '<br>Succesfully inserted in ' . $table . ' @ ' . $insert_id;







            // include 'src/libs/form/example.php';
            // if ( class_exists( 'Bsx_Mail_Form' ) && method_exists( 'Bsx_Mail_Form', 'print_form' ) ) {
            //     ( new Bsx_Mail_Form )->print_form( 1 );
            // }
        ?>
    </div>

    </section>
    <!-- /section (h1) -->

</main>

<?php get_footer(); ?>