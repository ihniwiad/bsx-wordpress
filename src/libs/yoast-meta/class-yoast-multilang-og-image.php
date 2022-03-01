<?php

/**
 * Yoast SEO fallback og image (for all languages)
 */

use Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter;

if ( class_exists( Image_Presenter::class ) && ! class_exists( 'Bsx_Image_Presenter' ) ) {

    class Bsx_Image_Presenter extends Image_Presenter {

        /**
         * Returns the image for a post.
         *
         * @return string The image tag.
         */
        public function present() {
            $images = $this->get();
            $found_invalid_url_image_in_images = false;

            $return = '';
            $return .= \PHP_EOL . "\t" . '<!-- modified Yoast SEO og image by Bsx_Image_Presenter -->';
            
            foreach ( $images as $image_index => $image_meta ) {
                $image_url = $image_meta['url'];

                // $return .= '<meta property="og:image" content="' . \esc_url( $image_url ) . '" />';

                // do not return empty meta tag as Yoast originally does if no image available
                if ( ! empty( esc_url( $image_url ) ) ) {

                    $return .= '<meta property="og:image" content="' . \esc_url( $image_url ) . '" />';

                    foreach ( static::$image_tags as $key => $value ) {
                        if ( empty( $image_meta[ $key ] ) ) {
                            continue;
                        }

                        $return .= \PHP_EOL . "\t" . '<meta property="og:image:' . \esc_attr( $key ) . '" content="' . $image_meta[ $key ] . '" />';
                    }
                }
                else {
                    // remember invalid (empty) image
                    $found_invalid_url_image_in_images = true;
                }
            }

            // show fallback image
            if ( empty( $images ) || $found_invalid_url_image_in_images ) {
                // return fallback og image from theme folder (all languages)

                global $homeUrl;
                global $rootRelatedAssetsPath;

                // TODO: check fpr .jpg, .jpeg, .png
                $og_image_path = $rootRelatedAssetsPath . 'img/ci/og/og-image.jpg';

                if ( file_exists( $og_image_path ) ) {

                    $return .= \PHP_EOL . "\t" . '<meta property="og:image" content="' . \esc_url( $homeUrl . $og_image_path ) . '" />';

                    $img_size = getimagesize( $og_image_path );

                    $image_meta = [
                        'width' => $img_size[ 0 ],
                        'height' => $img_size[ 1 ],
                        'type' => $img_size[ 'mime' ],
                    ];

                    foreach ( $image_meta as $key => $value ) {
                        $return .= \PHP_EOL . "\t" . '<meta property="og:image:' . \esc_attr( $key ) . '" content="' . $image_meta[ $key ] . '" />';
                    }
                }
                else {
                    $return .= \PHP_EOL . "\t" . '<!-- no og image found at ' . $og_image_path . ' -->';
                }
            }

            return $return;
        }
    }

}

// check which plugins available
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {

    function add_custom_og_image( $presenters ) {
        $_presenters = array();

        foreach ( $presenters as $presenter ) {
            if ( $presenter instanceof Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter ) {
                // replace Image_Presenter by custom Bsx_Image_Presenter
                $_presenters[] = new Bsx_Image_Presenter();
            }
            else {
                // keep others
                $_presenters[] = $presenter;
            }
        }
        return $_presenters;
    }

    add_filter( 'wpseo_frontend_presenters', 'add_custom_og_image' );
}