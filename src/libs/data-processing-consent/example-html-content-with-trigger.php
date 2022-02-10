<?php 

// example consent related html content (e.g. external iframes or videos)
?>
	<button class="btn btn-outline-primary" data-fn="data-processing-cat-consent-trigger" data-fn-options="{ cat: 'html-content' }">Activate category “HTML Content“</button>
<?php
if ( class_exists( 'Consent_Popup_Manager' ) && method_exists( 'Consent_Popup_Manager', 'consentApplyHtml' ) ) {
	Consent_Popup_Manager::consentApplyHtml( 'html-content', '<div class="border bg-light text-danger lead p-3 my-4">Some dangerous content, e.g external iframes or videos</div>' );
}