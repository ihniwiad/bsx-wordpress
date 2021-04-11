
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


var sendMail = function( $form ) {

    console.log( 'init sendMail()' );

    $form.on( 'submit', function( event ) {

        console.log( 'submit' );

        event.preventDefault();
        event.stopPropagation();

        var defaults = {
            invalidClass: Utils.classes.invalid,
            // sendEmptyValues: true
        };

        // get options from attr
        var options = Utils.getOptionsFromAttr( $form );

        options = $.extend( {}, defaults, options );

        // form message elem
        var $message = $form.parent().find( '[data-g-tg="message"]' );
        var $responseText = $message.find( '[data-g-tg="response-text"]' );


        // TODO: refactor later

        var formData = $form.serialize();

        $.ajax( {
            type: 'POST',
            url: $form.attr( 'action' ),
            data: formData
        } )
            .done( function( response ) {

                console.log( response );
                // $( formMessages ).text( response );

                // clear
                // $form.find( 'input, textarea' ).val( '' );
                $responseText.html( response );

                Utils.replaceFormByMessage( $form, { $message: $message } );
            } )
            .fail( function( data ) {

                if ( data.responseText !== '' ) {
                    console.log( data.responseText );
                } 
                else {
                    console.log( 'An unknown error occures. Your message could not be sent.' );
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