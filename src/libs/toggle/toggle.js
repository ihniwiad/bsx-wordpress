/*

<button type="button" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" data-fn="toggle" data-fn-target="[data-tg='navbar-collapse']">
    <i class="fa fa-navicon" aria-hidden="true"></i>
</button>

<div id="navbarNavDropdown" data-tg="navbar-collapse">
    ...
</div>

*/


// import DomData from './../../js/dom/dom-data'
// import MakeFnElems from './../../js/dom/function-elements'


import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


// const KEY = 'toggle'



// toggle (e.g. main navigation container)
$.fn.toggle = function() {

    var defaults = {
        openedClass: Utils.classes.open,
        closedClass: '',
        animatingClass: Utils.classes.animating,
        triggerOpenedClass: Utils.classes.active,
        triggerClosedClass: '',
        bodyOpenedClass: '',
        bodyClosedClass: '',
        openCallback: function() {},
        closeCallback: function() {},
        openedCallback: function() {},
        closedCallback: function() {},
        reset: false // reset to initial state whatever current state might be
    };

    var $elems = $( this );

    $elems.each( function() {

        var $elem = $( this );

        // get options from attr
        var options = Utils.getOptionsFromAttr( $elem );

        options = $.extend( {}, defaults, options );

        var targetSelector = $elem.attr( Utils.attributes.target ) || '';
        var $target = ( Utils.$targetElems.filter( targetSelector ).lenght > 0 ) ? Utils.$targetElems.filter( targetSelector ) : $( targetSelector );
        var transitionDuration = $target.getTransitionDuration();

        if ( $target.length > 0 ) {

            function _show() {
                $target
                    .addClass( options.openedClass )
                    .removeClass( options.closedClass )
                ;
                $elem
                    .removeClass( options.triggerClosedClass )
                    .addClass( options.triggerOpenedClass )
                ;
                Utils.ariaExpanded( $elem, 'true' );
                if ( options.bodyOpenedClass ) {
                    Utils.$body.addClass( options.bodyOpenedClass );
                }
                if ( options.bodyClosedClass ) {
                    Utils.$body.removeClass( options.bodyClosedClass );
                }
                options.openCallback();

                // set & remove options.animatingClass
                Utils.setRemoveAnimationClass( $target, options.animatingClass, options.openedCallback )
            }

            function _hide() {
                $target
                    .removeClass( options.openedClass )
                    .addClass( options.closedClass )
                ;
                $elem
                    .addClass( options.triggerClosedClass )
                    .removeClass( options.triggerOpenedClass )
                ;
                Utils.ariaExpanded( $elem, 'false' );
                if ( options.bodyOpenedClass ) {
                    Utils.$body.removeClass( options.bodyOpenedClass );
                }
                if ( options.bodyClosedClass ) {
                    Utils.$body.addClass( options.bodyClosedClass );
                }
                options.closeCallback();
                
                // set & remove options.animatingClass
                Utils.setRemoveAnimationClass( $target, options.animatingClass, options.closedCallback )
            }

            // click
            $elem.on( 'click', function() {

                // toggle 'options.openedClass' & aria-expanded (use 'options.openedClass' to check visibility since element might be ':visible' but out of viewport)
                // allow multiple classes (which would be separated by space)
                if ( ! options.reset && ! $target.is( '.' + options.openedClass.replace( ' ', '.' ) ) ) {
                    _show()
                }
                else {
                    _hide()
                }

            } );

            // show
            $elem.on( 'show', function() {
                _show()
            } );

            // hide
            $elem.on( 'hide', function() {
                _hide()
            } );

        }

    } );

};


// init
Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="toggle"]' ).toggle();



// init

// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( trigger ) => {
//     $( trigger ).toggle()
//   } )
// }

