/*

MARKUP

<a class="create-mt" data-fn="create-mt" data-mt-n="info" data-mt-d="example" data-mt-s="com"></a>

*/


// import DomData from './../../js/dom/dom-data'
// import MakeFnElems from './../../js/dom/function-elements'


import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


// const KEY = 'create-mt'



$.fn._createMt = function( options, index ) {

    var defaults = {
      // events: 'touch hover'
    };

    options = $.extend( {}, defaults, options );

    var a = '@';
    var d = '.';
    var p = 'ma' + 'il' + 'to:';

    $.fn._addHref = function( href, index ) {
        if ( 
            typeof $( this ).attr( 'href' ) == 'undefined' 
            || $( this ).attr( 'href' ).replace( / /g, '' ).indexOf( 'javascript:void(0)' ) == 0
        ) {
            $( this )
                .attr( 'href', href )
                // .off( 'mouseenter.' + index )
                // .off( 'touchstart.' + index )
            ;
            // console.log( 'added: ' + href );
        }
        else {
            // console.log( 'href already set (please destroy event listener)' );
        }
    }

    var $elem = $( this );

    var addr = $elem.attr( 'data-mt-n' ) + a + $elem.attr( 'data-mt-d' ) + d + $elem.attr( 'data-mt-s' );
    var href = p + addr;

    // add href
    // if ( Utils.hasTouch() ) {
    //     $elem.one( 'touchstart', function() {
    //         $( this )._addHref( href, index );
    //     } );
    // }
    // else {
    //     $elem.one( 'mouseenter mousedown', function() {
    //         $( this )._addHref( href, index );
    //     } );
    // }

    // do not separate touch devices since some browsers support `Utils.hasTouch()` but do NOT trigger `touchstart`
    $elem.one( 'mouseenter mousedown touchstart', function() {
        $( this )._addHref( href, index );
    } );
};

$.fn._initCreateMt = function( options ) {

    $( this ).each( function( i, elem ) {

        var $elem = $( elem );

        var options = Utils.getOptionsFromAttr( $elem );

        return $elem._createMt( options, i );

    } );

};


// init
Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="create-mt"]' )._initCreateMt();


// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( trigger ) => {
//     $( trigger )._initCreateMt()
//   } )
// }

