<?php 

// base64_encode ( string $string ) : string

/*
<figure class="wp-block-bsx-blocks-lazy-img">
  <script>
    document.write( '<picture><source media="(max-width: 459.98px)" srcset="" data-srcset="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-300x150.jpg" data-width="300" data-height="150"/><img loading="lazy" class="img-fluid" src="" alt="View into deep valley" data-src="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-768x384.jpg" width="768" height="384" data-fn="lazyload"/></picture>' );
  </script>
  <noscript>
    <img loading="lazy" class="img-fluid" src="https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-768x384.jpg" alt="View into deep valley" width="768" height="384"/>
  </noscript>
</figure>
*/

$img_list = array(
  'img' => array(
    'url' => 'https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-768x384.jpg',
    'width' => 768,
    'height' => 384,
    'alt' => 'View into deep valley'
  ),
  'source' => array(
    array(
      'url' => 'https://wp-example.sandbox.matthiasbroecker.de/wp-content/uploads/2020/04/ales-krivec-N-aTikX-b00-unsplash-1200x600-1-300x150.jpg',
      'width' => 300,
      'height' => 150,
      'media' => '(max-width: 459.98px)'
    ),
  )
);





