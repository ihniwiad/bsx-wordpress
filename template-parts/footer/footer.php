<?php 
global $phoneHrefRemovePatterns;
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
            <?php
                // make n cols
                $cols_count = 4;

                $conf_cols_count = get_option( 'footer_columns_count' );
                if ( isset( $conf_cols_count ) && $conf_cols_count != '' ) {
                    $cols_count = number_format( $conf_cols_count );
                }

                $col_class_names = 'col-12 col-sm-6 col-md';
                switch ( $cols_count ) {
                    case 4:
                        $col_class_names = 'col-6 col-md-3';
                        break;
                    case 3:
                        $col_class_names = 'col-12 col-md-4';
                        break;
                }

                for( $i = 0; $i < $cols_count; $i++ ) {
                    ?>
                        <div class="<?php echo $col_class_names; ?>">
                            <div>
                                <?php
                                    $menu_name = wp_get_nav_menu_name( 'footer-column-' . ( $i + 1 ) . '-menu' );
                                    echo '<!-- Footer Column 1 Menu (' . $menu_name . ') -->';
                                ?>
                                <strong><?php echo $menu_name; ?></strong>
                            </div>
                            <hr class="my-1">
                            <?php
                                wp_nav_menu( 
                                    array( 
                                        'theme_location' => 'footer-column-' . ( $i + 1 ) . '-menu',
                                        'menu' => '',
                                        'container' => '',
                                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                        'menu_class' => 'bsx-footer-col-nav list-unstyled',
                                        'menu_id' => '',
                                        'add_li_class' => '', // custom filter add_additional_class_on_li(), see functions.php 
                                        'add_a_class' => 'footer-link' // custom filteradd_additional_class_on_a(), see functions.php 
                                    ) 
                                ); 
                            ?>
                        </div>
                    <?php
                }
            ?>

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
                &copy; Copyright <?php echo date_format( date_create(), 'Y' ); ?> <a class="footer-link" href="<?php echo get_bloginfo( 'url' ) . '/'; ?>"><?php echo ( get_option( 'owner-name' ) ) ? get_option( 'owner-name' ) : get_bloginfo( 'name' ); ?></a>
            </div>
            <nav class="col-sm text-sm-right mb-1">
                <?php
                    echo '<!-- Footer Bottom Menu -->';
                    wp_nav_menu( 
                        array( 
                            'theme_location' => 'footer-bottom-menu',
                            'menu' => '',
                            'container' => '',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                            'menu_class' => 'bsx-footer-bottom-nav list-unstyled',
                            'menu_id' => '',
                            'add_li_class' => 'footer-bottom-menu-li', // custom filter add_additional_class_on_li(), see functions.php 
                            'add_a_class' => 'footer-link' // custom filteradd_additional_class_on_a(), see functions.php 
                        ) 
                    ); 
                ?>
            </nav>
        </div>

    </div>
    
</footer>