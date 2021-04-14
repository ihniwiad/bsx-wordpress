
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


var sendMail = function( $form ) {

    console.log( 'init sendMail()' );


    // prepare hv
    function rN( n ) {
        return Math.floor( Math.random() * n ) + 1;
    }
    function mV( k, v ) {
        return '<span class="bsx-hv-' + k + '">' + v + '</span>';
    }

    var $hv = $form.find( '[data-g-tg="hv"]' );
    var $hvo = $hv.find( '[data-g-tg="hvo"]' );
    var operators = [ '+', '-', '*', '/', '...', '?' ];
    var hvVal = '';
    var hvkey = rN( 100 );
    var mx = 14;
    var nc = 10;
    var hvkey = parseInt( ( Math.abs( hvkey % operators.length ) + hvVal ).substring( 0, 1 ) );
    if ( hvkey == 0 ) hvkey = 1;

    console.log( 'hvkey (initial): ' + hvkey )

    var hvo = operators[ Math.abs( hvkey % operators.length ) ];

    var itms = 1 + rN( 3 );
    var html = '';
    var k = hvVal;

    if ( itms > operators.length / 2 ) {
        console.log( '4 items' )
        // 4 (8..10)
        hvkey = hvkey < 8 ? 7 + rN( 3 ) : hvkey;
        var rn = rN( 3 );
        var tp = rN( 2 ) % 2 == 0 ? 1 : 0;
        console.log( 'type: ' + tp )
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
        console.log( '2 items' )
        // 2 (1..4)
        var rns = [ rN( 8 ), rN( 4 ) ];

        console.log( 'rns[ 0 ]: ' + rns[ 0 ] )
        console.log( 'rns[ 1 ]: ' + rns[ 1 ] )

        if ( hvkey == Math.pow( itms, 2 ) ) hvkey = 1;
        if ( rns[ 0 ] == 2 ) hvkey = Math.pow( itms, 2 ) - 1;
        if ( rns[ 0 ] % 2 === 0 && rns[ 0 ] != 1 && rns[ 0 ] % rns[ 1 ] === 0 && rns[ 0 ] > rns[ 1 ] && rns[ 1 ] === itms ) hvkey = Math.pow( itms, 2 );
        if ( hvkey == itms && rns[ 0 ] < rns[ 1 ] ) {
            rns = [ rns[ 1 ], rns[ 0 ] ];
            console.log( 'switched values' )
            console.log( '--> rns[ 0 ]: ' + rns[ 0 ] )
            console.log( '--> rns[ 1 ]: ' + rns[ 1 ] )
        }
        if ( hvkey === itms && rns[ 0 ] === rns[ 1 ] ) hvkey = hvkey - 1;
        hvkey = hvkey > 4 || hvkey < 1 ? 0 + rN( 4 ) : hvkey;
        for ( var i = 0; i < itms; i++ ) {
            html += mV( hvkey, rns[ i ] );
            hvVal += '|' + rns[ i ];
        }
    }
    else {
        console.log( '3 items' )
        // 3 (5..7)
        var rns = [ rN( 2 ), rN( 4 ), rN( 6 ) ];
        hvkey = hvkey < 5 || hvkey > 7 ? 4 + rN( 3 ) : hvkey;
        for ( var i = 0; i < itms; i++ ) {
            html += mV( hvkey, rns[ i ] );
            hvVal += '|' + rns[ i ];
        }
    }

    console.log( 'hvkey (final): ' + hvkey )

    hvVal = hvo + '|' + hvkey + hvVal;
    $hvo.html( hvo );
    $hv.html( html );
    $form
        .append( '<input type="hidden" name="hv__text_r" value="' + encodeURIComponent( hvVal ) + '">' )
        .append( '<input type="hidden" name="hv_k__x_r" value="' + k + '">' )
    ;




    $form.on( 'submit', function( event ) {

        console.log( 'submit' );

        event.preventDefault();
        event.stopPropagation();

        Utils.WaitScreen.show();

        var defaults = {
            invalidClass: Utils.classes.invalid,
            // sendEmptyValues: true
        };

        // get options from attr
        var options = Utils.getOptionsFromAttr( $form );

        options = $.extend( {}, defaults, options );

        // TODO: remove – form message elem
        // var $message = $form.parent().find( '[data-g-tg="message"]' );
        // var $responseText = $message.find( '[data-g-tg="response-text"]' );
        var $messageWrapper = $form.parent().find( '[data-g-tg="message-wrapper"]' );


        // TODO: refactor later

        var formData = $form.serialize();

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

        $.ajax( {
            type: 'POST',
            url: $form.attr( 'action' ),
            data: formData
        } )
            .done( function( response ) {

                Utils.WaitScreen.hide();

                console.log( response );
                // $( formMessages ).text( response );

                // clear
                // TODO: what about refresh human verification?
                // $form.find( 'input:not([hidden]), textarea' ).val( '' );
                // $responseText.html( response );

                // Utils.replaceFormByMessage( $form, { $message: $message } );
                var $$messageWrapper = $form.parent().find( '[data-g-tg="message-wrapper"]' );
                // var $responseText = $successMessage.find( '[data-g-tg="response-text"]' );
                showMessage( $messageWrapper, 'success', response );
            } )
            .fail( function( data ) {

                Utils.WaitScreen.hide();

                if ( data.responseText !== '' ) {
                    console.log( data.responseText );

                    showMessage( $messageWrapper, 'error', data.responseText );
                } 
                else {
                    console.log( 'An unknown error occured. Your message could not be sent.' );
                }
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