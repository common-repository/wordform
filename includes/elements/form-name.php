<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="wordformElementOptions-1" class="wordform-form-name-field-options-wrapper show-hide-common-class-all-options-wrapper-element" data-input-element-type="form-name" data-wrapper-element-type = "form-name" style="display: none;">		
	<ul class="wordform-required-ul">
		<li>
			<input class="wordform-form-name-display-checkbox" type="checkbox" /><?php esc_html_e( 'Hide Form Name', 'wordform' ); ?>
			<small>(<?php esc_html_e( 'Check to hide', 'wordform' ); ?>  )</small>
		</li>
	</ul>
	<hr/>

	<h4><?php esc_html_e( 'Form Name Label:', 'wordform' ); ?> </h4>
	<ul class="wordform-form-name-label">	
		<li>				    
			<input class="wordform-form-name-label-input" value="New Form" type="text" placeholder="Form Name Label" /><br/>
			<small class="wordform-form-name-label-input-hints" style="font-style: italic;"><?php esc_html_e( 'Always try to give a Form Name to understand better when embedded, Click Hide Form Name not to show at front end.', 'wordform' ); ?> </small>
		</li>
	</ul>	
	<hr/>

</div><!-- /.wordform-form-name-field-options-wrapper -->
