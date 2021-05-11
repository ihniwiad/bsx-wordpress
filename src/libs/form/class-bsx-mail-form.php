<?php

class Bsx_Mail_Form {

    public static $global_forms_count = 3;


    // pattern for placeholders
    // $pattern = "/\[+(\*|)+(text|email|number|message)+::+([a-zA-Z0-9-_ =\"])+\]+/s";
    // $matches = array();
    // $has_matches = preg_match( $pattern, $str, $matches );

    public function make_form_from_template( $index ) {

        $hash = hash( 'md5', 'x' . $index );

        $template = get_option( 'form-' . $index . '-form-template' );

        // pattern for placeholders (allow css selectors for js)
        $input_pattern = "/\[+(\*|)+(text|email|tel|file|number|message|human-verification-display|human-verification-input|submit)+::+([a-zA-Z0-9-_ =\".#\[\]\(\)]|)+\]/s";
        $translate_pattern = "/\[translate::+([a-zA-Z0-9-_ =\"'\(\).:?!])+\]/s";

        // replace input placeholders
        $matches = array();
        $has_matches = preg_match_all( $input_pattern, $template, $matches );

        $matches = $matches[ 0 ];
        // print_r( $matches );

        for ( $i = 0; $i < count( $matches ); $i++ ) {
            $replace = $this->parse_input( $matches[ $i ] );
            $template = str_replace( $matches[ $i ], $replace, $template );
        }

        // replace translate placeholders
        $matches = array();
        $has_matches = preg_match_all( $translate_pattern, $template, $matches );

        $matches = $matches[ 0 ];
        // print_r( $matches );

        for ( $i = 0; $i < count( $matches ); $i++ ) {
            $replace = $this->translate( $matches[ $i ] );
            $template = str_replace( $matches[ $i ], $replace, $template );
        }

        $html = '<div data-id="form-wrapper">';
            $html .= '<form novalidate method="post" action="' . get_bloginfo( 'url' ) . '/wp-json/bsx/v1/mailer/" data-fn="mail-form">';
                $html .= $template;
                $html .= '<input type="hidden" name="hv__text__r" value="" data-g-tg="hv">';
                $html .= '<input type="hidden" name="hv_k__x__r" value="" data-g-tg="hv-k">';
                $html .= '<input type="hidden" name="idh__text__r" value="' . $hash . '">';
            $html .= '</form>';
            $html .= '<div data-g-tg="message-wrapper">';
                $html .= '<div data-g-tg="success-message" aria-hidden="true" style="display: none;">';
                    $html .= '<div class="alert alert-success lead mb-4" role="alert">';
                        // TODO: include response here
                        $html .= '<span class="fa fa-check fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span>';
                        // $html .= '<span class="fa fa-check fa-lg" aria-hidden="true"></span> ' . esc_html__( 'Your message has been sent successfully.', 'bsx-wordpress' );
                    $html .= '</div>';
                    // TODO: remove next line
                    // $html .= '<pre data-g-tg="response-text"></pre>';
                $html .= '</div>';
                $html .= '<div data-g-tg="error-message" aria-hidden="true" style="display: none;">';
                    $html .= '<div class="alert alert-danger lead mb-4" role="alert">';
                        // TODO: include response here
                        $html .= '<span class="fa fa-exclamation-triangle fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span>';
                        // $html .= '<span class="fa fa-exclamation-triangle fa-lg" aria-hidden="true"></span> ' . esc_html__( 'An error occured. Your message has not been sent.', 'bsx-wordpress' );
                    // TODO: remove next line
                    // $html .= '<pre data-g-tg="response-text"></pre>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return $html;

    } // /make_form_from_template()


    public function print_form( $index ) {

        echo $this->make_form_from_template( $index );

    }


    private function translate( $translate_string ) {
        // from: [translate::MY TEXT EXAMPLE]
        
        // remove brackets from both sides
        $translate_string = ltrim( $translate_string, '[' );
        $translate_string = rtrim( $translate_string, ']' );

        // get translatable text
        $space_split = explode( '::', $translate_string );
        $trans_text = $space_split[ 1 ];

        $return = __( $trans_text, 'bsx-wordpress' );

        // print_r( $return );

        return $return;
    } // /translate()


