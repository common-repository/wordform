<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="<?php echo esc_attr( $element_wrapper_id ); ?>" class="wordform-checkbox-field-options-wrapper show-hide-common-class-all-options-wrapper-element" data-wrapper-element-type = "checkbox" data-element-index-val="<?php echo esc_attr( $element_index ); ?>" >
	
	<h4><?php esc_html_e( 'Field Required:', 'wordform' ); ?></h4>
	<ul class="wordform-required-ul">
		<li>
			<input class="required-checkbox" type="checkbox" /><?php esc_html_e( 'Required', 'wordform' ); ?>
			<small>( <?php esc_html_e( 'Check if Required', 'wordform' ); ?> )</small>
		</li>
	</ul>
	<hr/>

	<h4><?php esc_html_e( 'Checkbox Label:', 'wordform' ); ?></h4>
	<ul class="wordform-input-label">	
		<li>				    
			<input class="wordform-checkbox-label-name" type="text" placeholder="Checkbox Label" />
		</li>
	</ul>	
	<hr/>

	<h4><?php esc_html_e( 'Multiple Options:', 'wordform' ); ?><br/><small>(<?php esc_html_e( 'Check to show as default', 'wordform' ); ?>  )</small></h4>		
	<ul class="wordform-field-options">				
		<li> 
			<input type="checkbox" class="wordform-edit-checkbox-name" name="wordform-edit-checkbox-name" /><input class="wordform-input-checkbox-label-name" type="text" placeholder="Checkbox Text" />
			<span class="wordform-checkbox-field-remove dashicons dashicons-no" title="Remove"></span>					 
		</li> 
		<li><span class="wordform-add-checkbox-option dashicons dashicons-plus" title="Add Checkbox"></span></li> 
	</ul>

</div><!-- /.wordform-checkbox-field-options-wrapper -->
