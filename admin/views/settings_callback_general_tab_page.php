<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$general_tab_options = SFTCY_Wordform::sc_wordform_get_settings_general_tab_info();
?>
<div class="sc-wordform-setting-page-general-tab-table-wrapper">
	<form enctype="multipart/form-data" method="post" id="scWordformSettingsGeneralTabForm">
		<table style="border-spacing: 25px;">
			<tbody>
				<!-- Form width -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Form Width', 'wordform' ); ?>(<small>%</small>)</h4>
					</td>
					<td>   	
						<input type="text" name="sc-wordform-form-width" class="sc-wordform-form-width" value="<?php echo esc_attr( $general_tab_options['wordform_form_width'] ); ?>" readonly />				    
						<p class="sc-wordform-form-width-slider"></p>
						<small><?php esc_html_e( 'Form width size may vary based on different themes. Form full width (100%) should work best with all kind of devices, if not you can change the width what does best fit with your theme.', 'wordform' ); ?></small>
					</td>   				
				</tr>

				<!-- On Form Submission : Send Email -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'On Form Submission', 'wordform' ); ?></h4>   					
					</td>
					<td>   	
						<input type="checkbox" id="scWordformFormSubmissionSendEmail" class="sc-wordform-form-submission-send-email" name="sc-wordform-form-submission-send-email" <?php checked( 'yes', $general_tab_options['send_email_on_form_submission'] ); ?> >
						<label for="#scWordformFormSubmissionSendEmail"><?php esc_html_e( 'Send Email', 'wordform' ); ?></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;
						<small><?php esc_html_e( 'If checked then each Form submission by users, E-mail will be sent to your email. ', 'wordform' ) . sanitize_email( get_option( 'admin_email' ) ) . '. '; ?></small>
						<small><?php esc_html_e( 'Your Mail setting should work properly to receive email on each form submission.', 'wordform' ); ?></small>
					</td>   				
				</tr>
				
				<!-- On Form Submission : Show / Hide Form -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'On Form Submission', 'wordform' ); ?></h4>   					
					</td>
					<td>   	
						<input type="checkbox" id="scWordformFormSubmissionHideForm" class="sc-wordform-form-submission-hide-form" name="sc-wordform-form-submission-hide-form" <?php checked( 'yes', $general_tab_options['hide_form_on_form_submission'] ); ?> >
						<label for="#scWordformFormSubmissionHideForm"><?php esc_html_e( 'Hide Form', 'wordform' ); ?></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;
						<small><?php esc_html_e( 'If checked then on submission, Form will disappear to users. Requires refresh form page if already opened.', 'wordform' ); ?></small>
					</td>   				
				</tr>
				
				
				<!-- Submit Button : Background Color -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Form Submit Button Color', 'wordform' ); ?></h4>   					
					</td>
					<td>   	
						<input type="text" value="<?php echo esc_attr( $general_tab_options['submit_button_background_color'] ); ?>" class="sc-wordform-form-submit-button-background-color" name="sc-wordform-form-submit-button-background-color" >						
					</td>   				
				</tr>
				
				<!-- Submit Button : Background Color (Hover) -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Form Submit Button Color(Hover)', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<input type="text" value="<?php echo esc_attr( $general_tab_options['submit_button_background_hover_color'] ); ?>" class="sc-wordform-form-submit-button-background-hover-color" name="sc-wordform-form-submit-button-background-hover-color">	
					</td>
						
				</tr>
				
				<!-- Submit Button : Font-Color -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Submit Button Font Color', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<input type="text" value="<?php echo esc_attr( $general_tab_options['submit_button_font_color'] ); ?>" class="sc-wordform-form-submit-button-font-color" name="sc-wordform-form-submit-button-font-color">	
					</td>
						
				</tr>
				
				<!-- Submit Button : Font-Size -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Submit Button Text Size', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<select class="sc-wordform-form-submit-button-text-size" name="sc-wordform-form-submit-button-text-size">
							<?php
							for ( $i = 12; $i <= 40; $i += 2 ) {
								if ( $i == $general_tab_options['submit_button_font_size'] ) {
									echo '<option value="' . esc_attr( $i ) . '" selected>' . esc_html( $i ) . 'px</option>';
								} else {
									echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . 'px</option>';
								}
							}
							?>
														
						</select>						
					</td>
						
				</tr>
				
				<!-- Submit Button : Padding(Top-Bottom) -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Submit Button Padding(Top-Bottom)', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<select class="sc-wordform-form-submit-button-padding-top-bottom" name="sc-wordform-form-submit-button-padding-top-bottom">
							<?php
							for ( $i = 2; $i <= 30; $i++ ) {
								if ( $i == $general_tab_options['submit_button_padding_top_bottom'] ) {
									echo '<option value="' . esc_attr( $i ) . '" selected>' . esc_html( $i ) . 'px</option>';
								} else {
									echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . 'px</option>';
								}
							}
							?>
														
						</select>						
					</td>
						
				</tr>
				
				<!-- Submit Button : Padding(Left-Right) -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Submit Button Padding(Left-Right)', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<select class="sc-wordform-form-submit-button-padding-left-right" name="sc-wordform-form-submit-button-padding-left-right">
							<?php
							for ( $i = 2; $i <= 30; $i++ ) {
								if ( $i == $general_tab_options['submit_button_padding_left_right'] ) {
									echo '<option value="' . esc_attr( $i ) . '" selected>' . esc_html( $i ) . 'px</option>';
								} else {
									echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . 'px</option>';
								}
							}
							?>
														
						</select>						
					</td>
						
				</tr>
				
				<!-- Submit Button : Font-Weight -->
				<tr>
					<td>
						<h4><?php esc_html_e( 'Submit Button Font Weight', 'wordform' ); ?></h4>   					
					</td>
					<td>
						<?php
						$font_weight_normal = '';
						$font_weight_bold   = '';
						if ( $general_tab_options['submit_button_font_weight'] == 'normal' ) {
								$font_weight_normal = ' checked';
						} elseif ( $general_tab_options['submit_button_font_weight'] == 'bold' ) {
							$font_weight_bold = ' checked';
						}
						?>
						<input type="radio" name="sc-wordform-form-submit-button-font-weight" value="normal" <?php echo esc_attr( $font_weight_normal ); ?> /> <?php esc_html_e( 'Normal', 'wordform' ); ?>
						&nbsp;&nbsp;&nbsp;
						<input type="radio" name="sc-wordform-form-submit-button-font-weight" value="bold" <?php echo esc_attr( $font_weight_bold ); ?>/> <?php esc_html_e( 'Bold', 'wordform' ); ?>
					</td>
						
				</tr>
				
				

				<tr>
					<td>
						<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Save', 'wordform' ); ?></button>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<button type="button" class="button button-primary button-large sc-wordform-settings-general-tab-reset"><?php esc_html_e( 'Reset', 'wordform' ); ?></button>
					</td>
					<td class="sc-wordform-general-tab-update-info"></td>
				</tr>


			</tbody>
		</table>  
	</form>
</div>