    private function parse_input( $input_string ) {
        // from: [*text::name class="form-control" type="text" id="name"]
        // to: <input class="form-control" type="text" id="name" name="name__text__r" required>
        // required: [*... -> required
        // type: [*text::... -> type="text" | [*message::... -> <textarea>...</textarea>
        // name: ::name -> name="name__..." (name="MY-NAME__MY-TYPE__R-MEANS-REQUIRED", e.g. name="name__text__r")

        // remove brackets from both sides
        $input_string = ltrim( $input_string, '[' );
        $input_string = rtrim( $input_string, ']' );

        // devide conf data & attributes
        $space_split = explode( ' ', $input_string );
        $conf_data = $space_split[ 0 ];
        array_shift( $space_split );
        $attributes = implode( ' ', $space_split );

        $first_char = substr( $conf_data, 0, 1 );
        $required = false;
        if ( $first_char === '*' ) {
            $required = true;
            $conf_data = ltrim( $conf_data, '*' );
        }
        $conf_split = explode( '::', $conf_data );
        $type = $conf_split[ 0 ];
        $name = $conf_split[ 1 ];

        $return = '';

        switch ( $type ) {
            case 'message':
                $return .= '<textarea' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' type="' . $type . '" name="' . $name . '__' . $type . ( $required ? '__r' : '' ) . '"' . ( $required ? ' required' : '' ) . '></textarea>';
                break;

            case 'human-verification-input':
                $return .= '<input' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' type="text" name="human_verification__text__r" required>';
                break;

            case 'human-verification-display':
                $return .= '<div' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' data-g-tg="hv"></div>';
                break;

            case 'submit':
                $return .= '<input' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' type="submit" value="' . esc_html__( 'Send', 'bsx-wordpress' ) . '">';
                break;
            
            default:
                $return .= '<input' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' type="' . $type . '" name="' . $name . '__' . $type . ( $required ? '__r' : '' ) . '"' . ( $required ? ' required' : '' ) . '>';
                break;
        }

        // print_r( $return );

        return $return;
    } // /parse_input()


