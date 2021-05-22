// import $ from "jquery"

// var UTILS = {
//   selector: 'test-selector',
//   testClass: 'TEST',
//   functionElems: $( '[data-bsx]' )
// }

// export default UTILS


import $ from "jquery"
import BSX_UTILS from './utils'


var Utils = {
    $document:      $( document ),
    $window:        $( window ),
    $body:          $( 'body' ),
    $scrollRoot:    $( 'html, body'),

    $functionElems: null,
    $targetElems: null,

    events: {
        initJs: 'initJs'
    },

    selectors: {
        functionElement:    '[data-fn]',
        targetElement:      '[data-tg]',
        focussableElements: 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex="0"]'
    },

    attributes: {
        functionElement:    'data-fn',
        targetElement:      'data-tg',
        target:             'data-fn-target',
        options:            'data-fn-options',
        callback:           'data-fn-callback'
    },

    classes: {
        open:           'show',
        active:         'active',
        animating:      'animating',
        animatingIn:    'animating-in',
        animatingOut:   'animating-out',
        invalid:        'is-invalid'
    },
    
    mediaSize: null,
    mediaSizes: [ 
        {
            breakpoint: 0,
            label: 'xs'
        },
        {
            breakpoint: 576,
            label: 'sm'
        },
        {
            breakpoint: 768,
            label: 'md'
        },
        {
            breakpoint: 992,
            label: 'lg'
        },
        {
            breakpoint: 1200,
            label: 'xl'
        }
    ],

    anchorOffsetTop: 0,
    
};

// cache all functional elements
Utils.$functionAndTargetElems = $( Utils.selectors.functionElement + ', ' + Utils.selectors.targetElement );
Utils.$functionElems = Utils.$functionAndTargetElems.filter( Utils.selectors.functionElement );
Utils.$targetElems = Utils.$functionAndTargetElems.filter( Utils.selectors.targetElement );

// anchors offset top
var anchorOffsetTopSelector = '[data-fn~="anchor-offset-elem"]';
var anchorOffsetTopDistance = 20;
var $anchorOffsetTopElem = Utils.$functionElems.filter( anchorOffsetTopSelector );

$.fn._getAnchorOffset = function() {
    // if header element position is fixed scroll below header

    var offsetTop = anchorOffsetTopDistance;

    if ( $anchorOffsetTopElem.length > 0 && $anchorOffsetTopElem.css( 'position' ) == 'fixed' ) {
        offsetTop += $anchorOffsetTopElem.outerHeight();
    }

    return offsetTop;
}

Utils.anchorOffsetTop = $anchorOffsetTopElem._getAnchorOffset();

Utils.$window.on( 'sizeChange', function() {
    Utils.anchorOffsetTop = $anchorOffsetTopElem._getAnchorOffset();
} );

// get lang
Utils.lang = Utils.$body.parent().attr( 'lang' ) || 'en';

// convert type
function _convertType( value ) {
    try {
        value = JSON.parse( value );
        return value;
    }
    catch( e ) {
        // 'value' is not a json string.
        return value
    }
}

// get transition duration
$.fn.getTransitionDuration = function() {
    var duration = 0;
    var cssProperty = 'transition-duration';
    var prefixes = [ 'webkit', 'ms', 'moz', 'o' ];
    if ( this.css( cssProperty ) ) {
        duration = this.css( cssProperty );
    }
    else {
        for ( var i = 0; i < prefixes.length; i++ ) {
            if ( this.css( '-' + prefixes[ i ] + '-' + cssProperty ) ) {
                duration = this.css( '-' + prefixes[ i ] + '-' + cssProperty );
                break;
            }
        }
    }

    if ( duration.indexOf != undefined ) {
        return ( duration.indexOf( 'ms' ) > -1 ) ? parseFloat( duration ) : parseFloat( duration ) * 1000;
    }
    else {
        return 0;
    }
    
};

// set and remove animation class
Utils.setRemoveAnimationClass = function( elem, animatingClass, callback ) {
    var currentAnimatingClass = ( !! animatingClass ) ? animatingClass : Utils.classes.animating;
    var $this = $( elem );
    var transitionDuration = $this.getTransitionDuration();
    if ( transitionDuration > 0 ) {
        $this.addClass( animatingClass );
        var timeout = setTimeout( function() {
            $this.removeClass( animatingClass );
            if ( !! callback ) {
                callback();
            }
        }, transitionDuration );
    }
};

