<?php

// check if polylang plugin available
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


class Theme_Forms_Form_Template {


    public static function make_form_from_template( $index ) {

        global $functions_file_basename;


        // check if custom post form (modern) or fix form (deprecated)

        $form_id = $index;
        $is_deprecated_non_post_form = false;

        if ( $index < 6 ) {
            // is hash
            $is_deprecated_non_post_form = true;
        }

        if ( $is_deprecated_non_post_form ) {
            // get data from options
            $form_id = hash( 'md5', 'x' . $index );
            $template = get_option( 'form-' . $index . '-form-template' );
        }
        else {
            // get data from post meta
            $meta = get_post_meta( $form_id, 'theme_forms', true );
            $template = isset( $meta[ 'form_template' ] ) ? $meta[ 'form_template' ] : '';
        }


        // $template = filter_var( $template, FILTER_UNSAFE_RAW );

        // sanitize user generated HTML template
        // allow specific HTML tags
        // allow `a` with `href` for link to privacy policy, but remove `href="javascript: ..."`
        // if including more tags might be necessary to remove more attributes like `src="javascript: ..."`
        $template = strip_tags( $template, [ 'div', 'span', 'label', 'button', 'a', 'br' ] );
        $template = preg_replace( '/ (onload|onclick|onchange|onmouseover|onmouseout|onmousedown|onmouseup).*".*"/', '', $template );
        $template = preg_replace( '/ href.*"javascript:.*"/', '', $template );


        // pattern for placeholders (allow css selectors for js)
        // $input_pattern = "/\[+(\*|)+(text|email|tel|file|number|message|human-verification-display|human-verification-input|human-verification-refresh-attr|submit)+(::|)+([a-zA-Z0-9-_ =\"\,.#\[\]\(\)]|)+\]/s";
        // $translate_pattern = "/\[translate::+([a-zA-Z0-9-_ =\"'\(\)\,.:?!\+€\/])+\]/s";
        $input_chars = "[a-zA-Z0-9-_ =\"\,.#*\[\]\(\)]"; // allowed chars in input placeholder
        $translate_chars = "\[translate::+([a-zA-Z0-9-_ =\"'\(\)\,.#*:?!\+€\/])+\]"; // allowed chars in translation
        $input_placeholder_attr_chars = "[a-zA-Z0-9-_ =\"\,.#*\[\]\(\)]"; // allowed chars in input placeholder’s placeholder attribute

        // input pattern may contain translation in placeholder
        $input_pattern = sprintf( 
            "/\[+(\*|)+(text|email|tel|file|number|checkbox|radio|message|human-verification-display|human-verification-input|human-verification-refresh-attr|submit)+(::|)+(%s|)+(%s|)+(%s|)+\]/s",
            $input_chars,
            " placeholder=\"$translate_chars", // may contain translation or not
            $input_chars,
        );
        $translate_pattern = sprintf( 
            "/%s/s",
            $translate_chars,
        );

        // replace input placeholders
        $matches = array();
        $has_matches = preg_match_all( $input_pattern, $template, $matches );

        $matches = $matches[ 0 ];
        // print_r( $matches );

        for ( $i = 0; $i < count( $matches ); $i++ ) {
            $replace = self::parse_input( $matches[ $i ] );
            $template = str_replace( $matches[ $i ], $replace, $template );
        }

        // replace translate placeholders
        $matches = array();
        $has_matches = preg_match_all( $translate_pattern, $template, $matches );

        $matches = $matches[ 0 ];
        // print_r( $matches );

        for ( $i = 0; $i < count( $matches ); $i++ ) {
            $replace = self::translate( $matches[ $i ] );
            $template = str_replace( $matches[ $i ], $replace, $template );
        }

        // action url must be independent of language urls
        $action_url_trunc = get_bloginfo( 'url' );
        if ( is_plugin_active( 'polylang/polylang.php' ) ) {
            $default_lang = pll_default_language();
            // get dafault language home url instead of current language home url
            $action_url_trunc = pll_home_url( $default_lang );
        }
        // remove slash if ixists
        if ( substr( $action_url_trunc, -1 ) === '/' ) {
            $action_url_trunc = substr( $action_url_trunc, 0, strlen( $action_url_trunc ) - 1 );
        }


        // wrap template with form and hidden inputs
        ob_start();
        ?>
            <div data-id="form-wrapper">
                <form novalidate method="post" action="<?= $action_url_trunc ?>/wp-json/bsx/v1/mailer/" data-fn="mail-form">
                    <?= $template ?>
                    <input type="hidden" name="nonce" value="<?= bsx_theme_forms_create_nonce( $functions_file_basename ) ?>">
                    <input type="hidden" name="hv__text__r" value="" data-g-tg="hv">
                    <input type="hidden" name="hv_k__x__r" value="" data-g-tg="hv-k">
                    <input type="hidden" name="idh__text__r" value="<?= $form_id ?>">
                </form>
                <div data-g-tg="message-wrapper">
                    <div data-g-tg="success-message" aria-hidden="true" style="display: none;">
                        <div class="alert alert-success lead mb-4" role="alert">
                            <span class="fa fa-check fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span>
                        </div>
                    </div>
                    <div data-g-tg="error-message" aria-hidden="true" style="display: none;">
                        <div class="alert alert-danger lead mb-4" role="alert">
                            <span class="fa fa-exclamation-triangle fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span>
                        </div>
                    </div>
                </div><!-- /[data-g-tg="message-wrapper"] -->
            </div><!-- /[data-id="form-wrapper"] -->
        <?php
        $html = ob_get_clean();

        return $html;
    }


    private static function translate( $translate_string ) {
        // from: [translate::MY TEXT EXAMPLE]
        
        // remove brackets from both sides
        $translate_string = ltrim( $translate_string, '[' );
        $translate_string = rtrim( $translate_string, ']' );

        // get translatable text
        $space_split = explode( '::', $translate_string );
        $trans_text = $space_split[ 1 ];

        $return = esc_html__( $trans_text, 'bsx-wordpress' );

        // print_r( $return );

        return $return;
    } // /translate()


    private static function parse_input( $input_string ) {
        
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
        $separator = '::';
        if ( strpos( $conf_data, $separator ) !== false ) {
            $conf_split = explode( $separator, $conf_data );
            $type = $conf_split[ 0 ];
            $name = isset( $conf_split[ 1 ] ) ? $conf_split[ 1 ] : '';
        }
        else {
            $type = $conf_data;
        }

        $return = '';

        switch ( $type ) {
            case 'message':
                $return .= '<textarea' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' name="' . $name . '__' . $type . ( $required ? '__r' : '' ) . '"' . ( $required ? ' required' : '' ) . '></textarea>';
                break;

            case 'human-verification-input':
                $return .= '<input' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' type="text" name="human_verification__text__r" required>';
                break;

            case 'human-verification-display':
                $return .= '<div' . ( $attributes != '' ? ' ' . $attributes : '' ) . ' data-g-tg="hvd"></div>';
                break;

            case 'human-verification-refresh-attr':
                $return .= 'data-g-fn="refresh-hv"';
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

}


// function bsx_theme_forms_add_shortcode( $atts = [] ) {

//     $data = shortcode_atts( array(
//         'id' => '',
//     ), $atts );

//     if ( empty( $data[ 'id' ] ) ){
//         return "";
//     }

//     return ( new Bsx_Mail_Form )->make_form_from_template( $data[ 'id' ] );
// }
// add_shortcode( 'theme-form', 'bsx_theme_forms_add_shortcode' );