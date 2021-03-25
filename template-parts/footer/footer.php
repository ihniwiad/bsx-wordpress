<?php 
?>  

<!-- FOOTER -->

<footer class="page-footer" data-tg="sticky-container-above">

    <hr>

    <div class="container">

        <div class="text-center my-4">
            <a href="<?php echo get_bloginfo( 'url' ) . '/'; ?>">
                <!-- inline svg logo -->
        <?php 
            $logo = file_get_contents( $logoPath );
            echo $logo;
        ?>
            </a>
        </div>

        <div class="row">

            <div class="col-6 col-md-3">
                <div>
                    <strong>Amet orci</strong>
                </div>
                <hr class="my-1">
                <ul class="list-unstyled">
                    <li>
                        <a class="footer-link" href="#">Eget eros</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Faucibus</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Tincidunt</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Duis leo</a>
                    </li>
                </ul>
            </div>

            <div class="col-6 col-md-3">
                <div>
                    <strong>Sed fringilla</strong>
                </div>
                <hr class="my-1">
                <ul class="list-unstyled">
                    <li>
                        <a class="footer-link" href="#">Mauris sit </a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Amet nibh</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Donec sodales</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Sagittis magna</a>
                    </li>
                </ul>
            </div>

            <div class="col-6 col-md-3">
                <div>
                    <strong>Consequat</strong>
                </div>
                <hr class="my-1">
                <ul class="list-unstyled">
                    <li>
                        <a class="footer-link" href="#">Leo eget</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Bibendum sodales</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Augue velit</a>
                    </li>
                </ul>
            </div>

            <div class="col-6 col-md-3">
                <div>
                    <strong>Cursus nunc</strong>
                </div>
                <hr class="my-1">
                <ul class="list-unstyled">
                    <li>
                        <a class="footer-link" href="#">Aenean commodo</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Ligula eget </a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Dolor</a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Aenean massa. </a>
                    </li>
                    <li>
                        <a class="footer-link" href="#">Cum sociis</a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="text-center">
            <ul class="list-inline mb-0">

                <?php 
                    $footer_phone_mail_show = get_option( 'footer_phone_mail_show' );
                    $phone = get_option( 'phone' );
                    $mail = get_option( 'mail' );
                ?>
                <?php if ( $footer_phone_mail_show ) { ?>
                    <?php if ( $phone ) { ?>
                        <?php
                            // remove unwanted chars
                            $phoneHref = $phone;
                            $patterns = $phoneHrefRemovePatterns;
                            foreach ( $patterns as $pattern ) {
                                $phoneHref = preg_replace( $pattern, '', $phoneHref );
                            }
                        ?>
                        <li class="list-inline-item">
                            <a class="footer-icon-link hover-text-primary" href="tel:<?php echo $phoneHref; ?>"><i class="fa fa-phone"></i><span class="sr-only">Telefon</span></a>
                        </li>
                    <?php } ?>
                    <?php if ( $mail ) { ?>
                        <?php
                            // make attribute from mail address
                            $atPos = strpos( $mail, "@" );
                            $dotPos = strpos( $mail, "." );

                            $name = substr( $mail, 0, $atPos );
                            $domain = substr( $mail, $atPos + 1, $dotPos - $atPos - 1 );
                            $extension = substr( $mail, $dotPos + 1 );
                        ?>
                        <li class="list-inline-item">
                            <a class="footer-icon-link hover-text-primary" data-fn="create-mt" data-mt-n="<?php echo $name; ?>" data-mt-d="<?php echo $domain; ?>" data-mt-s="<?php echo $extension; ?>"><i class="fa fa-envelope"></i><span class="sr-only">E-Mail</span></a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <?php
                    $social_media_list = array(
                        array( 'id' => 'facebook', 'title' => 'Facebook', 'icon' => 'facebook' ),
                        array( 'id' => 'twitter', 'title' => 'Twitter', 'icon' => 'twitter' ),
                        array( 'id' => 'instagram', 'title' => 'Instagram', 'icon' => 'instagram' ),
                        array( 'id' => 'googleplus', 'title' => 'Google Plus', 'icon' => 'google-plus' ),
                        array( 'id' => 'xing', 'title' => 'Xing', 'icon' => 'xing' ),
                    );

                    $social_media_colors_use = get_option( 'social_media_colors_use' );

                    foreach( $social_media_list as $item ) {
                        $social_media_href = get_option( $item[ 'id' ] );
                        // print( 'TEST ' . $item[ 'id' ] );
                        $hover_class_name = ( $social_media_colors_use ) ? 'hover-text-' . $item[ 'id' ] : 'hover-text-primary';
                        if ( $social_media_href ) {
                            ?>
                                <li class="list-inline-item">
                                    <a class="footer-icon-link <?php echo $hover_class_name; ?>" href="<?php echo $social_media_href; ?>" target="_blank"><i class="fa fa-<?php echo $item[ 'icon' ]; ?>"></i><span class="sr-only"><?php echo $item[ 'title' ]; ?></span></a>
                                </li>
                            <?php
                        }
                    }
                ?>

            </ul>
        </div>

        <hr class="my-2">

        <div class="row small">
            <div class="col-sm mb-1">
                &copy; Copyright <?php echo date_format( date_create(), 'Y' ); ?> <a class="footer-link" href="<?php echo get_bloginfo( 'url' ) . '/'; ?>"><?php echo get_bloginfo( 'name' ); ?></a>
            </div>
            <nav class="col-sm text-sm-right mb-1">
                <?php


                    echo '<!-- footer bottom nav -->';
                    wp_nav_menu( 
                        array( 
                            'theme_location' => 'footer-bottom-menu',
                            'menu' => '',
                            'container' => '',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                            'menu_class' => 'bsx-footer-bottom-nav list-unstyled',
                            'menu_id' => '',
                            'before' => '', // in <li> before <a>
                            'after' => '', // in <li> after <a>
                            'link_before' => '', // in <a> before text
                            'link_after' => '', // in <a> after text,
                            'add_li_class' => 'footer-bottom-menu-li', // custom filter add_additional_class_on_li(), see functions.php 
                            'add_a_class' => 'footer-link' // custom filteradd_additional_class_on_a(), see functions.php 
                        ) 
                    ); 


                    // $footer_links_ids = array( '0', '1', '2' );
                    // $print_html = '';
                    // foreach ( $footer_links_ids as $id ) {
                    //     $title = get_option( 'footer_link_' . $id . '_title' );
                    //     $url = get_option( 'footer_link_' . $id . '_url' );
                    //     if ( $title && $url ) {
                    //         if ( $print_html != '' ) {
                    //             // is not first filled item (1st and 3rd item may be filled with 2nd empty)
                    //             $print_html .= '&ensp;|&ensp;';
                    //         }
                    //         $print_html .= '<a class="footer-link" href="' . $url . '">' . $title . '</a>';
                    //     }
                    // }
                    // print( $print_html );
                ?>
            </nav>
        </div>

    </div>
    
</footer>