// check if element is positiones inside (x, y, width, height) of another element
Utils.elemPositionedInside = function( elem, container ) {

    var $this = $( elem );
    var $container = $( container );

    var elemOffsetLeft = $this.offset().left;
    var elemOffsetTop = $this.offset().top;
    var elemWidth = $this.width();
    var elemHeight = $this.height();

    var containerOffsetLeft = $container.offset().left;
    var containerOffsetTop = $container.offset().top;
    var containerWidth = $container.outerWidth(); // include border since offset will calulate only to border
    var containerHeight = $container.outerHeight();

    return elemOffsetLeft >= containerOffsetLeft
        && ( elemOffsetLeft + elemWidth ) <= ( containerOffsetLeft + containerWidth )
        && elemOffsetTop >= containerOffsetTop
        && ( elemOffsetTop + elemHeight ) <= ( containerOffsetTop + containerHeight );
};

// calculate sizes to fit inner element into outer element (only if inner is larger than outer) keeping distance in x & y direction
Utils.getFitIntoSizes = function( settings ) {
    
    var outerWidth = settings.outerWidth || Utils.$window.width();
    var outerHeight = settings.outerHeight || Utils.$window.height();
    var innerWidth = settings.innerWidth;
    var innerHeight = settings.innerHeight;
    var xDistance = settings.xDistance || 0;
    var yDistance = settings.yDistance || 0;
    
    var resizeWidth;
    var resizeHeight;
    
    var outerRatio =  outerWidth / outerHeight;
    var innerRatio = ( innerWidth + xDistance ) / ( innerHeight + yDistance );
    
    if ( outerRatio > innerRatio ) {
        // limited by height
        resizeHeight = ( outerHeight >= innerHeight + yDistance ) ? innerHeight : outerHeight - yDistance;
        resizeWidth = parseInt( innerWidth / innerHeight * resizeHeight );
    }
    else {
        // limited by width
        resizeWidth = ( outerWidth >= innerWidth + xDistance ) ? innerWidth : outerWidth - xDistance;
        resizeHeight = parseInt( innerHeight / innerWidth * resizeWidth );
    }
    
    return [ resizeWidth, resizeHeight ];
}

// aria expanded
Utils.ariaExpanded = function( elem, value ) {
    if ( typeof value !== 'undefined' ) {
        $( elem ).attr( 'aria-expanded', value );
        return value;
    }
    return _convertType( $( elem ).attr( 'aria-expanded' ) );
};

// aria
Utils.aria = function( elem, ariaName, value ) {
    if ( typeof value !== 'undefined' ) {
        $( elem ).attr( 'aria-' + ariaName, value );
        return value;
    }
    else {
        return _convertType( $( elem ).attr( 'aria-' + ariaName ) );
    }
};

// hidden
Utils.hidden = function( elem, value ) {
    if ( typeof value !== 'undefined' ) {
        if ( value == true ) {
            $( elem ).attr( 'hidden', true );
        }
        else {
            $( elem ).removeAttr( 'hidden' );
        }
    }
    else {
        return _convertType( $( elem ).attr( hidden ) );
    }
};

// media size (media change event)
var mediaSize = '';
var mediaSizeBodyClassPrefix = 'media-';

