/*

REQUIRES: 
    $.fn.initLazyload
    Utils.UiHandler


<div class="owl-carousel owl-theme" data-fn="owl-carousel" data-fn-options="{ nav: false }">
    <div class="item bg-light display-4">1</div>
    <div class="item bg-light display-4">2</div>
    <div class="item bg-light display-4">3</div>
    ...
</div>

*/


/*
    TODO:
        - remove jquery-lazyload
        - add listener slider appear in screen (check scroll, resize)
            - start autoplay
            - start manual load in active slide
                - clone each loaded img into equal imgs in clone(s)
            - on change / drag init load in next / prev slide
*/


import $ from 'jquery'
// import * as jQuery from 'jquery'
import Utils from './../../js/leg-utils/utils/utils'

// import OwlCarousel from 'owl.carousel.es6'
// import 'owl.carousel'

// import $owl from './owl-carousel-es6'


$.fn.initOwlCarousel = function() {

    console.log( 'initOwlCarousel' );

    var $owls = $( this );

    $owls.each( function( index, elem ) {

        var $owl = $( elem );

        $owl.addClass( 'TEST' )

        var autoplaySpeed = function () { return Math.round( $owl.width() * 0.75 ) };

        var defaults = {
            loop: true,
            margin: 30,
            nav: true,
            navText: [ '<i class="fa fa-arrow-left" aria-label="Prev"></i>', '<i class="fa fa-arrow-right" aria-label="Next"></i>' ],
            responsive: { 0: { items: 1 } },
            autoplay: true,
            autoplaySpeed: autoplaySpeed(),
            smartSpeed: autoplaySpeed(),
            fluidSpeed: autoplaySpeed(),
            dragEndSpeed: autoplaySpeed(),
            navSpeed: autoplaySpeed(),
            dotsSpeed: autoplaySpeed(),
            autoplayTimeout: 8000,
            autoplayHoverPause: true
        };

        var options = $.extend( {}, defaults, Utils.getOptionsFromAttr( $owl ) );


        // trigger appear event
        var appearOptions = {
            appearEvent: 'owlAppear',
            appearOffset: 100
        };
        Utils.UiHandler.listenAppear( $owl, { 
            appearEvent: appearOptions.appearEvent, 
            appearOffset: appearOptions.appearOffset
        } );

        // listen appear event
        //$owl.on( appearOptions.appearEvent, function ( event ) {} );


        /*
            <div class="owl-carousel owl-theme" data-fn="owl-carousel" data-fn-options="{ multiLazyload: true, remoteNav: true, remoteNavSelector: '[data-fn=owl-remote-nav]', responsive: { 0: { items: 1 } } }">
                <div class="item">Slide 1</div>
                <div class="item">Slide 2</div>
                ...
            </div>
        */

        // multi lazyload
        if ( options.multiLazyload ) {

            if ( $.fn.initLazyload ) {

                // lazyOptions

                var defaults = {
                    appearEvent: 'appear',
                    loadEvent: 'load',
                    itemSelector: '.owl-item',
                    itemUnloadClass: 'unload',
                    lazyImgSelector: '[data-g-fn="lazyload"]',
                    lazyImgSrcAttr: 'g-src',
                    lazyImgUnloadClass: 'unload'
                };

                var lazyOptions = $.extend( {}, defaults, Utils.getOptionsFromAttr( $owl ) );

                var jqueryLazyloadOptions = {
                    data_attribute: lazyOptions.lazyImgSrcAttr
                };

                // manage multi lazyload
                var SliderMultiLazyload = {
                    currentIndex: null
                }

                // initialized
                $owl.one( 'initialized.owl.carousel', function ( event ) {

                    var $currentOwl = $( event.target );
                    var currentIndex = event.item.index;
                    var itemCount = event.item.count;
                    var $items = $currentOwl.find( lazyOptions.itemSelector );
                    var $currentItem = $items.eq( currentIndex );
                    var $currentItemWithAssociatedClones = $currentItem.add( $items.eq( currentIndex - itemCount ) ).add( $items.eq( currentIndex + itemCount ) );
                    var $lazyImgs = $items.find( lazyOptions.lazyImgSelector );

                    // initial set current index
                    if ( currentIndex ) {
                        SliderMultiLazyload.currentIndex = currentIndex;
                    }

                    // stop autoplay
                    if ( options.autoplay ) {
                        $currentOwl.trigger( 'stop.owl.autoplay' );
                    }

                    // add unload class to items
                    $items.addClass( lazyOptions.itemUnloadClass );

                    // prepare lazyload listeners
                    $lazyImgs.each( function() {

                        var $lazyImg = $( this );

                        $lazyImg
                            .addClass( lazyOptions.lazyImgUnloadClass )
                            .one( lazyOptions.appearEvent, function( event ) {

                                var $currentImg = $( event.target );

                                $currentImg.one( lazyOptions.loadEvent, function( event ) {
                                    $currentImg.removeClass( lazyOptions.lazyImgUnloadClass );
                                } );

                            } )
                        ;

                    } );

                    // init lazyload
                    $lazyImgs.initLazyload( jqueryLazyloadOptions );

                    // init after appeared
                    $currentOwl.one( appearOptions.appearEvent, function ( event ) {

                        // start autoplay
                        if ( options.autoplay ) {
                            $( event.target ).trigger( 'start.owl.autoplay' );
                            
                            // touch optimation – stop and restart autoplay after each change (mouse devices do stop and restart by hover event)
                            if ( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) {

                                // stop
                                $( event.target ).on( 'drag.owl.carousel', function( event ) {
                                    $( event.target ).trigger( 'stop.owl.autoplay' );
                                } );

                                // restart
                                $( event.target ).on( 'dragged.owl.carousel', function( event ) {
                                    $( event.target ).trigger( 'start.owl.autoplay' );
                                } );

                            }
                            

                        }

                        // trigger appear initial on current slide and associated clones
                        $currentItemWithAssociatedClones
                            .removeClass( lazyOptions.itemUnloadClass )
                            .find( lazyOptions.lazyImgSelector ).trigger( lazyOptions.appearEvent )
                        ;

                    } );

                } );

                // trigger appear if change slide
                $owl.on( 'changed.owl.carousel', function( event ) {

                    var $currentOwl = $( event.target );
                    var currentIndex = event.item.index;
                    var itemCount = event.item.count;
                    var $items = $currentOwl.find( lazyOptions.itemSelector );
                    var itemAndCloneCount = $items.length;
                    var $currentItem = $items.eq( currentIndex );
                    var $currentItemWithAssociatedClones = $currentItem.add( $items.eq( currentIndex - itemCount ) ).add( $items.eq( currentIndex + itemCount ) );
                    var $lazyImgs = $items.find( lazyOptions.lazyImgSelector );

                    $.fn._nthItemAndClonesTriggerAppear = function( currentIndex ) {

                        var $items = $( this );
                        var $currentItem = $items.eq( currentIndex );

                        if ( $currentItem.is( '.' + lazyOptions.itemUnloadClass ) ) {

                            var $currentItemWithAssociatedClones = $currentItem.add( $items.eq( currentIndex - itemCount ) ).add( $items.eq( currentIndex + itemCount ) );

                            // trigger appear on current slide and associated clones
                            $currentItemWithAssociatedClones
                                .removeClass( lazyOptions.itemUnloadClass )
                                .find( lazyOptions.lazyImgSelector ).trigger( lazyOptions.appearEvent )
                            ;
                        }
                    }
                    $items._nthItemAndClonesTriggerAppear( currentIndex );

                    // trigger appear for all slides between current and target if change using dots nav

                    // skip initialization events (which are not user interacted changes)
                    if ( SliderMultiLazyload.currentIndex && currentIndex ) {

                        // check if going forward or backward – check if starting from clone, if so, switch index to corresponding not-clone index
                        var clonesCount = itemAndCloneCount - itemCount;
                        var startedFromBeforeClone = SliderMultiLazyload.currentIndex < clonesCount / 2;
                        var startedFromAfterClone = SliderMultiLazyload.currentIndex >= itemAndCloneCount - ( clonesCount / 2 );

                        if ( startedFromBeforeClone ) {
                            SliderMultiLazyload.currentIndex  += itemCount;
                        }
                        else if ( startedFromAfterClone ) {
                            SliderMultiLazyload.currentIndex  -= itemCount;
                        }

                        if ( currentIndex > SliderMultiLazyload.currentIndex ) {
                            // forward
                            for ( var i = SliderMultiLazyload.currentIndex + 1; i < currentIndex; i++ ) {
                                $items._nthItemAndClonesTriggerAppear( i );
                            }
                        }
                        else {
                            // backward
                            for ( var i = SliderMultiLazyload.currentIndex - 1; i > currentIndex; i-- ) {
                                $items._nthItemAndClonesTriggerAppear( i );
                            }
                        }

                    }

                    // remember current index
                    SliderMultiLazyload.currentIndex = currentIndex;

                } );

            }
            else {
                // ! $.fn.initLazyload
                throw new Error( 'Required function “initLazyload” is missing!' );
            }
        }


        /*
            <nav data-fn="owl-remote-nav">
                <button data-g-fn="0">Slide 1</button>
                <button data-g-fn="1">Slide 2</button>
                ...
            </nav>
        */

        // remote slider nav
        if ( options.remoteNav && getElementFromSelector( options.remoteNavSelector ).length > 0 ) {

            var $sliderNav = getElementFromSelector( options.remoteNavSelector );

            var defaults = {
                itemSelector: '[data-g-fn]',
                itemIdAttr: 'data-g-fn',
                activeClass: 'active',
                activeRemoveClass: ''
            }

            var navOptions = $.extend( {}, defaults, Utils.getOptionsFromAttr( $sliderNav ) );

            var $sliderNavItems = $sliderNav.find( navOptions.itemSelector );

            // manage navigation
            var SliderNav = {
                currentIndex: 0,
                autoplaying: ( options.autoplay ) ? true : false
            }
            SliderNav.owlGoto = function( position ) {

                if ( options.autoplay && SliderNav.autoplaying ) {
                    $owl.trigger( 'stop.owl.autoplay' );
                }

                $owl.trigger( 'to.owl.carousel', position, autoplaySpeed() );

                if ( options.autoplay && SliderNav.autoplaying ) {
                    $owl.trigger( 'play.owl.autoplay' );
                }

            }
            SliderNav.listenChange = function() {

                // note: dots need to be enabled, otherwise event.page.index will always return -1

                $owl.on( 'changed.owl.carousel', function( event ) {

                    // adapt nav (initial fires -1, change to 0)
                    var page = ( event.page.index > -1 ) ? event.page.index : 0;

                    // each change will adapt nav, no matter if caused by (not active) nav clicking
                    SliderNav.set( page );

                } );
            }
            SliderNav.set = function( itemId ) {

                // reset active
                $sliderNavItems.eq( SliderNav.currentIndex )
                    .removeClass( navOptions.activeClass )
                    .addClass( navOptions.activeRemoveClass )
                ;

                // set new active
                $sliderNavItems.eq( itemId )
                    .addClass( navOptions.activeClass )
                    .removeClass( navOptions.activeRemoveClass )
                ;

                SliderNav.currentIndex = itemId;

            }

            // set click listener
            $sliderNavItems.each( function() {

                var $sliderNavItem = $( this );

                var targetItemId = parseInt( $sliderNavItem.attr( navOptions.itemIdAttr ) );

                $sliderNavItem.on( 'click', function() {

                    if ( targetItemId != SliderNav.currentIndex ) {
                        // set owl (owl change will adapt nav)
                        SliderNav.owlGoto( targetItemId, options.autoplaySpeed );
                    }
                    else {
                        // click on current item, do nothing
                    }
                } );

            } );

            // stop autoplay on nav hover
            if ( options.autoplay ) {

                //if ( typeof Modernizr !== 'undefined' && ! Modernizr.touchevents ) {

                    $sliderNav.hover( function() {
                        $owl.trigger( 'stop.owl.autoplay' );
                        SliderNav.autoplaying = false;
                    }, function() {
                        $owl.trigger( 'play.owl.autoplay' );
                        SliderNav.autoplaying = true;
                    } );

                //}

            }

            // listen slider change
            SliderNav.listenChange();

        }

    
        // stop if window inactive (if SliderNav stop only while autoplay not paused by SliderNav)
        if ( options.autoplay && ( typeof SliderNav === 'undefined' ) || ( typeof SliderNav !== 'undefined' && SliderNav.autoplaying ) ) {
            Utils.$window.blur( function() {
                $owl.trigger( 'stop.owl.autoplay' );
            } );
            Utils.$window.focus( function() {
                $owl.trigger( 'play.owl.autoplay' );
            } );
        }


        // init
        $owl.owlCarousel( options );

    } );

}

// init
Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="owl-carousel"]' ).initOwlCarousel();

