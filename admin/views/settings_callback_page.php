<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/pages-header-template.php';
?>
<div class="wrap" style="margin: 1%;">
	<div id="tabs" class="sc-wordform-tabs-wrapper-div" style="display: none;">
			<ul>          
			<li><a href="#scWordFormGeneral" style="font-weight: bold;"><i class="dashicons dashicons-admin-settings dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'General', 'wordform' ); ?></a></li>			
			<li><a href="#scWordFormValidation" style="font-weight: bold;"><i class="dashicons dashicons-admin-settings dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'Validation', 'wordform' ); ?></a></li>
			</ul>
				   
			<div id="scWordFormGeneral">
				<h3><i class="dashicons dashicons-admin-settings dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'General Settings', 'wordform' ); ?></h3>
				<?php require_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/settings_callback_general_tab_page.php'; ?>
			</div><!-- #scWordFormGeneral -->
		  
			<div id="scWordFormValidation">
				<h3><i class="dashicons dashicons-admin-settings dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'Validation Message Settings', 'wordform' ); ?></h3>
				<?php require_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/settings_callback_validation_tab_page.php'; ?>
			</div><!-- #scWordFormValidation -->                
	</div> <!-- /.tabs -->
</div><!-- /.wrap -->