var _getmediaSize = function() {
    var currentmediaSize;
    if ( !! window.matchMedia ) {
        // modern browsers
        for ( var i = 0; i < Utils.mediaSizes.length - 1; i++ ) {
            if ( window.matchMedia( '(max-width: ' + ( Utils.mediaSizes[ i + 1 ].breakpoint - 1 ) + 'px)' ).matches ) {
                currentmediaSize = Utils.mediaSizes[ i ].label;
                break;
            }
            else {
                currentmediaSize = Utils.mediaSizes[ Utils.mediaSizes.length - 1 ].label;
            }
        }
    }
    else {
        // fallback old browsers
        for ( var i = 0; i < Utils.mediaSizes.length - 1; i++ ) {
            if ( Utils.$window.width() < Utils.mediaSizes[ i + 1 ].breakpoint ) {
                currentmediaSize = Utils.mediaSizes[ i ].label;
                break;
            }
            else {
                currentmediaSize = Utils.mediaSizes[ Utils.mediaSizes.length - 1 ].label;
            }
        }
    }
    if ( currentmediaSize != Utils.mediaSize ) {
        // remove / set body class
        Utils.$body.removeClass( mediaSizeBodyClassPrefix + Utils.mediaSize );
        Utils.$body.addClass( mediaSizeBodyClassPrefix + currentmediaSize );

        Utils.mediaSize = currentmediaSize;
        Utils.$window.trigger( 'sizeChange' );
    }
};
Utils.$document.ready( function() {
    _getmediaSize();
    Utils.$window.trigger( 'sizeChangeReady' );
} );
Utils.$window.on( 'resize', function() {
    _getmediaSize();    
} );
// /media size (media change event)

// get options from attribute
// syntax: data-fn-options="{ focusOnOpen: '[data-tg=\'header-search-input\']', bla: true, foo: 'some text content' }"
Utils.getOptionsFromAttr = function( elem ) {
    var $this = $( elem );
    var options = $this.attr( Utils.attributes.options );
    if ( typeof options !== 'undefined' ) {
        return ( new Function( 'return ' + options ) )();
    }
    else {
        return {};
    }
}
// /get options from attribute

// get elem from selector
Utils.getElementFromSelector = function( selector ) {
    var $elem = Utils.$functionAndTargetElems.filter( selector );
    if ( $elem.length == 0 ) {
        $elem = $( selector );
    }
    return $elem;
}
// /get elem from selector

// check touch
Utils.hasTouch = function() {
    return (
            ( 'ontouchstart' in window )
            || ( navigator.maxTouchPoints > 0 )
            || ( navigator.msMaxTouchPoints > 0 )
    );
}
if ( Utils.hasTouch() ) {
    document.documentElement.classList.add( 'touchevents' );
}
Utils.hasTouchNotMouse = function() {
    return (
        Utils.hasTouch()
        && ! ( 'onmousemove' in window )
    );
}
// /check touch

// check browser
// detect ios / android
var isIos = /iPad|iPhone|iPod/.test( navigator.platform ) && ! window.MSStream;
var iosVersion = null;
var iosFullVersion = null;
var isAndroid = /(android)/i.test( navigator.userAgent );
var isWin = navigator.platform.indexOf( 'Win' ) > -1;
var isMobileIe = navigator.userAgent.match( /iemobile/i );
var isWinPhone = navigator.userAgent.match( /Windows Phone/i );
if ( isIos ) {
    document.body.className += ' is-ios';

    // detect version (required for fixes)
    var iosMaxVersion = 11;
    iosVersion = parseInt(
        ( '' + ( /CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec( navigator.userAgent ) || [ 0,'' ] )[ 1 ] )
        .replace( 'undefined', '3_2' ).replace( '_', '.' ).replace( /_/g, '' )
    ) || false;
    iosFullVersion = ( '' + ( /CPU.*OS ([0-9_]{1,9})|(CPU like).*AppleWebKit.*Mobile/i.exec( navigator.userAgent ) || [ 0,'' ] )[ 1 ] )
        .replace( 'undefined', '3_2' ) || false;
    if ( iosVersion !== false ) {
        document.body.className += ' ios' + iosVersion;
        for ( i = iosVersion; i <= iosMaxVersion; i++ ) {
            document.body.className += ' ioslte' + i;
        }
    }

}
else if ( isAndroid ) {
    document.body.className += ' is-android';
}
else if ( isWin ) {
    document.body.className += ' is-win';
    if ( isMobileIe ) {
        document.body.className += ' is-mobile-ie';
    }
}
if ( isWinPhone ) {
    document.body.className += ' is-win-phone';
}
function detectIe() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf( 'MSIE ' );
        if ( msie > 0 ) {
        return parseInt( ua.substring( msie + 5, ua.indexOf( '.', msie ) ), 10 );
    }
    var trident = ua.indexOf( 'Trident/' );
    if ( trident > 0 ) {
        var rv = ua.indexOf( 'rv:' );
        return parseInt( ua.substring( rv + 3, ua.indexOf( '.', rv ) ), 10 );
    }
    var edge = ua.indexOf( 'Edge/' );
    if ( edge > 0 ) {
        return parseInt( ua.substring( edge + 5, ua.indexOf( '.', edge ) ), 10 );
    }
    return false;
}
// detect ie gt 9
var ieMaxVersion = 14;
var ieVersion = detectIe();
var isIe = ( ieVersion !== false );
if ( isIe && ieVersion > 9 ) {
    document.body.className += ' ie ie' + ieVersion;
    for ( i = ieVersion; i <= ieMaxVersion; i++ ) {
        document.body.className += ' ielte' + i;
    }
}
// add browser data to utils to use global
Utils.AnalyzeBrowser = {
    isIos: function() { return isIos; },
    iosVersion: function() { return iosVersion; },
    iosFullVersion: function() { return iosFullVersion; },
    isAndroid: function() { return isAndroid; },
    isWin: function() { return isWin; },
    isIe: function() { return isIe; },
    ieVersion: function() { return ieVersion; },
    isMobileIe: function() { return isMobileIe; },
    isWinPhone: function() { return isWinPhone; }
};
// /check browser


