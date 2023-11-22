<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class Theme_Forms_List_Table extends WP_List_Table {

    // class constructor
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Theme Form Entry', 'bsx-wordpress' ), // singular name of the listed records
            'plural' => __( 'Theme Form Entries', 'bsx-wordpress' ), // plural name of the listed records
            'ajax' => false // should this table support ajax?
        ] );

        add_action( 'admin_head', array( &$this, 'add_style' ) );  
    }


    function add_style() {
        global $theme_forms_menu_slug;

        $page = ( isset( $_GET[ 'page' ] ) ) ? esc_attr( $_GET[ 'page' ] ) : false;
        if ( $theme_forms_menu_slug != $page ) {
            return;
        }
        echo '<style type="text/css">';
        echo '.wp-list-table .column-id { width: 5.5em; }';
        // echo '.wp-list-table .column-status { width: 8%; }';
        // echo '.wp-list-table .column-name, .wp-list-table .column-title { width: 15%; }';
        echo '</style>';
    }


    function get_data( $per_page = 5, $page_number = 1 ) {

        // get order params from url
        $orderby = isset( $_GET[ 'orderby' ] ) ? $_GET[ 'orderby' ] : 'date';
        $order = isset( $_GET[ 'order' ] ) ? strtoupper( $_GET[ 'order' ] ) : 'DESC';

        global $wpdb;
        $table_name = $wpdb->prefix . "bsx_themeforms_entries";
        $offset = ( $page_number - 1 ) * $per_page;
        // $result = $wpdb->get_results( "SELECT `id`, `date`, `title`, `email`, `name`, `form_title`, `status`, `content` FROM `$table_name` ORDER BY `$orderby` $order LIMIT $per_page OFFSET $offset", ARRAY_A );
        $result = $wpdb->get_results( "SELECT * FROM `$table_name` ORDER BY `$orderby` $order LIMIT $per_page OFFSET $offset", ARRAY_A );

        return $result;
    }


    function get_columns() {
        // some field names cannot be hidden in list table (see `$special` in `class-wp-screen.php`):
        // '_title', 'cb', 'comment', 'media', 'name', 'title', 'username', 'blogname'
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => esc_html__( 'ID', 'bsx-wordpress' ),
            'date' => esc_html__( 'Date', 'bsx-wordpress' ),
            'title' => esc_html__( 'Title', 'bsx-wordpress' ),
            'f_email' => esc_html__( 'Email', 'bsx-wordpress' ),
            'f_name' => esc_html__( 'Name', 'bsx-wordpress' ),
            'f_first_name' => esc_html__( 'First Name', 'bsx-wordpress' ),
            'f_last_name' => esc_html__( 'Last Name', 'bsx-wordpress' ),
            'form_title' => esc_html__( 'Form Title', 'bsx-wordpress' ),
            'f_phone' => esc_html__( 'Phone', 'bsx-wordpress' ),
            'f_company' => esc_html__( 'Company', 'bsx-wordpress' ),
            'f_subject' => esc_html__( 'Subject', 'bsx-wordpress' ),
            'status' => esc_html__( 'Status', 'bsx-wordpress' ),
            'content' => esc_html__( 'Content', 'bsx-wordpress' )
        );
        return $columns;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        // $columns = $this->get_columns();
        // $hidden = array();
        // $sortable = $this->get_sortable_columns(); // array();
        // $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->_column_headers = $this->get_column_info();

        // Process bulk action (before getting data)
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page( 'theme_forms_entries_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, // calculate the total number of items
            'per_page' => $per_page // determine how many items to show on a page
        ] );

        // $this->items = self::get_customers( $per_page, $current_page );
        $this->items = $this->get_data( $per_page, $current_page );
    }

    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
            case 'date':
            case 'title':
            case 'f_email':
            case 'f_name':
            case 'f_first_name':
            case 'f_last_name':
            case 'form_title':
            case 'f_phone':
            case 'f_company':
            case 'f_subject':
            case 'status':
            case 'content':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; // show for debugging purposes
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id'  => array( 'id', false ),
            'date'  => array( 'date', false ),
            'title'  => array( 'title', false ),
            'f_email'  => array( 'f_email', false ),
            'f_name'  => array( 'f_name', false ),
            'f_first_name'  => array( 'f_first_name', false ),
            'f_last_name'  => array( 'f_last_name', false ),
            'form_title' => array( 'form_title', false ),
            'f_phone'  => array( 'f_phone', false ),
            'f_company'  => array( 'f_company', false ),
            'status'   => array( 'status', false )
        );
        return $sortable_columns;
    }

    // column actions syntax: column_{key_name}
    function column_title( $item ) {

        global $functions_file_basename;

        // $actions = array(
        //     'view' => sprintf( esc_html__( 'View' ), $_REQUEST[ 'page' ], 'view', $item[ 'id' ] ),
        //     'edit' => sprintf( esc_html__( 'Edit' ), $_REQUEST[ 'page' ], 'edit', $item[ 'id' ] ),
        //     'delete' => sprintf( esc_html__( 'Delete' ), $_REQUEST[ 'page' ], 'delete', $item[ 'id' ] ),
        // );

        // create nonces
        $edit_nonce = wp_create_nonce( 'edit' . $functions_file_basename );
        $delete_nonce = wp_create_nonce( 'delete' . $item[ 'id' ] . $functions_file_basename );

        $actions = [
            'view' => sprintf( '<a href="admin.php?page=%s&action=%s&id=%s">' . esc_html__( 'View' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'view', absint( $item[ 'id' ] ) ),
            'edit' => sprintf( '<a href="admin.php?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Edit' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'edit', absint( $item[ 'id' ] ), $edit_nonce ),
            'delete' => sprintf( 
                '<a href="admin.php?page=%1$s&action=%2$s&id=%3$s&_wpnonce=%4$s" onclick="return confirm( \'%6$s\' );">%5$s</a>', 
                esc_attr( $_REQUEST[ 'page' ] ), 
                'delete', 
                absint( $item[ 'id' ] ), 
                $delete_nonce,
                esc_html__( 'Delete' ),
                sprintf(
                    /* translators: %1$s: The title of the entry. %1$s: The email address. %3$d: The id of the entry. */
                    esc_attr__( 'Really delete ”%1$s“ from %2$s (id: %3$d)?', 'bsx-wordpress' ),
                    $item[ 'title' ],
                    $item[ 'f_email' ],
                    absint( $item[ 'id' ] ),
                ),
            ),
        ];

        return sprintf( '%1$s %2$s', sprintf( '<a href="admin.php?page=%s&action=%s&id=%s">' . $item[ 'title' ] . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'view', absint( $item[ 'id' ] ) ), $this->row_actions( $actions ) );
    }

    function column_date( $item ) {
        return DateTime::createFromFormat( 'Y-m-d H:i:s', $item[ 'date' ] )->format( "D, j. F Y H:i:s" );
    }

    // checkbox column for bulk actions
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', 
            $item[ 'id' ],
        );
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __( 'Delete' )
        ];
        return $actions;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $table_name = $wpdb->prefix . "bsx_themeforms_entries";

        return $wpdb->get_var( "SELECT COUNT(*) FROM `$table_name`" );
    }






    // bulk actions


    public function process_bulk_action() {

        global $theme_forms_database_handler;

        // echo '<br>TEST_NONCE: ' . wp_create_nonce( 'bulk-' . $this->_args[ 'plural' ] );

        // echo '<pre style="width: 100%; overflow: auto;">';
        // print_r( $this );
        // echo '</pre>';

        // check post data if delete bulk action has been triggered
        if ( 
            ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'bulk-delete' )
            || ( isset( $_POST[ 'action2' ] ) && $_POST[ 'action2' ] == 'bulk-delete' )
        ) {
            // echo '<br>TEST_VERIFY_NONCE: ' . $_POST[ '_wpnonce' ];

            if ( isset( $_POST[ '_wpnonce' ] ) && wp_verify_nonce( $_POST[ '_wpnonce' ], 'bulk-' . $this->_args[ 'plural' ] ) ) {

                // nonce verified
                $deleted = false;
                $delete_ids = esc_sql( $_POST[ 'bulk-delete' ] );
                foreach ( $delete_ids as $id ) {
                    // delete item
                    $deleted = $theme_forms_database_handler->delete_row( $id );


                }

                if ( false === $deleted ) {
                    // error
                    printf(
                        '<div class="notice notice-error">
                            <p>%1$s</p>
                        </div>',
                        esc_html__( 'Error while trying to process bulk action. Your data has not been deleted.', 'bsx-wordpress' ),
                    );
                }
                else {
                    // successfully deleted
                    printf(
                        '<div class="notice notice-success">
                            <p>%1$s</p>
                        </div>',
                        esc_html__( 'One ore multiple entries were successfully deleted by bulk action.', 'bsx-wordpress' ),
                    );
                }
            }
            else {
                // nonce failed
                // error
                printf(
                    '<div class="notice notice-error">
                        <p>%1$s</p>
                    </div>',
                    esc_html__( 'Error while trying to process bulk action. Your data has not been deleted.', 'bsx-wordpress' ),
                );
            }
        }
        else {
            // do nothing
        }
    }


}