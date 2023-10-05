<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class Theme_Forms_List_Table extends WP_List_Table {

    // class constructor
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Theme Form Enty', 'bsx-wordpress' ), //singular name of the listed records
            'plural' => __( 'Theme Form Enties', 'bsx-wordpress' ), //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ] );

    }

    function get_data() {

        // get order params from url
        $orderby = isset( $_GET[ 'orderby' ] ) ? $_GET[ 'orderby' ] : 'date';
        $order = isset( $_GET[ 'order' ] ) ? strtoupper( $_GET[ 'order' ] ) : 'DESC';

        global $wpdb;
        $table_name = $wpdb->prefix . "bsx_themeforms_entries";
        // $result = $wpdb->get_results( "SELECT * FROM `$table_name` ORDER BY `$orderby` $order", ARRAY_A );
        $result = $wpdb->get_results( "SELECT `id`, `date`, `title`, `form_title`, `status`, `content` FROM `$table_name` ORDER BY `$orderby` $order", ARRAY_A );

        return $result;
    }

    function get_columns() {
        $columns = array(
            'date' => esc_html__( 'Date', 'bsx-wordpress' ),
            'title' => esc_html__( 'Title', 'bsx-wordpress' ),
            'form_title' => esc_html__( 'Form Title', 'bsx-wordpress' ),
            'status' => esc_html__( 'Status', 'bsx-wordpress' ),
            'content' => esc_html__( 'Content', 'bsx-wordpress' )
        );
        return $columns;
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns(); // array();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        // $this->items = $this->example_data;
        $this->items = $this->get_data();
    }

    function column_default( $item, $column_name ) {
        switch ( $column_name ) { 
            case 'date':
            case 'title':
            case 'form_title':
            case 'status':
            case 'content':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; // show for debugging purposes
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'date'  => array( 'date', false ),
            'form_title' => array( 'form_title', false ),
            'status'   => array( 'status' ,false )
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
        // $delete_nonce = wp_create_nonce( 'delete' . $functions_file_basename );

        $actions = [
            'view' => sprintf( '<a href="?page=%s&action=%s&id=%s">' . esc_html__( 'View' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'view', absint( $item[ 'id' ] ) ),
            'edit' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Edit' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'edit', absint( $item[ 'id' ] ), $edit_nonce ),
            // 'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">' . esc_html__( 'Delete' ) . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'delete', absint( $item[ 'id' ] ), $delete_nonce ),
        ];

        return sprintf( '%1$s %2$s', sprintf( '<a href="?page=%s&action=%s&id=%s">' . $item[ 'title' ] . '</a>', esc_attr( $_REQUEST[ 'page' ] ), 'view', absint( $item[ 'id' ] ) ), $this->row_actions( $actions ) );
    }

}