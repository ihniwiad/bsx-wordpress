<?php 

// get variables from functions.php
global $isDevMode;
global $assetsPath;
global $scriptsJsFileName;
global $scriptsJsVersion;
global $logoPath;


?>

			<?php include 'template-parts/footer/footer.php'; ?>

			<div class="to-top-wrapper" data-fn="to-top-wrapper">
				<a class="btn btn-secondary btn-only-icon" href="#top"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sr-only"><?php echo __( 'To top', 'bsx-wordpress' ); ?></span></a>
			</div>
		
		</div>

		<?php 

			include 'src/libs/data-processing-consent/example.php';

			// photoswipe shadowbox template

			if ( class_exists( 'Bsx_Photoswipe' ) &&  method_exists( Bsx_Photoswipe, 'shadowbox_template_html' )) {
				echo Bsx_Photoswipe::shadowbox_template_html();
			}

		?>
		
		<?php
			// js paths using relative path & version
			// $currentVendorJsFilePath = $assetsPath . $vendorJsFileName . '?v=' . $vendorJsVersion;
			$currentScriptsJsFilePath = $assetsPath . $scriptsJsFileName . '?v=' . $scriptsJsVersion;
			if ( $isDevMode ) {
				// $currentVendorJsFilePath = str_replace ( '.min', '' , $currentVendorJsFilePath );
				$currentScriptsJsFilePath = str_replace ( '.min', '' , $currentScriptsJsFilePath );
			}
		?>
		<!-- script src="<?php echo $currentVendorJsFilePath ?>" defer></script -->
		<script src="<?php echo $currentScriptsJsFilePath ?>" defer></script>

		<?php wp_footer(); ?>
		
	</body>
	
</html>