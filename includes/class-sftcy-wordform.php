<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordform' ) ) {
	class SFTCY_Wordform {
		private static $initiated                          = false;
		public static $sc_wordform_tbl                     = 'scwordform_created_forms';
		public static $sc_wordform_submission_tbl          = 'scwordform_submisson_forms_data';
		public static $sc_wordform_validation_messages_tbl = 'scwordform_validation_messages_data';
		public static $wordform_custom_block_name          = 'wordform-block/wordform';
		public static $preview_form_data                   = null;

		public function __construct() {
			if ( ! self::$initiated ) {
				self::initiate_hooks();
			}
		}

		/**
		 * Init Hooks
		 */
		private static function initiate_hooks() {
			add_action( 'init', array( __CLASS__, 'sc_wordform_create_block_init' ) );
			add_action( 'admin_menu', array( __CLASS__, 'add_scwordform_submenus' ) );
			// add_action( 'current_screen', [ __CLASS__, 'wordform_current_screen_actions' ] );
			add_filter( 'the_content', array( __CLASS__, 'wordform_post_page_rendering_with_form_data' ) );

			add_action( 'admin_notices', array( __CLASS__, 'scwordform_admin_notices' ) );
			add_action( 'plugins_loaded', array( __CLASS__, 'scwordform_load_textdomain' ) );
			// add_filter( 'plugin_row_meta',     array( __CLASS__, 'scwordform_row_link'), 10, 2 );
			add_filter( 'template_include', array( __CLASS__, 'sc_wordform_check_preview_page' ), 10, 1 );

			self::$initiated = true;
		}

		/**
		 * Activate required dependency
		 */
		public static function activate() {
			self::check_preactivation_requirements();
			self::sc_wordform_create_block_init();
			self::sc_wordform_db_tables_install();
			flush_rewrite_rules( true );
		}

		/**
		 * Check pre-activate requirements
		 */
		public static function check_preactivation_requirements() {
			if ( version_compare( PHP_VERSION, SFTCY_WORDFORM_MINIMUM_PHP_VERSION, '<' ) ) {
				wp_die( esc_html( 'Minimum PHP Version required: ' ) . SFTCY_WORDFORM_MINIMUM_PHP_VERSION );
			}
			global $wp_version;
			if ( version_compare( $wp_version, SFTCY_WORDFORM_MINIMUM_WP_VERSION, '<' ) ) {
				wp_die( esc_html( 'Minimum WordPress Version required: ' ) . SFTCY_WORDFORM_MINIMUM_WP_VERSION );
			}
		}

		public static function scwordform_load_textdomain() {
			load_plugin_textdomain( 'wordform', false, SFTCY_WORDFORM_PLUGIN_DIR . 'languages/' );
		}

		public static function sc_wordform_create_block_init() {
			register_block_type( SFTCY_WORDFORM_PLUGIN_DIR . 'admin/block/build', array() );
		}

		/**
		 * Get alll created from list
		 * display as select dropdown
		 * Block editor select form name dropdown
		 * return - array - [ ID, formID, formName ]
		 */
		public static function sc_wordform_get_all_forms_for_block_dynamic_callback() {
			global $wpdb;
			$table         = $wpdb->prefix . self::$sc_wordform_tbl;
			$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT id,form_id,form_name FROM %i ORDER BY id DESC', $table ), ARRAY_A );

			$options = array();
			foreach ( $query_results as $data ) {
				$temp             = array();
				$temp['formName'] = sanitize_text_field( $data['form_name'] );
				$temp['formID']   = sanitize_text_field( $data['form_id'] );
				$temp['ID']       = sanitize_text_field( $data['id'] );
				$options[]        = $temp;
			}
			return $options;
		}

		/**
		 * Create Tables
		 *
		 * @since 1.0.0
		 */
		public static function sc_wordform_db_tables_install() {
			global $wpdb;
			$wordform_created_form_table_name        = $wpdb->prefix . self::$sc_wordform_tbl;
			$wordform_submission_form_table_name     = $wpdb->prefix . self::$sc_wordform_submission_tbl;
			$wordform_validation_messages_table_name = $wpdb->prefix . self::$sc_wordform_validation_messages_tbl;
			$charset_collate                         = $wpdb->get_charset_collate();

			// wordform created forms Table
			$wordform_created_forms_tbl_sql = "CREATE TABLE $wordform_created_form_table_name (
			id         				INT(11) NOT NULL AUTO_INCREMENT,	
			form_name  				CHAR(100) DEFAULT '' NOT NULL,
			form_id    				CHAR(100) DEFAULT '' NOT NULL,
			form       				TEXT DEFAULT NULL,			
			builtform_html_data  	LONGTEXT DEFAULT NULL,			
			
			created_at 				DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,						
			updated_at 				DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

			// wordform submission forms data Table
			$wordform_submission_forms_tbl_sql = "CREATE TABLE $wordform_submission_form_table_name (
			id         				INT(11) NOT NULL AUTO_INCREMENT,												
			form_id    		 		CHAR(100) DEFAULT '' NOT NULL,
			submission_data  		TEXT DEFAULT NULL,
			submission_user_data  	TEXT DEFAULT NULL,
			created_at 				DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,						
			updated_at 				DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

			// wordform validation messages data Table
			$wordform_validation_messages_tbl_sql = "CREATE TABLE $wordform_validation_messages_table_name (
			id         				INT(11) NOT NULL AUTO_INCREMENT,												
			form_id    		 		CHAR(100) DEFAULT '' NOT NULL,
			validation_messages  	TEXT DEFAULT NULL,			
			created_at 				DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,						
			updated_at 				DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			// dbDelta( $sql );
			maybe_create_table( $wordform_created_form_table_name, $wordform_created_forms_tbl_sql );
			maybe_create_table( $wordform_submission_form_table_name, $wordform_submission_forms_tbl_sql );
			maybe_create_table( $wordform_validation_messages_table_name, $wordform_validation_messages_tbl_sql );
		}

		/**
		 * filter hook
		 * Created form display - preview & test
		 * return - template path
		 */
		public static function sc_wordform_check_preview_page( $template ) {
			if ( isset( $_GET['sc-wordform-id'] ) && ! empty( $_GET['sc-wordform-id'] ) ) {
				// global $created_form_escaped;
				// global $sc_wordform_id;
				$form_id = sanitize_text_field( $_GET['sc-wordform-id'] );
				// $sc_wordform_id = $form_id;
				// $created_form_escaped = SFTCY_BuildForm::wordform_built_to_display( $form_id );
				self::$preview_form_data = SFTCY_BuildForm::wordform_built_to_display( $form_id );
				$template = SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/sc-wordform-preview-page.php';
			}
			return $template;
		}
		
		/**
		 * Check built form preview page template data
		 * Built form data with proper sanitization & escaping
		 * @return - string
		 */
		public static function wordform_check_preview_template_data() {						
			return self::$preview_form_data;
		}

		/**
		 * Get created form  data by form_id
		 * param - $form_id
		 * return - array
		 */
		public static function sc_wordform_db_query( $form_id = null ) {
			global $wpdb;
			$table         = $wpdb->prefix . self::$sc_wordform_tbl;
			$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE form_id = %s LIMIT 1', array( $table, $form_id ) ), ARRAY_A );
			return $query_results;
		}

		/**
		 * Add admin menus
		 */
		public static function add_scwordform_submenus() {

			// Top Menu|Parent Menu - WordForm
			//add_menu_page( __( 'WordForm', 'wordform' ), 'WordForm', 'manage_options', 'word-form-topmenu', '', 'dashicons-forms', 6 );
			add_menu_page( __( 'WordForm', 'wordform' ), 'WordForm', 'manage_options', 'word-form-topmenu', '', plugin_dir_url( __FILE__ ) . '../admin/views/images/wordform-icon-box-shape-16x16.png', 6 );

			// Submenu - All Forms - sc-wordform-all-forms page slug
			add_submenu_page(
				'word-form-topmenu',
				__( 'WordForm - All Forms', 'wordform' ),
				__( 'All Forms', 'wordform' ),
				'manage_options',
				'word-form-topmenu',
				array( __CLASS__, 'add_scwordform_submenus_allforms_callback' )
			);

			// Submenu - Create Forms - sc-wordform-create-forms page slug
			add_submenu_page(
				'word-form-topmenu',
				__( 'WordForm - Create Forms', 'wordform' ),
				__( 'Create Form', 'wordform' ),
				'manage_options',
				'sc-wordform-create-forms',
				array( __CLASS__, 'add_scwordform_submenus_create_forms_callback' )
			);

			// Submenu - Submission data - sc-wordform-user-submission-data page slug
			add_submenu_page(
				'word-form-topmenu',
				__( 'WordForm - Submission Form Data', 'wordform' ),
				__( 'Submission Data', 'wordform' ),
				'manage_options',
				'sc-wordform-user-submission-data',
				array( __CLASS__, 'add_scwordform_submenus_user_submission_data_callback' )
			);

			// Submenu - Settings - sc-wordform-settings page slug
			add_submenu_page(
				'word-form-topmenu',
				__( 'WordForm -Settings', 'wordform' ),
				__( 'Settings', 'wordform' ),
				'manage_options',
				'sc-wordform-settings',
				array( __CLASS__, 'add_scwordform_submenus_settings_callback' )
			);
		}

		/**
		 * All Forms: Callback function of all forms
		 * Display all created forms
		 */
		public static function add_scwordform_submenus_allforms_callback() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$query_results = self::sc_wordform_get_all_created_forms_data();
			include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/allforms_callback_page.php';
		}

		/**
		 * Create Form: Create form page menu callback function
		 * Check if edit page - if not then create form page
		 */
		public static function add_scwordform_submenus_create_forms_callback() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( isset( $_GET['wordform-edit-id'] ) && ! empty( $_GET['wordform-edit-id'] ) ) {
				$wordform_id = isset( $_GET['wordform-edit-id'] ) && ! empty( $_GET['wordform-edit-id'] ) ? sanitize_text_field( $_GET['wordform-edit-id'] ) : '';
				global $wpdb;
				$table         = $wpdb->prefix . self::$sc_wordform_tbl;
				$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE form_id=%s ORDER BY id DESC', array( $table, $wordform_id ) ), ARRAY_A );
			}
			include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/create_forms_callback_page.php';
		}

		/**
		 * Submission Data: users submission data menu callback function
		 * Table data loaded by ajax request - Datatable
		 */
		public static function add_scwordform_submenus_user_submission_data_callback() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/user_submission_data_callback_page.php';
		}

		/**
		 * Callback Function
		 * Settings : validation Tab
		 * Get default validation messages for all forms initially
		 * Default 'all_form' validation messages display
		 */
		public static function add_scwordform_submenus_settings_callback() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$all_created_forms   = self::sc_wordform_get_all_created_forms_data();
			$validation_messages = self::sc_wordform_get_validation_messages_data_by_formid( 'all_form' );
			include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/settings_callback_page.php';
		}


		/**
		 * Get all created forms data
		 *
		 * @since 1.0.0
		 * return - array - All created forms data
		 */
		public static function sc_wordform_get_all_created_forms_data() {
			global $wpdb;
			$table         = $wpdb->prefix . self::$sc_wordform_tbl;
			$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i ORDER BY id DESC', $table ), ARRAY_A );
			return $query_results;
		}

		/**
		 * Menu - Settings - Validation Tab
		 * Settings validation messages Tab dropdown forms list
		 * Inner join validation messages table with created forms table
		 * Skipping deleted forms display to validation messages setting dropdown forms list by inner join
		 *
		 * @since 1.0.0
		 * return - array - [ id, form_name, form_id ]
		 */
		/*
		public static function wordform_validation_messages_table_inner_join_with_created_forms_table() {
		global $wpdb;
		$wordform_table = $wpdb->prefix . self::$sc_wordform_tbl;
		$wordform_validation_table = $wpdb->prefix . self::$sc_wordform_validation_messages_tbl;
		$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT wcf.id, form_name, wcf.form_id FROM %i as wvm INNER JOIN %i as wcf ON wvm.form_id = wcf.form_id WHERE form_name IS NOT NULL ORDER BY wvm.id DESC', [ $wordform_validation_table, $wordform_table ] ), ARRAY_A );
		return $query_results;
		}*/

		/**
		 * Menu - Submission Data
		 * Inner join users submission data table with created forms table
		 * Skipping deleted forms users submission data table by inner join
		 *
		 * @since 1.0.0
		 * return - array - [ id, form_name, form_id ]
		 */
		public static function wordform_submission_data_table_inner_join_with_created_forms_table() {
			global $wpdb;
			$wordform_table            = $wpdb->prefix . self::$sc_wordform_tbl;
			$wordform_submission_table = $wpdb->prefix . self::$sc_wordform_submission_tbl;
			$query_results             = $wpdb->get_results( $wpdb->prepare( 'SELECT wcf.id, form_name, wcf.form_id, wst.submission_data, wst.submission_user_data, wst.created_at FROM %i as wcf INNER JOIN %i as wst ON wcf.form_id = wst.form_id ORDER BY wst.id DESC', array( $wordform_table, $wordform_submission_table ) ), ARRAY_A );
			return $query_results;
		}


		/**
		 * Wordform default validation messages
		 * Fallback validation messages
		 */
		public static function wordform_default_validation_messages() {
			$validation_messages                                = array();
			$validation_messages['text-msg']                    = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['number-msg']                  = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['textarea-msg']                = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['radio-msg']                   = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['checkbox-msg']                = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['select-msg']                  = esc_html__( 'This field value required.', 'wordform' );
			$validation_messages['email-msg']                   = esc_html__( 'Valid email address required.', 'wordform' );
			$validation_messages['captcha-empty-msg']           = esc_html__( 'Please fill the captcha field.', 'wordform' );
			$validation_messages['captcha-wrong-msg']           = esc_html__( 'Wrong captcha value entered.', 'wordform' );
			$validation_messages['captcha-expire-msg']          = esc_html__( 'Captcha value expired.', 'wordform' );
			$validation_messages['form-submission-success-msg'] = esc_html__( 'We have received your message.', 'wordform' );
			return $validation_messages;
		}

		/**
		 * Get validation messages data for the selected form id
		 *
		 * @since 1.0.0
		 * return - array - query results
		 */
		public static function sc_wordform_get_validation_messages_data_by_formid( $form_id = null ) {
			$validation_messages = self::wordform_default_validation_messages();
			if ( ! is_null( $form_id ) || ! empty( $form_id ) ) {
				global $wpdb;
				$table         = $wpdb->prefix . self::$sc_wordform_validation_messages_tbl;
				$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE form_id = %s LIMIT 1', array( $table, $form_id ) ), ARRAY_A );
				if ( $query_results && isset( $query_results[0] ) && isset( $query_results[0]['validation_messages'] ) ) {
					// JSON string data in db
					return wp_unslash( json_decode( $query_results[0]['validation_messages'], true ) );
				}
			}
			// Return default validation messages if not set yet
			return $validation_messages;
		}

		/**
		 * Check validation messages data already set or Not for the selected form ID
		 *
		 * @since 1.0.0
		 * return - array - query results
		 */
		public static function sc_wordform_check_validation_messages_data_set_or_not( $form_id = null ) {
			if ( ! is_null( $form_id ) || ! empty( $form_id ) ) {
				global $wpdb;
				$table         = $wpdb->prefix . self::$sc_wordform_validation_messages_tbl;
				$query_results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %i WHERE form_id = %s LIMIT 1', array( $table, $form_id ) ), ARRAY_A );
				if ( $query_results ) {
					return $query_results;
				}
			}
			return array();
		}

		/**
		 * Settings : General Tab Info - Default Setting data
		 * return - array
		 */
		public static function wordform_get_settings_general_tab_default_data() {
			$general_tab_options                                   = array();
			$general_tab_options['submit_button_background_color'] = '#2271b1';
			$general_tab_options['submit_button_background_hover_color'] = '#006ba1';
			$general_tab_options['submit_button_font_color']             = '#ffffff';
			$general_tab_options['submit_button_font_size']              = 16;
			$general_tab_options['submit_button_padding_top_bottom']     = 12;
			$general_tab_options['submit_button_padding_left_right']     = 25;
			$general_tab_options['submit_button_font_weight']            = 'normal';
			$general_tab_options['send_email_on_form_submission']        = 'no';
			$general_tab_options['hide_form_on_form_submission']         = 'yes';
			$general_tab_options['wordform_form_width']                  = 100;
			return $general_tab_options;
		}

		/**
		 * Settings : General Tab Info
		 * get options data
		 * return - array
		 */
		public static function sc_wordform_get_settings_general_tab_info() {
			$general_tab_options = self::wordform_get_settings_general_tab_default_data();
			if ( $options = get_option( 'sftcy_wordform_settings_menu_general_tab_info' ) ) {
				// Form Width
				$general_tab_options['wordform_form_width'] = isset( $options['wordform_form_width'] ) ? sanitize_text_field( $options['wordform_form_width'] ) : $general_tab_options['wordform_form_width'];

				// Background Color
				$general_tab_options['submit_button_background_color'] = isset( $options['submit_button_background_color'] ) ? sanitize_hex_color( $options['submit_button_background_color'] ) : $general_tab_options['submit_button_background_color'];

				// Background Color (Hover)
				$general_tab_options['submit_button_background_hover_color'] = isset( $options['submit_button_background_hover_color'] ) ? sanitize_hex_color( $options['submit_button_background_hover_color'] ) : $general_tab_options['submit_button_background_hover_color'];

				// Font Size
				$general_tab_options['submit_button_font_size'] = isset( $options['submit_button_font_size'] ) ? sanitize_text_field( $options['submit_button_font_size'] ) : $general_tab_options['submit_button_font_size'];

				// Padding ( Top-Bottom)
				$general_tab_options['submit_button_padding_top_bottom'] = isset( $options['submit_button_padding_top_bottom'] ) ? sanitize_text_field( $options['submit_button_padding_top_bottom'] ) : $general_tab_options['submit_button_padding_top_bottom'];

				// Padding ( Left-Right)
				$general_tab_options['submit_button_padding_left_right'] = isset( $options['submit_button_padding_left_right'] ) ? sanitize_text_field( $options['submit_button_padding_left_right'] ) : $general_tab_options['submit_button_padding_left_right'];

				// Font Color
				$general_tab_options['submit_button_font_color'] = isset( $options['submit_button_font_color'] ) ? sanitize_hex_color( $options['submit_button_font_color'] ) : $general_tab_options['submit_button_font_color'];

				// Font Weight
				$general_tab_options['submit_button_font_weight'] = isset( $options['submit_button_font_weight'] ) ? sanitize_text_field( $options['submit_button_font_weight'] ) : $general_tab_options['submit_button_font_weight'];

				// Send Email On Form Submission
				$general_tab_options['send_email_on_form_submission'] = isset( $options['send_email_on_form_submission'] ) ? sanitize_text_field( $options['send_email_on_form_submission'] ) : $general_tab_options['send_email_on_form_submission'];

				// Hide On Form Submission
				$general_tab_options['hide_form_on_form_submission'] = isset( $options['hide_form_on_form_submission'] ) ? sanitize_text_field( $options['hide_form_on_form_submission'] ) : $general_tab_options['hide_form_on_form_submission'];
			}
			return $general_tab_options;
		}

		/**
		 * Default Wordform Width
		 *
		 * @return - integer - width value
		 */
		public static function wordform_current_form_width() {
			$general_tab_options = self::sc_wordform_get_settings_general_tab_info();
			return $general_tab_options['wordform_form_width'];
		}
		/**
		 * Check if send email setting is true
		 * Send Email on Each Form Submission by users at front-end
		 */
		public static function sc_wordform_send_email_on_form_submission_by_users( $data = array() ) {
			$options      = self::sc_wordform_get_settings_general_tab_info();
			$email_option = isset( $options['send_email_on_form_submission'] ) ? sanitize_text_field( $options['send_email_on_form_submission'] ) : 'no';
			$recipient    = sanitize_email( get_option( 'admin_email' ) );
			if ( isset( $email_option ) && $email_option == 'yes' && array_filter( $data ) && is_email( $recipient ) ) {
				SFTCY_Wordform_Ajaxhandler::$output['sendEmailOption'] = 'Yes';

				SFTCY_Wordform_FormSubmission::$sc_wordform_submission_array_data = json_decode( $data['submission_data'], true );
				$submissionData = SFTCY_Wordform_FormSubmission::sc_wordform_process_submission_data();

				$recipient        = $recipient;
				$from_name        = sanitize_text_field( get_option( 'blogname' ) );
				$subject          = 'WordForm - User Submitted Form [ ' . sanitize_text_field( $data['form_name'] ) . ' ]';
				$email_body       = $submissionData;
				$headers          = array( 'Content-Type: text/html; charset=UTF-8' );
				$mail_send_status = wp_mail( $recipient, $subject, $email_body, $headers, array() );
				SFTCY_Wordform_Ajaxhandler::$output['mailSendStatus'] = $mail_send_status;
			} else {
				SFTCY_Wordform_Ajaxhandler::$output['sendEmailOption'] = 'No';
			}
		}

		/**
		 * Hide Form on Submission status
		 * default - Hide Form - true
		 * return - boolean
		 */
		public static function wordform_hide_form_on_submission_status() {
			if ( $options = get_option( 'sftcy_wordform_settings_menu_general_tab_info' ) ) {
				return ( isset( $options['hide_form_on_form_submission'] ) && $options['hide_form_on_form_submission'] == 'yes' ) ? true : false;
			}
			return true;
		}

		/**
		 * Create nonce
		 * return - string - nonce value
		 */
		public static function sc_wordform_get_nonce() {
			return wp_create_nonce( 'scwordform_wpnonce' );
		}


		/**
		 * Check the current screen
		 * Check whether Post | Page add new editor
		 */
		public static function wordform_current_screen_actions( $current_screen ) {
			if ( isset( $current_screen->action ) && $current_screen->action === 'add' && isset( $current_screen->post_type ) && $current_screen->post_type === 'post' ) {
			} elseif ( isset( $current_screen->action ) && $current_screen->action === 'add' && isset( $current_screen->post_type ) && $current_screen->post_type === 'page' ) {
			}
		}

		/**
		 * 'the_content' hook callback
		 * While rendering single Post | Page
		 * return - updated filtered content - updated content with latest form elements
		 */
		public static function wordform_post_page_rendering_with_form_data( $content ) {
			// Check if we're inside the main loop in a single Post
			if ( is_singular() && in_the_loop() && is_main_query() ) {
				global $post;				
				if ( $post->ID && has_block( self::$wordform_custom_block_name ) ) {
					$rendering_post_id = $post->ID;
					$content           = self::wordform_embed_block_form_html_data_with_content( $rendering_post_id );
				}
			}
			return $content;
		}

		/**
		 * Build & Embed wordform - based on attributes 'wordformid' attached with Post | Page
		 * params - $postID - current rendering Post | Page ID
		 * return - updated content - always built the FORM with latest wordform form elements - if edited or changes after attach
		 *
		 * @since 1.0.0
		 */
		public static function wordform_embed_block_form_html_data_with_content( $postID = null ) {
			$post = null;
			$post = get_post( $postID );
			if ( ! is_null( $post ) && $post ) {
				$allblocks     = array();
				$blocks        = array();
				$blocks        = parse_blocks( $post->post_content );				
				$form_id_index = 0;
				foreach ( $blocks as $key => $block ) {
					if ( self::$wordform_custom_block_name == $block['blockName'] ) {
						if ( isset( $block['attrs']['wordformid'] ) && ! empty( $block['attrs']['wordformid'] ) ) {
							$formdata           = SFTCY_BuildForm::wordform_built_to_display( $block['attrs']['wordformid'] );
							//$updated_formdata   = '<div class="sc-wordform-block-editor-div-wrapper">' . $formdata . '</div>';
							$block['innerHTML'] = $formdata;							
							$block['innerContent'][0] = $formdata;							
							$allblocks[] = $block;							
						}
					} else {
						$allblocks[] = $block;
					}
				} // foreach				
				$content = serialize_blocks( $allblocks );				
				return $content;
			}
		}



		/**
		 * Admin notices
		 */
		public static function scwordform_admin_notices() {
			$admin_notice  = false;
			$query_results = self::sc_wordform_get_all_created_forms_data();
			if ( ! $query_results || ! array_filter( $query_results ) ) {
				$admin_notice = true;
			}

			if ( $admin_notice ) {
				$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
				if ( $page != 'sc-wordform-create-forms' ) {
					$url   = admin_url( 'admin.php?page=sc-wordform-create-forms' );
					$alink = '<a href="' . $url . '"> Click to create the Form.</a>';
					printf( '<div class="notice notice-info is-dismissible">' );
					printf( '<div class="sc-wordform-notice-wrapper" style="color: cornflowerblue;"><h4><i class="dashicons dashicons-forms"></i> WordForm - Simple & Easy Drag-Drop Form builder. You did not create any Form yet, try simple & easy Drag-Drop Form builder, try your first WordForm. %s</h4></div>', esc_url( $alink ) );
					printf( '</div>' );
				}
			}
		}

		public static function scwordform_row_link( $actions, $plugin_file ) {
			$wordsmtp_plugin = plugin_basename( SFTCY_WORDFORM_PLUGIN_DIR );
			$plugin_name     = basename( $plugin_file, '.php' );
			if ( $wordsmtp_plugin == $plugin_name ) {
				// $doclink[]        = '<a href="https://softcoy.com/wordform" title="WordForm - Docs" target="_blank">WordForm Docs</a>';
				// $doclink[]        = '<a href="https://softcoy.com/wordform" title="WordForm Support" target="_blank">Support</a>';
				// return array_merge( $actions, $doclink );
			}
			return $actions;
		}
	} // End class
}
