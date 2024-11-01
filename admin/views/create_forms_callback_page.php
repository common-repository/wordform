<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/pages-header-template.php';

if ( isset( $wordform_id ) && ! empty( $wordform_id ) ) {
	// Edit Created Forms
	// print_r($query_results );
	include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/create_forms_edit_page.php';
} else {
	// Create New Forms
	?>
		<div class="wordform-div-wrapper" style="padding: 5px;">	

				<!-- Drag Input Elements -->      
				<div class="wordform-draggable-elements-div-wrapper" style="padding: 15px;">
					<button class="draggable-element wordform-type-singletext button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="singletext" type="button">
					<i class="fa-sharp fa-regular fa-address-card"></i>
					<?php esc_html_e( 'Name', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-singlenumber button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="singlenumber" type="button">
					<i class="fa-sharp fa-solid fa-arrow-up-1-9"></i>
					<?php esc_html_e( 'Number', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-multitext button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="multitext" type="button">
					<i class="fa-sharp fa-solid fa-text-width"></i>
					<?php esc_html_e( 'Description', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-select button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="select" type="button">
					<i class="fa-sharp fa-solid fa-square-caret-down"></i>
					<?php esc_html_e( 'Dropdown Options', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-checkbox button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="checkbox" type="button">
					<i class="fa-sharp fa-solid fa-square-check"></i>
					<?php esc_html_e( 'Checkboxes', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-radio button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="radio" type="button">
					<i class="fa-sharp fa-regular fa-circle-dot"></i>
					<?php esc_html_e( 'Multiple Choice', 'wordform' ); ?></button>
					<button class="draggable-element wordform-type-email button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="email" type="button">
					<i class="fa-sharp fa-solid fa-at"></i>
					<?php esc_html_e( 'Email', 'wordform' ); ?></button>
					<?php if ( SFTCY_Wordform_Captcha::wordform_gd_library_available() ) { ?>
					<button class="draggable-element wordform-type-captcha button button-primary button-large wordform-drag-elements-button" data-wordform-element-name="captcha" type="button">
					<i class="fa-solid fa-shield"></i>
						<?php esc_html_e( 'Captcha', 'wordform' ); ?></button>				   				   				   
					<?php } ?>					
				</div>
				<small class="wordform-drag-elements-hints">
				<i class="fa-sharp fa-solid fa-circle-exclamation fa-beat fa-lg" style="color: #FFD43B; vertical-align: middle;"></i> 
				<?php esc_html_e( 'To add click or drag-drop element into DropZone. Rearrange by dragging elements inside DropZone, Click the element to add / update element\'s label / options.', 'wordform' ); ?></small>
				<hr/>

				<!-- Actions Update Info -->
				<p class="wordform-message-info"></p>
				
				<!-- Action Buttons -->
				<div class="wordform-form-action-buttons-wrapper">
					<button type="button" class="button button-primary button-large wordform-save-btn">						
						<i class="fa-solid fa-file-circle-check"></i>
						<?php esc_html_e( 'Save Form', 'wordform' ); ?>
					</button>

					<a type="button" class="button button-primary button-large wordform-preview-btn disabled" style="margin-left: 15px;" href="#" target="_blank">
						<span class="dashicons dashicons-welcome-view-site" style="vertical-align: text-top;"></span> <?php esc_html_e( 'Preview & Test', 'wordform' ); ?>
					</a>                                            
				</div>


			<!-- Start Form Builder Zone -->
			<div class="wordform-builder-div-wrapper">
				
				<!-- Element: FORM Name -->
				<div id="wordformElement-1" class="wordform-input-element-div-wrapper wordform-input-element-div-wrapper-form-name wordform-create-form-name-label-title" data-input-element-type="form-name" data-element-index="1" >
					<span><?php esc_html_e( 'New Form', 'wordform' ); ?></span>
				</div>
								
				<!-- Elements DropZone -->
				<span class="sc-wordform-dropzone-watermark-text"><?php esc_html_e( 'DROPZONE', 'wordform' ); ?></span>
				<div id="wordform-sortable" class="wordform-dropzone-div-wrapper"></div>

				<!--Element: Submit Button  -->
				<div id="wordformElement-2" class="wordform-input-element-div-wrapper wordform-input-element-div-wrapper-submit-button" data-input-element-type="submit-button" data-element-index="2">
					<button class="button button-primary button-large wordform-admin-create-form-submit-button"><?php esc_html_e( 'Submit', 'wordform' ); ?></button>
				</div>

		
				<input type="hidden" class="wordform-form-builder-element-number" value="2" />
				<input type="hidden" class="wordform-form-saved" value="false" />
				<input type="hidden" class="wordform-created-form-id" value="" />    
		
			</div><!-- /.wordform-builder-div-wrapper -->
			<!-- End Form Builder Zone -->

			<!-- Element Field Options - Right Sidebar -->
			<div class="wordform-builder-form-options-div-wrapper">
				<?php include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/form-name.php'; ?>
				<?php include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/submit-button.php'; ?>
			</div><!-- /.wordform-builder-form-options-div-wrapper -->

			  
		</div><!-- ./wordform-div-wrapper -->
	<?php
}
?>
