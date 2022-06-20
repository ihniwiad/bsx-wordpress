
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'

import * as formValidate from './form-validate'


var sendMail = function( $form ) {

    var $hvd = $form.find( '[data-g-tg="hvd"]' );
    var $hvo = $hvd.find( '[data-g-tg="hvo"]' );

    var prepareHv = function() {

        function rN( n ) {
            return Math.floor( Math.random() * n ) + 1;
        }
        function mV( k, v ) {
            return '<span class="bsx-hv-' + k + '">' + v + '</span>';
        }

        var operators = [ '+', '-', '*', '/', '...', '?' ];
        var hvVal = '';
        var hvkey = rN( 100 );
        var mx = 14;
        var nc = 10;
        var hvkey = parseInt( ( Math.abs( hvkey % operators.length ) + hvVal ).substring( 0, 1 ) );
        if ( hvkey == 0 ) hvkey = 1;

        var hvo = operators[ Math.abs( hvkey % operators.length ) ];

        var itms = 1 + rN( 3 );
        var html = '';
        var k = hvVal;

        if ( itms > operators.length / 2 ) {
            hvkey = hvkey < 8 ? 7 + rN( 3 ) : hvkey;
            var rn = rN( 5 );
            var tp = rN( 2 ) % 2 == 0 ? 1 : 0;
            for ( var i = 0; i < itms; i++ ) {
                // 8: remove 4th
                // 9: remove 3rd
                // 10: remove 2nd
                var x = tp ? ( String.fromCharCode( 65 + rn + i ) ).toLowerCase() : rn + i;
                if ( ( hvkey == nc && i != 1 ) || ( hvkey == nc - 1 && i != 2 ) || ( hvkey == nc - 2 && i != 3 ) ) {
                    html += mV( hvkey, x );
                    hvVal += '|' + x;
                }
            }
        }
        else if ( itms % 2 == 0 ) {
            var rns = [ rN( 8 ), rN( 4 ) ];

            hvkey = hvkey > 4 || hvkey < 1 ? 0 + rN( 4 ) : hvkey;

            if ( hvkey == Math.pow( itms, 2 ) ) {
                hvkey = 1;
            }
            if ( rns[ 0 ] == 2 ) {
                hvkey = Math.pow( itms, 2 ) - 1;
            }
            if ( 
                rns[ 1 ] === itms 
                && rns[ 0 ] % 2 === 0
                && rns[ 0 ] != 1 
                && rns[ 0 ] % rns[ 1 ] === 0
                && rns[ 0 ] > rns[ 1 ] 
            ) { 
                hvkey = Math.pow( itms, 2 );
            }
            if ( hvkey == itms && rns[ 0 ] < rns[ 1 ] ) {
                rns = [ rns[ 1 ], rns[ 0 ] ];
            }
            if ( hvkey === itms && rns[ 0 ] === rns[ 1 ] ) {
                hvkey = hvkey - 1;
            }

            for ( var i = 0; i < itms; i++ ) {
                html += mV( hvkey, rns[ i ] );
                hvVal += '|' + rns[ i ];
            }
        }
        else {
            var rns = [ rN( 3 ), rN( 7 ), rN( 6 ) ];
            hvkey = hvkey < 5 || hvkey > 7 ? 4 + rN( 3 ) : hvkey;
            for ( var i = 0; i < itms; i++ ) {
                html += mV( hvkey, rns[ i ] );
                hvVal += '|' + rns[ i ];
            }
        }

        hvVal = hvo + '|' + hvkey + hvVal;
        $hvo.html( hvo );
        $hvd.html( html );
        $form
            .find( '[data-g-tg="hv"]' ).val( encodeURIComponent( hvVal ) )
            .find( '[data-g-tg="hv-k"]' ).val( k )
        ;
    }
    // /prepareHv

    // init hv
    prepareHv();


    // refresh hv

    var $refreshHvTrigger = $form.find( '[data-g-fn="refresh-hv"]' );

    $refreshHvTrigger.on( 'click', function() {
        prepareHv();
    } );


    $form.on( 'submit', function( event ) {

        event.preventDefault();
        event.stopPropagation();

        var $formSubmit = $form.find( '[type="submit"]' );

        // disable submit button
        $formSubmit.prop( 'disabled', true );

        if ( ! $form.formValidate( { successCallback: false } ) ) {

            // enable submit button
            $formSubmit.prop( 'disabled', false );

            return false;
        }

        Utils.WaitScreen.show();

        var defaults = {
            invalidClass: Utils.classes.invalid,
            // sendEmptyValues: true
        };

        // get options from attr
        var options = Utils.getOptionsFromAttr( $form );

        options = $.extend( {}, defaults, options );

        var $messageWrapper = $form.parent().find( '[data-g-tg="message-wrapper"]' );

        var formData = $form.serialize();
        // var formData = Utils.getFormValues( $form );


        // DEBUG
        // console.log( 'typeof formData: ' + typeof formData )
        // console.log( 'TEST:' + formData )

        // console.log( 'TEST:' + JSON.stringify( formData, null, 2 ) )


        var showMessage = function( $messageWrapper, state, message ) {
            var $message = $messageWrapper.find( '[data-g-tg="' + state + '-message"]' );
            var $otherMessage = $messageWrapper.children().not( $message );
            var $responseText = $message.find( '[data-g-tg="response-text"]' );

            $otherMessage.hide();
            Utils.aria( $otherMessage, 'hidden', true );

            $message.show();
            Utils.aria( $message, 'hidden', false );
            $responseText.html( message );
        }

        var hideMessage = function( $messageWrapper, state ) {
            var $message = $messageWrapper.find( '[data-g-tg="' + state + '-message"]' );

            $message.hide();
            Utils.aria( $message, 'hidden', true );
        }

        var scrollMessageIntoViewport = function( $messageWrapper ) {

            var distanceTop = function() {
                return Utils.anchorOffsetTop;
            };
            var messageOffset = $messageWrapper.offset().top;
            var messageHeight = $messageWrapper.outerHeight( true );
            var distanceBottom = 20;
            var $scrollTarget = Utils.$scrollRoot;

            // only scroll if error is outside of viewport
            if ( messageOffset - distanceTop() < window.pageYOffset ) {
                // above the fold
                $scrollTarget.animate( {
                    scrollTop: messageOffset - distanceTop()
                } );
            }
            else if ( messageOffset + messageHeight + distanceBottom > ( window.pageYOffset + window.innerHeight ) ) {
                // below the fold
                $scrollTarget.animate( {
                    scrollTop: messageOffset - window.innerHeight + messageHeight + distanceBottom
                } );
            }
        }

        $.ajax( {
            type: 'POST',
            url: $form.attr( 'action' ),
            data: formData
        } )
            .done( function( response ) {

                prepareHv();

                Utils.WaitScreen.hide();

                // $( formMessages ).text( response );

                // clear inputs, remove success message on input click
                var $visibleInputs = $form.find( 'input:not([type="hidden"]), textarea' );
                // $visibleInputs
                //     .val( '' )
                //     .one( 'focus.removeMessage', function() {
                //         hideMessage( $messageWrapper, 'success' );
                //         $visibleInputs.off( 'focus.removeMessage' );
                //     } )
                // ;

                // keep values of radio & chekboxes untouched
                $visibleInputs.each( function() {
                    var $currentInput = $( this );
                    if ( $currentInput.attr( 'type' ) == 'radio' || $currentInput.attr( 'type' ) == 'checkbox' ) {
                        // do NOT manipulate value, just set unselected
                        $currentInput.prop('checked', false)
                    }
                    else {
                        // reset to ""
                        $currentInput.val( '' );
                    }
                    $currentInput.one( 'focus.removeMessage', function() {
                        hideMessage( $messageWrapper, 'success' );
                        $visibleInputs.off( 'focus.removeMessage' );
                    } )
                } );

                // show success
                showMessage( $messageWrapper, 'success', response );

                scrollMessageIntoViewport( $messageWrapper );

                // enable submit button
                $formSubmit.prop( 'disabled', false );
            } )
            .fail( function( data ) {

                prepareHv();

                Utils.WaitScreen.hide();

                if ( data.responseText !== '' ) {
                    // console.log( data.responseText );

                    // show error
                    showMessage( $messageWrapper, 'error', data.responseText );

                    scrollMessageIntoViewport( $messageWrapper );
                } 
                else {
                    console.log( 'An unknown error occured. Your message could not be sent.' );
                }

                // enable submit button
                $formSubmit.prop( 'disabled', false );
            } )
        ;

    } );

};


$.fn.initSendMail = function() {

    var $form = $( this );

    return sendMail( $form );
}


// init

Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="mail-form"]' ).each( function() {
    $( this ).initSendMail();
} );