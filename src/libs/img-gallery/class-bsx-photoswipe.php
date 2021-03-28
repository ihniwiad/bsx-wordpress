<?php

class Bsx_Photoswipe {

	public static function shadowbox_template_html() {
        $html = '
<!-- PHOTOSWIPE SHADOWBOX TEMPLATE -->

<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- Background of PhotoSwipe. 
         It’s a separate element as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>
    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">
        <!-- Container that holds slides. 
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don’t modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <!--  Controls are self-explanatory. Order can be changed. -->
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"><span><i class="fa fa-close" aria-hidden="true"></i><span class="sr-only">&nbsp;</span></span></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"><i class="fa fa-arrows-alt" aria-hidden="true"></i></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"><i class="fa fa-search-plus" aria-hidden="true"></i></button>
                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader- -active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <i class="fa fa-circle-o-notch" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center container text-center"></div>
            </div>
        </div>
    </div>
</div>
        ';

        return $html;
    }
    // /function

}