/*
<div data-id="form-wrapper">
    <!-- action param `c=?` may be required if cross origin request -->
    <form class="mb-4" novalidate action="https://foo.bar/json?foo=bar&amp;c=?" method="post" data-fn="ajax-form" data-fn-options="{ successKey: 'result', successValue: 'success', successMessageKey: 'msg', errorMessageKey: 'msg', }">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name">First name</label>
                    <input class="form-control" type="text" name="first_name" id="first_name">
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name">Last name</label>
                    <input class="form-control" type="text" name="last_name" id="last_name">
                    <div class="invalid-feedback">Please fill this field.</div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="mail">E-mail*</label>
                    <input class="form-control" type="email" name="mail" id="mail" required>
                    <div class="invalid-feedback">Please enter a valid mail.</div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="consent" name="consent" value="1" required>
                    <label class="form-check-label" for="consent">I aggree.</div>
                </div>
            </div>
        </div>

        <input class="btn btn-primary btn-lg mt-3" type="submit" value="Anmelden" name="subscribe">
    </form>
    <div data-g-tg="message-wrapper">
        <div data-g-tg="success-message" aria-hidden="true" style="display: none;"><div class="alert alert-success lead mb-4" role="alert"><span class="fa fa-check fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span></div></div>
        <div data-g-tg="error-message" aria-hidden="true" style="display: none;"><div class="alert alert-danger lead mb-4" role="alert"><span class="fa fa-exclamation-triangle fa-lg" aria-hidden="true"></span> <span data-g-tg="response-text"></span></div></div>
    </div>
</div>


*/

import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'

import * as formValidate from './form-validate'
import * as formReset from './form-reset'


var sendForm = function( $form ) {

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
            successKey: 'result',
            successValue: 'success',
            successMessageKey: 'msg',
            errorMessageKey: 'msg',
            invalidClass: Utils.classes.invalid,
            errorMessage: 'An unknown error occured. Your message could not be sent.',
            // sendEmptyValues: true
        };

        // get options from attr
        var options = Utils.getOptionsFromAttr( $form );

        options = $.extend( {}, defaults, options );

        var $messageWrapper = $form.parent().find( '[data-g-tg="message-wrapper"]' );

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
            type: $form.attr( 'method' ),
            url: $form.attr( 'action' ),
            data: $form.serialize(),
            cache: false,
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            success: function( data ) {

                // console.log( 'SUCCESS: ' + JSON.stringify( data, null, 2 ) );

                Utils.WaitScreen.hide();

                if ( data[ options.successKey ] == options.successValue ) {
                    // success

                    // clear form
                    $form.formReset();

                    // remove success message on input focus
                    var $visibleInputs = $form.find( 'input:not([type="hidden"]):not([type="submit"]), textarea' );
                    $visibleInputs
                        .one( 'focus.removeMessage', function() {
                            hideMessage( $messageWrapper, 'success' );
                            $visibleInputs.off( 'focus.removeMessage' );
                        } )
                    ;

                    var successMessage = data[ options.successMessageKey ];

                    // show success
                    showMessage( $messageWrapper, 'success', successMessage );
                }
                else {
                    var errorMessage = options.errorMessage;

                    if ( !! data[ options.errorMessageKey ] && data[ options.errorMessageKey ] !== '' ) {
                        errorMessage = data[ options.errorMessageKey ];
                    }
                    // show error
                    showMessage( $messageWrapper, 'error', errorMessage );
                }
                scrollMessageIntoViewport( $messageWrapper );

                // enable submit button
                $formSubmit.prop( 'disabled', false );
            }
        } )
            .fail( function( data ) {

                // console.log( 'FAIL: ' + JSON.stringify( data, null, 2 ) );

                Utils.WaitScreen.hide();

                var errorMessage = options.errorMessage;

                if ( !! data.responseText && data.responseText !== '' ) {
                    errorMessage = data.responseText;
                }

                // show error
                showMessage( $messageWrapper, 'error', data.responseText );

                scrollMessageIntoViewport( $messageWrapper );

                // enable submit button
                $formSubmit.prop( 'disabled', false );
            } )
        ;

    } );

};


$.fn.initSendForm = function() {

    var $form = $( this );

    return sendForm( $form );
}


// init

Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="ajax-form"]' ).each( function() {
    $( this ).initSendForm();
} );