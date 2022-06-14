<?php 
global $phoneHrefRemovePatterns;
?>
<!-- bsx-appnav-navbar-scroll-toggle -->
<header class="bsx-appnav-navbar bsx-appnav-fixed-top bsx-appnav-navbar-scroll-toggle" data-fn="anchor-offset-elem" data-tg="sticky-container-below">

    <nav class="bsx-appnav-navbar-container">

        <button class="bsx-appnav-navbar-toggler" id="toggle-navbar-collapse" type="button" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" data-fn="toggle" data-fn-options="{ bodyOpenedClass: 'nav-open' }" data-fn-target="[data-tg='navbar-collapse']" data-tg="dropdown-multilevel-excluded">
            <span class="sr-only"><?php echo __( 'Menu', 'bsx-wordpress' ); ?></span>
            <i class="fa fa-navicon" aria-hidden="true"></i>
        </button>

        <a class="bsx-appnav-navbar-brand" href="<?php echo get_bloginfo( 'url' ) . '/'; ?>">
            <!-- inline svg logo -->
            <?php 
                $logo = get_option( 'logo' );
                if ( is_numeric( $logo ) ) {
                    $img_url = wp_get_attachment_url( $logo );
                    $img_meta = wp_get_attachment_metadata( $logo );
                    $width = round( intval( $img_meta[ 'width' ] ) / 2 );
                    $height = round( intval( $img_meta[ 'height' ] ) / 2 );
                    echo '<img src="' . $img_url . '" width="' . $width . '" height="' . $height . '" alt="' . __( 'Logo', 'bsx-wordpress' ) . '">';
                }
                else {
                    if ( empty( $logo ) ) {
                        $logo = file_get_contents( $logoPath );
                    }
                    echo $logo;
                }
            ?>
        </a>

        <div class="bsx-appnav-navbar-collapse" id="navbarNavDropdown" data-tg="navbar-collapse">

            <?php
                // true will show configured menu, false will list all pages as menu
                $use_menu = true;

                if ( $use_menu ) :
                    // use menu
                    echo '<!-- Primary Menu: Bsx_Walker_Nav_Menu -->';
                    wp_nav_menu( 
                        array( 
                            'theme_location' => 'primary-menu',
                            'walker' => new Bsx_Walker_Nav_Menu(),
                            'menu' => '',
                            'container' => '',
                            'items_wrap' => '<ul id="%1$s" class="%2$s" aria-labelledby="toggle-navbar-collapse">%3$s</ul>',
                            'menu_class' => 'bsx-appnav-navbar-nav bsx-main-navbar-nav',
                            'menu_id' => '',
                            'before' => '', // in <li> before <a>
                            'after' => '', // in <li> after <a>
                            'link_before' => '', // in <a> before text
                            'link_after' => '', // in <a> after text
                            'create_clickable_parent_link_child' => false, // create ”Overview“ link for each dropdown parent (in dropdown list)
                            'create_dropdown_button_besides_link' => false, // split dropdown parent link and dropdown opening button
                        ) 
                    ); 
                else :
                    // use page list instead of menu
                    ?>
                        <ul class="bsx-appnav-navbar-nav bsx-main-navbar-nav" aria-labelledby="toggle-navbar-collapse">
                            <?php 
                                echo '<!-- Primary Menu: Bsx_Walker_Page -->';
                                wp_list_pages(
                                    array(
                                        'match_menu_classes' => true,
                                        'show_sub_menu_icons' => true,
                                        'title_li' => false,
                                        'walker'   => new Bsx_Walker_Page(),
                                    )
                                );
                            ?>
                        </ul>

                    <?php 
                endif;
            ?>

        </div>

        <div class="bsx-appnav-collapse-backdrop" data-fn="remote-event" data-fn-options="{ target: '#toggle-navbar-collapse' }" data-tg="dropdown-multilevel-excluded"></div>

        <ul class="bsx-appnav-navbar-nav bsx-icon-navbar-nav bsx-allmedia-dropdown-nav">
            <li class="">
                <a id="iconnav-link-1" href="javascript:void( 0 );" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false"><i class="fa fa-phone" aria-hidden="true"></i><span class="sr-only">Telefon</span></a>
                <ul class="ul-right" aria-labelledby="iconnav-link-1">
                    <li>
                        <?php
                            $phone = get_option( 'phone' );
                            $phoneAlt = 'Please fill custom setting “Phone“ in your Theme Settings.';
                            $phoneHref = $phone;
                            $phoneHrefAlt = '#phone-missing';
                            if ( $phone ) {
                                // remove unwanted chars
                                $patterns = $phoneHrefRemovePatterns;
                                foreach ( $patterns as $pattern ) {
                                    $phoneHref = preg_replace( $pattern, '', $phoneHref );
                                }
                                print( '
                                    <a class="" href="tel:' . $phoneHref . '">' . $phone . '</a>
                                ' );
                            }
                            else {
                                print( '
                                    <a class="" href="' . $phoneHrefAlt . '">' . $phoneAlt . '</a>
                                ' );
                            }
                        ?>
                    </li>
                </ul>
            </li>
            <li class="">
                <a id="iconnav-link-2" href="javascript:void( 0 );" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false"><i class="fa fa-envelope" aria-hidden="true"></i><span class="sr-only">E-Mail</span></a>
                <ul class="ul-right" aria-labelledby="iconnav-link-2">
                    <li>
                        <?php 
                            $mail = get_option( 'mail' );
                            $mailAlt = 'Please fill custom setting “Mail“ in your Theme Settings.';
                            $mailHrefAlt = '#mail-missing';
                            if ( $mail ) {
                                // make attribute from mail address
                                $atPos = strpos( $mail, "@" );
                                $dotPos = strpos( $mail, "." );

                                $name = substr( $mail, 0, $atPos );
                                $domain = substr( $mail, $atPos + 1, $dotPos - $atPos - 1 );
                                $extension = substr( $mail, $dotPos + 1 );
                                print( '
                                    <a class="create-mt" data-fn="create-mt" data-mt-n="' . $name . '" data-mt-d="' . $domain . '" data-mt-s="' . $extension . '"></a>
                                ' );
                            }
                            else {
                                print( '
                                    <a class="" href="' . $mailHrefAlt . '">' . $mailAlt . '</a>
                                ' );
                            }
                        ?>
                    </li>
                </ul>
            </li>
        </ul>

    </nav>

</header>


