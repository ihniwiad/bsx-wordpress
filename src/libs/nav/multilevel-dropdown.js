/*

EXAMPLE 1:

<ul>
    <li>
        <a href="#" aria-label="close" data-fn="dropdown-multilevel-close"></a>
    </li>
    <li>
        <a id="id-1" href="#" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false">Link</a>
        <ul aria-labelledby="id-1"><!--  -->
            ...
        </ul>
    </li>
    ...
</ul>

<div data-tg="dropdown-multilevel-excluded">I will be ignored</div>


EXAMPLE 2:

- external trigger for level 1 (anywhere)
- trigger and list do not have to share a common list

<a id="main-navbar-trigger" href="#" data-fn="dropdown-multilevel" aria-haspopup="true" aria-controls="main-navbar" aria-expanded="false">Open menu</a>

<nav id="main-navbar" aria-labelledby="main-navbar-trigger">
    <ul>
        <li>
            <a href="#" aria-label="close" data-fn="dropdown-multilevel-close"></a>
        </li>
        <li>
            <a id="id-1" href="#" data-fn="dropdown-multilevel" aria-haspopup="true" aria-expanded="false">Link</a>
            <ul aria-labelledby="id-1"><!--  -->
                ...
            </ul>
        </li>
        ...
    </ul>
</nav>

*/


// import DomData from './../../js/dom/dom-data'
// import MakeFnElems from './../../js/dom/function-elements'


import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'
// import BrowserAnalytics from './../../js/browser-analytics/index'


// const KEY = 'dropdown-multilevel'



// fix ios missing body click event (set event to all elements which are children of body)
if ( Utils.AnalyzeBrowser.isIos() ) {
    var bodyChildren = document.body.children;
    for ( var i = 0; i < bodyChildren.length; i++ ) {
        // if ( bodyChildren[ i ].tagName == 'DIV' ) {
            bodyChildren[ i ].setAttribute( 'onclick', 'void(0);' );
        // }
    }
}

