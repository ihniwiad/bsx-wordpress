
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'



var FormReset = {};

FormReset.reset = function( form, options ) {

    return reset( form, options );

    function reset( form, options ) {

        var $form = $( form );

        var $formControls = $form.find( 'input:not([type="hidden"]):not([type="submit"]), textarea' );

        $formControls.each( function() {
            var $formControl = $( this );

            if ( 
                $formControl.is( 'input' )
                && ( 
                    $formControl.attr( 'type' ) == 'radio'
                    || $formControl.attr( 'type' ) == 'checkbox'
                )
            ) {
                $formControl.prop( 'checked', false );
            }
            else {
                $formControl.val( '' );
            }
        } );
    }
};


$.fn.formReset = function( options ) {
    return FormReset.reset( this, options );
};


export default $.fn.formReset

