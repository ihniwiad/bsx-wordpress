<?php

/*
 * Breadcrumb
 */

/*
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Library</a></li>
    <li class="breadcrumb-item active" aria-current="page">Data</li>
  </ol>
</nav>
*/
class Bsx_Breadcrumb {

    public function init() {
        $this->registerShortcode();
    }

    private function registerShortcode() {
        add_shortcode( 'breadcrumb', function( $atts ) {

            $data = shortcode_atts( array(
                'type' => '', // box (light full width box)
            ), $atts );

            return $this->makeHtml( $data );
        } );
    }

    static private function start_list() {
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';
        return $html;
    }

    static private function end_list() {
        $html = '</ol>';
        $html .= '</nav>';
        return $html;
    }

    private function add_item( $title, $url = '', $data_id = '' ) {
        $html = '';
        if ( ! empty( $url ) ) {
            $html .= sprintf( 
                '<li class="breadcrumb-item"%s><a href="%s">%s</a></li>',
                ( ! empty( $data_id ) ? ' data-id="' . $data_id . '"' : '' ),
                $url,
                $title,
            );
        }
        else {
            // is current item
            $html .= sprintf( 
                '<li class="breadcrumb-item active" aria-current="page">%s</li>',
                $title,
            );
        }
        return $html;
    }

