<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SFTCY_Wordform_Ajaxhandler' ) ) {
	class SFTCY_Wordform_Ajaxhandler {

		private static $initiated   = false;
		public static $success_icon = '<i class="fa-regular fa-circle-check"></i>';
		public static $error_icon   = '<i class="fa-regular fa-circle-xmark"></i>';
		public static $output       = array();

		public function __construct() {
			if ( ! self::$initiated ) {
				self::initiate_hooks();
			}
		}

		private static function initiate_hooks() {
				// Enqueue scripts
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'sc_wordform_admin_required_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'sc_wordform_frontend_required_scripts' ) );
				add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'sc_wordform_block_editor_required_scripts' ) );
				add_action( 'wp_loaded', array( __CLASS__, 'wordform_both_admin_front_end_register_scripts' ) );
				add_filter( 'safe_style_css', array( __CLASS__, 'sc_wordform_style_filter_hook' ), 10, 1 );

				// Ajax : WordForm Elements
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_text', array( __CLASS__, 'sc_wordform_render_element_options_type_text' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_number', array( __CLASS__, 'sc_wordform_render_element_options_type_number' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_textarea', array( __CLASS__, 'sc_wordform_render_element_options_type_textarea' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_radio', array( __CLASS__, 'sc_wordform_render_element_options_type_radio' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_checkbox', array( __CLASS__, 'sc_wordform_render_element_options_type_checkbox' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_select', array( __CLASS__, 'sc_wordform_render_element_options_type_select' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_email', array( __CLASS__, 'sc_wordform_render_element_options_type_email' ) );
				add_action( 'wp_ajax_sc_wordform_render_element_options_type_captcha', array( __CLASS__, 'sc_wordform_render_element_options_type_captcha' ) );

				// Ajax : WordForm Captcha
				add_action( 'wp_ajax_wordform_generate_captcha_image', array( __CLASS__, 'wordform_generate_captcha_image' ) );

				// Ajax : Save created - edited wordform data - Delete
				add_action( 'wp_ajax_sc_wordform_save', array( __CLASS__, 'sc_wordform_save' ) );
				add_action( 'wp_ajax_sc_wordform_built_form_data_save', array( __CLASS__, 'sc_wordform_built_form_data_save' ) );
				add_action( 'wp_ajax_sc_wordform_all_forms_page_delete_form', array( __CLASS__, 'sc_wordform_all_forms_page_delete_form' ) );

				// Ajax : Settings : Validation tab
				add_action( 'wp_ajax_sc_wordform_settings_menu_validation_tab_data_save', array( __CLASS__, 'sc_wordform_settings_menu_validation_tab_data_save' ) );
				add_action( 'wp_ajax_sc_wordform_settings_menu_validation_tab_selected_form_data_save', array( __CLASS__, 'sc_wordform_settings_menu_validation_tab_selected_form_data_save' ) );
				// Ajax: Settings : General Tab
				add_action( 'wp_ajax_sc_wordform_settings_general_tab_form', array( __CLASS__, 'sc_wordform_settings_general_tab_form' ) );

				// Ajax : Front-end Users submission form data display through datatable
				add_action( 'wp_ajax_sc_wordform_users_submission_data_load', array( __CLASS__, 'sc_wordform_users_submission_data_load' ) );

				// Ajax : Front-end users form submit
				add_action( 'wp_ajax_sc_wordform_created_form_submission', array( __CLASS__, 'sc_wordform_created_form_submission' ) );
				add_action( 'wp_ajax_nopriv_sc_wordform_created_form_submission', [ __CLASS__, 'sc_wordform_created_form_submission'] );

				// REST API : End Points : Wordform Block Editor
				add_action(
					'rest_api_init',
					function () {
															register_rest_route(
																'wordform/v1',
																'/all-form-list',
																array(
																	'methods'  => 'POST',
																	'callback' => array( __CLASS__, 'sc_wordform_block_editor_get_forms_ajax_callback' ),
																	'permission_callback' => function () {
																			return true;},
																)
															);
					}
				);

				add_action(
					'rest_api_init',
					function () {
															register_rest_route(
																'wordform/v1',
																'/render-selected-form',
																array(
																	'methods'  => 'POST',
																	'callback' => array( __CLASS__, 'sc_wordform_block_editor_get_selected_form_ajax_callback' ),
																	'permission_callback' => function () {
																			return true;},
																)
															);
					}
				);

				self::$initiated = true;
		}


		/**
		 * Rest API Callback
		 * Rest Path: wordform/v1/all-form-list
		 * Get all created form lists for block editor to display & to choose from dropdown select menu
		 * return - json
		 */
		public static function sc_wordform_block_editor_get_forms_ajax_callback( $data ) {
			self::$output = array();
			$result       = $data->get_body();
			if ( $result ) {
				$result_arr = json_decode( $result, true );
				if ( isset( $result_arr['postID'] ) && ! empty( $result_arr['postID'] ) ) {
					$postID                   = sanitize_text_field( $result_arr['postID'] );
					$created_forms            = SFTCY_Wordform::sc_wordform_get_all_forms_for_block_dynamic_callback();
					self::$output['status']   = 'success';
					self::$output['formList'] = $created_forms;
					self::$output['postID']   = $postID;
				} else {
					self::$output['status']  = 'fail';
					self::$output['comment'] = 'postID not received.';
				}
			} else {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'No post Result data found.';
			}
			return wp_json_encode( self::$output );
		}

		/**
		 * Rest API Callback
		 * Rest Path: wordform/v1/render-selected-form
		 * Rendering the created form based on selected or chosen from block editor dropdown list
		 * Embed the selected form with the post
		 * Also save the POST ID attached with the selected form
		 * return json
		 */
		public static function sc_wordform_block_editor_get_selected_form_ajax_callback( $data ) {
			self::$output = array();

			$result = $data->get_body();
			if ( $result ) {
				$result_arr = json_decode( $result, true );
				if ( isset( $result_arr['WordFormID'] ) && ! empty( $result_arr['WordFormID'] ) ) {
					$form_id                  = sanitize_text_field( $result_arr['WordFormID'] );
					$attached_post_id         = sanitize_text_field( $result_arr['postID'] );
					self::$output['formdata'] = SFTCY_BuildForm::wordform_built_to_display( $form_id );
					self::$output['status']   = 'success';
					self::$output['reason']   = 'Form data ready to display.';
				}
			} else {
				self::$output['status'] = 'fail';
				self::$output['reason'] = 'no form ID received.';
			}
			return wp_json_encode( self::$output );
		}

		/**
		 * Register Scripts
		 * For both admin & front-end
		 */
		public static function wordform_both_admin_front_end_register_scripts() {
			wp_register_style( 'wordform-font-awesome-6-style', plugins_url( '../admin/css/fontawesome6-all.min.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
		}

		/**
		 * Enqueue admin scripts
		 * Enqueue only with plugin admin pages
		 *
		 * @since 1.0.0
		 */
		public static function sc_wordform_admin_required_scripts() {
			// get current admin screen
			global $pagenow;
			$screen              = get_current_screen();
			$admin_page          = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
			$attachto_wordformid = isset( $_GET['attach-wordform-id'] ) ? sanitize_text_field( $_GET['attach-wordform-id'] ) : '';
			$wordform_pages      = array( 'word-form-topmenu', 'sc-wordform-create-forms', 'sc-wordform-user-submission-data', 'sc-wordform-settings' );

			if ( in_array( $admin_page, $wordform_pages ) || ! empty( $attachto_wordformid ) ) {
				global $post;
				// Color picker Style
				wp_enqueue_style( 'wp-color-picker' );
				// Datatable Style
				wp_enqueue_style( 'sc-wordform-datatable-style', plugins_url( '../admin/css/dataTables.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
				// Wordform Style
				wp_enqueue_style( 'sc-wordform-admin-style', plugins_url( '../admin/css/sc-wordform-admin-misc-styles.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
				// jQuery UI Style
				wp_enqueue_style( 'sc-wordform-jquery-ui-style', plugins_url( '../admin/css/jquery-ui.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
				// Font Awesome
				// wp_enqueue_style('wordform-font-awesome-6-style', plugins_url('../admin/css/fontawesome6-all.min.css', __FILE__ ), array('sc-wordform-admin-style'), SFTCY_WORDFORM_VERSION, 'all' );
				wp_enqueue_style( 'wordform-font-awesome-6-style' );

				// Datatable JS
				wp_enqueue_script( 'sc-wordform-datatable-script', plugins_url( '../admin/js/dataTables.js', __FILE__ ), array( 'jquery' ), SFTCY_WORDFORM_VERSION, 'all' );
				// Cipboard JS
				wp_enqueue_script( 'sc-wordform-admin-copy-to-clipboard-script', plugins_url( '../admin/js/clipboard.min.js', __FILE__ ), array( 'jquery' ), SFTCY_WORDFORM_VERSION, true );
				// Wordform JS
				wp_enqueue_script( 'sc-wordform-admin-misc-script', plugins_url( '../admin/js/sc-wordform-admin-misc-script.js', __FILE__ ), array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-tabs', 'wp-color-picker' ), SFTCY_WORDFORM_VERSION, true );
				// Wordform JS 2
				wp_enqueue_script( 'wordform-admin-view-page-script', plugins_url( '../admin/js/wordform-admin-view-page-script.js', __FILE__ ), array( 'jquery', 'sc-wordform-admin-misc-script', 'jquery-ui-slider' ), SFTCY_WORDFORM_VERSION, true );

				$nonce = SFTCY_Wordform::sc_wordform_get_nonce();

				$localize_data = array(
					'adminajax_url'                 => admin_url( 'admin-ajax.php' ),
					'nonce'                         => $nonce,
					'current_screenid'              => $screen->id,
					'current_action'                => $screen->action,
					'current_posttype'              => $screen->post_type,
					'current_pagenow'               => $pagenow,
					'postID'                        => ! is_null( $post ) ? $post->ID : 0,
					'attachto_wordformid'           => $attachto_wordformid,
					'sc_wordform_submit_text'       => __( 'Submit', 'wordform' ),
					'wordform_default_template_url' => SFTCY_Wordform_Captcha::wordform_create_form_default_captcha_template_url(),
					'wordform_form_width'           => SFTCY_Wordform::wordform_current_form_width(),
					'wordform_copied_text'          => '<i class="fa-regular fa-copy"></i> ' . __( 'Copied', 'wordform' ),
					'wordform_copy_shortcode_text'  => '<i class="fa-regular fa-copy"></i> ' . __( 'Copy Shortcode', 'wordform' ),
					'noElementDroppedMsg'           => '<span class="dashicons dashicons-info"></span> ' . __( 'Click or drag-drop any element into DropZone to create Form!', 'wordform' ),
				);

				// Localize script
				wp_localize_script( 'sc-wordform-admin-misc-script', 'sc_wordform_metabox_script_obj', $localize_data );
				wp_localize_script( 'wordform-admin-view-page-script', 'wordform_admin_view_page_script_obj', $localize_data );
			}
		}

		/**
		 * Front-end required scripts
		 * If plugin active then enqueue scripts
		 */
		public static function sc_wordform_frontend_required_scripts() {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'wordform/wordform.php' ) ) {
				global $pagenow;
				// Front-end style
				wp_enqueue_style( 'sc-wordform-frontend-misc-style', plugins_url( '../assets/css/sc-wordform-frontend-misc-styles.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
				// Font Awesome
				wp_enqueue_style( 'wordform-font-awesome-6-style' );
				// Front-end JS
				wp_enqueue_script( 'sc-wordform-frontend-misc-script', plugins_url( '../assets/js/sc-wordform-frontend-misc-script.js', __FILE__ ), array( 'jquery' ), SFTCY_WORDFORM_VERSION, true );
				$nonce = SFTCY_Wordform::sc_wordform_get_nonce();

				// Localize script
				wp_localize_script(
					'sc-wordform-frontend-misc-script',
					'sc_wordform_frontend_misc_script_obj',
					array(
						'ajax_url'                => admin_url( 'admin-ajax.php' ),
						'nonce'                   => $nonce,
						'current_pagenow'         => $pagenow,
						'hide_form_on_submission' => SFTCY_Wordform::wordform_hide_form_on_submission_status(),
					)
				);
			}
		}

		/**
		 * Block editor scripts enqeue
		 * Only when plugin is active
		 */
		public static function sc_wordform_block_editor_required_scripts() {
			if ( is_plugin_active( 'wordform/wordform.php' ) && is_admin() ) {
				global $pagenow;
				$screen = get_current_screen();
				wp_enqueue_style( 'sc-wordform-frontend-block-editor-misc-style', plugins_url( '../assets/css/sc-wordform-block-editor-misc-styles.css', __FILE__ ), array(), SFTCY_WORDFORM_VERSION, 'all' );
				wp_enqueue_script( 'sc-wordform-block-editor-misc-script', plugins_url( '../assets/js/sc-wordform-block-editor-script.js', __FILE__ ), array( 'jquery' ), SFTCY_WORDFORM_VERSION, true );
				$nonce = SFTCY_Wordform::sc_wordform_get_nonce();
				// localize script
				wp_localize_script(
					'sc-wordform-block-editor-misc-script',
					'sc_wordform_block_editor_misc_script_obj',
					array(
						'ajax_url'         => admin_url( 'admin-ajax.php' ),
						'nonce'            => $nonce,
						'current_screenid' => $screen->id,
						'current_posttype' => $screen->post_type,
						'current_pagenow'  => $pagenow,
					)
				);
			}
		}

		/**
		 * Add custom style attributes
		 * To allow with wp_kses
		 */
		public static function sc_wordform_style_filter_hook( $styles ) {
			$styles[] = 'display';
			$styles[] = 'float';
			return $styles;
		}

		// Text: Load Text Options template
		public static function sc_wordform_render_element_options_type_text() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Text';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/text.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		// Number: Load Number Options template
		public static function sc_wordform_render_element_options_type_number() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Number';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/number.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}



		// Textarea: Load Textarea Options template
		public static function sc_wordform_render_element_options_type_textarea() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Textarea';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/textarea.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}


		// Radio: Load Radio Options template
		public static function sc_wordform_render_element_options_type_radio() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Radio';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/radio.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		// Dropdown: Load Select Dropdown template
		public static function sc_wordform_render_element_options_type_select() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Select';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/select.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		// Checkbox: Load Checkbox template
		public static function sc_wordform_render_element_options_type_checkbox() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Checkbox';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/checkbox.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		// Email: Load Email Options template
		public static function sc_wordform_render_element_options_type_email() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Email';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/email.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		// Captcha: Load Captcha Options template
		public static function sc_wordform_render_element_options_type_captcha() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$element_wrapper_id               = isset( $_POST['elementWrapperID'] ) ? sanitize_text_field( $_POST['elementWrapperID'] ) : '';
			self::$output                     = array();
			self::$output['elementType']      = 'Captcha';
			self::$output['elementWrapperID'] = $element_wrapper_id;
			$element_index                    = explode( '-', $element_wrapper_id )[1];
			ob_start();
			include_once SFTCY_WORDFORM_PLUGIN_INC . 'elements/captcha.php';
			self::$output['fieldEditOptionsHtml'] = ob_get_clean();
			if ( ! isset( $element_wrapper_id ) || empty( $element_wrapper_id ) ) {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Element wrapper ID missing.';
			} else {
				self::$output['status'] = 'success';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}



		/**
		 * Created form data
		 * Save: Created wordform
		 *
		 * @since 1.0.0
		 */
		public static function sc_wordform_save() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			global $wpdb;
			self::$output  = array();
			$form_saved    = isset( $_POST['saveMeta']['formSaved'] ) ? sanitize_text_field( $_POST['saveMeta']['formSaved'] ) : false;
			$saved_form_id = isset( $_POST['saveMeta']['createdFormID'] ) ? sanitize_text_field( $_POST['saveMeta']['createdFormID'] ) : '';
			$form_name     = isset( $_POST['saveMeta']['formName'] ) && ! empty( $_POST['saveMeta']['formName'] ) ? sanitize_text_field( $_POST['saveMeta']['formName'] ) : 'New Form';

			if ( isset( $_POST['params'] ) && is_array( $_POST['params'] ) ) {
				$sanitized_form_data = self::sc_wordform_sanitize_created_form_data( $_POST['params'] );
				$form_data           = wp_json_encode( $sanitized_form_data );
				// Already Saved once - Update
				if ( $form_saved && ! empty( $saved_form_id ) ) {
					$preview_url              = site_url() . '?sc-wordform-id=' . $saved_form_id;
					$update_data['form']      = $form_data;
					$update_data['form_name'] = $form_name;
					$where_data['form_id']    = trim( $saved_form_id );
					$updated_status           = $wpdb->update( $wpdb->prefix . SFTCY_Wordform::$sc_wordform_tbl, $update_data, $where_data );
					self::$output['status']   = 'success';
					// self::$output['reason']       = '<span class="dashicons dashicons-saved"></span> ' . __('Form updated successfully.');
					self::$output['reason']      = '<i class="fa-solid fa-file-circle-check"></i> Form[ <strong>' . $form_name . '</strong> ]' . __( ' updated successfully.', 'wordform' );
					self::$output['formSaved']   = true;
					self::$output['savedFormID'] = $saved_form_id;
					self::$output['previewURL']  = $preview_url;
				}
				// New form data - insert
				else {
					$insert_data              = array();
					$insert_data['form_name'] = $form_name;
					$insert_data['form']      = $form_data;
					$insert_record            = $wpdb->insert( $wpdb->prefix . SFTCY_Wordform::$sc_wordform_tbl, $insert_data );
					if ( $insert_record ) {
						$insert_id              = $wpdb->insert_id;
						$form_id                = 'wordform-' . $insert_id;
						$update_data['form_id'] = $form_id;
						$where_data['id']       = $insert_id;
						$update_status          = $wpdb->update( $wpdb->prefix . SFTCY_Wordform::$sc_wordform_tbl, $update_data, $where_data );
						if ( $update_status > 0 ) {
							$preview_url            = site_url() . '?sc-wordform-id=' . $form_id;
							self::$output['status'] = 'success';
							// self::$output['reason']           = '<span class="dashicons dashicons-saved"></span> ' . __('Form created successfully.');
							self::$output['reason']      = '<i class="fa-solid fa-file-circle-check"></i> Form[ <strong>' . $form_name . '</strong> ]' . __( ' created successfully.', 'wordform' );
							self::$output['formSaved']   = true;
							self::$output['savedFormID'] = $form_id;
							self::$output['previewURL']  = $preview_url;
						} else {
							self::$output['status'] = 'fail';
							self::$output['reason'] = 'Updating Form ID field failed.';
						}
					}
				} // else
			} else {
				self::$output['status'] = 'fail';
				self::$output['reason'] = 'First drag & drop form input elements to create the form.';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Sanitize created form data values
		 * return - sanitized original array data
		 */
		public static function sc_wordform_sanitize_created_form_data( &$form_data ) {
			foreach ( $form_data as $key => &$all_data ) {
				if ( isset( $all_data['multiOption'] ) ) {
					foreach ( $all_data['multiOption'] as $key => &$val ) {
						foreach ( $val as $key => &$option ) {
							$val[ $key ] = sanitize_text_field( $option );
						}
					}
				} else {
					foreach ( $all_data as $key => &$val ) {
						$all_data[ $key ] = sanitize_text_field( $val );
					}
				}
			} // foreach

			return $form_data;
		}

		/**
		 * Allowed html tags for wp_kses
		 * return - array
		 */
		public static function sc_wordform_allowed_html_tags() {
			$allowed_tags = array(
				'img'      => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
					'src'   => array(),
					'alt'   => array(),
				),
				'label'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
					'for'   => array(),
				),
				'div'      => array(
					'id'            => array(),
					'class'         => array(),
					'data-*'        => true,
					'wfd-invisible' => array(),
					'style'         => array(),
				),
				'ul'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'input'    => array(
					'id'            => array(),
					'class'         => array(),
					'type'          => array(),
					'name'          => array(),
					'value'         => array(),
					'checked'       => array(),
					'readonly'      => array(),
					'disabled'      => array(),
					'wfd-invisible' => array(),
					'placeholder'   => array(),
				),
				'textarea' => array(
					'id'            => array(),
					'class'         => array(),
					'name'          => array(),
					'value'         => array(),
					'readonly'      => array(),
					'disabled'      => array(),
					'wfd-invisible' => array(),
					'rows'          => array(),
					'cols'          => array(),
					'placeholder'   => array(),
				),
				'span'     => array(
					'id'    => array(),
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'button'   => array(
					'id'    => array(),
					'class' => array(),
					'type'  => array(),
					'style' => array(),
				),
				'i'        => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'li'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'h4'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'strong'   => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'b'        => array(
					'id'    => array(),
					'class' => array(),
				),
				'table'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'thead'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'tbody'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'tr'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'td'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'form'     => array(
					'id'      => array(),
					'class'   => array(),
					'name'    => array(),
					'enctype' => array(),
					'method'  => array(),
					'style'   => array(),
				),
				'select'   => array(
					'id'    => array(),
					'class' => array(),
					'name'  => array(),
					'style' => array(),
				),
				'option'   => array(
					'id'       => array(),
					'class'    => array(),
					'value'    => array(),
					'checked'  => array(),
					'selected' => array(),
					'style'    => array(),
				),
				'small'    => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'hr'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				),
				'br'       => array(
					'id'    => array(),
					'class' => array(),
					'style' => array(),
				)
			);
			return $allowed_tags;
		}

		/**
		 * Save built form data
		 * Save built form options data
		 * return - json
		 */
		public static function sc_wordform_built_form_data_save() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			global $wpdb;
			self::$output = array();
			$update_data  = array();
			// var_dump( $_POST );

			$builtform_data         = isset( $_POST['params']['builtForm'] ) ? wp_kses( $_POST['params']['builtForm'], self::sc_wordform_allowed_html_tags() ) : '';
			$builtform_options_data = isset( $_POST['params']['builtFormOptions'] ) ? wp_kses( $_POST['params']['builtFormOptions'], self::sc_wordform_allowed_html_tags() ) : '';
			// $builtform_data                              = isset( $_POST['params']['builtForm'] )? $_POST['params']['builtForm'] : '';
			// $builtform_options_data                      = isset( $_POST['params']['builtFormOptions'] )? $_POST['params']['builtFormOptions'] : '';

			/*
			print_r($builtform_data);
			print_r($builtform_options_data);
			exit(); */

			$builtform_html                = array(
				'builtform_data'         => $builtform_data,
				'builtform_options_data' => $builtform_options_data,
			);
			$builtform_html_json           = wp_json_encode( $builtform_html );
			self::$output['builtFormData'] = $builtform_html_json;
			$wordform_id                   = isset( $_POST['params']['formID'] ) ? sanitize_text_field( $_POST['params']['formID'] ) : '';

			if ( isset( $wordform_id ) && ! empty( $wordform_id ) ) {
				$update_data['builtform_html_data'] = $builtform_html_json;
				$where_data['form_id']              = $wordform_id;
				$update_record                      = $wpdb->update( $wpdb->prefix . SFTCY_Wordform::$sc_wordform_tbl, $update_data, $where_data );
				if ( $update_record ) {
					self::$output['status'] = 'success';
					self::$output['reason'] = __( 'Updated built form data successfully.', 'wordform' );
				} else {
					self::$output['status'] = 'success';
					self::$output['reason'] = __( 'Nothing changes.', 'wordform' );
				}
			} else {
				self::$output['status'] = 'fail';
				self::$output['reason'] = __( 'Invalid word form ID.', 'wordform' );
			}

			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * [ Currently Not Used ]
		 * Update attached block html form content on edit created form data
		 * Update attached block html form content on edit submit button attributes through settings - general tab
		 *
		 * @since 1.0.0
		 */
		/*
		public static function sc_wordform_block_editor_form_html_update( $postids = [], $form_id = null ) {
		foreach ( $postids as $postID ) {
			$post           = null;
			$post           = get_post( $postID );
			if ( ! is_null( $post ) && $post ) {
				$allblocks      = [];  // Reset for multiple post IDs
				$blocks         = []; // Reset for multiple post IDs
				$blocks         = parse_blocks( $post->post_content );
				foreach( $blocks as $key => $block ) {
					if ( 'wordform-block/wordform' == $block['blockName'] )  {

						$results                        = SFTCY_Wordform::sc_wordform_db_query( $form_id );
						SFTCY_BuildForm::$form_elements = json_decode( $results[0]['form'], true );
						$created_form                   = SFTCY_BuildForm::sc_wordform_build_form_from_elements( $form_id );
						$formdata                       = '';
						foreach ( $created_form as $form ) {
							$formdata                  .= $form;
						}
						$updated_formdata               = '<div class="sc-wordform-block-editor-div-wrapper">' . $formdata . '</div>';
						//var_dump( $formdata );
						//var_dump( $updated_formdata );
						$block['innerHTML']             =   $updated_formdata;
						if ( isset( $block['innerContent'][0] ) ) {
							$block['innerContent'][0]   =   $updated_formdata;
						}
						//var_dump($postID);
						//var_dump( $block['innerHTML'] );
						//var_dump( $block['innerContent'][0] );
						$allblocks[]                    = $block;
						//$allblocks[]  = NULL; // work
					}
					else {
						$allblocks[]    = $block;
					}
				} // foreach

				$content     = serialize_blocks($allblocks);
				$updatedPost = array(
					'ID'            => $postID,
					'post_content'  => $content
				);
				if ( wp_update_post( $updatedPost ) ) {
					self::$output[ $form_id ]['blockFormDataUpdated']  = true;
				}
				else {
					self::$output[ $form_id ]['blockFormDataUpdated']  = false;
				}
			}
		} // foreach

		}*/

		/**
		 * When delete form permanently
		 * Delete attached block html form content also from the attached Post | Page
		 *
		 * @since 1.0.0
		 */
		public static function sc_wordform_block_editor_form_html_delete( $postids = array(), $wordform_id = null ) {
			foreach ( $postids as $postID ) {
				$post = null;
				$post = get_post( $postID );
				if ( ! is_null( $post ) && $post ) {
					$allblocks = array();  // Reset for multiple post IDs
					$blocks    = array();  // Reset for multiple post IDs
					$blocks    = parse_blocks( $post->post_content );
					foreach ( $blocks as $key => $block ) {
						if ( SFTCY_Wordform::$wordform_custom_block_name == $block['blockName'] && isset( $block['attrs']['wordformid'] ) && $block['attrs']['wordformid'] == $wordform_id ) {
							$allblocks[] = null;
						} else {
							$allblocks[] = $block;
						}
					} // foreach
					$content     = serialize_blocks( $allblocks );
					$updatedPost = array(
						'ID'           => $postID,
						'post_content' => $content,
					);
					// Update post content
					// $updated_post_id = wp_update_post( $updatedPost, true );
					$updated_post_id = wp_update_post( $updatedPost );
					if ( is_wp_error( $updated_post_id ) ) {
						self::$output['postOnUpdateError'][ $postID ] = $updated_post_id->get_error_messages();
						self::$output['updateFailPostIds'][]          = $postID;
					} else {
						self::$output['updateSuccessPostIds'][]        = $postID;
						self::$output['wpUpdatePostStatus'][ $postID ] = 'success';
					}
				}
			} // foreach
		}


		/**
		 * Front end form submission by Users
		 * Sanitize - validate data
		 * Store form submission data
		 *
		 * @since 1.0.0
		 */
		public static function sc_wordform_created_form_submission() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			global $wpdb;
			self::$output     = array();
			$insert_data      = array();
			$sanitized_params = isset( $_POST['scWordFormPostData'] ) ? sanitize_text_field( urldecode_deep( $_POST['scWordFormPostData'] ) ) : '';
			wp_parse_str( $sanitized_params, $params );
			// print_r( $params );
			// exit();
			$wordform_id   = isset( $_POST['WordFormID'] ) ? sanitize_text_field( $_POST['WordFormID'] ) : '';
			$wordform_name = isset( $_POST['WordFormName'] ) ? sanitize_text_field( $_POST['WordFormName'] ) : '';
			if ( isset( $wordform_id ) && ! empty( $wordform_id ) && isset( $params['wordform'][ $wordform_id ] ) ) {
				// print_r( $params['wordform'][ $wordform_id ] );

				// Sanitize submission data
				$submission_sanitized_form_data = self::sc_wordform_front_end_submission_data_sanitize( $params['wordform'][ $wordform_id ] );
				// print_r( $submission_sanitized_form_data );

				SFTCY_Wordform_FormValidation::$sc_wordform_validation_data = array();
				SFTCY_Wordform_FormValidation::$wordform_id                 = $wordform_id;
				SFTCY_Wordform_FormValidation::sc_wordform_set_validation_messages();

				// Validate submission data
				foreach ( $submission_sanitized_form_data as $input_type => $input_data ) {
					switch ( $input_type ) {
						case 'text':
							SFTCY_Wordform_FormValidation::text_validation( $input_data );
							break;
						case 'number':
							SFTCY_Wordform_FormValidation::number_validation( $input_data );
							break;
						case 'textarea':
							SFTCY_Wordform_FormValidation::textarea_validation( $input_data );
							break;
						case 'radio':
							SFTCY_Wordform_FormValidation::radio_validation( $input_data );
							break;
						case 'checkbox':
							SFTCY_Wordform_FormValidation::checkbox_validation( $input_data );
							break;
						case 'select':
							SFTCY_Wordform_FormValidation::select_validation( $input_data );
							break;
						case 'email':
							SFTCY_Wordform_FormValidation::email_validation( $input_data );
							break;
						case 'captcha':
							SFTCY_Wordform_FormValidation::captcha_validation( $input_data );
							break;

					} // switch
				} // foreach

				// print_r( SFTCY_Wordform_FormValidation::$sc_wordform_validation_data );
				// exit();
				$validation_error_data = array();
				$validation_error_data = array_filter(
					SFTCY_Wordform_FormValidation::$sc_wordform_validation_data,
					function ( $validation_data ) {
						return ( $validation_data['validationStatus'] == 'error' );
					}
				);

				if ( array_filter( $validation_error_data ) ) {
					self::$output['status']              = 'fail';
					self::$output['failMsg']             = '<div class="sc-wordform-submisson-fail-msg-div-wrapper">' . __( 'Input validation error.', 'wordform' ) . '</div>';
					self::$output['validationAllData']   = SFTCY_Wordform_FormValidation::$sc_wordform_validation_data;
					self::$output['validationErrorData'] = $validation_error_data;
				} else {
					$wordform_submission_data_json       = wp_json_encode( $params['wordform'][ $wordform_id ] );
					$insert_data['form_id']              = $wordform_id;
					$insert_data['submission_data']      = $wordform_submission_data_json;
					$submission_user_info_arr            = self::sc_wordform_form_submission_users_data();
					$insert_data['submission_user_data'] = wp_json_encode( $submission_user_info_arr );
					$insert_record                       = $wpdb->insert( $wpdb->prefix . SFTCY_Wordform::$sc_wordform_submission_tbl, $insert_data );
					if ( isset( $insert_record ) && $insert_record ) {
						self::$output['status']               = 'success';
						self::$output['insertSubmissionData'] = true;
						// self::$output['successMsg']           = '<div class="sc-wordform-submission-success-msg-div-wrapper">We have received your message.</div>';
						self::$output['successMsg'] = '<div class="sc-wordform-submission-success-msg-div-wrapper">' . SFTCY_Wordform_FormValidation::$validation_form_submission_success_message . '</div>';

						// Send Email if true - on each form submission
						$insert_data['form_name'] = $wordform_name;
						SFTCY_Wordform::sc_wordform_send_email_on_form_submission_by_users( $insert_data );

						// Silently Delete Captcha value on Success everything
						if ( ! empty( SFTCY_Wordform_FormValidation::$wordform_captcha_session_name ) ) {
							self::$output['deleteUsedCaptcha'] = delete_transient( SFTCY_Wordform_FormValidation::$wordform_captcha_session_name );
						}
					} else {
						self::$output['status']               = 'fail';
						self::$output['insertSubmissionData'] = false;
						self::$output['failMsg']              = '<div class="sc-wordform-submisson-fail-msg-div-wrapper">' . __( 'Something went wrong, please try again.', 'wordform' ) . '</div>';
					}
				}
			}

			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Sanitize users submission data
		 * modify reference submission data to be sanitized
		 */
		public static function sc_wordform_front_end_submission_data_sanitize( &$submission_form_data ) {
			// print_r( $submission_form_data );
			// exit();

			if ( isset( $submission_form_data ) && array_filter( $submission_form_data ) ) {
				foreach ( $submission_form_data as $input_type => &$input_data ) {
					switch ( $input_type ) {
						case 'text':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'number':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'textarea':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_textarea_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'radio':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'checkbox':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'select':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'email':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_email( $data );
										},
										$input['values']
									);
								}
							}
							break;
						case 'captcha':
							foreach ( $input_data as $key => &$input ) {
								if ( isset( $input['values'] ) && array_filter( $input['values'] ) ) {
									$input['values'] = array_map(
										function ( $data ) {
											return sanitize_text_field( $data );
										},
										$input['values']
									);
								}
							}
							break;
					} // switch
				} // foreach
			}
			return $submission_form_data;
		}

		/**
		 * Collect visited user information data
		 * return - array
		 * since 1.0.0
		 */
		public static function sc_wordform_form_submission_users_data() {
			$user_visited_target_info                    = array();
			$user_visited_target_info['HTTP_HOST']       = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$user_visited_target_info['HTTP_USER_AGENT'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			$user_visited_target_info['SERVER_ADDR']     = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
			$user_visited_target_info['REMOTE_ADDR']     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$user_visited_target_info['HTTP_REFERER']    = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
			$user_visited_target_info['SERVER_NAME']     = isset( $_SERVER['SERVER_NAME'] ) ? esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';

			return $user_visited_target_info;
		}


		/**
		 * Ajax Request
		 * Request from Ajax datatable
		 * Users Submission form data load & display
		 * If form is deleted then we do not show users submission data of those deleted forms
		 *
		 * @since 1.0.0
		 * return - json
		 */
		public static function sc_wordform_users_submission_data_load() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			$query_results = SFTCY_Wordform::wordform_submission_data_table_inner_join_with_created_forms_table();
			if ( $query_results ) {
				$output_data = array();
				foreach ( $query_results as $data ) {
					$temp = array();

					$temp['formName'] = sanitize_text_field( $data['form_name'] );
					// $temp['formID']           = sanitize_text_field( $data['form_id'] );

					SFTCY_Wordform_FormSubmission::$sc_wordform_submission_array_data = json_decode( $data['submission_data'], true );
					$temp['submissionData'] = SFTCY_Wordform_FormSubmission::sc_wordform_process_submission_data();

					$date_create  = date_create( sanitize_text_field( $data['created_at'] ) );
					$temp['date'] = date_format( $date_create, 'j-M-y' );

					$output_data['data'][] = $temp;
				}
			}
			echo wp_json_encode( $output_data );
			wp_die();
		}


		/**
		 * Ajax Request
		 * Settings Menu - Validation messages data store
		 *
		 * @since 1.0.0
		 * return - json
		 */
		public static function sc_wordform_settings_menu_validation_tab_data_save() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			self::$output     = array();
			$sanitized_params = isset( $_POST['formData'] ) ? sanitize_text_field( urldecode_deep( $_POST['formData'] ) ) : '';
			wp_parse_str( $sanitized_params, $params );

			if ( isset( $params ) && array_filter( $params ) ) {
				foreach ( $params as $key => $val ) {
					$params[ $key ] = sanitize_text_field( $val );
				}

				global $wpdb;
				$table = $wpdb->prefix . SFTCY_Wordform::$sc_wordform_validation_messages_tbl;

				$validation_messages_data           = wp_json_encode( $params );
				$form_id                            = isset( $params['sc-wordform-validation-tab-selected-form-id'] ) ? sanitize_text_field( $params['sc-wordform-validation-tab-selected-form-id'] ) : '';
				$insert_data['form_id']             = $form_id;
				$insert_data['validation_messages'] = $validation_messages_data;
				$query_results                      = SFTCY_Wordform::sc_wordform_check_validation_messages_data_set_or_not( $form_id );
				// Update validation messages - if already exist
				if ( $query_results && array_filter( $query_results ) ) {
					$update_data['validation_messages'] = $validation_messages_data;
					$where_data['form_id']              = $form_id;
					$update_record                      = $wpdb->update( $table, $update_data, $where_data );
					if ( $update_record ) {
						self::$output['status']  = 'success';
						self::$output['Msg']     = self::$success_icon . ' ' . __( 'Updated success.', 'wordform' );
						self::$output['comment'] = 'Validation messages data updated for form ID: ' . $form_id;
					} else {
						self::$output['status']  = 'success';
						self::$output['Msg']     = self::$success_icon . ' ' . __( 'Updated success.', 'wordform' );
						self::$output['comment'] = 'Validation messages data not changes for form ID: ' . $form_id;
					}
				}
				// Insert
				else {
					$insert_record = $wpdb->insert( $table, $insert_data );
					if ( $insert_record ) {
						self::$output['status']  = 'success';
						self::$output['Msg']     = self::$success_icon . ' ' . __( 'Saved success.', 'wordform' );
						self::$output['comment'] = 'Validation messages data inserted successfully.';
					} else {
						self::$output['status']  = 'fail';
						self::$output['Msg']     = self::$error_icon . ' ' . __( 'Saved fail.', 'wordform' );
						self::$output['comment'] = 'Validation messages data inserted fail.';
					}
				}
			} // if ( isset( $params ) && array_filter( $params ) )
			else {
				self::$output['status']  = 'fail';
				self::$output['Msg']     = self::$error_icon . ' ' . __( 'Saved fail.', 'wordform' );
				self::$output['comment'] = 'Something went wrong.';
			}

			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Ajax Request
		 * Settings menu : Validation Tab
		 * Selected form validation messages save
		 *
		 * @since 1.0.0
		 * return - JSON
		 */
		public static function sc_wordform_settings_menu_validation_tab_selected_form_data_save() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			self::$output                     = array();
			$form_name                        = isset( $_POST['params']['FormName'] ) && ! empty( $_POST['params']['FormName'] ) ? sanitize_text_field( $_POST['params']['FormName'] ) : '';
			$form_id                          = isset( $_POST['params']['FormID'] ) && ! empty( $_POST['params']['FormID'] ) ? sanitize_text_field( $_POST['params']['FormID'] ) : '';
			self::$output['selectedFormName'] = $form_name;
			self::$output['selectedFormID']   = $form_id;

			if ( $form_id ) {
				// Get set validation messages if found else default validation messages
				$validation_messages = SFTCY_Wordform::sc_wordform_get_validation_messages_data_by_formid( $form_id );
				ob_start();
				include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/settings_callback_validation_tab_page_template.php';
				self::$output['htmlTemplate'] = ob_get_clean();
				self::$output['status']       = 'success';
				self::$output['comment']      = 'Validation messages checked for form ID [' . $form_id . '].';
			} else {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Valid Form ID missing.';
			}

			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Ajax - Request
		 * All Forms : Delete form
		 * Delete also added form from posts - pages attached with block inserter
		 * Also delete wordform when not attached with any Post or Page
		 * return - json
		 */
		public static function sc_wordform_all_forms_page_delete_form() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			self::$output = array();
			$wordform_id  = isset( $_POST['params']['wordformID'] ) && ! empty( $_POST['params']['wordformID'] ) ? sanitize_text_field( $_POST['params']['wordformID'] ) : '';
			$form_name    = isset( $_POST['params']['wordformName'] ) && ! empty( $_POST['params']['wordformName'] ) ? sanitize_text_field( $_POST['params']['wordformName'] ) : '';
			
			global $wpdb;
			$table         = $wpdb->prefix . SFTCY_Wordform::$sc_wordform_tbl;			

			// Get only attached block posts / pages of any status
			$args  = array(
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'any',
				's'              => SFTCY_Wordform::$wordform_custom_block_name,
				'posts_per_page' => -1,
			);
			$query = new WP_Query( $args );						
			
			// Filtering posts / pages Ids only attached blocks based on specific target attributes 'wordformid'
			if ( isset( $wordform_id ) && ! empty( $wordform_id ) ) {
				$target_wordform_attached_post_ids = array();
				foreach ( $query->posts as $post ) {
					if ( ! is_null( $post->post_content ) && $post->post_content ) {
						$blocks = array();  // Reset
						$blocks = parse_blocks( $post->post_content );
						foreach ( $blocks as $key => $block ) {
							if ( SFTCY_Wordform::$wordform_custom_block_name == $block['blockName']
								&& isset( $block['attrs']['wordformid'] )
								&& $block['attrs']['wordformid'] == $wordform_id ) {
								$target_wordform_attached_post_ids[] = $post->ID;
							}
						} // foreach
					}
				} // foreach
				
				// When Wordform attached with any Post or page - Delete attached worform contents only from attached post Ids
				if ( isset( $target_wordform_attached_post_ids ) && array_filter( $target_wordform_attached_post_ids ) ) {
					// Update attached form contents of Posts / Pages
					self::$output['updateSuccessPostIds'] = array();
					self::$output['updateFailPostIds']    = array();
					self::sc_wordform_block_editor_form_html_delete( $target_wordform_attached_post_ids, $wordform_id );
					// Delete from DB
					if ( count( self::$output['updateSuccessPostIds'] ) == count( $target_wordform_attached_post_ids ) ) {
						$delete_record = $wpdb->delete( $table, array( 'form_id' => $wordform_id ), array( '%s' ) );
						if ( is_wp_error( $delete_record ) ) {
							self::$output['wpdbDeleteError'] = $delete_record->get_error_message();
							self::$output['status']          = 'fail';
							self::$output['comment']         = $wordform_id . ' deleted fail.';
						} else {
							self::$output['status']  = 'success';
							self::$output['comment'] = $wordform_id . ' deleted successfully.';
						}
					} else {
						self::$output['status'][ $postID ]  = 'fail';
						self::$output['comment'][ $postID ] = $wordform_id . ' attached form contents of all Post/pages were not updated.';
					}
				}								
				// When Wordform not yet attached with any Post or Page
				elseif ( ! array_filter( $target_wordform_attached_post_ids ) ) {				    
						$delete_record = $wpdb->delete( $table, array( 'form_id' => $wordform_id ), array( '%s' ) );				    
						if ( is_wp_error( $delete_record ) ) {
							self::$output['wpdbDeleteError'] = $delete_record->get_error_message();
							self::$output['status']          = 'fail';
							self::$output['comment']         = $wordform_id . ' deleted fail.';
						} else {
							self::$output['status']  = 'success';
							self::$output['comment'] = $wordform_id . ' deleted successfully.';
						}				
				}				
			} 
			else {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Valid Form ID missing | This form ID not attached with any post or page.';
			}
 												
			
			// Restore original Post Data
			wp_reset_postdata();

			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Ajax Request
		 * Setting Menu : General Tab
		 * Save General Tab Form data
		 */
		public static function sc_wordform_settings_general_tab_form() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			self::$output     = array();
			$sanitized_params = isset( $_POST['params']['formData'] ) ? sanitize_text_field( urldecode_deep( $_POST['params']['formData'] ) ) : '';
			wp_parse_str( $sanitized_params, $params );
			// print_r($params);
			// exit();

			// Form Width
			if ( isset( $params['sc-wordform-form-width'] ) && ! empty( $params['sc-wordform-form-width'] ) ) {
				$wordform_form_width = sanitize_text_field( $params['sc-wordform-form-width'] );
			} else {
				$wordform_form_width = 40;
			}

			// Submit Button Background Color
			if ( isset( $params['sc-wordform-form-submit-button-background-color'] ) && ! empty( $params['sc-wordform-form-submit-button-background-color'] ) ) {
				if ( preg_match( '/^#[a-f0-9]{6}$/i', $params['sc-wordform-form-submit-button-background-color'] ) ) {
					$submit_background_hex_color = sanitize_hex_color( $params['sc-wordform-form-submit-button-background-color'] );
				} else {
					self::$output['status']  = 'fail';
					self::$output['comment'] = 'Background HEX Color code required.';
					self::$output['failMsg'] = self::$error_icon . ' ' . __( 'Valid background hex color required.', 'wordform' );
				}
			}
			// Submit Button Background Hover Color
			if ( isset( $params['sc-wordform-form-submit-button-background-hover-color'] ) && ! empty( $params['sc-wordform-form-submit-button-background-hover-color'] ) ) {
				if ( preg_match( '/^#[a-f0-9]{6}$/i', $params['sc-wordform-form-submit-button-background-hover-color'] ) ) {
					$submit_background_hover_hex_color = sanitize_hex_color( $params['sc-wordform-form-submit-button-background-hover-color'] );
				} else {
					self::$output['status']  = 'fail';
					self::$output['comment'] = 'Background HEX Hover Color code required.';
					self::$output['failMsg'] = self::$error_icon . ' ' . __( 'Valid background hex hover color required.', 'wordform' );
				}
			}

			// Submit Button Font Color
			if ( isset( $params['sc-wordform-form-submit-button-font-color'] ) && ! empty( $params['sc-wordform-form-submit-button-font-color'] ) ) {
				if ( preg_match( '/^#[a-f0-9]{6}$/i', $params['sc-wordform-form-submit-button-font-color'] ) ) {
					$submit_button_font_hex_color = sanitize_hex_color( $params['sc-wordform-form-submit-button-font-color'] );
				} else {
					self::$output['status']  = 'fail';
					self::$output['comment'] = 'Submit Button Font HEX Color code required.';
					self::$output['failMsg'] = self::$error_icon . ' ' . __( 'Valid font hex color required.', 'wordform' );
				}
			}

			// Submit Button Font Weight
			if ( isset( $params['sc-wordform-form-submit-button-font-weight'] ) && ! empty( $params['sc-wordform-form-submit-button-font-weight'] ) ) {
				$submit_button_font_weight = sanitize_text_field( $params['sc-wordform-form-submit-button-font-weight'] );
			} else {
				self::$output['status']  = 'fail';
				self::$output['comment'] = 'Submit Button Font weight value required.';
				self::$output['failMsg'] = self::$error_icon . ' ' . __( 'Check font-weight value.', 'wordform' );
			}

			// Submit Button Font size
			if ( isset( $params['sc-wordform-form-submit-button-text-size'] ) && ! empty( $params['sc-wordform-form-submit-button-text-size'] ) ) {
				$submit_button_font_size = sanitize_text_field( $params['sc-wordform-form-submit-button-text-size'] );
			} else {
				$submit_button_font_size = 16;
			}

			// Submit Button Padding (Top-Bottom)
			if ( isset( $params['sc-wordform-form-submit-button-padding-top-bottom'] ) && ! empty( $params['sc-wordform-form-submit-button-padding-top-bottom'] ) ) {
				$submit_button_padding_top_bottom = sanitize_text_field( $params['sc-wordform-form-submit-button-padding-top-bottom'] );
			} else {
				$submit_button_padding_top_bottom = 8;
			}

			// Submit Button Padding (Left-Right)
			if ( isset( $params['sc-wordform-form-submit-button-padding-left-right'] ) && ! empty( $params['sc-wordform-form-submit-button-padding-left-right'] ) ) {
				$submit_button_padding_left_right = sanitize_text_field( $params['sc-wordform-form-submit-button-padding-left-right'] );
			} else {
				$submit_button_padding_left_right = 25;
			}

			// Send Email
			if ( isset( $params['sc-wordform-form-submission-send-email'] ) && $params['sc-wordform-form-submission-send-email'] == 'on' ) {
				$form_submit_send_email = 'yes';
			} else {
				$form_submit_send_email = 'no';
			}

			// Hide Form
			if ( isset( $params['sc-wordform-form-submission-hide-form'] ) && $params['sc-wordform-form-submission-hide-form'] == 'on' ) {
				$form_submit_hide_form = 'yes';
			} else {
				$form_submit_hide_form = 'no';
			}

			// If no error
			if ( ! isset( self::$output['status'] ) || self::$output['status'] != 'fail' ) {
				$general_tab_options = array(
					'wordform_form_width'                  => $wordform_form_width,
					'submit_button_background_color'       => $submit_background_hex_color,
					'submit_button_background_hover_color' => $submit_background_hover_hex_color,
					'submit_button_font_color'             => $submit_button_font_hex_color,
					'submit_button_font_size'              => $submit_button_font_size,
					'submit_button_padding_top_bottom'     => $submit_button_padding_top_bottom,
					'submit_button_padding_left_right'     => $submit_button_padding_left_right,
					'submit_button_font_weight'            => $submit_button_font_weight,
					'send_email_on_form_submission'        => $form_submit_send_email,
					'hide_form_on_form_submission'         => $form_submit_hide_form,
				);
				if ( update_option( 'sftcy_wordform_settings_menu_general_tab_info', $general_tab_options ) ) {
					self::$output['status']     = 'success';
					self::$output['comment']    = 'Hex Colors are valid.';
					self::$output['successMsg'] = self::$success_icon . ' ' . __( 'Updated success.', 'wordform' );
				} else {
					self::$output['status']     = 'success';
					self::$output['comment']    = 'Nothing changes.';
					self::$output['successMsg'] = self::$success_icon . ' ' . __( 'Nothing changes.', 'wordform' );
				}
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}

		/**
		 * Generate Captcha Image with Captcha data
		 * return - json
		 */
		public static function wordform_generate_captcha_image() {
			check_ajax_referer( 'scwordform_wpnonce', 'security' );
			self::$output        = array();
			$captcha_create_info = SFTCY_Wordform_Captcha::wordform_create_captcha_image();
			if ( isset( $captcha_create_info['status'] ) && $captcha_create_info['status'] === 'success' ) {
				self::$output['imageBase64Src'] = $captcha_create_info['imageBase64Src'];
				self::$output['image']          = '<img src="' . $captcha_create_info['imageBase64Src'] . '" alt="Captcha Image" />';
				self::$output['status']         = 'success';
				self::$output['comment']        = 'Captcha image created successfully.';
			} else {
				self::$output['status']     = 'fail';
				self::$output['failDetail'] = $captcha_create_info;
				self::$output['comment']    = 'Captcha image failed to create.';
			}
			echo wp_json_encode( self::$output );
			wp_die();
		}
	} // End Class
}
