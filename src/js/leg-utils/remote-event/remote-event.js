/*

<div data-fn="remote-event" data-fn-options="{ , triggerEvent: 'click',  remoteEvent: 'click' }"></div>

*/


// import DomData from './../../dom/dom-data'
// import MakeFnElems from './../../dom/function-elements'


import $ from "jquery"
import Utils from './../utils/utils'


// const KEY = 'remote-event'



$.fn.remoteEvent = function() {

    var $elems = $( this );

    $elems.each( function() {

        var $elem = $( this );
        var options = Utils.getOptionsFromAttr( $elem );

        var targetSelector = '';
        if ( typeof options.target != 'undefined' ) {
            targetSelector = options.target;
        }
        var $target = ( Utils.$functionAndTargetElems.filter( targetSelector ).lenght > 0 ) ? Utils.$functionAndTargetElems.filter( targetSelector ) : $( targetSelector );
        
        var triggerEvent = options.triggerEvent || 'click';
        var remoteEvent = options.remoteEvent || 'click';

        $elem.on( triggerEvent, function() {
            if ( $target.length > 0 ) {
                $target.trigger( remoteEvent );
            }
        } );

    } );

};

// init

Utils.$functionElems.filter( '[data-fn="remote-event"]' ).remoteEvent();


// init

// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( trigger ) => {
//     $( trigger ).remoteEvent()
//   } )
// }

