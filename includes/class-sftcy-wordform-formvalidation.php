<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordform_FormValidation' ) ) {
	class SFTCY_Wordform_FormValidation {
		public static $wordform_id                   = null;
		public static $wordform_captcha_session_name = '';
		public static $sc_wordform_validation_data   = array();

		public static $validation_text_message                    = '';
		public static $validation_number_message                  = '';
		public static $validation_textarea_message                = '';
		public static $validation_radio_message                   = '';
		public static $validation_checkbox_message                = '';
		public static $validation_select_message                  = '';
		public static $validation_email_message                   = '';
		public static $validation_captcha_empty_message           = '';
		public static $validation_captcha_wrong_message           = '';
		public static $validation_captcha_expire_message          = '';
		public static $validation_form_submission_success_message = '';

		/**
		 * Fetch & set validation messages
		 */
		public static function sc_wordform_set_validation_messages() {
			// check if set validation messages
			if ( isset( self::$wordform_id ) && self::$wordform_id ) {
				$fallback_messages   = SFTCY_Wordform::wordform_default_validation_messages();
				$validation_messages = SFTCY_Wordform::sc_wordform_get_validation_messages_data_by_formid( self::$wordform_id );

				// Text Validation Message
				self::$validation_text_message = isset( $validation_messages['text-msg'] ) ? sanitize_text_field( $validation_messages['text-msg'] ) : sanitize_text_field( $fallback_messages['text-msg'] );
				// Number Validation Message
				self::$validation_number_message = isset( $validation_messages['number-msg'] ) ? sanitize_text_field( $validation_messages['number-msg'] ) : sanitize_text_field( $fallback_messages['number-msg'] );
				// Textarea Validation Message
				self::$validation_textarea_message = isset( $validation_messages['textarea-msg'] ) ? sanitize_text_field( $validation_messages['textarea-msg'] ) : sanitize_text_field( $fallback_messages['textarea-msg'] );
				// Radio Validation Message
				self::$validation_radio_message = isset( $validation_messages['radio-msg'] ) ? sanitize_text_field( $validation_messages['radio-msg'] ) : sanitize_text_field( $fallback_messages['radio-msg'] );
				// Checkbox Validation Message
				self::$validation_checkbox_message = isset( $validation_messages['checkbox-msg'] ) ? sanitize_text_field( $validation_messages['checkbox-msg'] ) : sanitize_text_field( $fallback_messages['checkbox-msg'] );
				// Select Validation Message
				self::$validation_select_message = isset( $validation_messages['select-msg'] ) ? sanitize_text_field( $validation_messages['select-msg'] ) : sanitize_text_field( $fallback_messages['select-msg'] );
				// Email Validation Message
				self::$validation_email_message = isset( $validation_messages['email-msg'] ) ? sanitize_text_field( $validation_messages['email-msg'] ) : sanitize_text_field( $fallback_messages['email-msg'] );
				// Captcha-Empty Validation Message
				self::$validation_captcha_empty_message = isset( $validation_messages['captcha-empty-msg'] ) ? sanitize_text_field( $validation_messages['captcha-empty-msg'] ) : sanitize_text_field( $fallback_messages['captcha-empty-msg'] );
				// Captcha-Wrong Validation Message
				self::$validation_captcha_wrong_message = isset( $validation_messages['captcha-wrong-msg'] ) ? sanitize_text_field( $validation_messages['captcha-wrong-msg'] ) : sanitize_text_field( $fallback_messages['captcha-wrong-msg'] );
				// Captcha-Expire Validation Message
				self::$validation_captcha_expire_message = isset( $validation_messages['captcha-expire-msg'] ) ? sanitize_text_field( $validation_messages['captcha-expire-msg'] ) : sanitize_text_field( $fallback_messages['captcha-expire-msg'] );

				// Form Submission Validation Success Message
				self::$validation_form_submission_success_message = isset( $validation_messages['form-submission-success-msg'] ) ? sanitize_text_field( $validation_messages['form-submission-success-msg'] ) : sanitize_text_field( $fallback_messages['form-submission-success-msg'] );
			}
		}
		/**
		 * Text validation
		 */
		public static function text_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Text';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_text_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Text';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Text';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Number validation
		 */
		public static function number_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Number';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_number_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Number';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Number';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Textarea validation
		 */
		public static function textarea_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Textarea';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_textarea_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Textarea';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Textarea';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Radio validation
		 */
		public static function radio_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Radio';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_radio_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Radio';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Radio';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Checkbox validation
		 */
		public static function checkbox_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Checkbox';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_checkbox_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Checkbox';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Checkbox';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Select validation
		 */
		public static function select_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Select';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_select_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Select';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Select';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Email validation
		 */
		public static function email_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Email';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( $input_data['required'] == 'true' ) {
						$temp['required']         = true;
						$temp['validationMsg']    = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? self::$validation_email_message : '';
						$temp['validationStatus'] = ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ? 'error' : 'success';
					} else {
						$temp['inputType']        = 'Email';
						$temp['required']         = false;
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Email';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}

		/**
		 * Captcha validation
		 */
		public static function captcha_validation( $data = array() ) {
			if ( array_filter( $data ) ) {
				foreach ( $data as $key => $input_data ) {
					$temp              = array();
					$temp['inputType'] = 'Captcha';

					$temp['validationMsgClass'] = 'validation-msg-' . $key;
					$temp['label']              = $input_data['label'];
					if ( isset( $input_data['required'] ) && $input_data['required'] == 'true' ) {
						$temp['required'] = true;
					}

					// Retrieve server Captcha value
					$captcha_server_value = false;
					if ( isset( $input_data['captcha_session_name'] ) ) {
						$captcha_server_value                = get_transient( $input_data['captcha_session_name'] );
						self::$wordform_captcha_session_name = $input_data['captcha_session_name'];
					}

					// Captcha field empty
					if ( ! isset( $input_data['values'] ) || ! array_filter( $input_data['values'] ) ) {
						$temp['validationMsg']    = self::$validation_captcha_empty_message;
						$temp['validationStatus'] = 'error';
					}
					// Captcha expired or not set properly
					elseif ( ! $captcha_server_value ) {
						$temp['validationMsg']    = self::$validation_captcha_expire_message;
						$temp['validationStatus'] = 'error';
					}
					// Captcha entered wrong
					elseif ( isset( $input_data['values'][0] ) && str_replace( ' ', '', $input_data['values'][0] ) !== $captcha_server_value ) {
						$temp['validationMsg']    = self::$validation_captcha_wrong_message;
						$temp['validationStatus'] = 'error';
					}
					// Captcha entered correct
					elseif ( isset( $input_data['values'][0] ) && str_replace( ' ', '', $input_data['values'][0] ) == $captcha_server_value ) {
						$temp['validationMsg']    = '';
						$temp['validationStatus'] = 'success';
					}
					// something else happened
					else {
						$temp['validationMsg']    = __( 'Something went wrong with Captcha. Refresh Page.', 'wordform' );
						$temp['validationStatus'] = 'error';
					}

					self::$sc_wordform_validation_data[] = $temp;
				} // foreach
			} else {
				$temp['inputType']          = 'Captcha';
				$temp['validationMsgClass'] = 'validation-msg-' . $key;
				$temp['validationMsg']      = __( 'Input element data missing.', 'wordform' );
				$temp['validationStatus']   = 'error';

					self::$sc_wordform_validation_data[] = $temp;
			}
		}
	} // End class
}
