<?php

namespace Bsx;

require_once( 'src/libs/accordion/class-accordion.php' );

?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BSX WordPress</title>

    <link rel="preload" href="assets/css/style.min.css" as="style">
    <link href="assets/css/style.min.css" rel="stylesheet">

  </head>
  <body>



    <a class="sr-only sr-only-focusable" href="#main">SKIP TO MAIN<?php //echo __( 'Skip to main content', 'bsx-wordpress-example' ); ?></a>
  
    <div class="wrapper" id="top">
      <?php include 'template-parts/html-header.php'; ?>


      <div class="container below-navbar-content mb-5">
        <h1 data-bsx="key_1 key_5">BSX WordPress</h1>
        <p data-bsx="key_1">Please see console log.</p>
        <p class="foo bar blub bla" data-bsx="key_1">I have css classes.</p>
        <div data-bsx="key_2">Some div</div>
        <p data-bsx="key_3 key_5">Another paragraph</p>
      </div>


      <!-- section class="mb-5">
        <div class="container">
          <h2>Element positioned inside</h2>

          <div class="row">
            <div class="col">
              <div class="outer-elem" data-bsx="outer">
                OUTER
                <div class="inner-elem" data-bsx-tg="inner">
                  INNER
                </div>
              </div>
            </div>
            <div class="col">
              <div class="outer-elem" data-bsx="outer">
                OUTER
                <div class="inner-elem inner-elem-1" data-bsx-tg="inner">
                  INNER
                </div>
              </div>
            </div>
          </div>
        </div>
      </section -->
      <?php
        // example positioned inside
        include 'src/test/is-positioned-inside/example.php';
      ?>


      <div class="container">
        <?php
          // list of example accordions
          include 'src/libs/accordion/example.php';
        ?>
      </div>


      <section class="of-hidden">
        <div class="container">
          <h2 class="opacity-0" data-bsx="ape" data-ape-conf="{ appearedClass: 'move-in-right', nonAppearedClass: 'opacity-0', repeat: true }">Appear effects</h2>
          <p class="opacity-0" data-bsx="ape" data-ape-conf="{ appearedClass: 'move-in-left', nonAppearedClass: 'opacity-0', repeat: true }">
            Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc.
          </p>
          <p data-bsx="ape" data-ape-conf="{ addClassDelay: 400, appearedClass: 'test-appeared red', repeat: true }">
            Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.
          </p>
          <p data-bsx="ape" data-ape-conf="{ addClassDelay: 400, appearedClass: 'test-appeared blue' }">
            In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue.
          </p>
          <p data-bsx="ape" data-ape-conf="{ addClassDelay: 400, appearedClass: 'test-appeared green' }">
            Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus.
          </p>
        </div>
      </section>

    </div>
    <!-- /.wrapper -->

    <script src="./assets/js/scripts.js"></script>
  </body>
</html>