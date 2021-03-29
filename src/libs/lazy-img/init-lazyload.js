
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


$.fn.initLazyload = function( options ) {

    var defaults = {
        effect: 'fadeIn',
        event: ( Utils.hasTouch() ) ? 'scroll touchmove' : 'scroll',
        data_attribute: 'src'
    };

    options = $.extend( {}, defaults, options );

    var $elems = $( this );

    $elems.each( function( i, image ) {

        var $image = $( image );

        if ( !! $image.attr( 'data-fn-effect' ) ) {
            options.effect = $image.attr( 'data-fn-effect' );
        }

        $image.lazyload( options, i );
        
    } );

}


export default $.fn.initLazyload
