<?php 

// base64_encode ( string $string ) : string

/*
<figure>
  <script>
    document.write( '<picture><source media="(max-width: 459.98px)" srcset="" data-srcset="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-300x150.jpg" data-width="300" data-height="150"/><img loading="lazy" class="img-fluid" src="" alt="View into deep valley" data-src="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-768x384.jpg" width="768" height="384" data-fn="lazyload"/></picture>' );
  </script>
  <noscript>
    <img loading="lazy" class="img-fluid" src="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-768x384.jpg" alt="View into deep valley" width="768" height="384"/>
  </noscript>
</figure>


<figure>
  <script>
    document.write( '<picture><source media="(orientation: portrait) and (max-width: 499.98px)" srcset="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI3NjhweCIgaGVpZ2h0PSIzODRweCIgdmlld0JveD0iMCAwIDc2OCAzODQiPjxyZWN0IGZpbGw9Im5vbmUiIHdpZHRoPSI3NjgiIGhlaWdodD0iMzg0Ii8+PC9zdmc+" data-srcset="http://localhost/wp-example/wp-content/uploads/2020/04/sergio-jara-yX9WbPbz8J8-unsplash-1200x600-1-768x384.jpg" data-width="768" data-height="384"/><source media="(max-width: 459.98px)" srcset="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI3NjhweCIgaGVpZ2h0PSIyNTZweCIgdmlld0JveD0iMCAwIDc2OCAyNTYiPjxyZWN0IGZpbGw9Im5vbmUiIHdpZHRoPSI3NjgiIGhlaWdodD0iMjU2Ii8+PC9zdmc+" data-srcset="http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-768x256.jpg" data-width="768" data-height="256"/><source media="(max-width: 767.98px)" srcset="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDI0cHgiIGhlaWdodD0iMzQxcHgiIHZpZXdCb3g9IjAgMCAxMDI0IDM0MSI+PHJlY3QgZmlsbD0ibm9uZSIgd2lkdGg9IjEwMjQiIGhlaWdodD0iMzQxIi8+PC9zdmc+" data-srcset="http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-1024x341.jpg" data-width="1024" data-height="341"/><img class="img-fluid" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTM2cHgiIGhlaWdodD0iNTEycHgiIHZpZXdCb3g9IjAgMCAxNTM2IDUxMiI+PHJlY3QgZmlsbD0ibm9uZSIgd2lkdGg9IjE1MzYiIGhlaWdodD0iNTEyIi8+PC9zdmc+" alt="Rocky island with palms" data-src="http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-1536x512.jpg" width="1536" height="512" data-fn="lazyload"/></picture>' );
  </script>
  <noscript>
    <img loading="lazy" class="img-fluid" src="http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-1536x512.jpg" alt="Rocky island with palms" width="1536" height="512"/>
  </noscript>
</figure>
*/



class LazyImg {

  public $data;

  function __construct( $data ) {
    $this->data = $data;
  }

  // default class names
  // const DEFAULT_TARGET_OPENED_CLASS = 'open';
  // const DEFAULT_TRIGGER_OPENED_CLASS = 'open';

  // config attr params
  // const CONFIG_ATTR_KEYS = array(
  //   'multi_open' => 'multipleOpen',
  //   'target_opened_class' => 'targetOpenedClass',
  //   'trigger_opened_class' => 'triggerOpenedClass',
  // );

  private function createPlaceholder( $width, $height ) {
    return base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . 'px" height="' . $height . 'px" viewBox="0 0 ' . $width . ' ' . $height . '" role="img" focusable="false"><rect fill="none" width="' . $width . '" height="' . $height . '"/></svg>' );
  }

  public function print() {

    $figure_class_name = '';
    if ( isset( $this->data[ 'figure' ] ) && isset( $this->data[ 'figure' ][ 'class_name' ] ) ) {
      $figure_class_name =  $this->data[ 'figure' ][ 'class_name' ];
    }
    
    // list
    $output = '<figure' . ( $figure_class_name ? ' class="' . $figure_class_name . '"' : '' ) . '>';
    $output .= '<script>';
    $output .= "document.write( '<picture>";


    // items
    if ( isset ( $this->data[ 'source' ] ) && sizeof( $this->data[ 'source' ] > 0 ) ) {
      foreach ( $this->data[ 'source' ] as $item ) {
        if ( isset( 
          $item[ 'url' ],
          $item[ 'width' ],
          $item[ 'height' ], 
          $item[ 'media' ] 
        ) ) {
          $output .= '<source media="' . $item[ 'media' ] . '" srcset="data:image/svg+xml;base64,' . $this->createPlaceholder( $item[ 'width' ], $item[ 'height' ] ) . '" data-srcset="' . $item[ 'url' ] . '" data-width="' . $item[ 'width' ] . '" data-height="' . $item[ 'height' ] . '"/>';
        }
      } 
    }

    // check if all img data available
    $img_data_complete = isset ( 
      $this->data[ 'img' ], 
      $this->data[ 'img' ][ 'url' ], 
      $this->data[ 'img' ][ 'alt' ], 
      $this->data[ 'img' ][ 'width' ], 
      $this->data[ 'img' ][ 'height' ] 
    );

    if ( $img_data_complete ) {
      $output .= '<img class="img-fluid' . ( isset( $this->data[ 'img' ][ 'class_name' ] ) ? ' ' . $this->data[ 'img' ][ 'class_name' ] : '' ). '" src="data:image/svg+xml;base64,' . $this->createPlaceholder( $this->data[ 'img' ][ 'width' ], $this->data[ 'img' ][ 'height' ] ) . '" alt="' . $this->data[ 'img' ][ 'alt' ] . '" data-src="' . $this->data[ 'img' ][ 'url' ] . '" width="' . $this->data[ 'img' ][ 'width' ] . '" height="' . $this->data[ 'img' ][ 'height' ] . '" data-fn="lazyload"/>';
    }

    $output .= "</picture>' );";
    $output .= '</script>';
    $output .= '<noscript>';

    if ( $img_data_complete ) {
      $output .= '<img class="img-fluid' . ( isset( $this->data[ 'img' ][ 'class_name' ] ) ? ' ' . $this->data[ 'img' ][ 'class_name' ] : '' ). '" src="' . $this->data[ 'img' ][ 'url' ] . '" alt="' . $this->data[ 'img' ][ 'alt' ] . '" width="' . $this->data[ 'img' ][ 'width' ] . '" height="' . $this->data[ 'img' ][ 'height' ] . '" loading="lazy"/>';
    }

    $output .= '</noscript>';
    $output .= '</figure>';

    echo $output;
  }

}






