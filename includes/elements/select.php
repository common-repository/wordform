<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="<?php echo esc_attr( $element_wrapper_id ); ?>" class="wordform-select-field-options-wrapper show-hide-common-class-all-options-wrapper-element" data-wrapper-element-type = "select" data-element-index-val="<?php echo esc_attr( $element_index ); ?>" >	

	<h4><?php esc_html_e( 'Field Required:', 'wordform' ); ?></h4>
	<ul class="wordform-required-ul">
		<li>
			<input class="required-checkbox" type="checkbox" /><?php esc_html_e( 'Required', 'wordform' ); ?>
			<small>( <?php esc_html_e( 'Check if Required', 'wordform' ); ?> )</small>
		</li>
	</ul>
	<hr/>

	<h4><?php esc_html_e( 'Dropdown Label:', 'wordform' ); ?></h4>
	<ul class="wordform-input-label">	
		<li>				    
			<input class="wordform-select-label-name" type="text" placeholder="Dropdown Label" />
		</li>
	</ul>	
	<hr/>

	<h4><?php esc_html_e( 'Select Options:', 'wordform' ); ?><br/><small>(<?php esc_html_e( 'Check to show as default', 'wordform' ); ?> )</small></h4>		
	
	<ul class="wordform-field-options">				
		<li> 
			<input type="radio" class="wordform-edit-select-name" name="wordform-edit-select-name-<?php echo esc_attr( $element_index ); ?>" /><input class="wordform-input-select-label-name" type="text" placeholder="Select Text" />
			<span class="wordform-select-field-remove dashicons dashicons-no" title="Remove"></span>					 
		</li> 
		<li><span class="wordform-add-select-option dashicons dashicons-plus" title="Add Select Text"></span></li> 
	</ul>

</div><!-- /.wordform-select-field-options-wrapper -->

