<?php


class Theme_Forms_Database_Handler {

	var $table_name = 'bsx_themeforms_entries';

	public function create_table() {

		add_action( 'after_switch_theme', 'bsx_theme_forms_create_table' );

		function bsx_theme_forms_create_table() {

		    // create db table if not already existing

		    global $wpdb;
		    $charset_collate = $wpdb->get_charset_collate();

		    // see data types: https://www.w3schools.com/sql/sql_datatypes.asp

		    $table_name = $wpdb->prefix . 'bsx_themeforms_entries';
		    $sql = "CREATE TABLE $table_name (
		        id BIGINT(20) AUTO_INCREMENT primary key NOT NULL,
		        date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		        data_gmt DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		        date_modified DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		        form_id MEDIUMINT(9) NOT NULL,
		        form_title TEXT NOT NULL,
		        title TEXT NOT NULL,
		        content LONGTEXT NOT NULL,
		        status VARCHAR(30) NOT NULL,
		        comment TEXT NOT NULL,
		        ip_address VARCHAR(128) NOT NULL,
		        user_agent VARCHAR(256) NOT NULL,
		        history LONGTEXT NOT NULL,
		        fields LONGTEXT NOT NULL,
		        f_name VARCHAR(100) NOT NULL,
		        f_first_name VARCHAR(64) NOT NULL,
		        f_last_name VARCHAR(64) NOT NULL,
		        f_email VARCHAR(100) NOT NULL,
		        f_phone VARCHAR(64) NOT NULL,
		        f_company VARCHAR(100) NOT NULL,
		        f_subject VARCHAR(256) NOT NULL
		    ) $charset_collate;";

		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		    maybe_create_table( $table_name, $sql );


		    // $table_name = $wpdb->prefix . 'test';
		    // $sql = "CREATE TABLE $table_name (
		    //     id mediumint(9) AUTO_INCREMENT primary key NOT NULL
		    //     time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		    //     count smallint(5) NOT NULL,
		    //     text text NOT NULL,
		    //     UNIQUE KEY id (id)
		    // ) $charset_collate;";

		    // maybe_create_table( $table_name, $sql );


			// // add column if not exists
			// $row = $wpdb->get_row( "SELECT * FROM $table_name" );
			// if ( ! isset( $row->first_name ) ){
			// 	$wpdb->query( "ALTER TABLE $table_name ADD first_name VARCHAR(64) NOT NULL" );
			// }
			// if ( ! isset( $row->last_name ) ){
			// 	$wpdb->query( "ALTER TABLE $table_name ADD last_name VARCHAR(64) NOT NULL" );
			// }
			// if ( ! isset( $row->company ) ){
			// 	$wpdb->query( "ALTER TABLE $table_name ADD company VARCHAR(100) NOT NULL" );
			// }
			// if ( ! isset( $row->subject ) ){
			// 	$wpdb->query( "ALTER TABLE $table_name ADD subject VARCHAR(256) NOT NULL" );
			// }

		}

	} // function create_table()


	public function get_row( $row_id ) {
        global $wpdb;

        $table = $wpdb->prefix . $this->table_name;

        return $wpdb->get_results( "SELECT * FROM `$table` WHERE `id` = $row_id" );
	} // function get_row()


	public function create_row( $data, $format ) {
        global $wpdb;

        $table = $wpdb->prefix . $this->table_name;

        $wpdb->insert( $table, $data, $format );
        return $wpdb->insert_id;

	} // function create_row()


	public function update_row( $row_id, $data, $format ) {
        global $wpdb;

        $table = $wpdb->prefix . $this->table_name;
        $where = array( 
            'id' => $row_id,
        );
        $where_format = array(
            '%d',
        );
        $updated = $wpdb->update( $table, $data, $where, $format, $where_format );

        return $updated;

	} // function update_row()


	public function delete_row( $row_id ) {
        global $wpdb;

        $table = $wpdb->prefix . $this->table_name;
        $where = array( 
            'id' => $row_id,
        );
        $where_format = array(
            '%d',
        );

        return $wpdb->delete( $table, $where, $where_format );
	} // function delete_row()

}





