<?php
/**
 * @package Stealth_Update
 * @author Scott Reilly
 * @version 2.2
 */
/*
Plugin Name: Stealth Update
Version: 2.2
Plugin URI: http://coffee2code.com/wp-plugins/stealth-update/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: stealth-update
Description: Adds the ability to update a post without updating the post_modified timestamp for the post.

Compatible with WordPress 2.9+, 3.0+, 3.1+, 3.2+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/stealth-update/

TODO:
	* Update screenshots for WP 3.2

*/

/*
Copyright (c) 2009-2011 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( ! class_exists( 'c2c_StealthUpdate' ) ) :

class c2c_StealthUpdate {

	private static $field             = 'stealth_update';
	private static $meta_key          = '_stealth-update'; // Filterable via 'stealth_update_meta_key' filter
	private static $prev_field        = 'previous_last_modified';
	private static $textdomain        = 'stealth-update';
	private static $textdomain_subdir = 'lang';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * The stealth update capability is only exposed for non-draft posts/pages.
	 */
	public static function do_init() {
		global $pagenow, $post;
		self::load_textdomain();
		self::$meta_key = esc_attr( apply_filters( 'stealth_update_meta_key', self::$meta_key ) );
//		if ( is_admin() && ( 'post.php' == $pagenow ) && !empty( $post->ID ) && ( 'draft' != $post->post_status ) )
		if ( is_admin() && ( 'post.php' == $pagenow ) && empty( $post ) )
			add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'add_ui' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ), 2, 2 );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		$subdir = empty( self::$textdomain_subdir ) ? '' : ( '/' . self::$textdomain_subdir );
		load_plugin_textdomain( self::$textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Draws the UI to prompt user if stealth update should be present for the post.
	 *
	 * @since 2.0
	 *
	 * @return void (Text is echoed.)
	 */
	public static function add_ui() {
		global $post;

		if ( apply_filters( 'c2c_stealth_update_default', false, $post ) )
			$value = '1';
		else
			$value = get_post_meta( $post->ID, self::$meta_key, true );
		$checked = checked( $value, '1', false );

		echo "<div class='misc-pub-section'><label class='selectit c2c-stealth-update' for='" . self::$field . "' title='";
		esc_attr_e( 'If checked, the post\'s modification date won\'t be updated to reflect the update when the post is saved.', self::$textdomain );
		echo "'>\n";
		echo "<input type='hidden' name='" . self::$prev_field . "' value='" . esc_attr( $post->post_modified ) . "' />\n";
		echo "<input id='" . self::$field . "' type='checkbox' $checked value='1' name='" . self::$field . "' />\n";
		_e( 'Stealth update?', self::$textdomain );
		echo '</label></div>' . "\n";
	}

	/**
	 * On post insert, save the value of stealth update custom field and possibly revert post_modified date
	 *
	 * @since 2.0
	 *
	 * @param array $data Data
	 * @param array $postarr Array of post fields and values for post being saved
	 * @return array The unmodified $data
	 */
	public static function wp_insert_post_data( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) && ( 'revision' != $postarr['post_type'] ) &&
			! ( isset( $_POST['action'] ) && 'inline-save' == $_POST['action'] ) ) {
			// Update the value of the stealth update custom field
			$new_value = isset( $postarr[self::$field] ) ? $postarr[self::$field] : '';
			update_post_meta( $postarr['ID'], self::$meta_key, $new_value );

			// Possibly revert the post_modified date to the previous post_modified date
			if ( isset( $postarr[self::$field] ) && $postarr[self::$field] && isset( $postarr[self::$prev_field] ) ) {
				$data['post_modified'] = $postarr[self::$prev_field];
				$data['post_modified_gmt'] = get_gmt_from_date( $data['post_modified'] );
			}
		}
		return $data;
	}

} // end c2c_StealthUpdate

c2c_StealthUpdate::init();

endif; // end if !class_exists()

?>