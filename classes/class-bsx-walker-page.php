<?php
/**
 * Custom page walker for this theme.
 *
 * @package WordPress
 * @subpackage bsx wordpress example
 * @since bsx wordpress example 1.0
 */

if ( ! class_exists( 'Bsx_Walker_Page' ) ) {
	/**
	 * CUSTOM PAGE WALKER
	 * A custom walker for pages, based on TwentyTwenty theme.
	 */
	class Bsx_Walker_Page extends Walker_Page {

		/**
		 * Outputs the beginning of the current element in the tree.
		 *
		 * @see Walker::start_el()
		 * @since 2.1.0
		 *
		 * @param string  $output       Used to append additional content. Passed by reference.
		 * @param WP_Post $page         Page data object.
		 * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
		 * @param array   $args         Optional. Array of arguments. Default empty array.
		 * @param int     $current_page Optional. Page ID. Default 0.
		 */
		public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {

			$createClickableParentLinkChild = true;

			// get nav config from meta box
			$meta_hidden_in_main_nav = 0;
			$meta_nav_type = 0;

			$meta = get_post_meta( $page->ID, 'nav_fields', true );

			if ( isset( $meta ) ) {
				if ( isset( $meta[ 'hidden_in_main_nav' ] ) ) {
					// echo( '<script>console.log( \'$meta[ "hidden_in_main_nav" ] (' . $page->ID . '): ' . $meta[ 'hidden_in_main_nav' ] . '\' );</script>' );
					$meta_hidden_in_main_nav = $meta[ 'hidden_in_main_nav' ];
				}

				if ( isset( $meta[ 'nav_type' ] ) ) {
					// $nav_type_custom_config = $meta[ 'nav_type' ];
					// echo( '<script>console.log( \'$meta[ "nav_type" ] (' . $page->ID . '): ' . $meta[ 'nav_type' ] . '\' );</script>' );
					$meta_nav_type = $meta[ 'nav_type' ];
				}
				
			}

			if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
				$t = "\t";
				$n = "\n";
			} else {
				$t = '';
				$n = '';
			}
			if ( $depth ) {
				$indent = str_repeat( $t, $depth );
			} else {
				$indent = '';
			}

			$css_class = array();

			// check if page has custom meta `nav_type` to configure dropdown type, if value `'1'` add .bsx-appnav-bigmenu-dropdown to <li>

			// make bigmenu if config set
			if ( $meta_nav_type != 0 ) {
				if ( $meta_nav_type && intval( $meta_nav_type ) > 0 ) {
					$css_class[] = 'bsx-appnav-bigmenu-dropdown';
					if ( intval( $meta_nav_type ) > 1 ) {
						$css_class[] = 'columns-' . $meta_nav_type;
					}
					else {
						// default 3 columns
						$css_class[] = 'columns-3';
					}
				}
			}

			$css_class[] = 'page-' . $page->ID;

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				//$css_class[] = 'page_item_has_children';
			}

			// prepare check for is homepage or is custom blog parent
			$pageHref = esc_url( get_permalink( $page->ID ) );
			$homeHref = get_bloginfo( 'url' );

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
					//$css_class[] = 'current_page_ancestor';
					// add css class `active` to current ancestor
					$css_class[] = 'active';
				}
				if ( $page->ID === $current_page ) {
					//$css_class[] = 'current_page_item';
					// add css class `active` to current
					$css_class[] = 'active';
				} elseif ( $_current_page && $page->ID === $_current_page->post_parent ) {
					//$css_class[] = 'current_page_parent';
				}
			} elseif ( get_option( 'page_for_posts' ) === $page->ID ) {
				$css_class[] = 'current_page_parent';
				// blog page while blog post shown
				$css_class[] = 'active';
			} 
			else {
				// check if current href matches current uri, if so add class `active`
				$serverName = $_SERVER['SERVER_NAME'];
				$serverUri = $_SERVER['REQUEST_URI'];

				$serverNameSplittedPageHref = explode( $serverName , $pageHref )[ 1 ];
				$HashSplittedServerUri = explode( '#' , $serverUri )[ 0 ];
				$paramsSplittedServerUri = explode( '?' , $HashSplittedServerUri )[ 0 ];

				if ( $serverNameSplittedPageHref === $paramsSplittedServerUri ) {
					$css_class[] = 'active';
				}

			}

			// check if homepage, prepare custom home link
			if ( $homeHref . '/' == $pageHref || $homeHref == $pageHref ) {
				// is homepage – make mobile only icon home link

				$css_class[] = 'bsx-appnav-desktop-hidden';
				$args['link_before'] = '<i class="fa fa-home" aria-hidden="true"></i>&nbsp;<span class="sr-only">';
				$args['link_after'] = '</span>';
			}


			/** This filter is documented in wp-includes/class-walker-page.php */
			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
			$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

			if ( '' === $page->post_title ) {
				/* translators: %d: ID of a post. */
				$page->post_title = sprintf( __( '#%d (no title)', 'twentytwenty' ), $page->ID );
			}

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$atts                 = array();
			$atts['href']         = get_permalink( $page->ID );

			// link id is required for each dropdown link to connect link with dropdown list
			$linkId = 'appnav-link-' . $page->ID;

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				// add css class `bsx-appnav-dropdown-toggle` to link of dropdown item
				$atts['class']            = 'bsx-appnav-dropdown-toggle';
				// add id, data & aria attr (corresponding ul needs aria-labelledby="CORRESPONDING_LINK_ID_HERE")
				$atts['id']               = $linkId;
				$atts['data-fn']          = 'dropdown-multilevel';
				$atts['aria-haspopup']    = 'true';
				$atts['aria-expanded']    = 'false';
			}

			$atts['aria-current'] = ( $page->ID === $current_page ) ? 'page' : '';

			/** This filter is documented in wp-includes/class-walker-page.php */
			$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_page );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$args['list_item_before'] = '';
			$args['list_item_after']  = '';

			/*
			// Wrap the link in a div and append a sub menu toggle.
			if ( isset( $args['show_toggles'] ) && true === $args['show_toggles'] ) {
				// Wrap the menu item link contents in a div, used for positioning.
				$args['list_item_before'] = '<div class="ancestor-wrapper">';
				$args['list_item_after']  = '';

				// Add a toggle to items with children.
				if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {

					$toggle_target_string = '.menu-modal .page-item-' . $page->ID . ' > ul';
					$toggle_duration      = twentytwenty_toggle_duration();

					// Add the sub menu toggle.
					$args['list_item_after'] .= '<button class="toggle sub-menu-toggle fill-children-current-color" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" data-toggle-duration="' . absint( $toggle_duration ) . '" aria-expanded="false"><span class="screen-reader-text">' . __( 'Show sub menu', 'twentytwenty' ) . '</span>' . twentytwenty_get_theme_svg( 'chevron-down' ) . '</button>';

				}

				// Close the wrapper.
				$args['list_item_after'] .= '</div><!-- .ancestor-wrapper -->';
			}

			// Add icons to menu items with children.
			if ( isset( $args['show_sub_menu_icons'] ) && true === $args['show_sub_menu_icons'] ) {
				if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
					// remove icon element
					$args['list_item_after'] = '';
				}
			}
			*/

			if ( ! $meta_hidden_in_main_nav ) {

				$output .= $indent . sprintf(
					'<li%s>%s<a%s>%s%s%s</a>%s',
					$css_classes,
					$args['list_item_before'],
					$attributes,
					$args['link_before'],
					/** This filter is documented in wp-includes/post-template.php */
					apply_filters( 'the_title', $page->post_title, $page->ID ),
					$args['link_after'],
					$args['list_item_after']
				);

				/*
				if ( ! empty( $args['show_date'] ) ) {
					if ( 'modified' === $args['show_date'] ) {
						$time = $page->post_modified;
					} else {
						$time = $page->post_date;
					}

					$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
					$output     .= ' ' . mysql2date( $date_format, $time );
				}
				*/

				// add opening ul here since required page id is known here but not in `start_lvl()`
				if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
					$output .= "{$n}{$indent}<ul aria-labelledby=\"" . $linkId . "\">{$n}{$indent}<li class=\"bsx-appnav-back-link\">{$n}{$indent}<a href=\"#\" aria-label=\"Menüebene schließen\" data-label=\"" . __( 'Back', 'bsx-wordpress' ) . "\" data-fn=\"dropdown-multilevel-close\"></a>{$n}</li>{$n}";

					if ( $createClickableParentLinkChild ) {
						$output .= "<li class=\"page-" . $page->ID . "\"><a href=\"" . $pageHref . "\">" . __( 'Overview', 'bsx-wordpress' ) . "</a></li>";
					}
				}

			} // /! $meta_hidden_in_main_nav
			else {
				// since closing tag cannot be removed open hidden opening tag
				$output .= $indent . '<li style="display: none !important" aria-hidden="true">';
			}
		}


		/**
		 * Starts the list before the elements are added.
		 *
		 * The $args parameter holds additional values that may be used with the child
		 * class methods. This method is called at the start of the output list.
		 *
		 * @since 2.1.0
		 * @abstract
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param int    $depth  Depth of the item.
		 * @param array  $args   An array of additional arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
				$t = "\t";
				$n = "\n";
			} else {
				$t = '';
				$n = '';
			}
			$indent  = str_repeat( $t, $depth );
			// opening ul has been moved to `start_el()` since required page id is known there but not here
			$output .= "";
		}
	}
}
