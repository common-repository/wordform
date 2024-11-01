<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordform_Shortcode' ) ) {
	class SFTCY_Wordform_Shortcode {

		public static $initiated = false;

		public function __construct() {
			if ( ! self::$initiated ) {
				self::initiate_hooks();
			}
		}

		/**
		 * Init hook
		 */
		public static function initiate_hooks() {
			add_action( 'init', array( __CLASS__, 'sc_wordform_add_shortcode' ) );

			self::$initiated = true;
		}

		/**
		 * Add shortcode
		 */
		public static function sc_wordform_add_shortcode() {
			add_shortcode( 'wordform', array( __CLASS__, 'sc_wordform_shortcode_built_form_callback' ) );
		}

		/**
		 *  Create form
		 *  [wordform wid="wordform-13"]
		 *  return html form data
		 */
		public static function sc_wordform_shortcode_built_form_callback( $atts, $content, $tag ) {
			$form_id         = isset( $atts['wid'] ) && ! empty( $atts['wid'] ) ? sanitize_text_field( $atts['wid'] ) : '';
			$built_form_html = '';
			if ( $form_id && ! empty( $form_id ) ) {
				$built_form_html = SFTCY_BuildForm::wordform_built_to_display( $form_id );
			}
			return $built_form_html;
		}
	} // End class
}
