<?php

function validateFormData( array $post_data ): array {

    $sanitized_values = array();
    $validation_ok = true;

    foreach ( $_POST as $key => $value ) {
        // extract type for validation from input name `mytype__myname`

        // form names are typically like `phone__tel__r`
        // {field name}__{field type}__{required or nothing}
        // contain only letters and lowdash `_`
        $key = preg_replace( "/[^a-zA-Z0-9_]+/", "", $key );
        $split_key = explode( '__', $key);
        $name = $split_key[ 0 ];
        $type = $split_key[ 1 ] ?? '';
        // TODO: get reqiured & type from template, ignore anything in input name after '__'
        $required = ( isset( $split_key[ 2 ] ) && $split_key[ 2 ] === 'r' ) ? true : false;
        if ( $key == 'nonce' ) $required = true;

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
        else {
            // TODO: escape before saving? quotes too?
            $value = strip_tags( $value );
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
    }

    return [ $sanitized_values, $validation_ok ];
}


// check humal verification
function validate_human_verification( array $sanitized_values ): bool {

    if ( ! isset( $sanitized_values[ 'hv' ] ) || ! isset( $sanitized_values[ 'human_verification' ] ) ) return false;

    $hv_value = $sanitized_values[ 'hv' ]; // generated hidden value – use to calculate result
    $user_hv_value = $sanitized_values[ 'human_verification' ]; // user entered result

    $hv_value = urldecode( $hv_value );

    $hv_values_extract = explode( '|', $hv_value );

    $hv_type = intval( $hv_values_extract[ 1 ] );
    $hv_values = [];
    for ( $i = 2; $i < count( $hv_values_extract ); $i++ ) {
        $hv_values[] = $hv_values_extract[ $i ];
    }

    // check if found values
    if ( empty( $hv_values ) ) return false;

    switch ( $hv_type ) {
        case 1:
            $original_hv_value = intval( $hv_values[ 0 ] ) + intval( $hv_values[ 1 ] );
            break;
        case 2:
            $original_hv_value = intval( $hv_values[ 0 ] ) - intval( $hv_values[ 1 ] );
            break;
        case 3:
            $original_hv_value = intval( $hv_values[ 0 ] ) * intval( $hv_values[ 1 ] );
            break;
        case 4:
            $original_hv_value = intval( $hv_values[ 0 ] ) / intval( $hv_values[ 1 ] );
            break;
        case 5:
            $original_hv_value = intval( $hv_values[ 0 ] . $hv_values[ 1 ] ) + intval( $hv_values[ 2 ] );
            break;
        case 6:
            $original_hv_value = intval( $hv_values[ 0 ] . $hv_values[ 1 ] ) - intval( $hv_values[ 2 ] );
            break;
        case 7:
            $original_hv_value = intval( $hv_values[ 0 ] ) + intval( $hv_values[ 1 ] ) + intval( $hv_values[ 2 ] );
            break;
        // check if numeric
        case 8:
            $before_target_value = $hv_values[ count( $hv_values ) - 1 ];
            if ( is_numeric( $before_target_value ) ) {
                $original_hv_value = intval( $before_target_value ) + 1;
            }
            else {
                $original_hv_value = chr( ord( $before_target_value ) + 1 );
            }
            break;
        case 9:
            $before_target_value = $hv_values[ count( $hv_values ) - 2 ];
            if ( is_numeric( $before_target_value ) ) {
                $original_hv_value = intval( $before_target_value ) + 1;
            }
            else {
                $original_hv_value = chr( ord( $before_target_value ) + 1 );
            }
            break;
        case 10:
            $before_target_value = $hv_values[ count( $hv_values ) - 3 ];
            if ( is_numeric( $before_target_value ) ) {
                $original_hv_value = intval( $before_target_value ) + 1;
            }
            else {
                $original_hv_value = chr( ord( $before_target_value ) + 1 );
            }
            break;
    } // /switch

    return ( $original_hv_value == $user_hv_value );

}


// form id might be custom post id or (deprecated:) hash of fix theme form index (1...n)
// that’s why a function is required to get it
function get_form_identifiers( int|string $form_id_or_hash ): array {

    // TODO: replace later by (only) custom post id

    $is_deprecated_non_post_form = false;
    $form_id = 0;

    if ( is_numeric( $form_id_or_hash ) && strlen( $form_id_or_hash ) < 32 ) {
        // is post id
        $form_id = $form_id_or_hash;
    }
    else {
        // is hash
        $is_deprecated_non_post_form = true;

        // get form index by hash
        $forms_count = Bsx_Mail_Form::get_forms_count();
        for ( $i = 1; $i <= $forms_count; $i++ ) {
            // revert (deprecated) hash to form id
            if ( hash( 'md5', 'x' . $i ) === $form_id_or_hash ) {
                $form_id = $i;
                break;
            }
        }
    }

    return [ $form_id, $is_deprecated_non_post_form ];
}


// replacing placeholders in text (mail subject, mail content)
function replace_placeholders( $text, $sanitized_values ) {
    // $text = str_replace ( '[site-title]', get_the_title(), $text );
    $text = str_replace ( '[site-url]', get_site_url(), $text );
    foreach ( $sanitized_values as $key => $value ) {
        $text = str_replace( '[' . $key . ']', $value, $text );
    }
    return $text;
}


// escape unescaped user content before dieplaying it
// function escape( string $userInput ): string {
//     return htmlspecialchars( strip_tags( $userInput ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
// }


// prepare mail content for sending with wp_mail()

// function utf8_base64( string $subjectEtc ): string {
//     return '=?UTF-8?B?' . base64_encode( escape( $subjectEtc ) ) . '?=';
// }
function prepare_headers( string $sender_mail ): string {
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
    // $headers .= 'Content-Transfer-Encoding: base64' . "\r\n"; // if using base64 encoding
    $headers .= 'From: ' . $sender_mail . "\r\n";
    // $headers .= "CC: somebodyelse@example.com";
    return $headers;
}


// get form config data
function get_mail_details( int $form_id, bool $is_deprecated_non_post_form ): array {

    // get data from post meta (modern) or options (deprecated)

    if ( $is_deprecated_non_post_form ) {
        // get data from options

        $recipient_mail = get_option( 'form-' . $form_id . '-recipient-email' );
        $sender_mail = get_option( 'form-' . $form_id . '-sender-email' );
        $mail_subject = get_option( 'form-' . $form_id . '-subject' );
        $mail_content = get_option( 'form-' . $form_id . '-mail-template' );

        $recipient_mail_2 = get_option( 'form-' . $form_id . '-recipient-email-2' );
        $sender_mail_2 = get_option( 'form-' . $form_id . '-sender-email-2' );
        $mail_subject_2 = get_option( 'form-' . $form_id . '-subject-2' );
        $mail_content_2 = get_option( 'form-' . $form_id . '-mail-template-2' );
    }
    else {
        // get data from post meta

        $meta = get_post_meta( $form_id, 'theme_forms', true );

        $recipient_mail = isset( $meta[ 'recipient_email' ] ) ? $meta[ 'recipient_email' ] : '';
        $sender_mail = isset( $meta[ 'sender_email' ] ) ? $meta[ 'sender_email' ] : '';
        $mail_subject = isset( $meta[ 'subject' ] ) ? $meta[ 'subject' ] : '';
        $mail_content = isset( $meta[ 'email_template' ] ) ? $meta[ 'email_template' ] : '';

        $recipient_mail_2 = isset( $meta[ 'recipient_2_email' ] ) ? $meta[ 'recipient_2_email' ] : '';
        $sender_mail_2 = isset( $meta[ 'sender_2_email' ] ) ? $meta[ 'sender_2_email' ] : '';
        $mail_subject_2 = isset( $meta[ 'subject_2' ] ) ? $meta[ 'subject_2' ] : '';
        $mail_content_2 = isset( $meta[ 'email_2_template' ] ) ? $meta[ 'email_2_template' ] : '';
    }

    return [
        $recipient_mail,
        $sender_mail,
        $mail_subject,
        $mail_content,
        $recipient_mail_2,
        $sender_mail_2,
        $mail_subject_2,
        $mail_content_2,
    ];
}


function write_into_database( array $config ): int|false {
    global $theme_forms_database_handler;

    if ( ! ( isset( $theme_forms_database_handler ) && $theme_forms_database_handler instanceof Theme_Forms_Database_Handler ) ) {
        return false;
    }
    if ( ! $config ) return false;

    [
        'form_id' => $form_id,
        'is_deprecated_non_post_form' => $is_deprecated_non_post_form,
        'mail_subject' => $mail_subject,
        'mail_content' => $mail_content,
        'sanitized_values' => $sanitized_values,
    ] = $config;

    $data = array(
        'date' => current_time( 'mysql' ),
        'data_gmt' => current_time( 'mysql', 1 ),
        'form_id' => $form_id,
        'form_title' => $is_deprecated_non_post_form ? 'Theme Form ' . $form_id : get_the_title( $form_id ) . ' (' . $form_id . ')',
        'title' => $is_deprecated_non_post_form ? $mail_subject : get_the_title( $form_id ) . ': ' . $mail_subject,

        'content' => $mail_content,
        'status' => 'auto-logged',
        'fields' => serialize( $sanitized_values ),
        'comment' => '',
        'ip_address' => $_SERVER[ 'REMOTE_ADDR' ],

        'user_agent' => $_SERVER[ 'HTTP_USER_AGENT' ],
        'f_email' => ( isset( $sanitized_values[ 'email' ] ) ) ? $sanitized_values[ 'email' ] : '',
        'f_name' => ( isset( $sanitized_values[ 'name' ] ) ) ? $sanitized_values[ 'name' ] : '',
        'f_phone' => ( isset( $sanitized_values[ 'phone' ] ) ) ? $sanitized_values[ 'phone' ] : '',
        'f_first_name' => ( isset( $sanitized_values[ 'first_name' ] ) ) ? $sanitized_values[ 'first_name' ] : '',

        'f_last_name' => ( isset( $sanitized_values[ 'last_name' ] ) ) ? $sanitized_values[ 'last_name' ] : '',
        'f_company' => ( isset( $sanitized_values[ 'company' ] ) ) ? $sanitized_values[ 'company' ] : '',
        'f_subject' => ( isset( $sanitized_values[ 'subject' ] ) ) ? $sanitized_values[ 'subject' ] : '',
    );
    $format = array(
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',

        '%s',
        '%s',
        '%s',
        '%s',
        '%s',

        '%s',
        '%s',
        '%s',
        '%s',
        '%s',

        '%s',
        '%s',
        '%s',
    );

    // returns inserted row index or false if error
    $insert_id = $theme_forms_database_handler->create_row( $data, $format );

    return $insert_id;
}


// if mail (1) content is empty, create fallback listing all fields
function get_mail_content_fallback( array $sanitized_values ): string {
    foreach ( $sanitized_values as $key => $value ) {
        $mail_content .= $key . ': ' . $value . "\n";
    }
}


// checks if string is placeholder `[my_placeholder_name]`, is so replaces by `my_placeholder_name[ 'my_placeholder_name' ]`
function check_if_placeholder_replace( string $text_or_placeholder, array $sanitized_values ): string {
    // ckeck if $recipient_mail_2 is mail or placeholder
    if ( substr( $text_or_placeholder, 0, 1 ) === '[' && substr( $text_or_placeholder, -1 ) === ']' ) {
        // is placeholder, get placeholder name
        $placeholder_name = ltrim( $text_or_placeholder, '[' );
        $placeholder_name = rtrim( $text_or_placeholder, ']' );
        // get placeholder value
        return isset( $sanitized_values[ $placeholder_name ] ) ? $sanitized_values[ $placeholder_name ] : '';
    }
    return $text_or_placeholder;
}


// block external referrers
function referrer_is_same_host_or_empty(): bool {
    // check referrer host is current host, disallow external access
    $referrer = $_SERVER[ 'HTTP_REFERER' ];
    $host_pattern = "/http+(s|)+:\/\/+([a-z0-9-_])+\//s";
    $matches = array();
    $has_matches = preg_match( $host_pattern, $referrer, $matches );
    $referrer_host = ( isset( $matches[ 0 ] ) ) ? $matches[ 0 ] : '';

    // check referrer, must be empty or same host
    $server_name = $_SERVER[ 'SERVER_NAME' ]; // domain (not protocol)
    $protocol = ( ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] !== 'off' || $_SERVER[ 'SERVER_PORT' ] == 443 ) ? "https://" : "http://"; // protocol
    $current_host = $protocol . $server_name . '/';

    return ( empty( $referrer_host ) || $referrer_host === $current_host );
}



