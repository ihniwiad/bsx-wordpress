<?php

class Theme_Forms_Admin_Pages {

    public function init() {
        global $functions_file_basename;
        global $theme_forms_database_handler;


        // echo '<pre style="width: 100%; overflow: auto;">';
        // print_r( $wpdb->tables );
        // echo '</pre>';

        // TODO: check if missing database table
        // show_message( __( 'Missing database table – Please deactivate and activate your Theme to create the missing table.', 'bsx-wordpress' ) );




        // check url if show list or action

        $allowed_action_values = [ 'view', 'edit', 'delete' ];

        if ( isset( $_GET[ 'action' ] ) && in_array( $_GET[ 'action' ], $allowed_action_values ) && isset( $_GET[ 'id' ] ) && is_numeric( $_GET[ 'id' ] ) ) {
            // show single entry
            $id = $_GET[ 'id' ];

            if ( $_GET[ 'action' ] == 'view' ) {
                // show view page
                $this->show_view_page( $id );
            }
            else if ( $_GET[ 'action' ] == 'edit' ) {
                // show edit page
                $this->show_edit_page( $id );
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

                $this->show_list_page();
            }
            else {
                // show nothing
            }




        }
        else {
            // show list page

            $this->show_list_page();

        }

    } // /init()


    private function show_view_page( $id ) {

        global $functions_file_basename;
        global $theme_forms_database_handler;

        // $table_name = $wpdb->prefix . 'bsx_themeforms_entries';
        // // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id", ARRAY_A );
        // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id" );
        
        $result = $theme_forms_database_handler->get_row( $id );

        ?>
            <h1 class="page-title"><?php echo esc_html__( 'View Theme Form Entry', 'bsx-wordpress' ) . ' ' . $id; ?></h1>
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
                                    <?php echo $result[ 0 ]->content; ?>
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
                                        echo '<body>';
                                        $count = 0;
                                        foreach ( $fields as $key => $value ) {
                                            printf(
                                                '<tr%s><td><b>%s</b></td><td>%s</td></tr>',
                                                ( $count % 2 == 0 ) ? '' : ' style="background: #f6f6f6;"',
                                                $key,
                                                $value,
                                            );
                                            $count += 1;
                                        }
                                        echo '</tbody>';
                                        echo '</table>';
                                    ?>
                                </div>

                                <hr>

                                <?php 
                                    // echo '<pre style="width: 100%; overflow: auto;">';
                                    // print_r( $result );
                                    // echo '</pre>';
                                ?>
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
                                        $result[ 0 ]->id,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Date', 'bsx-wordpress' ),
                                        DateTime::createFromFormat( 'Y-m-d H:i:s', $result[ 0 ]->date )->format( "D, j. F Y H:i:s" ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form ID', 'bsx-wordpress' ),
                                        $result[ 0 ]->form_id,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form Title', 'bsx-wordpress' ),
                                        $result[ 0 ]->form_title,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Status', 'bsx-wordpress' ),
                                        $result[ 0 ]->status,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'IP Address', 'bsx-wordpress' ),
                                        $result[ 0 ]->ip_address,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'User Agent', 'bsx-wordpress' ),
                                        $result[ 0 ]->user_agent,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Comment', 'bsx-wordpress' ),
                                        $result[ 0 ]->comment,
                                    );
                                ?>
                            </div>

                        </div>
                        
                        <!-- actions box -->
                        <div class="postbox">

                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e( 'Actions', 'bsx-wordpress' ); ?></h2>
                            </div>

                            <div class="inside">
                                <?php
                                    // create nonces
                                    // $edit_nonce = wp_create_nonce( 'edit' . $functions_file_basename );

                                    printf( '<a class="button button-primary button-large" href="?page=%s&action=%s&id=%s">' . esc_html__( 'Edit' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'edit', absint( $id ) );
                                ?>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        <?php

    } // /show_view_page()


    private function show_edit_page( $id ) {
        
        global $functions_file_basename;
        global $theme_forms_database_handler;

        $fields_prefix = 'field_';

        // check if post data


        // echo '<pre style="width: 100%; overflow: auto;">';
        // print_r( $_POST );
        // echo '</pre>';


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
                // echo '<br>' . $key;
                if ( substr( $key, 0, strlen( $fields_prefix ) ) === $fields_prefix ) {
                    $shorted_key = substr( $key, strlen( $fields_prefix ) );
                    // echo '<br>' . $shorted_key;
                    $fields[ $shorted_key ] = $value;
                }
            }

            // echo '<pre style="width: 100%; overflow: auto;">';
            // print_r( $fields );
            // echo '</pre>';

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


            $index = 0;
            foreach ( $_POST as $key => $value ) {
                if ( in_array( $key, $allowed_keys ) ) {
                    $data[ $key ] = $value;
                    $format[] = $allowed_format[ $index ];
                    $index++;
                }
            }



            // echo '<!-- saving -->';
            // if ( isset( $_POST[ 'comment' ] ) && ! empty( $_POST[ 'comment' ] ) ) {
            if ( ! empty( $data ) && count( $data ) == count( $format ) ) {
                // save 

                // echo '</br>SAVE comment:<br>' . $_POST[ 'comment' ];

                // echo '<pre style="width: 100%; overflow: auto;">';
                // print_r( $data );
                // echo '</pre>';
                // echo '<pre style="width: 100%; overflow: auto;">';
                // print_r( $format );
                // echo '</pre>';

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


        

        // $table_name = $wpdb->prefix . 'bsx_themeforms_entries';
        // // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id", ARRAY_A );
        // $result = $wpdb->get_results( "SELECT * FROM `$table_name` WHERE `id` = $id" );

        $result = $theme_forms_database_handler->get_row( $id );



        ?>
            <h1 class="page-title"><?php echo esc_html__( 'Edit Theme Form Entry', 'bsx-wordpress' ) . ' ' . $id; ?></h1>
            <div class="">
                <?php
                    printf( '<a class="button" href="?page=%s">%s</a>', esc_attr( $_REQUEST[ 'page' ] ), '&larr; ' . esc_html__( 'Entries List', 'bsx-wordpress' ) );
                ?>
            </div>

            <form id="poststuff" class="" method="POST">
                <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce( 'edit' . $functions_file_basename ); ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

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
                                <input id="title" type="text" name="title" size="30" spellcheck="true" autocomplete="off" value="<?php echo $result[ 0 ]->title; ?>">
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header">
                            </div>
                            <div class="inside">

                                <h3 class=""><?php esc_html_e( 'Content', 'bsx-wordpress' ); ?></h3>
                                <p>
                                    <?php
                                        // echo $result[ 0 ]->content;
                                        printf( 
                                            $detail_textarea_template, 
                                            'content',
                                            esc_html__( 'Content', 'bsx-wordpress' ),
                                            esc_textarea( $result[ 0 ]->content ),
                                            12,
                                        );
                                    ?>

                                </p>

                                <hr>

                                <h3 class=""><?php esc_html_e( 'Fields', 'bsx-wordpress' ); ?></h3>
                                <div>
                                    <?php 
                                        $fields = unserialize( $result[ 0 ]->fields );

                                        // TODO: movi into text/config class
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
                                        echo '<body>';
                                        $count = 0;
                                        foreach ( $fields as $key => $value ) {
                                            printf(
                                                '<tr%3$s><td><b><label for="edit-%1$s">%1$s</label></b></td><td><input id="edit-%1$s" name="' . $fields_prefix . '%1$s" value="%2$s" style="width: 100%%;"%4$s></td></tr>',
                                                $key,
                                                $value,
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

                                <?php 
                                    // echo '<pre style="width: 100%; overflow: auto;">';
                                    // print_r( $result );
                                    // echo '</pre>';
                                ?>
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
                                        $result[ 0 ]->id,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Date', 'bsx-wordpress' ),
                                        DateTime::createFromFormat( 'Y-m-d H:i:s', $result[ 0 ]->date )->format( "D, j. F Y H:i:s" ),
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form ID', 'bsx-wordpress' ),
                                        $result[ 0 ]->form_id,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'Form Title', 'bsx-wordpress' ),
                                        $result[ 0 ]->form_title,
                                    );

                                    // prepare options
                                    // TODO: movi into text/config class
                                    $options_values = [
                                        'auto-logged' => 'auto-logged', // esc_html__( 'Auto logged', 'bsx-wordpress' ),
                                        'to-do' => 'to-do', // esc_html__( 'To do', 'bsx-wordpress' ),
                                        'done' => 'done', // esc_html__( 'Done', 'bsx-wordpress' ),
                                    ];
                                    $options = '';
                                    foreach ( $options_values as $key => $value ) {
                                        $options .= sprintf( 
                                            $detail_select_option_template, 
                                            $key,
                                            $value,
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
                                        $result[ 0 ]->ip_address,
                                    );
                                    printf( 
                                        $detail_template, 
                                        esc_html__( 'User Agent', 'bsx-wordpress' ),
                                        $result[ 0 ]->user_agent,
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
                                <h2 class="hndle"><?php esc_html_e( 'Actions', 'bsx-wordpress' ); ?></h2>
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
                                                        $result[ 0 ]->title,
                                                        $result[ 0 ]->email,
                                                        absint( $id ),
                                                    ),
                                                );
                                            ?>
                                        </div>

                                        <div class="clear"></div>
                                    </div>
                                </div>

                                <?php
/*
// create nonces
$edit_nonce = wp_create_nonce( 'edit' . $functions_file_basename );
// $delete_nonce = wp_create_nonce( 'delete' . $functions_file_basename );

$actions = [
'view' => sprintf( '<a href="?page=%s&action=%s&id=%s">' . esc_html__( 'View' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'view', absint( $item[ 'id' ] ) ),
'edit' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Edit' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'edit', absint( $item[ 'id' ] ), $edit_nonce ),
// 'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Delete' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'delete', absint( $item[ 'id' ] ), $delete_nonce ),
];
*/
                                    // create nonces
                                    // $edit_nonce = wp_create_nonce( 'edit' . $functions_file_basename );


                                ?>
                            </div>

                        </div>

                    </div>

                </div>
            </form>
        <?php

    } // show_edit_page()


    private function show_list_page() {

        ?>
            <h1><?php esc_html_e( 'Theme Form Entries', 'bsx-wordpress' ); ?></h1>
        <?php

        // $entries = $wpdb->get_results( "SELECT * FROM $table" );

        // echo '<pre style="width: 100%; overflow: auto;">';
        // print_r( $entries );
        // echo '</pre>';

        // TEST
        // foreach( $entries as &$entry ) {

        //     printf(
        //         '<div><a href="%s">%s</a></div>',
        //         esc_url(
        //             add_query_arg(
        //                 [
        //                     // 'view'     => 'edit',
        //                     'id' => $entry->id,
        //                 ],
        //                 admin_url( 'admin.php?page=theme-form-entries' )
        //             )
        //         ),
        //         esc_html( $entry->form_title . ' [' . $entry->title . '] (id: ' . $entry->id . ')' )
        //     );
        // }



        // list contents in table

        $theme_forms_list_table = new Theme_Forms_List_Table();
        $theme_forms_list_table->prepare_items(); 
        $theme_forms_list_table->display();

    } // /show_list_page()

}
