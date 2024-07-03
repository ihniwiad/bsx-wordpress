<?php

function bsx_theme_forms_create_nonce( $secret_string ) {
    $ip_address = $_SERVER[ 'REMOTE_ADDR' ];
    $today = strtotime( 'today' ); // expires on midnight

    return md5( $ip_address . $secret_string . $today );
}

function bsx_theme_forms_verify_nonce( $nonce, $secret_string ) {
    $ip_address = $_SERVER[ 'REMOTE_ADDR' ];
    $today = strtotime( 'today' ); // expires on midnight
    $yesterday = strtotime( 'yesterday' ); // allow nonce have been created yesterday

    return (
        $nonce == md5( $ip_address . $secret_string . $today )
        || $nonce == md5( $ip_address . $secret_string . $yesterday )
    );
}