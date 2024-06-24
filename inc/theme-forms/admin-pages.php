<?php

class Theme_Forms_Admin_Pages {

    public static function init() {
        global $functions_file_basename;
        global $theme_forms_database_handler;
        global $theme_forms_list_table;


        // check url if show list or action

        $allowed_action_values = [ 'view', 'edit', 'delete' ];

        if ( isset( $_GET[ 'action' ] ) && in_array( $_GET[ 'action' ], $allowed_action_values ) && isset( $_GET[ 'id' ] ) && is_numeric( $_GET[ 'id' ] ) ) {
            // show single entry
            $id = $_GET[ 'id' ];

            if ( $_GET[ 'action' ] == 'view' ) {
                // show view page
                self::show_view_page( $id );
            }
            else if ( $_GET[ 'action' ] == 'edit' ) {
                // show edit page
                self::show_edit_page( $id );
            }
            else if ( $_GET[ 'action' ] == 'delete' ) {
                // delete, then show list page

                if ( isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], 'delete' . $id . $functions_file_basename ) ) {
                    // delete item
                    $deleted = $theme_forms_database_handler->delete_row( $id );

                    if ( false === $deleted ) {
                        // error
                        printf(
                            '<div class="notice notice-error">
                                <p>%1$s</p>
                            </div>',
                            esc_html__( 'Error while trying to update database. Your data has not been deleted.', 'bsx-wordpress' ),
                        );
                    }
                    else {
                        // successfully updated
                        printf(
                            '<div class="notice notice-success">
                                <p>%1$s</p>
                            </div>',
                            esc_html__( 'Your entry was successfully deleted.', 'bsx-wordpress' ),
                        );
                    }
                }
                else {
                    // skip delete
                }

                self::show_list_page();
            }
            else {
                // show nothing
            }




        }
        else {
            // show list page

            self::show_list_page();

        }

    } // /init()


    public static function show_view_page( $id ) {

        global $functions_file_basename;
        global $theme_forms_database_handler;

        // $table_name = $wpdb->prefix . 'bsx_themeforms_entries';
        // // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id", ARRAY_A );
        // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id" );
        
        $result = $theme_forms_database_handler->get_row( $id );

        ?>
            <h1 class="page-title"><?php 
                printf(
                    /* translators: %s: entry id */
                    esc_html__( 'View Theme Form Entry %s', 'bsx-wordpress' ),
                    esc_html( $id )
                ); ?></h1>
            <div class="">
                <?php
                    printf( '<a class="button" href="?page=%s">%s</a>', esc_attr( $_REQUEST[ 'page' ] ), '&larr; ' . esc_html__( 'Entries List', 'bsx-wordpress' ) );
                ?>
            </div>

            <div id="poststuff" class="">
                <div id="post-body" class="metabox-holder columns-2">

                    <!-- left column -->
                    <div id="post-body-content">
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle">
                                    <?php echo esc_html( $result[ 0 ]->title ); ?>
                                </h2>
                            </div>
                            <div class="inside">

                                <h3 class=""><?php esc_html_e( 'Content', 'bsx-wordpress' ); ?></h3>
                                <p>
                                    <?php 
                                        // might be stored in database with `\n` or `<br/>`
                                        $content = $result[ 0 ]->content;
                                        $content = str_replace( '&lt;br/&gt;', "<br/>", $content );
                                        $content = str_replace( "<br/>", "\n", $content );
                                        $content = str_replace( "<br />", "\n", $content );
                                        $content = str_replace( "\n\n", "\n", $content );
                                        echo nl2br( esc_html( $content ) );
                                    ?>
                                </p>

                                <hr>

                                <h3 class=""><?php esc_html_e( 'Fields', 'bsx-wordpress' ); ?></h3>
                                <div>
                                    <?php 
                                        $fields = unserialize( $result[ 0 ]->fields );

                                        echo '<table style="width: 100%;">';
                                        printf(
                                            '<thead style="background: #f0f0f1; height: 2.5em;"><th>%s</th><th>%s</th></thead>',
                                            esc_html__( 'Field Name', 'bsx-wordpress' ),
                                            esc_html__( 'Field Value', 'bsx-wordpress' ),
                                        );
                                        echo '<tbody>';
                                        $count = 0;
                                        foreach ( $fields as $key => $value ) {
                                            printf(
                                                '<tr%s><td style="vertical-align: top"><b>%s</b></td><td>%s</td></tr>',
                                                ( $count % 2 == 0 ) ? '' : ' style="background: #f6f6f6;"',
                                                esc_html( $key ),
                                                nl2br( esc_html( $value ) ),
                                            );
                                            $count += 1;
                                        }
                                        echo '</tbody>';
                                        echo '</table>';
                                    ?>
                                </div>

                                <hr>
                            </div>
                        </div>
                    </div>

                    <!-- right column -->
                    <div id="postbox-container-1" class="postbox-container">
            
                        <!-- details box -->
                        <div class="postbox">

                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e( 'Details', 'bsx-wordpress' ); ?></h2>
                            </div>

                            <div class="inside">
                                <?php
                                    $detail_template = '<p><strong>%s:</strong> <span>%s</span></p>';

                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'ID', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->id ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Date', 'bsx-wordpress' ),
                                        date_i18n( "D, j. F Y H:i:s", DateTime::createFromFormat( 'Y-m-d H:i:s', $result[ 0 ]->date )->getTimestamp() )
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form ID', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->form_id ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form Title', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->form_title ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Status', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->status ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'IP Address', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->ip_address ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'User Agent', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->user_agent ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Comment', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->comment ),
                                    );
                                ?>
                            </div>

                        </div>
                        
                        <!-- actions box -->
                        <div class="postbox">

                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e( 'Actions' ); ?></h2>
                            </div>

                            <div class="inside">
                                <?php
                                    printf( '<a class="button button-primary button-large" href="?page=%s&action=%s&id=%s">' . esc_html__( 'Edit' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'edit', absint( $id ) );
                                ?>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        <?php

    } // /show_view_page()


    public static function show_edit_page( $id ) {
        
        global $functions_file_basename;
        global $theme_forms_database_handler;

        $fields_prefix = 'field_'; // form input prefix for all fields

        // check if post data

        // verify nonce
        if (
            isset( $_POST[ 'wpnonce' ] ) && wp_verify_nonce( $_POST[ 'wpnonce' ], 'edit' . $functions_file_basename )
            && isset( $_POST[ 'id' ] ) && is_numeric( $_POST[ 'id' ] )
            && isset( $theme_forms_database_handler ) && $theme_forms_database_handler instanceof Theme_Forms_Database_Handler
        ) {
            // check all fields

            $data = [];
            $format = [];

            // get fields, serialize
            $fields = [];
            foreach ( $_POST as $key => $value ) {
                if ( substr( $key, 0, strlen( $fields_prefix ) ) === $fields_prefix ) {
                    $shorted_key = substr( $key, strlen( $fields_prefix ) );
                    $fields[ $shorted_key ] = $value;
                }
            }

            $data[ 'fields' ] = serialize( $fields );
            $format[] = '%s';


            // get other data but fields
            $allowed_keys = [
                'title',
                'content',
                'status',
                'comment',
            ];
            $allowed_format = [
                '%s',
                '%s',
                '%s',
                '%s',
            ];

            // special field keys that are stored in database to be listed/sortable in backend list table
            $allowed_field_keys = [
                'f_email', // will have $fields_prefix in $_POST object
                'f_name',
                'f_phone',
                'f_first_name',
                'f_last_name',

                'f_company',
                'f_subject',
            ];
            $allowed_field_format = [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',

                '%s',
                '%s',
            ];


            foreach ( $_POST as $key => $value ) {
                if ( in_array( $key, $allowed_keys ) ) {
                    $data[ $key ] = $value;
                    $format[] = $allowed_format[ intval( array_keys( $allowed_keys, $key ) ) ];
                }
                else if ( substr( $key, 0, strlen( $fields_prefix ) ) === $fields_prefix ) {
                    // is prefixed field name
                    // database columns use prefix `f_` for fields (only special fields are saved in database as column)
                    $unprefixed_key = 'f_' . substr( $key, strlen( $fields_prefix ), strlen( $key ) );
                    if ( in_array( $unprefixed_key, $allowed_field_keys ) ) {
                        $data[ $unprefixed_key ] = $value;
                        $format[] = $allowed_field_format[ intval( array_keys( $allowed_field_keys, $unprefixed_key ) ) ];
                    }
                }
            }


            if ( ! empty( $data ) && count( $data ) == count( $format ) ) {
                // save 

                // add modified date
                $data[ 'date_modified' ] = current_time( 'mysql' );
                $format[] = '%s';

                $updated = $theme_forms_database_handler->update_row( 
                    $_POST[ 'id' ],
                    $data,
                    $format,
                );

                if ( false === $updated ) {
                    // error
                    printf(
                        '<div class="notice notice-error">
                            <p>%1$s</p>
                        </div>',
                        esc_html__( 'Error while trying to update database. Your data has not been saved.', 'bsx-wordpress' ),
                    );
                }
                else {
                    // successfully updated
                    printf(
                        '<div class="notice notice-success">
                            <p>%1$s</p>
                        </div>',
                        esc_html__( 'Your entry was successfully updated.', 'bsx-wordpress' ),
                    );
                }
            }
        }
        else {
            echo '<!-- not saving, just displaying -->';
        }



        $result = $theme_forms_database_handler->get_row( $id );



        ?>
            <h1 class="page-title"><?php echo esc_html__( 'Edit Theme Form Entry', 'bsx-wordpress' ) . ' ' . esc_html( $id ); ?></h1>
            <div class="">
                <?php
                    printf( '<a class="button" href="?page=%s">%s</a>', esc_attr( $_REQUEST[ 'page' ] ), '&larr; ' . esc_html__( 'Entries List', 'bsx-wordpress' ) );
                ?>
            </div>

            <form id="poststuff" class="" method="POST">
                <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce( 'edit' . $functions_file_basename ); ?>">
                <input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>">

                <div id="post-body" class="metabox-holder columns-2">

                    <?php
                        $detail_template = '<p><strong>%s:</strong> <span>%s</span></p>';

                        $detail_textarea_template = '<div><label for="edit-%1$s">%2$s:</label></div><div><textarea id="edit-%1$s" name="%1$s" rows="%4$d" style="width: 100%%;">%3$s</textarea></div>';

                        $detail_select_template = '<div><label for="edit-%1$s">%2$s:</label></div><div><select id="edit-%1$s" name="%1$s" style="width: 100%%;">%3$s</select></div>';
                        $detail_select_option_template = '<option value="%1$s"%3$s>%2$s</option>';
                    ?>

                    <!-- left column -->
                    <div id="post-body-content">



                        <div id="titlediv">
                            <div id="titlewrap">
                                <label id="title-prompt-text" class="screen-reader-text" for="title"><?php esc_html__( 'Title', 'bsx-wordpress' ); ?>;</label>
                                <input id="title" type="text" name="title" size="30" spellcheck="true" autocomplete="off" value="<?php echo esc_attr( $result[ 0 ]->title ); ?>">
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header">
                            </div>
                            <div class="inside">

                                <h3 class=""><?php esc_html_e( 'Content', 'bsx-wordpress' ); ?></h3>
                                <p>
                                    <?php
                                        printf( 
                                            $detail_textarea_template, 
                                            'content',
                                            esc_html__( 'Content', 'bsx-wordpress' ),
                                            str_replace( '&lt;br/&gt;', "\n", esc_textarea( $result[ 0 ]->content ) ), // is textarea
                                            12,
                                        );
                                    ?>

                                </p>

                                <hr>

                                <h3 class=""><?php esc_html_e( 'Fields', 'bsx-wordpress' ); ?></h3>
                                <div>
                                    <?php 
                                        $fields = unserialize( $result[ 0 ]->fields );

                                        // TODO: move into text/config class
                                        $readonly_field_values = [
                                            'human_verification',
                                            'hv',
                                            'hv_k',
                                            'idh',
                                        ];

                                        echo '<table style="width: 100%;">';
                                        printf(
                                            '<thead style="background: #f0f0f1; height: 2.5em;"><th>%s</th><th>%s</th></thead>',
                                            esc_html__( 'Field Name', 'bsx-wordpress' ),
                                            esc_html__( 'Field Value', 'bsx-wordpress' ),
                                        );
                                        echo '<tbody>';
                                        $count = 0;
                                        foreach ( $fields as $key => $value ) {
                                            $input_or_textarea = str_contains( $value, "\n" )
                                                ? '<textarea id="edit-%1$s" name="' . $fields_prefix . '%1$s" rows="5" style="width: 100%%;"%4$s>%2$s</textarea>'
                                                : '<input id="edit-%1$s" name="' . $fields_prefix . '%1$s" value="%2$s" style="width: 100%%;"%4$s>'
                                            ;
                                            printf(
                                                '<tr%3$s><td style="vertical-align: top"><b><label for="edit-%1$s">%1$s</label></b></td><td>' . $input_or_textarea . '</td></tr>',
                                                esc_html( $key ),
                                                // TODO: check $value for "\n", show as <br>
                                                str_contains( $value, "\n" ) ? esc_textarea( $value ) : esc_html( $value ),
                                                ( $count % 2 == 0 ) ? '' : ' style="background: #f6f6f6;"',
                                                ( in_array( $key, $readonly_field_values ) ) ? ' readonly' : '',
                                            );
                                            $count += 1;
                                        }
                                        echo '</tbody>';
                                        echo '</table>';
                                    ?>
                                </div>

                                <hr>
                            </div>
                        </div>
                    </div>

                    <!-- right column -->
                    <div id="postbox-container-1" class="postbox-container">
            
                        <!-- details box -->
                        <div class="postbox">

                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e( 'Details', 'bsx-wordpress' ); ?></h2>
                            </div>

                            <div class="inside">
                                <?php
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'ID', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->id ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Date', 'bsx-wordpress' ),
                                        // DateTime::createFromFormat( 'Y-m-d H:i:s', $result[ 0 ]->date )->format( "D, j. F Y H:i:s" ),
                                        date_i18n( "D, j. F Y H:i:s", DateTime::createFromFormat( 'Y-m-d H:i:s', $result[ 0 ]->date )->getTimestamp() )
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form ID', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->form_id ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form Title', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->form_title ),
                                    );

                                    // prepare options
                                    // TODO: move into text/config class
                                    $options_values = [
                                        'auto-logged' => 'auto-logged', // esc_html__( 'Auto logged', 'bsx-wordpress' ),
                                        'to-do' => 'to-do', // esc_html__( 'To do', 'bsx-wordpress' ),
                                        'done' => 'done', // esc_html__( 'Done', 'bsx-wordpress' ),
                                    ];
                                    $options = '';
                                    foreach ( $options_values as $key => $value ) {
                                        $options .= sprintf( 
                                            $detail_select_option_template, 
                                            esc_html( $key ),
                                            esc_html( $value ),
                                            ( $key == $result[ 0 ]->status ) ? ' selected' : '',
                                        );
                                    }
                                    // display select
                                    printf( 
                                        $detail_select_template,
                                        'status',
                                        esc_html__( 'Status', 'bsx-wordpress' ),
                                        $options,
                                    );

                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'IP Address', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->ip_address ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'User Agent', 'bsx-wordpress' ),
                                        esc_html( $result[ 0 ]->user_agent ),
                                    );
                                    printf( 
                                        $detail_textarea_template,
                                        'comment',
                                        esc_html__( 'Comment', 'bsx-wordpress' ),
                                        esc_textarea( $result[ 0 ]->comment ),
                                        3,
                                    );
                                ?>
                            </div>

                        </div>
                        
                        <!-- actions box -->
                        <div class="postbox" id="submitdiv">

                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e( 'Actions' ); ?></h2>
                            </div>

                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="major-publishing-actions">

                                        <div id="publishing-action">
                                            <?php
                                                printf(
                                                    '<button class="button button-primary button-large" type="submit">%s</button>',
                                                    esc_html__( 'Save' ),
                                                );
                                            ?>
                                        </div>
                                        <div id="delete-action">
                                            <?php

                                                // prepare delete nonce
                                                $delete_nonce = wp_create_nonce( 'delete' . $id . $functions_file_basename );

                                                printf(
                                                    '<a class="submitdelete deletion" href="?page=%1$s&action=%2$s&id=%3$d&_wpnonce=%4$s" onclick="return confirm( \'%6$s\' );">%5$s</a>',
                                                    esc_attr( $_REQUEST[ 'page' ] ),
                                                    'delete',
                                                    absint( $id ),
                                                    $delete_nonce,
                                                    esc_html__( 'Delete' ),
                                                    sprintf(
                                                        /* translators: %1$s: The title of the entry. %1$s: The email address. %3$d: The id of the entry. */
                                                        esc_attr__( 'Really delete ”%1$s“ from %2$s (id: %3$d)?', 'bsx-wordpress' ),
                                                        esc_html( $result[ 0 ]->title ),
                                                        esc_html( $result[ 0 ]->f_email ),
                                                        absint( $id ),
                                                    ),
                                                );
                                            ?>
                                        </div>

                                        <div class="clear"></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </form>
        <?php

    } // show_edit_page()


    public static function show_list_page() {
        global $theme_forms_list_table;

        ?>
            <h1><?php esc_html_e( 'Theme Form Entries', 'bsx-wordpress' ); ?></h1>
            <!-- form method="post" onSubmit="return function() { if ( ! confirm( 'Sure to delete items ' + this.form.serialize() + '?' ) ) return false; else this.form.submit(); }" -->
            <!-- form method="post" onsubmit="return confirm( 'Do you want to delete ' + Object.values( this ).reduce( ( obj, field ) => { obj[ field.name ] = field.value; return obj }, {} ) + '?' )" -->
            <form method="post" onsubmit="return confirm( '<?php esc_html_e( 'Do you really want to delete one or multiple items?', 'bsx-wordpress' ) ?>' )">
                <?php
                    // list contents in table
                    if ( isset( $theme_forms_list_table ) && $theme_forms_list_table instanceof Theme_Forms_List_Table ) {
                        // $theme_forms_list_table->screen_option(); 
                        $theme_forms_list_table->prepare_items(); 
                        $theme_forms_list_table->display();
                    }
                ?>
            </form>
        <?php

    } // /show_list_page()

}
