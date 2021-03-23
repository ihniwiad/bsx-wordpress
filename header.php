<?php 

include 'variables.php';

?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?><?php if ( $isDevMode ) echo ' data-dev="'.$isDevMode.'"' ?> data-id="<?php echo get_the_ID(); ?>">

    <head>
    
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- 
        <?php // <title>BSX WordPress</title> ?>
        -->

        <!-- fonts preload -->
        <?php include $relativeAssetsPath . 'css/fonts-preloads.php'; ?>

        <?php
            // make css & js paths using relative path & version
            $currentCssFilePath = $assetsPath . $cssFileName . '?v=' . $cssVersion;
            //$currentVendorJsFilePath = $assetsPath . $vendorJsFileName . '?v=' . $vendorJsVersion;
            //$currentScriptsJsFilePath = $assetsPath . $scriptsJsFileName . '?v=' . $scriptsJsVersion;
            if ( $isDevMode ) {
                $currentCssFilePath = str_replace ( '.min', '' , $currentCssFilePath );
                //$currentVendorJsFilePath = str_replace ( '.min', '' , $currentVendorJsFilePath );
                //$currentScriptsJsFilePath = str_replace ( '.min', '' , $currentScriptsJsFilePath );
            }
        ?>
        <!-- css preload -->
        <link rel="preload" href="<?php echo $currentCssFilePath ?>" as="style">
        <?php
        /*
        <!-- TEST – js preload -->
        <link rel="preload" href="<?php echo $currentVendorJsFilePath ?>" as="script">
        <link rel="preload" href="<?php echo $currentScriptsJsFilePath ?>" as="script">
        */
        ?>

        <!-- atf style -->
        <style>
            <?php
                if ( $isDevMode ) {
                    $atf_style = file_get_contents( $assetsPath . 'css/atf.css' );
                }
                else {
                    $atf_style = file_get_contents( $assetsPath . 'css/atf.min.css' );
                }
                echo $atf_style;
            ?>
        </style>
        
        <!-- css -->
        <link href="<?php echo $currentCssFilePath ?>" rel="stylesheet">

        <!-- favicons -->
        <link rel="icon" type="image/png" href="<?php echo $assetsPath ?>img/ci/icon/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="<?php echo $assetsPath ?>img/ci/icon/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="<?php echo $assetsPath ?>img/ci/icon/favicon-96x96.png" sizes="96x96">

        <link rel="apple-touch-icon" href="<?php echo $assetsPath ?>img/ci/icon/apple-touch-icon-180x180.png" sizes="180x180">
        <link rel="manifest" href="<?php echo $assetsPath ?>img/ci/icon/site.webmanifest">
        <link rel="shortcut icon" href="<?php echo $assetsPath ?>img/ci/icon/favicon.ico">

        <?php wp_head();?>
        
    </head>
    
    <body>

        <a class="sr-only sr-only-focusable" href="#main"><?php echo __( 'Skip to main content', 'bsx-wordpress' ); ?></a>
    
        <div class="wrapper" id="top">

            <?php include 'template-parts/header/header.php'; ?>