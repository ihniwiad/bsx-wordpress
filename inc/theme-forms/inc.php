<?php

require_once( __DIR__ . '/functions.php' );
require_once( __DIR__ . '/database.php' );
$theme_forms_database_handler = new Theme_Forms_Database_Handler;
$theme_forms_database_handler->create_table();
require_once( __DIR__ . '/class-theme-forms-list-table.php' );
require_once( __DIR__ . '/custom-post-type.php' );
require_once( __DIR__ . '/meta-box.php' );
require_once( __DIR__ . '/admin-menu.php' );
require_once( __DIR__ . '/admin-pages.php' );
require_once( __DIR__ . '/rest-route.php' );
require_once( __DIR__ . '/form-template.php' );
require_once( __DIR__ . '/shortcode.php' );