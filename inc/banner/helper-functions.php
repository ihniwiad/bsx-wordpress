<?php

class BSXWP_Banner_Helper_Fn {


	/**
	 *	Applys banner HTML (Custom Post Type 'Banner')
	 *
	 *	@param 	string $banner_type 	Banner type (Meta value of Custom Post Type 'Banner')
	 *
	 *	@return string 					HTML
	 */
	
	public static function getBannerHtml( $banner_type ) {
		if ( ! $banner_type ) {
			return;
		}

		$html = '<!-- apply banner "' . $banner_type . '" -->';

        // query banner
        $args = array(
            'post_type' => 'banner-cpt',
            'meta_key' => 'banner',
            'meta_value' => BSXWP_Helper_Fn::metaArrayQueryVal( [ 'key' => 'banner_type', 'val' => $banner_type ] ),
            // 'meta_value' => serialize( 'banner_type' ) . serialize( 'blog' ), // creates serialized key value pair, e.g. `s:3:"foo";s:3:"bar";` to match in serialized array, e.g. `a:1:{s:3:"foo";s:3:"bar";}`
            'meta_compare' => 'LIKE', // contains (no need to be equal)
            'posts_per_page' => -1,
            'order' => 'DESC',
        );
        $custom_query = new WP_Query( $args );

        while ( $custom_query->have_posts() ) : 
            $custom_query->the_post();

            // remove Gutenberg block comments
            $post_content = preg_replace( '/<!--(.|s)*?-->/', '', get_the_content() );
            $html .= $post_content;
        endwhile;

        wp_reset_postdata();

		return $html;
	}

    public static function sanitizeUrl( $incomplete_url, $type = 'http' ) {
        $fixed_url = $incomplete_url; // fallback
        if ( 
            $type === 'http' 
            && substr( $incomplete_url, 0, 7 ) != 'http://' 
            && substr( $incomplete_url, 0, 8 ) != 'https://'
        ) {
            $fixed_url = 'http://' . $incomplete_url;
        }
        return $fixed_url;
    }

}