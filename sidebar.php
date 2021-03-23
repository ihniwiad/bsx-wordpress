<aside class="col-sm-3 col-sm-offset-1 blog-sidebar" data-id="sidebar">
	<div class="sidebar-module sidebar-module-inset">
		<h4>About</h4>
		<p><?php the_author_meta( 'description' ); ?></p>
	</div>
	<div class="sidebar-module">
		<h4>Archives</h4>
		<ol class="list-unstyled">
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ol>
	</div>
	<div class="sidebar-module">
		<h4>Elsewhere</h4>
		<ol class="list-unstyled">
			<?php if ( get_option('github') ) { ?><li><a href="<?php echo get_option('github'); ?>">GitHub</a></li><?php } ?>
			<?php if ( get_option('twitter') ) { ?><li><a href="<?php echo get_option('twitter'); ?>">Twitter</a></li><?php } ?>
			<?php if ( get_option('facebook') ) { ?><li><a href="<?php echo get_option('facebook'); ?>">Facebook</a></li><?php } ?>
		</ol>
	</div>
</aside>
<!-- /[data-id="sidebar"] -->