<?php 

include 'variables.php';

?>

			<?php include 'template-parts/footer/footer.php'; ?>

			<div class="to-top-wrapper" data-fn="to-top-wrapper">
				<a class="btn btn-secondary btn-only-icon" href="#top"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sr-only"><?php echo __( 'To top', 'bsx-wordpress' ); ?></span></a>
			</div>
		
		</div>

		<?php 
			// photoswipe shadowbox template

			// if ( class_exists( 'BsxPhotoswipe001' ) ) {
			// 	$BsxPhotoswipe = new BsxPhotoswipe001;
			// 	if ( method_exists( $BsxPhotoswipe, 'printPhotoswipeShadowboxTemplate' ) ) {
			// 		$BsxPhotoswipe->printPhotoswipeShadowboxTemplate();
			// 	}
			// }

		?>
		
		<?php
			// js paths using relative path & version
			$currentVendorJsFilePath = $assetsPath . $vendorJsFileName . '?v=' . $vendorJsVersion;
			$currentScriptsJsFilePath = $assetsPath . $scriptsJsFileName . '?v=' . $scriptsJsVersion;
			if ( $isDevMode ) {
				$currentVendorJsFilePath = str_replace ( '.min', '' , $currentVendorJsFilePath );
				$currentScriptsJsFilePath = str_replace ( '.min', '' , $currentScriptsJsFilePath );
			}
		?>
		<!-- script src="<?php echo $currentVendorJsFilePath ?>" defer></script -->
		<script src="<?php echo $currentScriptsJsFilePath ?>" defer></script>

		<?php wp_footer(); ?>
		
	</body>
	
</html>