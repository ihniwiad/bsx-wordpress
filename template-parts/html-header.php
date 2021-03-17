<?php 

?>
<!-- bsx-appnav-navbar-scroll-toggle -->
<header class="bsx-appnav-navbar bsx-appnav-fixed-top bsx-appnav-navbar-scroll-toggle" data-fn="anchor-offset-elem" data-tg="sticky-container-below">

    <nav class="bsx-appnav-navbar-container">

        <button class="bsx-appnav-navbar-toggler" id="toggle-navbar-collapse" type="button" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="MENU<?php // echo __( 'Menu', 'bsx-wordpress-example' ); ?>" data-fn="toggle" data-fn-options="{ bodyOpenedClass: 'nav-open' }" data-fn-target="[data-tg='navbar-collapse']" data-tg="dropdown-multilevel-excluded">
            <span class="sr-only">MENU<?php // echo __( 'Menu', 'bsx-wordpress-example' ); ?></span>
            <i class="fa fa-navicon" aria-hidden="true"></i>
        </button>

        <a class="bsx-appnav-navbar-brand" href="####<?php //echo get_bloginfo( 'url' ) . '/'; ?>">
          <!-- inline svg logo -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 263 64" width="136" height="32" role="img" focusable="false"><title>Example logo</title><path fill="#3E3E40" d="M95.817 49.58h19.808v4.952H89.983V7.185h5.834V49.58zM142.013 6.438c3.256 0 6.217.577 8.887 1.729a19.232 19.232 0 0 1 6.852 4.952c1.898 2.149 3.367 4.714 4.408 7.698 1.041 2.985 1.561 6.311 1.561 9.972 0 3.755-.52 7.135-1.561 10.142-1.041 3.008-2.533 5.575-4.477 7.699-1.945 2.126-4.273 3.765-6.986 4.918-2.715 1.153-5.766 1.729-9.158 1.729s-6.455-.576-9.191-1.729-5.088-2.78-7.055-4.884-3.483-4.646-4.545-7.631c-1.062-2.984-1.594-6.309-1.594-9.972 0-3.799.542-7.213 1.627-10.244 1.086-3.028 2.623-5.606 4.613-7.731 1.989-2.125 4.386-3.765 7.19-4.919 2.804-1.152 5.946-1.729 9.429-1.729zm-.61 4.681c-2.398 0-4.58.475-6.547 1.424s-3.663 2.283-5.088 4.002c-1.424 1.719-2.521 3.788-3.289 6.207-.77 2.42-1.154 5.099-1.154 8.037 0 2.985.385 5.688 1.154 8.106.768 2.42 1.865 4.5 3.289 6.241 1.425 1.741 3.131 3.076 5.121 4.002 1.99.928 4.207 1.392 6.648 1.392 2.352 0 4.512-.464 6.479-1.392a14.326 14.326 0 0 0 5.053-3.968c1.402-1.718 2.486-3.787 3.256-6.207.77-2.419 1.154-5.098 1.154-8.038 0-3.029-.373-5.776-1.119-8.242-.746-2.464-1.822-4.545-3.223-6.24a14.44 14.44 0 0 0-5.088-3.935c-1.989-.926-4.204-1.389-6.646-1.389zM209.507 14.646a24.625 24.625 0 0 0-5.867-2.239 26.36 26.36 0 0 0-6.207-.746c-2.895 0-5.551.453-7.971 1.356-2.42.905-4.512 2.193-6.275 3.866-1.762 1.675-3.131 3.687-4.104 6.037-.973 2.352-1.457 4.952-1.457 7.802 0 2.894.475 5.528 1.424 7.901.949 2.375 2.271 4.399 3.969 6.072 1.695 1.673 3.719 2.962 6.07 3.866s4.951 1.356 7.801 1.356c1.357 0 2.689-.079 4.002-.237a26.908 26.908 0 0 0 3.936-.78V33.84h-10.719v-4.747h16.551v23.673c-2.441.77-4.771 1.335-6.986 1.696-2.217.36-4.477.543-6.783.543-3.844 0-7.338-.587-10.48-1.765-3.143-1.175-5.822-2.824-8.037-4.952-2.217-2.124-3.936-4.69-5.156-7.698-1.221-3.007-1.832-6.342-1.832-10.005 0-3.527.645-6.749 1.934-9.666s3.074-5.428 5.359-7.529c2.283-2.104 5.008-3.742 8.174-4.919 3.164-1.176 6.648-1.763 10.445-1.763 2.217 0 4.354.158 6.412.475 2.057.316 4.148.928 6.273 1.831l-.476 5.632zM243.22 6.438c3.256 0 6.217.577 8.887 1.729a19.207 19.207 0 0 1 6.85 4.952c1.9 2.149 3.369 4.714 4.41 7.698 1.039 2.985 1.561 6.311 1.561 9.972 0 3.755-.521 7.135-1.561 10.142-1.041 3.008-2.533 5.575-4.477 7.699-1.945 2.126-4.273 3.765-6.988 4.918-2.713 1.153-5.766 1.729-9.156 1.729-3.393 0-6.457-.576-9.191-1.729-2.736-1.153-5.088-2.78-7.055-4.884s-3.482-4.646-4.545-7.631-1.594-6.309-1.594-9.972c0-3.799.543-7.213 1.627-10.244 1.086-3.028 2.623-5.606 4.613-7.731s4.385-3.765 7.189-4.919c2.805-1.152 5.947-1.729 9.43-1.729zm-.612 4.681c-2.396 0-4.578.475-6.545 1.424s-3.664 2.283-5.088 4.002-2.521 3.788-3.289 6.207c-.77 2.42-1.154 5.099-1.154 8.037 0 2.985.385 5.688 1.154 8.106.768 2.42 1.865 4.5 3.289 6.241s3.131 3.076 5.121 4.002c1.99.928 4.207 1.392 6.648 1.392 2.35 0 4.51-.464 6.479-1.392a14.326 14.326 0 0 0 5.053-3.968c1.4-1.718 2.486-3.787 3.256-6.207.77-2.419 1.154-5.098 1.154-8.038 0-3.029-.373-5.776-1.121-8.242-.746-2.464-1.82-4.545-3.221-6.24a14.457 14.457 0 0 0-5.088-3.935c-1.989-.926-4.204-1.389-6.648-1.389z"></path><path fill="#0275D8" d="M57.576 15.369c-2.759-4.728-6.502-8.471-11.229-11.23C41.618 1.381 36.455 0 30.857 0c-5.599 0-10.761 1.381-15.489 4.139-4.728 2.76-8.471 6.503-11.23 11.23S0 25.26 0 30.857c0 5.599 1.379 10.762 4.138 15.488 2.759 4.729 6.502 8.473 11.23 11.23 4.728 2.759 9.89 4.139 15.489 4.139 5.598 0 10.761-1.38 15.489-4.139 4.727-2.758 8.471-6.502 11.229-11.23 2.759-4.727 4.139-9.89 4.139-15.488 0-5.597-1.38-10.76-4.138-15.488zm-40.641 1.567c1.005-1.004 2.216-1.507 3.636-1.507 1.419 0 2.632.503 3.636 1.507s1.507 2.217 1.507 3.637-.502 2.631-1.507 3.636-2.217 1.507-3.636 1.507c-1.42 0-2.631-.502-3.636-1.507-1.004-1.005-1.506-2.216-1.506-3.636s.502-2.634 1.506-3.637zm28.627 21.113c-.991 3.242-2.839 5.854-5.544 7.836-2.706 1.982-5.76 2.973-9.161 2.973-3.402 0-6.456-.99-9.161-2.973-2.706-1.982-4.554-4.594-5.544-7.836a2.467 2.467 0 0 1 .161-1.948c.321-.629.83-1.051 1.527-1.266a2.473 2.473 0 0 1 1.949.161c.629.321 1.051.83 1.266 1.526.669 2.144 1.908 3.878 3.716 5.203 1.808 1.326 3.837 1.989 6.087 1.989s4.279-.663 6.087-1.989c1.808-1.325 3.046-3.06 3.716-5.203a2.437 2.437 0 0 1 1.286-1.526 2.526 2.526 0 0 1 1.969-.161 2.452 2.452 0 0 1 1.487 1.266c.32.629.373 1.28.159 1.948zm-.783-13.841c-1.004 1.005-2.216 1.507-3.636 1.507s-2.632-.502-3.636-1.507S36 21.992 36 20.572s.502-2.633 1.507-3.637 2.216-1.507 3.636-1.507c1.419 0 2.631.503 3.636 1.507 1.005 1.004 1.507 2.217 1.507 3.637s-.502 2.631-1.507 3.636z"></path></svg>
        </a>

        <!--

        TODO: mark current nav item for sr
          <span class="sr-only">(current)</span>

        -->

        <div class="bsx-appnav-navbar-collapse" id="navbarNavDropdown" data-tg="navbar-collapse">



          
            <ul class="bsx-appnav-navbar-nav bsx-main-navbar-nav" aria-labelledby="toggle-navbar-collapse">

                        <!-- Bsx_Walker_Page -->

                        <!-- ?php 
                            wp_list_pages(
                                array(
                                    'match_menu_classes' => true,
                                    'show_sub_menu_icons' => true,
                                    'title_li' => false,
                                    'walker'   => new Bsx_Walker_Page(),
                                )
                            );
                        ? -->

                <li>
                    <a class="" href="#">Home</a>
                </li>
                <li>
                    <a class="" href="#">Lorem ipsum</a>
                </li>
                <li class="">
                    <a class="bsx-appnav-dropdown-toggle" id="navbarDropdownMenuLink-a0" href="#" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false">Dolor sit</a>
                    <ul class="" aria-labelledby="navbarDropdownMenuLink-a0">
                        <li class="bsx-appnav-back-link">
                            <a class="" href="#" aria-label="Menüebene schließen" data-label="Zurück" data-fn="dropdown-multilevel-close"></a>
                        </li>
                        <li class="">
                            <a class="bsx-appnav-dropdown-toggle" id="navbarDropdownMenuLink-a0a" href="#" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false">Aliquam lorem</a>
                            <ul class="" aria-labelledby="navbarDropdownMenuLink-a0a">
                                <li class="bsx-appnav-back-link">
                                    <a class="" href="#" aria-label="Menüebene schließen" data-label="Zurück" data-fn="dropdown-multilevel-close"></a>
                                </li>
                                <li>
                                    <a class="" href="#">Ante in</a>
                                </li>
                                <li class="">
                                    <a class="" href="#">Dapibus</a>
                                </li>
                                <li>
                                    <a class="" href="#">Viverra quis</a>
                                </li>
                                <li>
                                    <a class="" href="#">Feugiat a</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>

        </div>

        <div class="bsx-appnav-collapse-backdrop" data-fn="remote-event" data-fn-options="{ target: '#toggle-navbar-collapse' }" data-tg="dropdown-multilevel-excluded"></div>

        <ul class="bsx-appnav-navbar-nav bsx-icon-navbar-nav bsx-allmedia-dropdown-nav">
            <li class="">
                <a id="iconnav-link-1" href="javascript:void( 0 );" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false"><i class="fa fa-phone" aria-hidden="true"></i><span class="sr-only">Telefon</span></a>
                <ul class="ul-right" aria-labelledby="iconnav-link-1">
                    <li>
                        <a class="" href="tel:+49 1234.567890">+49 1234.567890</a>
                    </li>
                </ul>
            </li>
            <li class="">
                <a id="iconnav-link-2" href="javascript:void( 0 );" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false"><i class="fa fa-envelope" aria-hidden="true"></i><span class="sr-only">E-Mail</span></a>
                <ul class="ul-right" aria-labelledby="iconnav-link-2">
                    <li>
                        <a class="create-mt" data-fn="create-mt" data-mt-n="mail" data-mt-d="example" data-mt-s="com"></a>
                    </li>
                </ul>
            </li>
        </ul>

    </nav>

</header>