// cookie handler
Utils.CookieHandler = {
    setCookie: function( cookieName, cookieValue, expiresDays, path, sameSite ) {
        var date = new Date();
        var sameSiteDefault = 'strict';
        date.setTime( date.getTime() + ( expiresDays * 24 * 60 * 60 * 1000 ) );
        document.cookie = cookieName + '=' + cookieValue + '; ' + 'expires=' + date.toUTCString() + ( !! path ? '; path=' + path : '' ) + '; sameSite=' + ( !! sameSite ? sameSite : sameSiteDefault ) + ( sameSite == 'none' ? '; secure' : '' );
    },
    getCookie: function( cookieName ) {
        var searchStr = cookieName + '=';
        var cookies = document.cookie.split( ';' );
        for ( var i = 0; i < cookies.length; i++ ) {
            var cookie = cookies[ i ];
            while ( cookie.charAt( 0 ) == ' ' ) {
                cookie = cookie.substring( 1 );
            };
            if ( cookie.indexOf( searchStr ) == 0 ) {
                return cookie.substring( searchStr.length, cookie.length );
            };
        }
        return '';
    }
};
// /cookie handler


// ui handler
Utils.UiHandler = {
    id: -1,
    listenAppear: function( elem, options ) {

        var $elem = $( elem );

        var defaults = {
            appearEventTriggered: false,
            appearEvent: 'appear.uiHandler',
            appearOffset: 0
        };

        // get options from function
        var options = $.extend( {}, defaults, options );

        // get options from attr
        options = $.extend( {}, options, Utils.getOptionsFromAttr( $elem ) );

        // data
        $elem.data( {
            appearEventTriggered: false,
            id: Utils.UiHandler.id + 1
        } );

        function _elemInWindow( elem, tol ) {

            var $this = $( elem );
            var tolerance = tol || 0;

            var elemOffsetTop = $this.offset().top;
            var elemHeight = $this.height();

            var windowScrollTop = Utils.$window.scrollTop();
            var windowHeight = Utils.$window.height();

            return ( ! ( elemOffsetTop > windowScrollTop + windowHeight + tolerance ) ) 
                && ( ! ( windowScrollTop > elemOffsetTop + elemHeight + tolerance ) );
        };

        Utils.$window.on( 'scroll.' + options.appearEvent + '.' + $elem[ 'id' ] + ' resize.' + options.appearEvent + '.' + $elem[ 'id' ], function() {
            if ( 
                ! $elem.data( 'appearEventTriggered' )
                && _elemInWindow( $elem, options.appearOffset )
            ) {
                $elem[ 'appearEventTriggered' ] = true;
                $elem.trigger( options.appearEvent );
            }
            else {
                if ( $elem.data.appearEventTriggered ) {
                    Utils.$window.off( 'scroll.' + options.appearEvent + '.' + $elem[ 'id' ] + ' resize.' + options.appearEvent + '.' + $elem[ 'id' ] );
                }
            }
        } );
    }
};
// /ui handler


