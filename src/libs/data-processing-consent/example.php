<?php

$consent_data = array(
  array(
    'cat' => 'analytics',
    'cat_label' => 'Analyse',
    'items' => array(
      array(
        'type' => 'script-src',
        'position' => 'header',
        'code' => 'http://localhost/cookie-related-popup/test/testing.js'
      ),
      array(
        'type' => 'script-content',
        'position' => 'header',
        'code' => "console.log( 'hello from inline script' );"
      ),
      array(
        'type' => 'html',
        'code' => '<div class="container py-3">Hello, im consent-related <span style="background: #fc3;">HTML-content</span>.</div>'
      )
    )
  ),
  array(
    'cat' => 'other-category',
    'cat_label' => 'Other category',
    'items' => array(
      array(
        'type' => 'script-src',
        'position' => 'header',
        'code' => 'http://localhost/cookie-related-popup/test/testing-2.js'
      ),
      array(
        'type' => 'script-content',
        'position' => 'header',
        'code' => "console.log( 'hello from inline script 2' );"
      )
    )
  ),
  array(
    'cat' => 'empty-category',
    'cat_label' => 'Empty category'
  )
);


if ( class_exists( 'Consent_Popup_Manager' ) ) {
  $consent_popup_manager = new Consent_Popup_Manager( $consent_data );
}




?>

<!-- consent popup -->    
<div class="fixed-banner fixed-banner-bottom fixed-banner-closable bg-secondary d-none" id="consent-popup" aria-labeledby="consent-popup-trigger" tabindex="-1" role="dialog" hidden data-fn="cookie-related-elem" data-tg="data-processing-popup" data-fn-options="{ cookieName: 'dataProcessingConsentBannerHidden', cookieExpiresDays: 365, hiddenCookieValue: '1', hiddenClass: 'd-none', remoteOpenable: true }">
      
  <div class="container py-3">
    
    <form data-fn="data-processing-form" data-fn-options="{ cookieName: 'dataProcessingConsent', cookieExpiresDays: 365, categoryInputSelector: '[data-g-tg=category-input]' }">
      <div class="form-row align-items-center">

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="data-processing-consent-essential" value="essential" checked disabled data-g-tg="category-input">
          <label class="form-check-label" for="data-processing-consent-essential">Essential</label>
        </div>

        <?php
          if ( method_exists( $consent_popup_manager, 'printCheckboxes' ) ) {
            $consent_popup_manager->printCheckboxes();
          }
        ?>

        <!--
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="data-processing-consent-0-0" value="analytics" data-g-tg="category-input">
          <label class="form-check-label" for="data-processing-consent-0-0">Analytics</label>
        </div>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="data-processing-consent-1-0" value="other-category" data-g-tg="category-input">
          <label class="form-check-label" for="data-processing-consent-1-0">Other category</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="data-processing-consent-2-0" value="google-maps" data-g-tg="category-input">
          <label class="form-check-label" for="data-processing-consent-2-0">Google Maps</label>
        </div>
        -->

        <div class="col-auto">
          <button class="btn btn-outline-primary btn-sm" type="submit" data-fn="cookie-related-elem-close" data-g-fn="save">Save</button>
        </div>

        <div class="col-auto">
          <button class="btn btn-primary btn-sm" data-fn="cookie-related-elem-close" data-g-fn="allow-all">Allow all</button>
        </div>

      </div>
    </form>
    
  </div>
  
</div>


<?php
  if ( method_exists( $consent_popup_manager, 'printData' ) ) {
    // build consent data
    $consent_popup_manager->printData();
  }
?>