    private function register_form_settings() {

        // $theme_form_settings_add_menu = function() {
        //     theme_form_settings_add_menu( $this->forms_count );
        // }

        // register menu
        function theme_form_settings_add_menu() {
            // page 1
            add_menu_page( 
                esc_html__( 'Theme Forms', 'bsx-wordpress' ), // page title
                esc_html__( 'Theme Forms', 'bsx-wordpress' ), // menu title
                'manage_options', // capability
                'theme_form_options', // menu_slug
                'theme_form_settings_page_1', // function to show related content
                'dashicons-email', // icon url
                1 // position
            );
            // add subpage
            // add_submenu_page( 
            //     'theme_form_options', // parent_slug
            //     esc_html__( 'Form 1' ), // page_title
            //     esc_html__( 'Form 1' ), // menu_title
            //     'manage_options', // capability
            //     'theme-form-settings-1', // menu_slug, 
            //     'theme_form_settings_page_1', // function = '', 
            //     2 // position = null
            // );
            add_submenu_page( 
                'theme_form_options', // parent_slug
                esc_html__( 'Form 2' ), // page_title
                esc_html__( 'Form 2' ), // menu_title
                'manage_options', // capability
                'theme-form-settings-2', // menu_slug, 
                'theme_form_settings_page_2', // function = '', 
                2 // position = null
            );
            add_submenu_page( 
                'theme_form_options', // parent_slug
                esc_html__( 'Form 3' ), // page_title
                esc_html__( 'Form 3' ), // menu_title
                'manage_options', // capability
                'theme-form-settings-3', // menu_slug, 
                'theme_form_settings_page_3', // function = '', 
                3 // position = null
            );
            // pages 2...max
            // for ( $i = 2; $i <= 5; $i++ ) {
            //     // add subpage
            //     add_submenu_page( 
            //         'theme_form_options', // parent_slug
            //         esc_html__( 'Form '. $i ), // page_title
            //         esc_html__( 'Form '. $i ), // menu_title
            //         'manage_options', // capability
            //         'theme-form-settings-' . $i , // menu_slug, 
            //         'theme_form_settings_' . $i, // function = '', 
            //         1 // position = null
            //     );
            // }
        }
        add_action( 'admin_menu', 'theme_form_settings_add_menu' );

        // pages for menu

        // $function = $prefix . '_custom_type_init';
        // if ( function_exists( $function ) ) {
        //     $function();
        // }

        // add_action('init', function() use($args) {
        //     //...
        // });

        // for ( $i = 2; $i <= 5; $i++ ) {
        // }

        // page 1...max, call with index $i (1...max)
        function theme_form_settings_page_1() { ?>
            <div class="wrap">
                <h2><?php esc_html__( 'Form 1', 'bsx-wordpress' ); ?></h2>
                <form method="post" action="options.php">
                    <?php
                        do_settings_sections( 'theme_form_1_options_form' ); // page
                        settings_fields( 'custom-settings-theme-form-1' ); // option group (may have multiple sections)
                        submit_button();
                    ?>
                </form>
            </div>
        <?php }
        function theme_form_settings_page_2() { ?>
            <div class="wrap">
                <h2><?php esc_html__( 'Form 2', 'bsx-wordpress' ); ?></h2>
                <form method="post" action="options.php">
                    <?php
                        do_settings_sections( 'theme_form_2_options_form' ); // page
                        settings_fields( 'custom-settings-theme-form-2' ); // option group (may have multiple sections)
                        submit_button();
                    ?>
                </form>
            </div>
        <?php }
        function theme_form_settings_page_3() { ?>
            <div class="wrap">
                <h2><?php esc_html__( 'Form 3', 'bsx-wordpress' ); ?></h2>
                <form method="post" action="options.php">
                    <?php
                        do_settings_sections( 'theme_form_3_options_form' ); // page
                        settings_fields( 'custom-settings-theme-form-3' ); // option group (may have multiple sections)
                        submit_button();
                    ?>
                </form>
            </div>
        <?php }

        // pages 1...max
        // for ( $i = 1; $i <= 5; $i++ ) {
        //     $function_name_string = '$theme_form_settings_page_' . $i;
        //     $function_name_string = function() {
        //         return theme_form_settings_page( $i );
        //     };
        // }

        /**
         * custom settings, create pages setup
         */

        $forms_count = self::$global_forms_count;

        add_action( 'admin_init', function() use ( $forms_count ) {
        // function theme_form_settings_page_setup() {

            // pages 1...max
            // $forms_count = 2;
            for ( $i = 1; $i <= $forms_count; $i++ ) {

                // section form
                add_settings_section(
                    'theme-form-' . $i . '-settings-section-form', // id
                    sprintf( esc_html__( 'Form %d template', 'bsx-wordpress' ), $i ), // title
                    null, // callback function
                    'theme_form_' . $i . '_options_form' // page
                );

                add_settings_field(
                    'form-' . $i . '-form-template', // id
                    esc_html__( 'Form template', 'bsx-wordpress' ), // title
                    'render_theme_form_textarea_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-form', // section = 'default'
                    array(
                        'form-' . $i . '-form-template',
                        'label_for' => 'form-' . $i . '-form-template',
                        'description'  => sprintf( 
                            __( '%sUse input placeholders:%sMandatory field: %sOptional field: %sTranslation: %sHuman verification display: %sHuman verification input: %s', 
                            'bsx-wordpress' ),
                            '<p><strong>',
                            '</strong></p><p><small>',
                            '<code>[*email::email class="form-control" id="email"]</code> (type: email, name: <b>email</b>)<br>',
                            '<code>[text::name class="form-control" id="name"]</code> (type: text, name: <b>name</b><br>',
                            '<code>[translate::Email]</code><br>',
                            '<code>[human-verification-display:: class="input-group-text"]</code><br>',
                            '<code>[*human-verification-input:: class="form-control" id="human-verification"]</code><br></small></p>',
                        ),
                    ) // args = array()
                );

                // register each field
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-form-template' // option name
                );

                // section mail 1
                add_settings_section(
                    'theme-form-' . $i . '-settings-section-mail', // id
                    sprintf( esc_html__( 'Form %d mail', 'bsx-wordpress' ), $i ), // title
                    null, // callback function
                    'theme_form_' . $i . '_options_form' // page
                );

                // fields for section
                add_settings_field(
                    'form-' . $i . '-recipient-email', // id
                    esc_html__( 'Recipient email', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail', // section = 'default'
                    array(
                        'form-' . $i . '-recipient-email',
                        'label_for' => 'form-' . $i . '-recipient-email'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-sender-email', // id
                    esc_html__( 'Sender email', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail', // section = 'default'
                    array(
                        'form-' . $i . '-sender-email',
                        'label_for' => 'form-' . $i . '-sender-email'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-subject', // id
                    esc_html__( 'Subject', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail', // section = 'default'
                    array(
                        'form-' . $i . '-subject',
                        'label_for' => 'form-' . $i . '-subject'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-mail-template', // id
                    esc_html__( 'Email template', 'bsx-wordpress' ), // title
                    'render_theme_form_textarea_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail', // section = 'default'
                    array(
                        'form-' . $i . '-mail-template',
                        'label_for' => 'form-' . $i . '-mail-template',
                        'description'  => sprintf( 
                            __( '%sUse placeholders (Subject and Email template):%s', 
                            'bsx-wordpress' ),
                            '<p><strong>',
                            '</strong></p><p><small><code>[email]</code>, <code>[name]</code>, <code>[site-url]</code>, ...</small></p>',
                        ),
                    ) // args = array()
                );

                // register each field
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-recipient-email' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-sender-email' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-subject' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-mail-template' // option name
                );


                // section mail 1
                add_settings_section(
                    'theme-form-' . $i . '-settings-section-mail-2', // id
                    sprintf( esc_html__( 'Form %d mail 2 (optional)', 'bsx-wordpress' ), $i ), // title
                    null, // callback function
                    'theme_form_' . $i . '_options_form' // page
                );

                // fields for section
                add_settings_field(
                    'form-' . $i . '-recipient-email-2', // id
                    esc_html__( 'Recipient email', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail-2', // section = 'default'
                    array(
                        'form-' . $i . '-recipient-email-2',
                        'label_for' => 'form-' . $i . '-recipient-email-2'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-sender-email-2', // id
                    esc_html__( 'Sender email', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail-2', // section = 'default'
                    array(
                        'form-' . $i . '-sender-email-2',
                        'label_for' => 'form-' . $i . '-sender-email-2'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-subject-2', // id
                    esc_html__( 'Subject', 'bsx-wordpress' ), // title
                    'render_theme_form_input_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail-2', // section = 'default'
                    array(
                        'form-' . $i . '-subject-2',
                        'label_for' => 'form-' . $i . '-subject-2'
                    ) // args = array()
                );
                add_settings_field(
                    'form-' . $i . '-mail-template-2', // id
                    esc_html__( 'Email template', 'bsx-wordpress' ), // title
                    'render_theme_form_textarea_field', // callback, use unique function name
                    'theme_form_' . $i . '_options_form', // page
                    'theme-form-' . $i . '-settings-section-mail-2', // section = 'default'
                    array(
                        'form-' . $i . '-mail-template-2',
                        'label_for' => 'form-' . $i . '-mail-template-2'
                    ) // args = array()
                );

                // register each field
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-recipient-email-2' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-sender-email-2' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-subject-2' // option name
                );
                register_setting(
                    'custom-settings-theme-form-' . $i, // option group
                    'form-' . $i . '-mail-template-2' // option name
                );
            }

        // }
        } );
        // Shared  across sections
        // modified from https://wordpress.stackexchange.com/questions/129180/add-multiple-custom-fields-to-the-general-settings-page
        function render_theme_form_input_field( $args ) {
            $options = get_option( $args[ 0 ] );
            if ( isset( $args[ 'description' ] ) ) {
                echo '<div>' . $args[ 'description' ] . '</div>';
            }
            echo '<input type="text" id="'  . $args[ 0 ] . '" name="'  . $args[ 0 ] . '" value="' . $options . '" size="50" />';
        }
        function render_theme_form_textarea_field( $args ) {
            $options = get_option( $args[ 0 ] );
            if ( isset( $args[ 'description' ] ) ) {
                echo '<div>' . $args[ 'description' ] . '</div>';
            }
            echo '<textarea  id="'  . $args[ 0 ] . '" name="'  . $args[ 0 ] . '" rows="20" cols="80" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace;">' . $options . '</textarea>';
        }
        // add_action( 'admin_init', 'theme_form_settings_page_setup' );

    } // /register_form_settings()


    private function register_mailer_rest_route() {

        /**
         * callback function for routes endpoint
         */
        function bsx_mailer_post_endpoint( $request ) {
        // add_action( 'admin_init', function() use ( $forms_count ) {

            if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
                // ok, try send mail

                // TEST ($response is used only for testing)
                // $response = '';

                // $response .= var_dump( $request );

                $sanitized_values = array();
                $validation_ok = true;

                foreach ( $_POST as $key => $value ) {
                    // extract type for validation from input name `mytype__myname`
                    $split_key = explode( '__', $key);
                    $name = $split_key[ 0 ];
                    $type = $split_key[ 1 ];
                    $required = ( isset( $split_key[ 2 ] ) && $split_key[ 2 ] === 'r') ? true : false;

                    $value = trim( $value );

                    // sanitize and validate
                    if ( $type === 'email' ) {
                        $value = filter_var( $value, FILTER_SANITIZE_EMAIL );
                        if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
                            $validation_ok = false;
                        }
                    }
                    elseif ( $type === 'number' ) {
                        $value = intval( $value );
                        if ( ! is_numeric( $value ) ) {
                            $validation_ok = false;
                        }
                    }

                    // validate others
                    if ( $required ) {
                        if ( $type === 'x' ) {
                            // validate empty
                            if ( ! $value === '' ) {
                                $validation_ok = false;
                            }
                        }
                        elseif ( empty( $value ) && ! $value === '0' ) {
                            // validate non empty, allow '0'
                            $validation_ok = false;
                        }
                    }

                    // add to $values
                    $sanitized_values[ $name ] = $value;


                    // $response .= $name . ' (' . $type . ', required: ' . $required . '): ' . $value . '<br>';
                }


                // TODO: get template kex by secret form value (hash)
                // $index = 1;
                // $hash = hash( 'md5', 'x' . $index );

                // workaround since not knowing forms count here, assuming max 30 forms
                $forms_count = 30;
                $form_index = '';
                for ( $i = 1; $i <= $forms_count; $i++ ) {
                    if ( hash( 'md5', 'x' . $i ) === $sanitized_values[ 'idh' ] ) {
                        $form_index = $i;
                        break;
                    }
                }


                function replace_placeholders( $text, $sanitized_values ) {
                    // $text = str_replace ( '[site-title]', get_the_title(), $text );
                    $text = str_replace ( '[site-url]', get_site_url(), $text );
                    foreach ( $sanitized_values as $key => $value ) {
                        $text = str_replace ( '[' . $key . ']', $value, $text );
                    }
                    return $text;
                }

                $mail_subject = '';
                $mail_content = '';

                // human verification
                $_calc_hv_value = '';
                if ( $sanitized_values[ 'hv' ] ) {
                    $hv_value = urldecode ( $sanitized_values[ 'hv' ] );

                    $hv_values_extract = explode( '|', $hv_value );

                    $hv_type = intval( $hv_values_extract[ 1 ] );
                    $hv_values = [];
                    for ( $i = 2; $i < count( $hv_values_extract ); $i++ ) {
                        $hv_values[] = $hv_values_extract[ $i ];
                    }

                    // check if found values
                    if ( ! empty( $hv_values ) ) {
                        switch ( $hv_type ) {
                            case 1:
                                $_calc_hv_value = intval( $hv_values[ 0 ] ) + intval( $hv_values[ 1 ] );
                                break;
                            case 2:
                                $_calc_hv_value = intval( $hv_values[ 0 ] ) - intval( $hv_values[ 1 ] );
                                break;
                            case 3:
                                $_calc_hv_value = intval( $hv_values[ 0 ] ) * intval( $hv_values[ 1 ] );
                                break;
                            case 4:
                                $_calc_hv_value = intval( $hv_values[ 0 ] ) / intval( $hv_values[ 1 ] );
                                break;
                            case 5:
                                $_calc_hv_value = intval( $hv_values[ 0 ] . $hv_values[ 1 ] ) + intval( $hv_values[ 2 ] );
                                break;
                            case 6:
                                $_calc_hv_value = intval( $hv_values[ 0 ] . $hv_values[ 1 ] ) - intval( $hv_values[ 2 ] );
                                break;
                            case 7:
                                $_calc_hv_value = intval( $hv_values[ 0 ] ) + intval( $hv_values[ 1 ] ) + intval( $hv_values[ 2 ] );
                                break;
                            // check if numeric
                            case 8:
                                $before_target_value = $hv_values[ count( $hv_values ) - 1 ];
                                if ( is_numeric( $before_target_value ) ) {
                                    $_calc_hv_value = intval( $before_target_value ) + 1;
                                }
                                else {
                                    $_calc_hv_value = chr( ord( $before_target_value ) + 1 );
                                }
                                break;
                            case 9:
                                $before_target_value = $hv_values[ count( $hv_values ) - 2 ];
                                if ( is_numeric( $before_target_value ) ) {
                                    $_calc_hv_value = intval( $before_target_value ) + 1;
                                }
                                else {
                                    $_calc_hv_value = chr( ord( $before_target_value ) + 1 );
                                }
                                break;
                            case 10:
                                $before_target_value = $hv_values[ count( $hv_values ) - 3 ];
                                if ( is_numeric( $before_target_value ) ) {
                                    $_calc_hv_value = intval( $before_target_value ) + 1;
                                }
                                else {
                                    $_calc_hv_value = chr( ord( $before_target_value ) + 1 );
                                }
                                break;
                        } // /switch
                    }
                    else {
                        $validation_ok = false;
                    }
                }
                else {
                    $validation_ok = false;
                }

                $mail_subject = replace_placeholders( get_option( 'form-' . $form_index . '-subject' ), $sanitized_values );
                if ( empty( $mail_subject ) ) {
                    // fallback subject
                    $mail_subject = 'Mail from contact form at ' . get_site_url();
                }

                $mail_content = replace_placeholders( get_option( 'form-' . $form_index . '-mail-template' ), $sanitized_values );

                if ( empty( $mail_content ) ) {
                    // fallback content
                    foreach ( $sanitized_values as $key => $value ) {
                        $mail_content .= $key . ': ' . $value . "\n";
                    }
                }

                // get recipient mail
                $recipient_mail = get_option( 'form-' . $form_index . '-recipient-email' );
                $recipient_mail_2 = get_option( 'form-' . $form_index . '-recipient-email-2' );

                $sender_mail = get_option( 'form-' . $form_index . '-sender-email' );

                // ckeck if $recipient_mail_2 is filled
                $mail_2_ok = false;

                if ( ! empty( $recipient_mail_2 ) ) {

                    $sender_mail_2 = get_option( 'form-' . $form_index . '-sender-email-2' );

                    // ckeck if $recipient_mail_2 is mail or placeholder
                    if ( substr( $recipient_mail_2, 0, 1 ) === '[' && substr( $recipient_mail_2, -1 ) === ']' ) {
                        // is placeholder, get placeholder name
                        $placeholder_name = ltrim( $recipient_mail_2, '[' );
                        $placeholder_name = rtrim( $placeholder_name, ']' );
                        // get placeholder value
                        $recipient_mail_2 = isset( $sanitized_values[ $placeholder_name ] ) ? $sanitized_values[ $placeholder_name ] : '';
                    }

                    $mail_subject_2 = replace_placeholders( get_option( 'form-' . $form_index . '-subject-2' ), $sanitized_values );
                    $mail_content_2 = replace_placeholders( get_option( 'form-' . $form_index . '-mail-template-2' ), $sanitized_values );


                    // check all mail 2 variables to be valid
                    if ( 
                        filter_var( $recipient_mail_2, FILTER_VALIDATE_EMAIL )
                        && filter_var( $sender_mail_2, FILTER_VALIDATE_EMAIL )
                        && ! empty( $mail_subject_2 )
                        && ! empty( $mail_content_2 )
                    ) {
                        $mail_2_ok = true;
                    }
                }
                // TODO: check for mail 2 filled
                // TODO: allow placeholder [email] in mail 2



                // TEST
                // $test = '';
                // if ( isset( $hv_values ) && ! empty( $hv_values ) ) {
                //     foreach ( $hv_values as $val ) {
                //         $test .= $val . ', ';
                //     }
                // }
                // else {
                //     $test = 'undefiened $hv_values';
                // }

                // $response .= '$recipient_mail_2: ' . $recipient_mail_2 . "\n\n";
                // $response .= '$sender_mail_2: ' . $sender_mail_2 . "\n\n";
                // $response .= '$mail_subject_2: ' . $mail_subject_2 . "\n\n";
                // $response .= '$mail_content_2: ' . $mail_content_2 . "\n\n";
                // $response .= '$mail_2_ok: ' . $mail_2_ok . "\n\n";
                // $response .= 'FORM INDEX: ' . ( isset( $form_index ) ? $form_index : 'undefined' ) . "\n\n" . 'HV VALUE:' . "\n\n" . ( isset( $hv_value ) ? $hv_value : 'undefined' ) . "\n\n" . "(type: " . ( isset( $hv_type ) ? $hv_type : 'undefined' ) .", values: $test) (calc_hv_value: $_calc_hv_value)" . "\n\n" . 'SANITIZED OUTPUT:' . "\n\n";
                // foreach ( $sanitized_values as $key => $value ) {
                //     $response .= $key . ': ' . $value . '<br>';
                // }
                // /TEST

                
                // check referrer host is current host, disallow external access
                $referrer = $_SERVER[ 'HTTP_REFERER' ];
                $host_pattern = "/http+(s|)+:\/\/+([a-z0-9-_])+\//s";
                $matches = array();
                $has_matches = preg_match( $host_pattern, $referrer, $matches );
                $referrer_host = $matches[ 0 ];

                // check referrer, must be empty or same host
                // $mail_content .= "\n\n" . 'REFERER HOST: ' . $referrer_host . "\n\n";
                $server_name = $_SERVER[ 'SERVER_NAME' ]; // domain (not protocol)
                $protocol = ( ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] !== 'off' || $_SERVER[ 'SERVER_PORT' ] == 443 ) ? "https://" : "http://"; // protocol
                $current_host = $protocol . $server_name . '/';
                // $mail_content .= 'HOST: ' . $current_host . "\n\n";



                // TODO: check hv
                // && $sanitized_values[ 'human_verification' ] === $_calc_hv_value 
                if ( 
                    $validation_ok 
                    && ( empty( $referrer_host ) || $referrer_host === $current_host ) 
                    && $sanitized_values[ 'human_verification' ] == $_calc_hv_value 
                    && ! empty( $recipient_mail ) 
                    && filter_var( $sender_mail, FILTER_VALIDATE_EMAIL ) 
                ) {
                    // validation ok, try sending

                    // prepare headers
                    $headers = 'From: ' . $sender_mail . "\r\n";
                    // $headers .= "CC: somebodyelse@example.com";

                    if ( isset( $sender_mail_2 ) ) {
                        $headers_2 = 'From: ' . $sender_mail_2 . "\r\n";
                    }

                    if (
                        // true 
                        mail( $recipient_mail, $mail_subject, $mail_content, $headers )
                        && ( 
                            ! $mail_2_ok
                            || ( $mail_2_ok && mail( $recipient_mail_2, $mail_subject_2, $mail_content_2, $headers_2 ) ) 
                        )
                    ) {
                        // TEST RETURN – TODO: remove
                        // return rest_ensure_response( 'SUBJECT' . "\n\n" . $mail_subject . "\n\n\n" . 'CONTENT' . "\n\n" . $mail_content . "\n\n\n" . $response );

                        // TODO: enable following line
                        return rest_ensure_response( esc_html__( 'Thank you. Your message has been sent successfully.', 'bsx-wordpress' ) );
                    }
                    else {
                        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went wrong while trying to send email.', 'bsx-wordpress' ), array( 'status' => 500 ) );
                    } 
                } 
                else {
                    // validation not ok, send forbidden 403

                    return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your data is invalid or you are not allowed to access this server.', 'bsx-wordpress' ), array( 'status' => 403 ) );
                }
             
                // error 500
                return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'bsx-wordpress' ), array( 'status' => 500 ) );
            }
            else {
                // not ok, send forbidden 403

                return new WP_Error( 'rest_api_sad', esc_html__( 'There was a problem with your submission.', 'bsx-wordpress' ), array( 'status' => 403 ) );
            }

        }


        /**
         * register routes for endpoint
         *
         * read more here: https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/
         */

        function bsx_mailer_register_rest_route() {
            // call with POST data: http://localhost/wordpress-testing/wp-json/bsx/v1/mailer/
            register_rest_route( 'bsx/v1', '/mailer/', array(
                'methods'  => 'POST', // WP_REST_Server::CREATABLE
                'callback' => 'bsx_mailer_post_endpoint',
                'permission_callback' => function() { return ''; },
            ) );
        }
        add_action( 'rest_api_init', 'bsx_mailer_register_rest_route' );

    } // /register_mailer_rest_route()




    // [theme-form id="1"]
    public function add_shortcode() {

        function add_form_shortcode( $atts = [] ) {

            $data = shortcode_atts( array(
                'id' => '',
            ), $atts );

            // no cats defined
            if ( empty( $data[ 'id' ] ) ){
                return "";
            }

            return ( new Bsx_Mail_Form )->make_form_from_template( $data[ 'id' ] );
            // return 'TEST ' . $data[ 'id' ];
        }
        add_shortcode( 'theme-form', 'add_form_shortcode' );

    }


    public function init() {

        $this->register_form_settings();

        $this->register_mailer_rest_route();

        $this->add_shortcode();

    } // /init()

}