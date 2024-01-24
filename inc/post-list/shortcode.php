<?php

// example: [post-list ids="4560, 4645, 4599" heading="My Example Headline" heading-level="3"]
// default heading level: 2

class PostListShortcode {

    public function init(){
        $this->registerShortcode();
    }

    private function registerShortcode() {
        add_shortcode( 'post-list', function( $atts ) {

            $data = shortcode_atts( array(
                'ids' => '',
                'heading' => '',
                'heading-level' => '',
            ), $atts );

            if ( empty( $data[ "ids" ] ) ) {
                return "";
            }
            // get ids as array
            $ids = array();
            foreach ( explode( ",", str_replace( " ", "", $data[ "ids" ] ) ) as $id ) {
                if ( intval( $id ) > 0 ) {
                    $ids[] = intval( $id );
                }
            }
            // no ids found
            if ( empty( $ids ) ){
                return "";
            }
            // $ids = array( 4560, 4645, 4599 );

            return $this->getPostsHTML( $ids, $data[ "heading" ], $data[ "heading-level" ] );
        } );
    }

    private function getPostsHTML( $ids, $heading, $heading_level ){

        // $args = array( 
        //     'post_type' => 'post',
        //     'post__in' => $ids,
        //     // 'order' => 'ASC',
        //     'posts_per_page' => -1,
        // );
        // $custom_query = new WP_Query( $args );
        ob_start();
        get_template_part( "template-parts/post-list/post-list", "", array( "ids" => $ids, "heading" => $heading, "heading_level" => $heading_level ) );
        $data = ob_get_clean();
        return $data;
    }

}
