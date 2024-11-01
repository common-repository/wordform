<?php
/**
 * Plugin Name: WordForm
 * Plugin URI:  http://softcoy.com/wordform/
 * Description: WordForm - Drag & drop easy forms builder for WordPress sites. Block enabled WordForm plugin to add / attach your created Forms with your page / post easily.
 * Version:     1.2.1
 * Author:      softcoy
 * Author URI:  https://softcoy.com/
 * License:     GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordform
 * Domain Path: /languages
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

	Copyright 2024 softcoy.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'SFTCY_WORDFORM_VERSION' ) ) {
	define( 'SFTCY_WORDFORM_VERSION', '1.2.1' );
}
if ( ! defined( 'SFTCY_WORDFORM_MINIMUM_PHP_VERSION' ) ) {
	define( 'SFTCY_WORDFORM_MINIMUM_PHP_VERSION', '7.2' );
}
if ( ! defined( 'SFTCY_WORDFORM_MINIMUM_WP_VERSION' ) ) {
	define( 'SFTCY_WORDFORM_MINIMUM_WP_VERSION', '6.0' );
}
if ( ! defined( 'SFTCY_WORDFORM_PLUGIN_DIR' ) ) {
	define( 'SFTCY_WORDFORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'SFTCY_WORDFORM_PLUGIN_INC' ) ) {
	define( 'SFTCY_WORDFORM_PLUGIN_INC', plugin_dir_path( __FILE__ ) . '/includes/' );
}


if ( ! function_exists( 'sftcy_wordform_autoloader' ) ) {

	/**
	 * Autoload Class files
	 *
	 * @param $sc_class - class name
	 * @since 1.0.0
	 */
	function sftcy_wordform_autoloader( $sc_class ) {
		$sc_class  = 'class-' . trim( $sc_class );
		$classfile = SFTCY_WORDFORM_PLUGIN_INC . strtolower( str_replace( '_', '-', $sc_class ) ) . '.php';
		if ( file_exists( $classfile ) ) {
			require_once $classfile;
		}
	}
}
	spl_autoload_register( 'sftcy_wordform_autoloader' );
	new SFTCY_Wordform_Autoloader();
	register_activation_hook( __FILE__, array( 'SFTCY_Wordform', 'activate' ) );
