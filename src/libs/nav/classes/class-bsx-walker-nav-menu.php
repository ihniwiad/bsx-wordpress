<?php
/**
 * Custom Nav Menu API: Walker_Nav_Menu class
 *
 * @package WordPress
 * @subpackage Nav_Menus
 * @since 4.6.0
 */

/**
 * Core class used to implement an HTML list of nav menu items.
 *
 * @since 3.0.0
 *
 * @see Walker
 */

if ( ! class_exists( 'Bsx_Walker_Nav_Menu' ) ) {

  class Bsx_Walker_Nav_Menu extends Walker_Nav_Menu {
    /**
     * What the class handles.
     *
     * @since 3.0.0
     * @var string
     *
     * @see Walker::$tree_type
     */
    public $tree_type = array( 'post_type', 'taxonomy', 'custom' );

    /**
     * Database fields to use.
     *
     * @since 3.0.0
     * @todo Decouple this.
     * @var array
     *
     * @see Walker::$db_fields
     */
    public $db_fields = array(
      'parent' => 'menu_item_parent',
      'id'     => 'db_id',
    );

    /**
     * Starts the list before the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::start_lvl()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {

      if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
      } else {
        $t = "\t";
        $n = "\n";
      }
      $indent = str_repeat( $t, $depth );

      // opening ul has been moved to `start_el()` since required page id is known there but not here
      $output .= "";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::end_lvl()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
      if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
      } else {
        $t = "\t";
        $n = "\n";
      }
      $indent  = str_repeat( $t, $depth );
      $output .= "$indent</ul>{$n}";
    }

    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

      $createClickableParentLinkChild = true;

      // check if current url is subfolder of blog url
      $blog_url = get_permalink( get_option( 'page_for_posts' ) );
      $path = $_SERVER[ 'REQUEST_URI' ]; // path after domain
      $server_name = $_SERVER[ 'SERVER_NAME' ]; // domain (not protocol)
      $protocol = ( ! empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] !== 'off' || $_SERVER[ 'SERVER_PORT' ] == 443 ) ? "https://" : "http://"; // protocol
      $current_url = $protocol . $server_name . $path;

      $page_is_blog_subpage = false;
      if ( strrpos( ( $current_url ), $blog_url ) === 0 && $current_url != $blog_url ) {
        $page_is_blog_subpage = true;
      }

      // check if current menu item is blog link
      $item_is_blog_link = false;
      if ( ! empty( $item->url ) && $item->url === $blog_url ) {
        $item_is_blog_link = true;
      }

      if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
      } else {
        $t = "\t";
        $n = "\n";
      }
      $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

      $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;

      // get object id (e.g. page id) and type (e.g. page)
      $object_id = isset( $item->object_id ) ? $item->object_id : '';
      $object_type = isset( $item->object ) ? $item->object : '';

      if ( 
        in_array( 'current_page_item', $classes, true ) 
        || in_array( 'current_page_ancestor', $classes, true ) 
        || $item_is_blog_link && $page_is_blog_subpage
      ) {
        $classes[] = 'active';
      }


      /**
       * Filters the arguments for a single nav menu item.
       *
       * @since 4.4.0
       *
       * @param stdClass $args  An object of wp_nav_menu() arguments.
       * @param WP_Post  $item  Menu item data object.
       * @param int      $depth Depth of menu item. Used for padding.
       */
      $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

      /**
       * Filters the CSS classes applied to a menu item's list item element.
       *
       * @since 3.0.0
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
       * @param WP_Post  $item    The current menu item.
       * @param stdClass $args    An object of wp_nav_menu() arguments.
       * @param int      $depth   Depth of menu item. Used for padding.
       */
      $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
      $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

      /**
       * Filters the ID applied to a menu item's list item element.
       *
       * @since 3.0.1
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
       * @param WP_Post  $item    The current menu item.
       * @param stdClass $args    An object of wp_nav_menu() arguments.
       * @param int      $depth   Depth of menu item. Used for padding.
       */
      $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
      $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

      // add id and type
      $item_identifier = ! empty( $object_id ) && ! empty( $object_type ) ? ' data-' . $object_type . '="' . $object_id . '"' : '';

      // $output .= $indent . '<li' . $id . $class_names . ' data-test-li>';
      $output .= $indent . '<li' . $id . $class_names . $item_identifier . '>';

      $atts           = array();
      $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
      $atts['target'] = ! empty( $item->target ) ? $item->target : '';
      if ( '_blank' === $item->target && empty( $item->xfn ) ) {
        $atts['rel'] = 'noopener';
      } else {
        $atts['rel'] = $item->xfn;
      }
      $atts['href']         = ! empty( $item->url ) ? $item->url : '';
      $atts['aria-current'] = $item->current ? 'page' : '';


      // TEST
      // $atts['data-test-a'] = '1';

      // TEST: read $args
      // if ( $item->current ) {
      //   echo '</div>dump $args: ' . var_dump( $args ) . '<div>';
      // }
      // TEST: read $item
      // if ( $item->current ) {
      //   echo '</div>dump $item: ' . var_dump( $item ) . '<div>';
      // }

      // link id is required for each dropdown link to connect link with dropdown list
      $linkId = 'appnav-link-' . $item->ID;
      $dropdownId = 'appnav-dropdown-' . $item->ID;

      $classes = empty( $item->classes ) ? array() : (array) $item->classes;

      // check if has children (inspired from twentytwentyone)
      if ( in_array( 'menu-item-has-children', $classes, true ) ) {
        // add css class `bsx-appnav-dropdown-toggle` to link of dropdown item
        $atts['class']            = 'bsx-appnav-dropdown-toggle';
        // add id, data & aria attr (corresponding ul needs aria-labelledby="CORRESPONDING_LINK_ID_HERE")
        $atts['id']               = $linkId;
        $atts['data-fn']          = 'dropdown-multilevel';
        $atts['aria-haspopup']    = 'true';
        $atts['aria-controls']    = $dropdownId;
        $atts['aria-expanded']    = 'false';
      }

      /**
       * Filters the HTML attributes applied to a menu item's anchor element.
       *
       * @since 3.6.0
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param array $atts {
       *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
       *
       *     @type string $title        Title attribute.
       *     @type string $target       Target attribute.
       *     @type string $rel          The rel attribute.
       *     @type string $href         The href attribute.
       *     @type string $aria_current The aria-current attribute.
       * }
       * @param WP_Post  $item  The current menu item.
       * @param stdClass $args  An object of wp_nav_menu() arguments.
       * @param int      $depth Depth of menu item. Used for padding.
       */
      $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

      $attributes = '';
      foreach ( $atts as $attr => $value ) {
        if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
          $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
          $attributes .= ' ' . $attr . '="' . $value . '"';
        }
      }

      // remember href for overview subitem
      $pageHref = '';
      if ( isset( $atts[ 'href' ] ) ) {
          $pageHref = $atts[ 'href' ];
      }

      /** This filter is documented in wp-includes/post-template.php */
      $title = apply_filters( 'the_title', $item->title, $item->ID );

      /**
       * Filters a menu item's title.
       *
       * @since 4.4.0
       *
       * @param string   $title The menu item's title.
       * @param WP_Post  $item  The current menu item.
       * @param stdClass $args  An object of wp_nav_menu() arguments.
       * @param int      $depth Depth of menu item. Used for padding.
       */
      $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

      $args = is_object( $args ) ? $args : (object) $args;

      $item_output  = $args->before;
      $item_output .= '<a' . $attributes . '>';
      $item_output .= $args->link_before . $title . $args->link_after;
      $item_output .= '</a>';
      $item_output .= $args->after;

      /**
       * Filters a menu item's starting output.
       *
       * The menu item's starting output only includes `$args->before`, the opening `<a>`,
       * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
       * no filter for modifying the opening and closing `<li>` for a menu item.
       *
       * @since 3.0.0
       *
       * @param string   $item_output The menu item's starting HTML output.
       * @param WP_Post  $item        Menu item data object.
       * @param int      $depth       Depth of menu item. Used for padding.
       * @param stdClass $args        An object of wp_nav_menu() arguments.
       */
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

      // start ul here (after a is built) instead of in `start_lvl()` function

      $classes = empty( $item->classes ) ? array() : (array) $item->classes;

      // check if has children (inspired from twentytwentyone)
      if ( in_array( 'menu-item-has-children', $classes, true ) ) {

        // Default class.
        $classes = array( 'sub-menu' );

        /**
         * Filters the CSS class(es) applied to a menu list element.
         *
         * @since 4.8.0
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
         * @param stdClass $args    An object of `wp_nav_menu()` arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        // $output .= "{$n}{$indent}<ul$class_names id=\"" . $dropdownId . "\" aria-labelledby=\"" . $linkId . "\" data-test-ul>{$n}";
        $output .= "{$n}{$indent}<ul$class_names id=\"" . $dropdownId . "\" aria-labelledby=\"" . $linkId . "\">{$n}";

        // add back item
        $output .= $n . $indent . '<li class="bsx-appnav-back-link">' . $n . $indent . '<a href="#" aria-label="' . __( 'Close Menu item', 'bsx-wordpress' ) . '" data-label="' . __( 'Back', 'bsx-wordpress' ) . '" data-fn="dropdown-multilevel-close"></a>' . $n . '</li>' . $n;

        // add overview item
        if ( $createClickableParentLinkChild ) {
          $output .= "<li class=\"auto-parent-link-" . $object_id . "\"><a href=\"" . $pageHref . "\">" . __( 'Overview', 'bsx-wordpress' ) . "</a></li>";
        }

      }
      else {
        // do nothing since there is no list to open
      }
    }

    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     *
     * @see Walker::end_el()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Page data object. Not used.
     * @param int      $depth  Depth of page. Not Used.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
      if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
      } else {
        $t = "\t";
        $n = "\n";
      }
      $output .= "</li>{$n}";
    }

  }

}
