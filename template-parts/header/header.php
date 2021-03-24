<?php 
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
                $logo = file_get_contents( $logoPath );
                echo $logo;
            ?>
        </a>

        <div class="bsx-appnav-navbar-collapse" id="navbarNavDropdown" data-tg="navbar-collapse">
            
            <ul class="bsx-appnav-navbar-nav bsx-main-navbar-nav" aria-labelledby="toggle-navbar-collapse">

                <!-- Bsx_Walker_Page -->
                <?php 
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