    public function makeHtml() {

        if ( is_front_page() ) {
            return;
        }

        global $post;
        $post_type = get_post_type();
        $custom_taxonomy  = ''; // if custom taxonomy place here

        $home_page_id = get_option( 'page_on_front' );
        $home_page_title = get_the_title( $home_page_id );

        $current_path = $_SERVER[ 'REQUEST_URI' ]; // path after domain

        $html = '';

        // start list
        $html .= $this::start_list();

        // add home item
        $html .= $this->add_item( $home_page_title, get_home_url() );

        // add other items
        if ( is_single() ) {
            // echo ' (SINGLE) ';

            // If post type is not post
            if ( $post_type != 'post' ) {
                $post_type_object   = get_post_type_object( $post_type );
                // $post_type_url     = get_post_type_archive_link( $post_type );
                // fix since above is empty
                $post_type_url = get_home_url() . '/' . $post_type_object->rewrite[ 'slug' ] . '/';

                $html .= $this->add_item( $post_type_object->labels->name, $post_type_url );
            }

            // Get categories
            $category = get_the_category( $post->ID );

            // If category not empty
            if ( ! empty( $category ) ) {
                // echo ' (CAT NOT EMPTY) ';

                $home_page_url = get_the_permalink( $home_page_id );
                $posts_page_id = get_option( 'page_for_posts' );
                $posts_page_url = get_the_permalink( $posts_page_id );

                // Arrange category parent to child
                $category_values = array_values( $category );
                $get_last_category = end( $category_values );
                // $get_last_category = $category[count($category) - 1];
                $get_parent_category = rtrim( get_category_parents( $get_last_category->term_id, true, ',' ), ',' );
                $cat_parent = explode( ',', $get_parent_category );

                // store prarent categories
                $store_parent_cats = '';

                $loop_count = 0;

                foreach ( $cat_parent as $p ) {

                    // extract url and title from $p

                    $has_matches = preg_match( '/href=["\']?([^"\'>]+)["\']?/', $p, $match );
                    $info = parse_url( $match[ 1 ] ); // returning array of scheme, host  & path
                    $url = $info[ "scheme" ] . "://" . $info[ "host" ] . $info[ "path" ];

                    $expl = explode( '</', $p );
                    $expl = explode( '>', $expl[ 0 ] );
                    $title = isset( $expl[ 1 ] ) ? $expl[ 1 ] : '';

                    // check if url is duplicate of blog page url when using %category% for blog permalink
                    // MY_WEBPAGE_HOME_URL/category/MY_BLOG_HOME_FOLDER/
                    $category_str = '/category/';
                    if ( strpos( $url, $category_str ) !== false ) {
                        $url_split = explode( $category_str, $url );
                        if ( $posts_page_url === $home_page_url . $url_split[ 1 ] ) {
                            // change url to blog home url (having equal contents but shorter url)
                            $url = $posts_page_url;
                        }
                    }

                    $store_parent_cats .=  $this->add_item( $title, $url );

                    $loop_count++;
                }

            }

            // ifcustom post type within custom taxonomy
            $taxonomy_exists = taxonomy_exists( $custom_taxonomy );

            if ( empty( $get_last_category ) && ! empty( $custom_taxonomy ) && $taxonomy_exists ) {
                // echo ' (CUST TAX) ';

                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id = $taxonomy_terms[ 0 ]->term_id;
                $cat_link = get_term_link($taxonomy_terms[ 0 ]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[ 0 ]->name;

            }

            // Check if the post is in a category
            if ( ! empty( $get_last_category ) ) {
                // echo ' (GET LAST CAT) ';

                $html .= $store_parent_cats;
                $html .= $this->add_item( get_the_title() );
            } 
            elseif( ! empty( $cat_id ) ) {
                // echo ' (CAT ID) ';

                $html .= $this->add_item( $cat_name, $cat_link );
                $html .= $this->add_item( get_the_title() );
            }
            else {
                // echo ' (ELSE CAT ID) ';

                $html .= $this->add_item( get_the_title() );
            }

        } 
        elseif ( is_archive() ) {
            // echo ' (ARCH) ';

            if ( is_tax() ) {
                // if post type is not post
                if ( $post_type != 'post' ) {
                    $post_type_object = get_post_type_object( $post_type );
                    $post_type_link = get_post_type_archive_link( $post_type );

                    if ( isset( $post_type_object->labels ) && isset( $post_type_object->labels->name ) ) {
                        $html .= $this->add_item( $post_type_object->labels->name, $post_type_link, 'archive-tax' );
                    }
                    else {
                        $html .= '<!-- archive object empty -->';
                    }

                }
                $custom_tax_name = get_queried_object()->name;
                $html .= $this->add_item( $custom_tax_name );
            } 
            elseif ( is_category() ) {
                // echo ' (CAT) ';

                $parent = get_queried_object()->category_parent;

                if ( $parent !== 0 ) {
                    $parent_category = get_category( $parent );
                    $category_link   = get_category_link( $parent );
                    $html .= $this->add_item( $parent_category->name, esc_url( $category_link ) );
                }
                $html .= $this->add_item( single_cat_title( '', false ) );
            } 
            elseif ( is_tag() ) {
                // echo ' (TAG) ';

                // get tag information
                $term_id = get_query_var('tag_id');
                $taxonomy = 'post_tag';
                $args = 'include=' . $term_id;
                $terms = get_terms( $taxonomy, $args );
                $get_term_name = $terms[ 0 ]->name;

                $html .= $this->add_item( $get_term_name );
            } 
            elseif ( is_day() ) {
                // echo ' (DAY) ';

                // year
                $html .= $this->add_item( get_the_time('Y'), get_year_link( get_the_time('Y') ) );
                // month
                $html .= $this->add_item( get_the_time('M'), get_month_link( get_the_time('Y') ) );
                // day
                $html .= $this->add_item( get_the_time('jS') .' '. get_the_time('M') );
            } 
            elseif ( is_month() ) {
                // echo ' (MONTH) ';

                // year
                $html .= $this->add_item( get_the_time('Y'), get_year_link( get_the_time('Y') ) );

                // month
                $html .= $this->add_item( get_the_time('M') );
            } 
            elseif ( is_year() ) {
                // echo ' (YEAR) ';

                // year
                $html .= $this->add_item( get_the_time('Y') );
            } 
            elseif ( is_author() ) {
                // echo ' (AUTHOR) ';

                // auhor

                // get author information
                global $author;
                $userdata = get_userdata( $author );

                $html .= $this->add_item( $userdata->display_name );
            } 
            else {
                $html .= $this->add_item( post_type_archive_title() );
            }

        } 
        elseif ( is_page() ) {
            // echo ' (PAGE) ';

            // parents
            if ( $post->post_parent ) {

                // ff child page, get parents
                $anc = get_post_ancestors( $post->ID );

                // get parents in right order
                $anc = array_reverse( $anc );

                // parents loop
                foreach ( $anc as $ancestor ) {
                    $html .= $this->add_item( get_the_title( $ancestor ), get_permalink( $ancestor ) );
                }

            }

            // current page
            $html .= $this->add_item( get_the_title() );
        } 
        elseif ( is_search() ) {
            // echo ' (SEARCH) ';

            // $html .= $this->add_item( __( 'Search', 'bsx-wordpress' ) . ': ' . get_search_query() );
            // do not show query string since already shown below
            $html .= $this->add_item( __( 'Search', 'bsx-wordpress' ) );
        } 
        elseif ( is_404() ) {
            // echo ' (404) ';

            $html .= $this->add_item( __( 'Error 404', 'bsx-wordpress' ) );
        }
        else if ( get_post_type( $post ) === 'post' ) {
            // echo ' (IS POST PAGE) ';

            $posts_page_id = get_option( 'page_for_posts' );
            $html .= $this->add_item( get_the_title( $posts_page_id ) );
        }
        else {
            // echo ' (ELSE – NOTHING) ';
        }

        // end list
        $html .= $this::end_list();

        return $html;
    }

    public function print() {

        $html = $this->makeHtml();

        echo $html;
    }
}
// /class Bsx_Breadcrumb


