<?php /*


<?php if ( post_password_required() ) {
	return;
} ?>

<div class="container mt-5" id="comments" data-id="comments">

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
<?php
printf( _nx( 'One comment on "%2$s"', '%1$s comments on "%2$s"', get_comments_number(), 'comments title'),
number_format_i18n( get_comments_number() ), get_the_title() );
?>
		</h3>
		<ul class="comment-list">
<?php
wp_list_comments( array(
'short_ping'  => true,
'avatar_size' => 50,
) );
?>
		</ul>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments">
<?php _e( 'Comments are closed.' ); ?>
		</p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div>
<!-- /.container[data-id="comments"] -->

*/