// get form values
// Utils.getFormValues = function( form ) {

//     var values = {};

//     $formElems = $( form ).find( 'input, select, textarea' );

//     $formElems.each( function( i, elem ) {

//         var $elem = $( elem );

//         if ( $elem.attr( 'type' ) == 'checkbox' ) {

//             var checkboxName = $elem.attr( 'name' );
//             var $checkboxGroup = $formElems.filter( '[name="' + checkboxName + '"]' );
//             var checkboxGroupCount = $checkboxGroup.length;

//             if ( checkboxGroupCount > 1 ) {
//                 var checkboxGroupValues = [];
//                 $checkboxGroup.each( function( j, groupElem ) {
//                     var $groupElem = $( groupElem );
//                     if ( $groupElem.is( ':checked' ) ) {
//                         checkboxGroupValues.push( $groupElem.val() );
//                     }
//                 } );
//                 if ( checkboxGroupValues.length > 0 ) {
//                     values[ checkboxName ] = checkboxGroupValues;
//                 }
//                 else {
//                     values[ checkboxName ] = null;
//                 }
//             }
//             else {
//                 values[ checkboxName ] = $elem.is( ':checked' ) ? $elem.val() : null;
//             }
//         }
//         else if ( $elem.attr( 'type' ) == 'radio' ) {
//             if ( $elem.is( ':checked' ) ) {
//                 values[ $elem.attr( 'name' ) ] = $elem.val();
//             }
//         }
//         else {
//             values[ $elem.attr( 'name' ) ] = $elem.val();
//         }
//     } );
//     return values;
// }

// replace form by message
Utils.replaceFormByMessage = function( form, options ) {

    var $form = $( form );
    var $parent = $form.parent();
    var $message = ( !! options && !! options.$message ) ? options.$message : $form.next();

    // hide form, show message instead
    $parent.css( { height: ( parseInt( $parent.css( 'height' ) ) + 'px' ) } );
    $form.fadeOut( function() {
        $message.fadeIn();
        $parent.animate( { height: ( parseInt( $message.css( 'height' ) ) + 'px' ) }, function() {
            $parent.removeAttr( 'style' );
        } );
    } );
    $form.aria( 'hidden', true );
    $message.aria( 'hidden', false );

    // TODO: scroll up to message begin
    // TODO: split success / error message ?

}
// /replace form by message

// execute callback function
// Utils.executeCallbackFunction = function( elem ) {

//     var callbackStr = $( elem ).attr( Utils.attributes.callback );

//     if ( !! callbackStr ) {

//         // get function name
//         var explode = callbackStr.split( '(' );
//         var callbackName = explode[ 0 ];

//         var callback = Function( callbackStr );

//         if ( !! callback && typeof window[ callbackName ] === 'function' ) {
//             callback();
//         }

//     }
// }
// /execute callback function


// wait screen

/*
<!-- WAIT SCREEN -->
<div class="wait-screen" data-tg="wait-screen">
    <i class="fa fa-circle-o-notch fa-spin wait-screen-icon" aria-hidden="true"></i>
</div>
*/

Utils.WaitScreen = {
    isOpen: false,
    count: 0,
    show: function() {
        Utils.WaitScreen.count++;
        Utils.WaitScreen.$waitScreen.addClass( Utils.WaitScreen.options.openClass );
        Utils.WaitScreen.isOpen = true;
    },
    hide: function( forceClosing ) {
        Utils.WaitScreen.count--;
        if ( Utils.WaitScreen.count < 1 || forceClosing ) {
            Utils.WaitScreen.count = 0;
            Utils.WaitScreen.$waitScreen.removeClass( Utils.WaitScreen.options.openClass );
            Utils.WaitScreen.isOpen = false;
        }
    },
    init: function( options ) {
        var defaults = {
            openClass: Utils.classes.open
        }
        Utils.WaitScreen.options = $.extend( {}, defaults, options );
        Utils.WaitScreen.$waitScreen = Utils.$targetElems.filter( '[' + Utils.attributes.targetElement + '~="wait-screen"]' );
    }
};

// init wait screen
Utils.WaitScreen.init();

// /wait screen

export default Utils;
