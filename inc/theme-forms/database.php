<?php


add_action( 'after_switch_theme', 'bsx_theme_forms_create_table' );

function bsx_theme_forms_create_table() {

    // create db table

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // see data types: https://www.w3schools.com/sql/sql_datatypes.asp

    $table_name = $wpdb->prefix . 'bsx_themeforms_entries';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) AUTO_INCREMENT primary key NOT NULL,
        date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        data_gmt DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        date_modified DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
        form_id MEDIUMINT(9) NOT NULL,
        form_title TEXT NOT NULL,
        title TEXT NOT NULL,
        content LONGTEXT NOT NULL,
        email VARCHAR(100) NOT NULL,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(64) NOT NULL,
        status VARCHAR(30) NOT NULL,
        fields LONGTEXT NOT NULL,
        comment TEXT NOT NULL,
        ip_address VARCHAR(128) NOT NULL,
        user_agent VARCHAR(256) NOT NULL,
        history LONGTEXT NOT NULL
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );


    // $table_name = $wpdb->prefix . 'test';
    // $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    //     id mediumint(9) NOT NULL AUTO_INCREMENT,
    //     time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    //     count smallint(5) NOT NULL,
    //     text text NOT NULL,
    //     UNIQUE KEY id (id)
    // ) $charset_collate;";

    // dbDelta( $sql );


	// add column if not exists
	// $row = $wpdb->get_row( "SELECT * FROM $table_name" );
	// if ( ! isset( $row->email ) ){
	// 	$wpdb->query( "ALTER TABLE $table_name ADD email VARCHAR(100) NOT NULL" );
	// }

}



