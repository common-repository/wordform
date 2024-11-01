<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="<?php echo esc_attr( $element_wrapper_id ); ?>" class="wordform-number-field-options-wrapper show-hide-common-class-all-options-wrapper-element" data-wrapper-element-type = "number" data-element-index-val="<?php echo esc_attr( $element_index ); ?>" >	

	<h4><?php esc_html_e( 'Field Required:', 'wordform' ); ?></h4>
	<ul class="wordform-required-ul">
		<li>
			<input class="required-checkbox" type="checkbox" /><?php esc_html_e( 'Required', 'wordform' ); ?>
			<small>( <?php esc_html_e( 'Check if Required', 'wordform' ); ?> )</small>
		</li>
	</ul>
	<hr/>

	<h4><?php esc_html_e( 'Number Label:', 'wordform' ); ?></h4>
	<ul class="wordform-input-label">	
		<li>				    
			<input class="wordform-number-label-name" type="text" placeholder="Number Label" />
		</li>
	</ul>	
	<hr/>


</div><!-- /.wordform-number-field-options-wrapper -->
