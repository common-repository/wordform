<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/pages-header-template.php';
$nonce = SFTCY_Wordform::sc_wordform_get_nonce();
?>
<div id="wordformUsersSubmissionWrapperTable" style="display: none;">
	<h3><i class="dashicons dashicons-editor-table dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'Users Form Submission Data', 'wordform' ); ?></h3>
	<table id="wordformUsersSubmissionDataTable" class="display hover stripe">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Form Name', 'wordform' ); ?></th>				
				<th><?php esc_html_e( 'Submission Data', 'wordform' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wordform' ); ?></th>				
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>						
</div><!-- #wordformUsersSubmissionWrapperTable -->

