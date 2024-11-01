<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @author Softcoy
 * @package Captcha Generation
 * @version 1.0.0
 * @copyright 2024 Softcoy
 */
if ( ! class_exists( 'SFTCY_Wordform_Captcha' ) ) {
	class SFTCY_Wordform_Captcha {
		public static $output                   = array();
		public static $captcha_image_width      = 200;
		public static $captcha_image_height     = 100;
		public static $captcha_generated_number = 0;

		/**
		 * Create Captcha Image
		 * base64 encoded image object
		 * return - array
		 */
		public static function wordform_create_captcha_image( $template = 'all', $length = 4 ) {
			self::$output = array();
			if ( self::wordform_gd_library_available() ) {
				$random_captcha_number          = self::wordform_generate_random_captcha_number( $length );
				self::$captcha_generated_number = $random_captcha_number;
				if ( $template === 'all' ) {
					$templates                 = self::wordform_captcha_background_template();
					$background_template_image = $templates[ array_rand( $templates ) ];
				} else {
					$background_template_image = $template;
				}

				if ( file_exists( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/captcha-templates-images/' . $background_template_image ) ) {
					$captcha = imagecreatefromjpeg( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/captcha-templates-images/' . $background_template_image );
					if ( $captcha ) {
						$color = imagecolorallocate( $captcha, 0, 0, 0 );
						$font  = SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/fonts/' . self::wordform_create_form_font_name();
						imagettftext( $captcha, 20, 0, wp_rand( 20, 50 ), wp_rand( 20, 50 ), $color, $font, $random_captcha_number );
						ob_start();
						imagepng( $captcha );
						$image_contents = base64_encode( ob_get_contents() );
						ob_end_clean();
						self::$output['captchaValue']   = str_replace( ' ', '', $random_captcha_number ); // Remove spaces
						self::$output['imageBase64Src'] = 'data:image/png;base64,' . $image_contents;
						self::$output['status']         = 'success';
						imagedestroy( $captcha );
					} else {
						self::$output['status']    = 'fail';
						self::$output['errorInfo'] = error_get_last();
						self::$output['comment']   = 'imagecreatefromjpeg() failed to create image.';
					}
				} else {
					self::$output['status']  = 'fail';
					self::$output['comment'] = 'Image template not found.';
				}
			} else {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'GD extension not loaded.';
			}
			return self::$output;
		}

		/**
		 * Generate Captcha random number
		 *
		 * @param - length - Random number length in digits
		 * return - generated captcha number
		 */
		public static function wordform_generate_random_captcha_number( $length = 4 ) {
			$random_numbers_arr = array();
			for ( $i = 0; $i < $length; $i++ ) {
				$random_numbers_arr[] = wp_rand( wp_rand( 0, 9 ), wp_rand( 0, 9 ) );
			}
			return implode( ' ', $random_numbers_arr );
		}

		/**
		 * Check GD extension is available
		 * return - boolean
		 */
		public static function wordform_gd_library_available() {
			if ( extension_loaded( 'gd' ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Select captcha backgorung template
		 * default directory: admin/views/captcha-templates-images/ of plugin
		 * return - array - all template image names - background template image names
		 */
		public static function wordform_captcha_background_template() {
			$templates = array();
			foreach ( glob( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/captcha-templates-images/*.*' ) as $file ) {
				$templates[] = trim( basename( $file ) );
			}
			/*
			$templates = [ 'captcha-background-template-1-150x70.jpg',
							'captcha-background-template-2-150x70.jpg',
							'captcha-background-template-3-150x70.jpg',
							'captcha-background-template-4-150x70.jpg'
						];*/
			return $templates;
		}

		/**
		 * Captcha session expiration
		 * return - int - seconds
		 */
		public static function wordform_captcha_session_expired_duration() {
			return 60 * 30; // 30 minutes
		}

		/**
		 * Admin create form
		 * Captcha default template
		 * return - string - default image url
		 */
		public static function wordform_create_form_default_captcha_template_url() {
			if ( file_exists( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/captcha-templates-images/captcha-background-template-1-150x70.jpg' ) ) {
				return plugin_dir_url( __DIR__ ) . 'admin/views/captcha-templates-images/captcha-background-template-1-150x70.jpg';
			}
			return '';
		}

		/**
		 * Admin create form
		 * Captcha default Font
		 * return - string - font name
		 */
		public static function wordform_create_form_font_name() {
			if ( file_exists( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/fonts/BlackOpsOne-Regular.ttf' ) ) {
				return 'BlackOpsOne-Regular.ttf';
			}
			return '';
		}
	} // End Class
}