/**
 * callback function for routes endpoint
 */

function bsx_mailer_post_endpoint( $request ) {
    global $functions_file_basename;


    // check if POST, return error if not
    if ( $_SERVER[ "REQUEST_METHOD" ] != "POST" ) {
        return new WP_Error( 'rest_api_sad', esc_html__( 'There was a problem with your submission.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // block external access
    if ( ! referrer_is_same_host_or_empty() ) {
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your are not allowed to access this server.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // validate form data
    [ $sanitized_values, $validation_ok ] = validateFormData( $_POST );


    // error if invalid form data
    if ( ! $validation_ok ) {
        // validation not ok, send forbidden 403
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your data is invalid.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // check nonce, error if not contained or doesn’t match
    if ( ! isset( $sanitized_values[ 'nonce' ] ) || wp_verify_nonce( $sanitized_values[ 'nonce' ], $functions_file_basename ) ) {
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Access denied!', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // check human verification, error if not valid
    if ( ! validate_human_verification( $sanitized_values ) ) {
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Human verification failed. Try again if you are a human.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // get form if from hidden input, error if unset
    if ( ! isset( $sanitized_values[ 'idh' ] ) ) {
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your form data is incomplete. Access denied!', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // start getting data to create mail(s)


    // get form identifiers – might be (modern) custom post form (deprecated) fix form
    // $form_id ... (int) post id (modern) or form index (deprecated)
    [ $form_id, $is_deprecated_non_post_form ] = get_form_identifiers( $sanitized_values[ 'idh' ] );


    // get mail datails (deprecated or modern origin)
    [
        $recipient_mail, // must be filled
        $sender_mail, // – " –
        $mail_subject, // usually filled, else have fallback
        $mail_content, // – " –
        $recipient_mail_2, // might be empty
        $sender_mail_2, // – " –
        $mail_subject_2, // – " –
        $mail_content_2, // – " –
    ] = get_mail_details( $form_id, $is_deprecated_non_post_form );


    // check both e-mail adresses of (first) mail, error if unset or invalid
    if (
        ! filter_var( $recipient_mail, FILTER_VALIDATE_EMAIL ) 
        || ! filter_var( $sender_mail, FILTER_VALIDATE_EMAIL ) 
    ) {
        // missing form config, send forbidden 403
        return new WP_Error( 'rest_api_sad', esc_html__( 'Unable to send message. Missing configuration data.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // replace placeholders (mail 1)
    $mail_subject = replace_placeholders( $mail_subject, $sanitized_values );
    $mail_content = replace_placeholders( $mail_content, $sanitized_values );


    // fallbacks if empty
    if ( empty( $mail_subject ) ) $mail_subject = 'Mail from contact form at ' . get_site_url();
    if ( empty( $mail_content )) $mail_content = get_mail_content_fallback( $sanitized_values );


    // check all mail data of (second) mail, skip if unset or invalid
    $mail_2_defined = false;

    if ( 
        ! empty( $recipient_mail_2 ) 
        && filter_var( $recipient_mail_2, FILTER_VALIDATE_EMAIL )
        && filter_var( $sender_mail_2, FILTER_VALIDATE_EMAIL )
        && ! empty( $mail_subject_2 )
        && ! empty( $mail_content_2 )
    ) {
        // might be e-mail address or placeholder, e.g. [email]
        $recipient_mail_2 = check_if_placeholder_replace( $recipient_mail_2, $sanitized_values );

        // replace placeholders (mail 2)
        $mail_subject_2 = replace_placeholders( $mail_subject_2, $sanitized_values );
        $mail_content_2 = replace_placeholders( $mail_content_2, $sanitized_values );

        $mail_2_defined = true;
    }


    // all data ok, try sending


    // write into database (before sendign)
    $successfully_stored_in_db = write_into_database( [
        'form_id' => $form_id,
        'is_deprecated_non_post_form' => $is_deprecated_non_post_form,
        'mail_subject' => $mail_subject,
        'mail_content' => $mail_content,
        'sanitized_values' => $sanitized_values,
    ] );


    // check if successfully stored in db, error if not (prio 1 is db, prio 2 is mail)
    if ( ! $successfully_stored_in_db ) {
        return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Error while trying to connect database.', 'bsx-wordpress' ), array( 'status' => 403 ) );
    }


    // send mail(s)
    $mail_sucessfully_sent = wp_mail( 
        strip_tags( $recipient_mail ), 
        // utf8_base64( escape( $mail_subject ) ), 
        // utf8_base64( escape( $mail_content ) ), 
        strip_tags( $mail_subject ),
        strip_tags( $mail_content ),
        prepare_headers( strip_tags( $sender_mail ) ) 
    );

    $mail_2_successfully_sent_or_not_defined = ( 
        ! $mail_2_defined
        || ( 
            $mail_2_defined 
            && wp_mail( 
                strip_tags( $recipient_mail_2 ), 
                // utf8_base64( escape( $mail_subject_2 ) ), 
                // utf8_base64( escape( $mail_content_2 ) ), 
                strip_tags( $mail_subject_2 ),
                strip_tags( $mail_content_2 ),
                prepare_headers( strip_tags( $sender_mail_2 ) ) 
            ) 
        ) 
    );


    if ( $mail_sucessfully_sent && $mail_2_successfully_sent_or_not_defined ) {
        // success
        return rest_ensure_response( esc_html__( 'Thank you. Your message has been sent successfully.', 'bsx-wordpress' ) );
    }
    else {
        // error while sending after storing in database
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went wrong while trying to send message.', 'bsx-wordpress' ), array( 'status' => 500 ) );
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