// dropdown multilevel (e.g. main navigation lists)
$.fn.dropdownMultilevel = function( options ) {

    // config
    var defaults = {
        openedClass: Utils.classes.open,
        hasOpenedSublevelClass: 'has-' + Utils.classes.open,
        animatingClass: Utils.classes.animating,
        closeElemSelector: '[' + Utils.attributes.functionElement + '~="dropdown-multilevel-close"]',
        excludedBodyElements: '[' + Utils.attributes.targetElement + '~="dropdown-multilevel-excluded"]',
        scrollDuration: 100
    };

    options = $.extend( {}, defaults, options );

    // vars
    var openedElems = []; // remember opened elements
    var $elems = $( this );
    var $excludedElems = Utils.$functionAndTargetElems.filter( options.excludedBodyElements );

    // functions
    $.fn._getTarget = function() {
        // gets target (li) defined by triggers target attribute or gets parent li as target
        var $this = $( this );
        var $target;
        if ( $this.attr( Utils.attributes.target ) ) {
            // has fn target attr
            var targetSelector = $this.attr( Utils.attributes.target );
            $target = ( Utils.$functionAndTargetElems.filter( targetSelector ).lenght > 0 ) ? Utils.$functionAndTargetElems.filter( targetSelector ) : $( targetSelector );
        }
        else {
            // parent
            $target = $this.parent( 'li' );
        }
        return $target;
    };
    $.fn._getList = function() {
        // gets relatet list (ul) using aria labelledby attribute (refers to trigger id)
        var $this = $( this );
        return $this._getTarget().find( '[aria-labelledby="' + $this.attr( 'id' ) + '"]' );
    };
    $.fn._getCloseElem = function() {
        // gets close link (must be placed within first list element)
        var $this = $( this );
        return $this._getList().children().fist().find( '[' + Utils.attributes.functionElement + '~="dropdown-multilevel-close"]' );
    };
    $.fn._getParentList = function() {
        // gets parent ul to target (doesn’t have to be parent to trigger)
        var $this = $( this );
        return $this._getTarget().parent( 'ul' );
    };
    $.fn._openDropdown = function() {

        var $this = $( this );
        var $thisTarget = $this._getTarget();
        var $thisParentList = $this._getParentList();
        $thisTarget
            .addClass( options.openedClass );
        Utils.setRemoveAnimationClass( $thisTarget, options.animatingClass );
        Utils.ariaExpanded( $this, 'true' );
        $thisParentList
            // scroll up to keep opened sublevel in position
            .animate({ scrollTop: 0 }, options.scrollDuration, function() {
                $( this ).addClass( options.hasOpenedSublevelClass );
            } );

        // remember
        openedElems.push( $this );
    };
    $.fn._closeDropdown = function() {

        var $this = $( this );
        var $thisTarget = $this._getTarget();
        var $thisParentList = $this._getParentList();
        $thisTarget
            .removeClass( options.openedClass );
        Utils.setRemoveAnimationClass( $thisTarget, options.animatingClass );
        Utils.ariaExpanded( $this, 'false' );
        $thisParentList.removeClass( options.hasOpenedSublevelClass );

        // remember
        openedElems.pop();
    };
    function _closeAllDropdowns() {

        // close from latest to earliest
        for ( var i = openedElems.length - 1; i >= 0; i-- ) {
            $( openedElems[ i ] )._closeDropdown();
        }

    };

    function _listenBodyWhileDropdownOpen( currentOpenedElems ) {

        Utils.$body.one( 'click.body', function( bodyEvent ) {

            var $bodyEventTarget = $( bodyEvent.target );

            // if dropdowns open
            if ( currentOpenedElems.length > 0 ) {

                if ( $.inArray( $bodyEventTarget[ 0 ], $excludedElems ) == -1 ) {

                    var $currentLatestOpenedList = $( currentOpenedElems[ currentOpenedElems.length - 1 ] )._getList();

                    if ( $currentLatestOpenedList.children().children( options.closeElemSelector ).parent().has( $bodyEventTarget ).length > 0 ) {
                        // click on close button

                        // TODO: allow executing link if bigmenu deepest level shown but still has sublevels

                        bodyEvent.preventDefault();

                        // close current dropdown level
                        $( currentOpenedElems[ currentOpenedElems.length - 1 ] )._closeDropdown();

                        // create new close listener
                        _listenBodyWhileDropdownOpen( openedElems );
                    }
                    else if ( $currentLatestOpenedList.has( $bodyEventTarget ).length > 0 || $currentLatestOpenedList.is( $bodyEventTarget ) ) {
                        // click on opend list (event is inside list || event is list)

                        // create new close listener
                        _listenBodyWhileDropdownOpen( openedElems );
                    }
                    else if ( ! $currentLatestOpenedList.has( $bodyEventTarget ).length > 0 ) {
                        // click outside dropdowns

                        //close all
                        _closeAllDropdowns();
                    }

                }
                else {

                    // create new close listener
                    _listenBodyWhileDropdownOpen( openedElems );

                }

            }

        } );

    }

    $elems.each( function() {

        var $elem = $(this);
        var targetSelector = $elem.attr( Utils.attributes.target ) || '';
        var $target = $elem._getTarget(); // ( targetSelector != '' ) ? $( targetSelector ) : $elem.parent();
        var $list = $elem._getList(); // $target.find( '[aria-labelledby="' + $elem.attr( 'id' ) + '"]' );

        $elem.on( 'click', function( event ) {

            if ( $target.length > 0 && $list.length > 0 ) {

                // remove event listener if click on dropdown trigger since new event listener will be created after click
                Utils.$body.off( 'click.body' );

                // check if clicked on open dropdown trigger
                var $eventTarget = $( event.target );
                var $latestOpenedElem = $( openedElems[ openedElems.length - 1 ] );

                if ( $latestOpenedElem.has( $eventTarget ).length > 0 || $latestOpenedElem.is( $eventTarget ) ) {

                    event.preventDefault();

                    // close current dropdown level
                    $( openedElems[ openedElems.length - 1 ] )._closeDropdown();

                    if ( openedElems.length > 0 ) {

                        // create new close listener
                        _listenBodyWhileDropdownOpen( openedElems );
                    }

                }
                else {

                    // check if do something (check visibility and position inside parent since might be visible but out of sight)
                    if ( ! $list.is( ':visible' ) || ! Utils.elemPositionedInside( $list, $target.parent() ) ) {

                        // show list, stop link execution
                        event.preventDefault();

                        // close opened dropdowns if not parents
                        if ( openedElems.length > 0 ) {

                            var $latestOpenedList = $( openedElems[ openedElems.length - 1 ] )._getList();

                            // check if clicked dropdown is inside or outside of already opened dropdown
                            if ( ! $latestOpenedList.has( $elem ).length > 0 ) {

                                // click outside opened dropdown – close all
                                _closeAllDropdowns();
                            }
                            else {
                                // keep opened dropdowns
                            }

                        }

                        // open
                        $elem._openDropdown();

                        // check options, do any special taks?
                        var options;
                        if ( ( options = Utils.getOptionsFromAttr( $elem ) ) ) {
                            if ( options.focusOnOpen ) {
                                Utils.$functionAndTargetElems.filter( options.focusOnOpen ).focus();
                            }
                        }

                        event.stopPropagation();

                        // create new close listener
                        _listenBodyWhileDropdownOpen( openedElems );

                    }
                    else {
                        // related list is already shown, do not open or close anything

                        // create new close listener
                        _listenBodyWhileDropdownOpen( openedElems );
                    }
                }

            }

        } );

    } );

    // close all dropdowns on resize & orientationchange
    Utils.$window.on( 'orientationchange sizeChange', function() {
        _closeAllDropdowns();
    } );

};

// init

Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="dropdown-multilevel"]' ).dropdownMultilevel();


// init

// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( trigger ) => {
//     $( trigger ).dropdownMultilevel()
//   } )
// }



