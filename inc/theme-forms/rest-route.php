<?php

/**
 * callback function for routes endpoint
 */

function bsx_mailer_post_endpoint( $request ) {
    global $theme_forms_database_handler;

    if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
        // ok, validate, try sending

        $sanitized_values = array();
        $validation_ok = true;

        foreach ( $_POST as $key => $value ) {
            // extract type for validation from input name `mytype__myname`
            $key = filter_var( $key, FILTER_SANITIZE_STRING );
            $split_key = explode( '__', $key);
            $name = $split_key[ 0 ];
            $type = $split_key[ 1 ];
            $required = ( isset( $split_key[ 2 ] ) && $split_key[ 2 ] === 'r' ) ? true : false;

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
                // better use strip_tags()
                // $value = filter_var( $value, FILTER_SANITIZE_STRING );
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







        // TODO: replace later by (custom) post id

        $form_id = $sanitized_values[ 'idh' ];
        $is_deprecated_non_post_form = false;
        $form_index = ''; // only used for deprecated non post forms

        if ( is_numeric( $form_id ) && strlen( $form_id ) < 32 ) {
        	// is post id

        	// do nothing
        }
        else {
        	// is hash
        	$is_deprecated_non_post_form = true;

	        // get form index by hash
	        $forms_count = Bsx_Mail_Form::get_forms_count();
	        for ( $i = 1; $i <= $forms_count; $i++ ) {
	            if ( hash( 'md5', 'x' . $i ) === $sanitized_values[ 'idh' ] ) {
	                $form_index = $i;
	                break;
	            }
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



        // TODO: get data from post meta instead of optiona

        if ( $is_deprecated_non_post_form ) {
        	// get data from options

	        $recipient_mail = get_option( 'form-' . $form_index . '-recipient-email' );
	        $sender_mail = get_option( 'form-' . $form_index . '-sender-email' );
	        $mail_subject = strip_tags( get_option( 'form-' . $form_index . '-subject' ) );
	        $mail_content = filter_var( get_option( 'form-' . $form_index . '-mail-template' ), FILTER_UNSAFE_RAW );

	        $recipient_mail_2 = get_option( 'form-' . $form_index . '-recipient-email-2' );
	        $sender_mail_2 = get_option( 'form-' . $form_index . '-sender-email-2' );
	        $mail_subject_2 = strip_tags( get_option( 'form-' . $form_index . '-subject-2' ) );
	        $mail_content_2 = filter_var( get_option( 'form-' . $form_index . '-mail-template-2' ), FILTER_UNSAFE_RAW );
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




        // sanitize

        $sanitized_mail_subject = strip_tags( $mail_subject );
        $sanitized_mail_content = filter_var( $mail_content, FILTER_UNSAFE_RAW );
        $sanitized_mail_subject_2 = strip_tags( $mail_subject_2 );
        $sanitized_mail_content_2 = filter_var( $mail_content_2, FILTER_UNSAFE_RAW );




        $mail_subject = replace_placeholders( $sanitized_mail_subject, $sanitized_values );
        if ( empty( $mail_subject ) ) {
            // fallback subject (only mail 1)
            $mail_subject = 'Mail from contact form at ' . get_site_url();
        }

        // TODO: better sanitation possible?
        $mail_content = replace_placeholders( $sanitized_mail_content, $sanitized_values );
        $mail_content = str_replace ( "\n", "<br/>", $mail_content );

        if ( empty( $mail_content ) ) {
            // fallback content (only mail 1)
            foreach ( $sanitized_values as $key => $value ) {
                $mail_content .= $key . ': ' . $value . "\n";
            }
        }


        // ckeck if $recipient_mail_2 is filled
        $mail_2_ok = false;

        if ( ! empty( $recipient_mail_2 ) ) {


            // ckeck if $recipient_mail_2 is mail or placeholder
            if ( substr( $recipient_mail_2, 0, 1 ) === '[' && substr( $recipient_mail_2, -1 ) === ']' ) {
                // is placeholder, get placeholder name
                $placeholder_name = ltrim( $recipient_mail_2, '[' );
                $placeholder_name = rtrim( $placeholder_name, ']' );
                // get placeholder value
                $recipient_mail_2 = isset( $sanitized_values[ $placeholder_name ] ) ? $sanitized_values[ $placeholder_name ] : '';
            }

            // sanitize
            $mail_subject_2 = replace_placeholders( $sanitized_mail_subject_2, $sanitized_values );

            // TODO: better sanitation possible?
            $mail_content_2 = replace_placeholders( $sanitized_mail_content_2, $sanitized_values );
            $mail_content_2 = str_replace ( "\n", "<br/>", $mail_content_2 );


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

        // check form backend config
        if (
            filter_var( $recipient_mail, FILTER_VALIDATE_EMAIL ) 
            && filter_var( $sender_mail, FILTER_VALIDATE_EMAIL ) 
        ) {
            // backend config ok, check validation

            if ( 
                $validation_ok 
                && ( empty( $referrer_host ) || $referrer_host === $current_host ) 
                && $sanitized_values[ 'human_verification' ] == $_calc_hv_value 
            ) {
                // validation ok, try sending

                // prepare headers (both mails)
                $global_headers = 'MIME-Version: 1.0' . "\r\n";
                $global_headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

                $headers = $global_headers . 'From: ' . $sender_mail . "\r\n";
                // $headers .= "CC: somebodyelse@example.com";

                // make utf-8 compatible
                $encoded_mail_subject = '=?UTF-8?B?'.base64_encode( $mail_subject ).'?=';

                if ( isset( $sender_mail_2 ) && ! empty( $sender_mail_2 ) ) {
                    $headers_2 = $global_headers . 'From: ' . $sender_mail_2 . "\r\n";
                    
                    // make utf-8 compatible
                    $encoded_mail_subject_2 = '=?UTF-8?B?'.base64_encode( $mail_subject_2 ).'?=';
                }


                // write into database

                if ( ! ( isset( $theme_forms_database_handler ) && $theme_forms_database_handler instanceof Theme_Forms_Database_Handler ) ) {

                    return new WP_Error( 'rest_api_sad', esc_html__( 'Error while trying to connect database.', 'bsx-wordpress' ), array( 'status' => 403 ) );
                }

                $data = array(
                    'date' => current_time( 'mysql' ),
                    'data_gmt' => current_time( 'mysql', 1 ),
                    'form_id' => $is_deprecated_non_post_form ? $form_index : $form_id,
                    'form_title' => $is_deprecated_non_post_form ? 'Theme Form ' . $form_index : get_the_title( $form_id ) . ' (' . $form_id . ')',
                    'title' => $mail_subject,

                    'content' => $mail_content,
                    'status' => 'auto-logged',
                    'fields' => serialize( $sanitized_values ),
                    'comment' => '',
                    'ip_address' => $_SERVER[ 'REMOTE_ADDR' ],

                    'user_agent' => $_SERVER[ 'HTTP_USER_AGENT' ],
                    'email' => ( isset( $sanitized_values[ 'email' ] ) ) ? $sanitized_values[ 'email' ] : '',
                    'name' => ( isset( $sanitized_values[ 'name' ] ) ) ? $sanitized_values[ 'name' ] : '',
                    'phone' => ( isset( $sanitized_values[ 'phone' ] ) ) ? $sanitized_values[ 'phone' ] : '',
                    'first_name' => ( isset( $sanitized_values[ 'first_name' ] ) ) ? $sanitized_values[ 'first_name' ] : '',

                    'last_name' => ( isset( $sanitized_values[ 'last_name' ] ) ) ? $sanitized_values[ 'last_name' ] : '',
                    'company' => ( isset( $sanitized_values[ 'company' ] ) ) ? $sanitized_values[ 'company' ] : '',
                    'subject' => ( isset( $sanitized_values[ 'subject' ] ) ) ? $sanitized_values[ 'subject' ] : '',
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

                // returns false if error
                $inserted = $theme_forms_database_handler->create_row( $data, $format );

                if (
                    // true 
                    $inserted
                    && wp_mail( $recipient_mail, $encoded_mail_subject, $mail_content, $headers )
                    && ( 
                        ! $mail_2_ok
                        || ( $mail_2_ok && wp_mail( $recipient_mail_2, $encoded_mail_subject_2, $mail_content_2, $headers_2 ) ) 
                    )
                ) {
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
        }
        else {
            // missing form config, send forbidden 403
            return new WP_Error( 'rest_api_sad', esc_html__( 'Unable to send email. Missing configuration data.', 'bsx-wordpress' ), array( 'status' => 403 ) );
        }

     
        // error 500
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went wrong while trying to send email.', 'bsx-wordpress' ), array( 'status' => 500 ) );
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


    // } // /register_mailer_rest_route()