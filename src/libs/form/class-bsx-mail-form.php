<?php

// check if polylang plugin available
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class Bsx_Mail_Form {

    public static $global_forms_count = 3;

    public static function get_forms_count() {
        // TODO: replace later by (custom) post id
        return self::$global_forms_count;
    }



    public static function print_form( $index ) {

        echo Theme_Forms_Form_Template::make_form_from_template( $index );

    }


    private function register_form_settings() {

        // TODO: what about automation from page 1...n with – $forms_count = Bsx_Mail_Form::get_forms_count();

        // register menu
        function theme_form_settings_add_menu() {
            // page 1
            add_menu_page( 
                esc_html__( 'Theme Forms (Deprecated)', 'bsx-wordpress' ), // page title
                esc_html__( 'Theme Forms (Deprecated)', 'bsx-wordpress' ), // menu title
                'manage_options', // capability
                'theme_form_options', // menu_slug
                'theme_form_settings_page_1', // function to show related content
                'dashicons-email', // icon url
                1 // position
            );
            add_submenu_page( 
                'theme_form_options', // parent_slug
                sprintf( esc_html__( 'Form %d' ), 2 ), // page_title
                sprintf( esc_html__( 'Form %d' ), 2 ), // menu_title
                'manage_options', // capability
                'theme-form-settings-2', // menu_slug, 
                'theme_form_settings_page_2', // function = '', 
                2 // position = null
            );
            add_submenu_page( 
                'theme_form_options', // parent_slug
                sprintf( esc_html__( 'Form %d' ), 3 ), // page_title
                sprintf( esc_html__( 'Form %d' ), 3 ), // menu_title
                'manage_options', // capability
                'theme-form-settings-3', // menu_slug, 
                'theme_form_settings_page_3', // function = '', 
                3 // position = null
            );
            // add_submenu_page( 
            //     'theme_form_options', // parent_slug
            //     esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // page_title
            //     esc_html__( 'Theme Form Entries', 'bsx-wordpress' ), // menu_title
            //     'manage_options', // capability
            //     'theme-form-entries', // menu_slug, 
            //     'theme_form_show_entries', // function = '', 
            //     3 // position = null
            // );
        }
        add_action( 'admin_menu', 'theme_form_settings_add_menu' );

        // pages for menu

        // add_action( 'init', function() use( $args ) {
        //     //...
        // } );

        // page 1...max, call with index $i (1...max)
        function theme_form_settings_page_1() { ?>
            <div class="wrap">
                <h2><?php sprintf( esc_html__( 'Form %d' ), 1 ); ?></h2>
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
                <h2><?php sprintf( esc_html__( 'Form %d' ), 2 ); ?></h2>
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
                <h2><?php sprintf( esc_html__( 'Form %d' ), 3 ); ?></h2>
                <form method="post" action="options.php">
                    <?php
                        do_settings_sections( 'theme_form_3_options_form' ); // page
                        settings_fields( 'custom-settings-theme-form-3' ); // option group (may have multiple sections)
                        submit_button();
                    ?>
                </form>
            </div>
        <?php }
        /*
        function theme_form_show_entries() { 
            global $functions_file_basename;
            ?>
            <div class="wrap">
                <?php
                    ( new Theme_Forms_Admin_Pages )->init();
                ?>
            </div>
        <?php }
        */

        /**
         * custom settings, create pages setup
         */

        $forms_count = self::$global_forms_count;

        add_action( 'admin_init', function() use ( $forms_count ) {
        // function theme_form_settings_page_setup() {

            // pages 1...max
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
                        'description'  => '<h4>' . __( 'Input placeholder syntax', 'bsx-wordpress' ) . '</h4>'
                            . '<p><small>'
                            . __( 'Input', 'bsx-wordpress' ) . ': <code>[*my_type::my_name class="form-control" id="some-id" class="foo" data-foo="bar"]</code><br>'
                            . __( 'Syntax', 'bsx-wordpress' ) . ': <code>[*</code> required, <code>[</code> non-required, <code>my_type::</code> input type, <code>::my_name</code> name, <code> id="some-id" class="foo" data-foo="bar"]</code> attributes (optional)<br>'
                            . __( 'Translation', 'bsx-wordpress' ) . ': <code>[translate::My translatable text.]</code> (using Theme translations)<br>'
                            . '</small></p>'
                            . '<h4>' . __( 'Input examples', 'bsx-wordpress' ) . '</h4>'
                            . '<p><small>'
                            . '</small></p>'
                            . '<p><small>'
                            . __( 'Mandatory input example', 'bsx-wordpress' ) . ': <code>[*email::email class="form-control" id="email"]</code> type: email, name: email<br>'
                            . __( 'Optional input example', 'bsx-wordpress' ) . ': <code>[text::name class="form-control" id="name"]</code> type: text, name: name<br>'
                            . __( 'Textarea example', 'bsx-wordpress' ) . ': <code>[*message::message class="form-control" id="message" rows="4"]</code><br>'
                            . __( 'Translation example', 'bsx-wordpress' ) . ': <code>[translate::Email]</code><br>'
                            . __( 'Human verification display', 'bsx-wordpress' ) . ': <code>[human-verification-display:: class="input-group-text"]</code><br>'
                            . __( 'Human verification input', 'bsx-wordpress' ) . ': <code>[*human-verification-input:: class="form-control" id="human-verification"]</code><br>'
                            . __( 'Human verification refresh code attribute', 'bsx-wordpress' ) . ': <code>&lt;button [human-verification-refresh-attr]&gt;[translate::Refresh code]&lt;/button&gt;</code>'
                            . '</small></p>'
                            . '<h4>' . __( 'Embed shortcode', 'bsx-wordpress' ) . '<h4><p><code>[theme-form id="' . $i . '"]</code></p>',
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
                            '<p>',
                            '</p><p><small><code>[email]</code>, <code>[name]</code>, <code>[site-url]</code>, ...</small></p>',
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
                        'label_for' => 'form-' . $i . '-recipient-email-2',
                        'description'  => sprintf( 
                            __( '%sOptional use email placeholder, e.g.:%s', 
                            'bsx-wordpress' ),
                            '<p>',
                            '</p><p><small><code>[email]</code></small></p>',
                        ),
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

        } );
        // Shared  across sections
        // modified from https://wordpress.stackexchange.com/questions/129180/add-multiple-custom-fields-to-the-general-settings-page
        function render_theme_form_input_field( $args ) {
            $option = get_option( $args[ 0 ] );
            if ( isset( $args[ 'description' ] ) ) {
                // no user input, no need to escape
                echo '<div>' . $args[ 'description' ] . '</div>';
            }
            echo '<input type="text" id="'  . esc_attr( $args[ 0 ] ) . '" name="'  . esc_attr( $args[ 0 ] ) . '" value="' . esc_attr( $option ) . '" size="50" />';
        }
        function render_theme_form_textarea_field( $args ) {
            $option = get_option( $args[ 0 ] );
            if ( isset( $args[ 'description' ] ) ) {
                // no user input, no need to escape
                echo '<div>' . $args[ 'description' ] . '</div>';
            }
            echo '<textarea  id="'  . esc_attr( $args[ 0 ] ) . '" name="'  . esc_attr( $args[ 0 ] ) . '" rows="20" cols="80" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace;">' . esc_textarea( $option ) . '</textarea>';
        }

    } // /register_form_settings()


    public function init() {

        $this->register_form_settings();

    } // /init()

}