<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$fallback_messages = SFTCY_Wordform::wordform_default_validation_messages();
?>
<!-- Input field validation messages  -->
<tr>
	<td colspan="2">
		<h4><span class="dashicons dashicons-marker"></span> <?php esc_html_e( 'Input Field Validation Messages', 'wordform' ); ?></h4>   					
	</td>
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-regular fa-address-card"></i>
			<?php esc_html_e( 'Name', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['text-msg'] ) ? esc_attr( $validation_messages['text-msg'] ) : esc_attr( $fallback_messages['text-msg'] ); ?>" name="text-msg" class="sc-wordform-validation-text-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-solid fa-arrow-up-1-9"></i>
			<?php esc_html_e( 'Number', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['number-msg'] ) ? esc_attr( $validation_messages['number-msg'] ) : esc_attr( $fallback_messages['number-msg'] ); ?>" name="number-msg" class="sc-wordform-validation-number-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-solid fa-text-width"></i>
			<?php esc_html_e( 'Description', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['textarea-msg'] ) ? esc_attr( $validation_messages['textarea-msg'] ) : esc_attr( $fallback_messages['textarea-msg'] ); ?>" name="textarea-msg" class="sc-wordform-validation-textarea-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-solid fa-square-caret-down"></i>
			<?php esc_html_e( 'Dropdown options', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['select-msg'] ) ? esc_attr( $validation_messages['select-msg'] ) : esc_attr( $fallback_messages['select-msg'] ); ?>" name="select-msg" class="sc-wordform-validation-select-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-solid fa-square-check"></i>
			<?php esc_html_e( 'Checkboxes', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['checkbox-msg'] ) ? esc_attr( $validation_messages['checkbox-msg'] ) : esc_attr( $fallback_messages['checkbox-msg'] ); ?>" name="checkbox-msg" class="sc-wordform-validation-checkbox-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-regular fa-circle-dot"></i>
			<?php esc_html_e( 'Multiple Choice', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['radio-msg'] ) ? esc_attr( $validation_messages['radio-msg'] ) : esc_attr( $fallback_messages['radio-msg'] ); ?>" name="radio-msg" class="sc-wordform-validation-radio-msg" />				
	</td>   				
</tr>


<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-sharp fa-solid fa-at"></i>
			<?php esc_html_e( 'Email', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['email-msg'] ) ? esc_attr( $validation_messages['email-msg'] ) : esc_attr( $fallback_messages['email-msg'] ); ?>" name="email-msg" class="sc-wordform-validation-email-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-solid fa-shield"></i>
			<?php esc_html_e( 'Captcha[Empty]', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['captcha-empty-msg'] ) ? esc_attr( $validation_messages['captcha-empty-msg'] ) : esc_attr( $fallback_messages['captcha-empty-msg'] ); ?>" name="captcha-empty-msg" class="sc-wordform-validation-captcha-empty-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-solid fa-shield"></i>
			<?php esc_html_e( 'Captcha[Wrong]', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['captcha-wrong-msg'] ) ? esc_attr( $validation_messages['captcha-wrong-msg'] ) : esc_attr( $fallback_messages['captcha-wrong-msg'] ); ?>" name="captcha-wrong-msg" class="sc-wordform-validation-captcha-wrong-msg" />				
	</td>   				
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-solid fa-shield"></i>
			<?php esc_html_e( 'Captcha[Expire]', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['captcha-expire-msg'] ) ? esc_attr( $validation_messages['captcha-expire-msg'] ) : esc_attr( $fallback_messages['captcha-expire-msg'] ); ?>" name="captcha-expire-msg" class="sc-wordform-validation-captcha-expire-msg" />				
	</td>   				
</tr>



<tr>
	<td colspan="2">
		<hr/>
	</td>
</tr>


<!-- Form submission Messages -->
<tr>
	<td colspan="2">
		<h4><span class="dashicons dashicons-marker"></span> <?php esc_html_e( 'Form submission Messages', 'wordform' ); ?></h4>   					
	</td>
</tr>

<tr>
	<td class="sc-wordform-settings-validation-tab-label-td">
		<h4>
			<i class="fa-solid fa-check-double"></i>
			<?php esc_html_e( 'Form Submission Success', 'wordform' ); ?>
		</h4>   					
	</td>
	<td>   	
		<input type="text" value="<?php echo isset( $validation_messages['form-submission-success-msg'] ) ? esc_attr( $validation_messages['form-submission-success-msg'] ) : esc_attr( $fallback_messages['form-submission-success-msg'] ); ?>" name="form-submission-success-msg" class="sc-wordform-form-submission-success-msg" />				
	</td>   				
</tr>
