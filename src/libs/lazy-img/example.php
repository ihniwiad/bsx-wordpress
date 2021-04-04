<?php



$img_list = array();

$img_list[ 0 ] = array(
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
    )
  )
);

$img_list[ 1 ] = array(
  'img' => array(
    'url' => 'http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-1536x512.jpg',
    'width' => 1536,
    'height' => 512,
    'alt' => 'Rocky island with palms'
  ),
  'source' => array(
    array(
      'url' => 'http://localhost/wp-example/wp-content/uploads/2020/04/sergio-jara-yX9WbPbz8J8-unsplash-1200x600-1-768x384.jpg',
      'width' => 768,
      'height' => 384,
      'media' => '(orientation: portrait) and (max-width: 499.98px)'
    ),
    array(
      'url' => 'http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-768x256.jpg',
      'width' => 768,
      'height' => 256,
      'media' => '(max-width: 459.98px)'
    ),
    array(
      'url' => 'http://localhost/wp-example/wp-content/uploads/2021/01/sergio-jara-yX9WbPbz8J8-unsplash-3000x1000-1-1024x341.jpg',
      'width' => 1024,
      'height' => 341,
      'media' => '(max-width: 767.98px)'
    )
  )
);

?>


<section>
  <h2>Lazy img</h2>

  <?php
    $lazy_img_0 = new LazyImg( $img_list[ 0 ] );
    $lazy_img_0->print();
  ?>

</section>
<section>
  <h2>Lazy img (multiple formats)</h2>

  <?php
    $lazy_img_1 = new LazyImg( $img_list[ 1 ] );
    $lazy_img_1->print();
  ?>

</section>