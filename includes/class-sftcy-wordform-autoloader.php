<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordform_Autoloader' ) ) {
	class SFTCY_Wordform_Autoloader {

		public function __construct() {
			new SFTCY_Wordform();
			new SFTCY_Wordform_Ajaxhandler();
			new SFTCY_BuildForm();
			new SFTCY_Wordform_Shortcode();
			new SFTCY_Wordform_FormValidation();
			new SFTCY_Wordform_FormSubmission();
			new SFTCY_Wordform_Captcha();
		}
	} // End Class
}
