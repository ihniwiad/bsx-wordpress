<?php


function bsx_theme_forms_add_shortcode( $atts = [] ) {

    $data = shortcode_atts( array(
        'id' => '',
    ), $atts );

    if ( empty( $data[ 'id' ] ) ){
        return "";
    }

    return Theme_Forms_Form_Template::make_form_from_template( $data[ 'id' ] );
}
add_shortcode( 'theme-form', 'bsx_theme_forms_add_shortcode' );