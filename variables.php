<?php 

// TODO: move into functions.php


// paths

$serverName = $_SERVER[ 'SERVER_NAME' ];
$homeUrl = get_bloginfo( 'url' ) . '/';

$rootPath = get_bloginfo( 'template_directory' ).'/';
$resourcesPath = 'resources/';

$relativeAssetsPath = 'assets/';
$assetsPath = $rootPath . $relativeAssetsPath;

// make equal protocol
$rootRelatedAssetsPath = explode( str_replace( 'https://', 'http://', $homeUrl ), str_replace( 'https://', 'http://', $assetsPath ) )[ 1 ];

// get css file version using absolute file path
$cssFileName = 'css/style.min.css';
$cssFilePath = $rootRelatedAssetsPath . $cssFileName;
$cssVersion = file_exists( $cssFilePath ) ? filemtime( $cssFilePath ) : 'null';

// get js file versions
$vendorJsFileName = 'js/vendor.min.js';
$vendorJsFilePath = $rootRelatedAssetsPath . $vendorJsFileName;
$vendorJsVersion = file_exists( $vendorJsFilePath ) ? filemtime( $vendorJsFilePath ) : 'null';

$scriptsJsFileName = 'js/scripts.min.js';
$scriptsJsFilePath = $rootRelatedAssetsPath . $scriptsJsFileName;
$scriptsJsVersion = file_exists( $scriptsJsFilePath ) ? filemtime( $scriptsJsFilePath ) : 'null';


// include classes
// if ( class_exists( 'BsxPhotoswipe001' ) ) {
// 	$BsxPhotoswipe = new BsxPhotoswipe001;
// }


// dev mode
$isDevMode = false;
if ( isset( $_GET[ 'dev' ] ) && $_GET[ 'dev' ] == '1' ) {
	$isDevMode = true;
}

