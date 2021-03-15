<?php

class Accordion {

  public $data;
  public $id;

  function __construct( $data, $id ) {
    $this->data = $data;
    $this->id = $id;
  }

  // default class names
  const DEFAULT_TARGET_OPENED_CLASS = 'open';
  const DEFAULT_TRIGGER_OPENED_CLASS = 'open';

  // config attr params
  const CONFIG_ATTR_KEYS = array(
    'multi_open' => 'multipleOpen',
    'target_opened_class' => 'targetOpenedClass',
    'trigger_opened_class' => 'triggerOpenedClass',
  );

  // public function print_content( data, id ) {
  //   // call constant: self::DEFAULT_TARGET_OPENED_CLASS;
  //   echo self::DEFAULT_TARGET_OPENED_CLASS;
  // }

  public function print() {
    
    // list
    $output = '<ul class="list-unstyled" data-acc';
    if ( isset ( $this->data[ 'config' ][ 'multi_open' ] ) ||  isset ( $this->data[ 'config' ][ 'trigger_opened_class' ] ) || isset ( $this->data[ 'config' ][ 'target_opened_class' ] ) ) {
      $output .= ' data-acc-conf="{ ';
      if ( isset ( $this->data[ 'config' ][ 'multi_open' ] ) ) {
        $output .= self::CONFIG_ATTR_KEYS[ 'multi_open' ] . ': ' . ( $this->data[ 'config' ][ 'multi_open' ] ? 'true' : 'false' ) . ', ';
      }
      if ( isset ( $this->data[ 'config' ][ 'trigger_opened_class' ] ) ) {
        $output .= self::CONFIG_ATTR_KEYS[ 'trigger_opened_class' ] . ': ' . "'" . $this->data[ 'config' ][ 'trigger_opened_class' ] . "'" . ', ';
      }
      if ( isset ( $this->data[ 'config' ][ 'target_opened_class' ] ) ) {
        $output .= self::CONFIG_ATTR_KEYS[ 'target_opened_class' ] . ': ' . "'" . $this->data[ 'config' ][ 'target_opened_class' ] . "'" . ', ';
      }
      // remove last 2 chars
      $output = substr( $output, 0, -2 );
      $output .= ' }"';
    }
    $output .= '>';

    // items
    if ( isset ( $this->data[ 'items' ] ) && sizeof( $this->data[ 'items' ] > 0 ) ) {
      $index = 0;
      foreach ( $this->data[ 'items' ] as $item ) {
        if ( isset( $item[ 'title' ], $item[ 'content' ] ) ) {
          $output .= '<li data-acc-itm>';
            $output .= '<section>';

              $output .= '<h3 class="my-0">';

                // set trigger aria & opened class
                $trigger_opened_class = $item[ 'opened' ] ? ( isset( $this->data[ 'config' ][ 'trigger_opened_class' ] ) ? ' ' . $this->data[ 'config' ][ 'trigger_opened_class' ] : ' ' . self::DEFAULT_TRIGGER_OPENED_CLASS )  : '';
                $aria_expanded_val = $item[ 'opened' ] ? 'true' : 'false';
                $aria_disabled_attr = ! $this->data[ 'multi_open' ] && $item[ 'opened' ] ? ' aria-disabled="true"' : '';
                $output .= '<button class="acc-header' . $trigger_opened_class . '" id="acc-' . $this->id . '-' . $index . '-trig" data-bsx="acc" aria-controls="acc-' . $this->id . '-' . $index . '-cont" aria-expanded="' . $aria_expanded_val . '">';
                
                  $output .= '<span class="acc-header-text">' . $item[ 'title' ] . '</span>';
                  $output .= '<span class="acc-header-icon"></span>';
                $output .= '</button>';
              $output .= '</h3>';

              // set target opened class
              $target_opened_class = $item[ 'opened' ] ? ( isset( $this->data[ 'config' ][ 'target_opened_class' ] ) ? ' ' . $this->data[ 'config' ][ 'target_opened_class' ] : ' ' . self::DEFAULT_TARGET_OPENED_CLASS )  : '';
              $output .= '<div class="bsx-acc-content' . $target_opened_class . '" id="acc-' . $this->id . '-' . $index . '-cont" role="region" aria-labeledby="acc-' . $this->id . '-' . $index . '-trig">';
              
                $output .= '<div acc-cnt-inr>';
                  $output .= $item[ 'content' ];
                $output .= '</div>';
              $output .= '</div>';

            $output .= '</section>';
          $output .= '</li>';
        }
        $index += 1;
      } 
    }

    $output .= '</ul>';

    echo $output;

  }

}

