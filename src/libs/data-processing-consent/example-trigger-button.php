<?php 

// example consent popup trigger

if ( class_exists( 'Consent_Popup_Manager' ) && method_exists( 'Consent_Popup_Manager', 'popupTriggerHtml' ) ) {
echo Consent_Popup_Manager::popupTriggerHtml();
}