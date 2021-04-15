<?php

class Bsx_Mail_Form {

    public function register_form_settings() {

    }

    public function init() {


        // REST ROUTES

        /**
         * callback function for routes endpoint
         */
        function bsx_mailer_post_endpoint( $request ) {

            if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" ) {
                // ok, try send mail

                $response = '';

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

                if ( ! empty( $sanitized_values[ 'subject' ] ) ) {
                    $mail_subject = replace_placeholders( $sanitized_values[ 'subject' ], $sanitized_values );
                }
                else {
                    // fallback subject
                    $mail_subject = 'Mail from contact form at ' . get_site_url();
                }

                if ( ! empty( $sanitized_values[ 'template' ] ) ) {
                    $mail_content = replace_placeholders( $sanitized_values[ 'template' ], $sanitized_values );
                }
                else {
                    // fallback content
                    foreach ( $sanitized_values as $key => $value ) {
                        $mail_content .= $key . ': ' . $value . '\n';
                    }
                }

                // TEST
                $test = '';
                if ( isset( $hv_values ) && ! empty( $hv_values ) ) {
                    foreach ( $hv_values as $val ) {
                        $test .= $val . ', ';
                    }
                }
                else {
                    $test = 'undefiened $hv_values';
                }
                // /TEST

                $response .= 'HV VALUE:' . "\n\n" . ( isset( $hv_value ) ? $hv_value : 'undefined' ) . "\n\n" . "(type: " . ( isset( $hv_type ) ? $hv_type : 'undefined' ) .", values: $test) (calc_hv_value: $_calc_hv_value)" . "\n\n" . 'SANITIZED OUTPUT:' . "\n\n";
                foreach ( $sanitized_values as $key => $value ) {
                    $response .= $key . ': ' . $value . '<br>';
                }

                // get recipient mail
                $recipient_mail = get_option( 'mail' );
                // TODO: get from somewhere, e.g. theme config
                $from_mail = 'noreply@example.com';

                // TODO: check hv
                // && $sanitized_values[ 'human_verification' ] === $_calc_hv_value 
                if ( $validation_ok && $sanitized_values[ 'human_verification' ] == $_calc_hv_value && ! empty( $recipient_mail ) ) {

                    // prepare headers
                    $headers = 'From: ' . $from_mail . "\r\n";
                    // $headers .= "CC: somebodyelse@example.com";

                    // mail( $recipient_mail, $mail_subject, $mail_content, $headers );

                    // return rest_ensure_response( $response );
                    return rest_ensure_response( 'RECIPIENT: ' . $recipient_mail . "\n\n" . 'SUBJECT' . "\n\n" . $mail_subject . "\n\n\n" . 'CONTENT' . "\n\n" . $mail_content . "\n\n\n" . $response );
                } 
                else {
                    // validation not ok, send forbidden 403

                    return new WP_Error( 'rest_mailer_invalid', esc_html__( 'Your request is invalid.', 'bsx-wordpress' ), array( 'status' => 403 ) );
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
                'methods'  => WP_REST_Server::CREATABLE,
                'callback' => 'bsx_mailer_post_endpoint',
            ) );
        }
        add_action( 'rest_api_init', 'bsx_mailer_register_rest_route' );

    } // /init()

}