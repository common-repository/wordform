<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="<?php echo esc_attr( $element_wrapper_id ); ?>" class="wordform-captcha-field-options-wrapper show-hide-common-class-all-options-wrapper-element" data-wrapper-element-type = "captcha" data-element-index-val="<?php echo esc_attr( $element_index ); ?>" >	

	<h4><?php esc_html_e( 'Captcha:', 'wordform' ); ?></h4>
	<ul class="wordform-input-label">	
		<li>				    
			<input class="wordform-captcha-label-name" type="text" placeholder="Captcha" />
		</li>
	</ul>	
	<hr/>
	
	<?php
	$captcha_templates     = (array) SFTCY_Wordform_Captcha::wordform_captcha_background_template();
	$template_label_id_all = 'templateLabel-' . count( $captcha_templates ) + 1 . '-' . $element_index;

	$default_template = 'captcha-background-template-1-150x70.jpg';
	?>
	<h4><?php esc_html_e( 'Captcha Template:', 'wordform' ); ?></h4>
	<ul class="wordform-field-options">
		<li>
			<label for="<?php echo esc_attr( $template_label_id_all ); ?>" class="wordform-captcha-template-selection-label-wrapper">
				<input type="radio" id="<?php echo esc_attr( $template_label_id_all ); ?>" name="wordform-captcha-template-selection-<?php echo esc_attr( $element_index ); ?>" class="wordform-captcha-template-selection wordform-captcha-template-radio" value="all" /> 
				<strong><?php esc_html_e( 'All', 'wordform' ); ?></strong>				
				<small>(<?php esc_html_e( 'Template will be selected randomly.', 'wordform' ); ?>)</small>
			</label>
		</li>
	<?php
	foreach ( $captcha_templates  as $key => $captcha_background_template_image_name ) {
		$template_label_id  = 'templateLabel-' . $key . '-' . $element_index;
		$template_image_url = plugin_dir_url( __DIR__ ) . '../admin/views/captcha-templates-images/' . $captcha_background_template_image_name;
		?>
		<li>
			<label for="<?php echo esc_attr( $template_label_id ); ?>" class="wordform-captcha-template-selection-label-wrapper">	        
			<input id="<?php echo esc_attr( $template_label_id ); ?>" name="wordform-captcha-template-selection-<?php echo esc_attr( $element_index ); ?>" class="wordform-captcha-template-image-selection wordform-captcha-template-radio" value="<?php echo esc_attr( $captcha_background_template_image_name ); ?>" type="radio" <?php checked( $default_template, $captcha_background_template_image_name, true ); ?>   />					    
			<img class="wordform-captcha-template-image" src="<?php echo esc_url( $template_image_url ); ?>" alt="Captcha Image" />			
			</label>          
		</li>
	<?php } ?>	
	</ul>
	<hr/>
	

</div><!-- /.wordform-captcha-field-options-wrapper -->
