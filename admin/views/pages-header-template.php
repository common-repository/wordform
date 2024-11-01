<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!-- WordForm - ICON  -->
<div class="wrap">
	<div class="wordform-image-icon-wrapper-div">
		<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '/images/wordform-icon-box-shape-50x50.png' ); ?>" alt="WordForm" />
		<!--<span class="dashicons dashicons-forms wordform-main-dashicon" style="font-size: 50px; width: 50px; height: 50px;"></span>-->
		<span class="wordform-all-pages-header-label-name"><?php esc_html_e('WordForm', 'wordform');?></span>
	</div>	
</div>
