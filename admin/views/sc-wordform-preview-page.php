<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<html>
	<head>
		<?php wp_head(); ?> 
	</head>
	<body <?php body_class(); ?> > 
	<?php wp_body_open(); ?>	  
		<?php block_template_part( 'header' ); ?>		
		<main>                    
			<div class="entry-content wp-block-post-content has-global-padding is-layout-constrained">						   
				<div class="wordform-preview-n-test-form-wrapper"><?php echo SFTCY_Wordform::wordform_check_preview_template_data(); ?></div>		
			</div>
		</main> 		 
		<?php // block_template_part('footer'); ?>		
	<?php wp_footer(); ?>	      	      	   
	</body>
</html>
