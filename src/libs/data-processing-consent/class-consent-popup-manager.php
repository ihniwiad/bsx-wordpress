<?php

class Consent_Popup_Manager {

  public $data;

  function __construct( $data ) {
    $this->data = $data;
  }

  private function consentApplyScriptBySrc( $cat, $src, $pos = 'footer' ) {
    echo '<div data-tg="data-processing-consent-content" data-category="' . $cat . '" data-position="' . $pos . '" data-script-src="' . $src . '" aria-hidden="true"></div>' . "\n";
  }

  private function consentApplyScriptByContent( $cat, $content, $pos = 'footer' ) {
    echo '<div data-tg="data-processing-consent-content" data-category="' . $cat . '" data-position="' . $pos . '" data-script-content="' . htmlspecialchars( $content ) . '" aria-hidden="true"></div>' . "\n";
  }

  private function consentApplyHtml( $cat, $html ) {
    echo '<div data-tg="data-processing-consent-content" data-category="' . $cat . '" data-html="' . htmlspecialchars( $html ) . '" aria-hidden="true"></div>' . "\n";
  }

  public function printData() {

    echo '<!-- consent related data (hidden) -->';

    foreach ( $this->data as $cat ) {
      if ( isset( $cat[ 'cat' ] ) && isset( $cat[ 'cat_label' ] ) && isset( $cat[ 'items' ] ) && sizeof( $cat[ 'items' ] ) > 0 ) {
        foreach ( $cat[ 'items' ] as $item ) {
          if ( isset( $item[ 'type' ] ) &&  isset( $item[ 'code' ] ) ) {
            if ( $item[ 'type' ] == 'script-src' ) {
              echo $this->consentApplyScriptBySrc( $cat[ 'cat' ], $item[ 'code' ], $item[ 'position' ] );
            }
            elseif ( $item[ 'type' ] == 'script-content' ) {
              echo $this->consentApplyScriptByContent( $cat[ 'cat' ], $item[ 'code' ], $item[ 'position' ] );
            }
            elseif ( $item[ 'type' ] == 'html' ) {
              echo $this->consentApplyHtml( $cat[ 'cat' ], $item[ 'code' ] );
            }
          }
        }
      }
    }
  }

  public function printCheckboxes() {

    echo '<!-- consent checkboxes -->';

    foreach ( $this->data as $cat ) {
      if ( isset( $cat[ 'cat' ] ) && isset( $cat[ 'cat_label' ] ) ) {
        echo 
          '<div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="data-processing-consent-' . $cat[ 'cat' ] . '" value="' . $cat[ 'cat' ] . '" data-g-tg="category-input">
            <label class="form-check-label" for="data-processing-consent-' . $cat[ 'cat' ] . '">' . $cat[ 'cat_label' ] . '</label>
          </div>';
      }
    }
  }

  public static function popupTriggerHtml() {
    return 
      '<!-- button showing consent popup -->
        <button class="btn btn-primary" id="consent-popup-trigger" aria-haspopup="true" aria-controls="consent-popup" aria-expanded="false" data-fn="data-processing-popup-trigger">' . __( 'See/change cookie settings', 'bsx-wordpress' ) . '</button>';
  }

  public function printHtml( $cat, $html ) {

    // TODO: add cat if not existing

    echo '<!-- consent html -->';

    echo $this->consentApplyHtml( $cat, $html );
  }

}