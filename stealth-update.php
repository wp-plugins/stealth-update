<?php
/**
 * Plugin Name: Stealth Update
 * Version:     2.4.2
 * Plugin URI:  http://coffee2code.com/wp-plugins/stealth-update/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com
 * Text Domain: stealth-update
 * Domain Path: /lang/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Adds the ability to update a post without having WordPress automatically update the post's post_modified timestamp.
 *
 * Compatible with WordPress 3.6+ through 4.1+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/stealth-update/
 *
 * @package Stealth_Update
 * @author Scott Reilly
 * @version 2.4.2
 */

/*
TODO:
	* Make it work for direct, non-UI calls to wp_update_post()
	* Add class function get_meta_key() as getter for meta_key and
	  filter on request rather than init to allow late filtering
*/

/*
	Copyright (c) 2009-2015 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_StealthUpdate' ) ) :

class c2c_StealthUpdate {

	private static $field      = 'stealth_update';
	private static $meta_key   = '_stealth-update'; // Filterable via 'stealth_update_meta_key' filter
	private static $prev_field = 'previous_last_modified';

	/**
	 * Returns version of the plugin.
	 *
	 * @since 2.2.1
	 */
	public static function version() {
		return '2.4.2';
	}

	/**
	 * Initializer.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * The stealth update capability is only exposed for non-draft posts/pages.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_stealth_update_meta_key' with default meta key name
	 */
	public static function do_init() {
		global $pagenow, $post;

		// Load textdomain
		load_plugin_textdomain( 'stealth-update', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'lang' );

		// Deprecated as of 2.3.
		$meta_key = esc_attr( apply_filters( 'stealth_update_meta_key', self::$meta_key ) );

		// Apply custom filter to obtain meta key name.
		$meta_key = esc_attr( apply_filters( 'c2c_stealth_update_meta_key', $meta_key ) );

		// Only override the meta key name if one was specified. Otherwise the
		// default remains (since a meta key is necessary)
		if ( ! empty( $meta_key ) ) {
			self::$meta_key = $meta_key;
		}

		// Register hooks
//		if ( is_admin() && ( 'post.php' == $pagenow ) && !empty( $post->ID ) && ( 'draft' != $post->post_status ) )
		if ( is_admin() && ( 'post.php' == $pagenow ) && empty( $post ) ) {
			add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		}
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data',   array( __CLASS__, 'wp_insert_post_data' ), 2, 2 );
	}

	/**
	 * Draws the UI to prompt user if stealth update should be present for the post.
	 *
	 * @since 2.0
	 * @uses apply_filters() Calls 'c2c_stealth_update_default' with stealth publish state default (false)
	 */
	public static function add_ui() {
		global $post;

		if ( apply_filters( 'c2c_stealth_update_default', false, $post ) ) {
			$value = '1';
		} else {
			$value = get_post_meta( $post->ID, self::$meta_key, true );
		}
		$checked = checked( $value, '1', false );

		echo "<div class='misc-pub-section'><label class='selectit c2c-stealth-update' for='" . self::$field . "' title='";
		esc_attr_e( 'If checked, the post\'s modification date won\'t be updated to reflect the update when the post is saved.', 'stealth-update' );
		echo "'>\n";
		echo "<input type='hidden' name='" . self::$prev_field . "' value='" . esc_attr( $post->post_modified ) . "' />\n";
		echo "<input id='" . self::$field . "' type='checkbox' $checked value='1' name='" . self::$field . "' />\n";
		_e( 'Stealth update?', 'stealth-update' );
		echo '</label></div>' . "\n";
	}

	/**
	 * On post insert, save the value of stealth update custom field and possibly revert post_modified date
	 *
	 * @since 2.0
	 *
	 * @param  array $data    Data
	 * @param  array $postarr Array of post fields and values for post being saved
	 * @return array The unmodified $data
	 */
	public static function wp_insert_post_data( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) && ( 'revision' != $postarr['post_type'] ) ) {
			// Update the value of the stealth update custom field
			$new_value = isset( $postarr[ self::$field ] ) ? $postarr[ self::$field ] : '';
			update_post_meta( $postarr['ID'], self::$meta_key, $new_value );

			// Possibly revert the post_modified date to the previous post_modified date
			if ( isset( $postarr[ self::$field ] ) && $postarr[ self::$field ] && isset( $postarr[ self::$prev_field ] ) ) {
				$data['post_modified']     = $postarr[ self::$prev_field ];
				$data['post_modified_gmt'] = get_gmt_from_date( $data['post_modified'] );
			}
		}
		return $data;
	}

} // end c2c_StealthUpdate

c2c_StealthUpdate::init();

endif; // end if !class_exists()
