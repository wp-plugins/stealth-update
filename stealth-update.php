<?php
/**
 * @package Stealth_Update
 * @author Scott Reilly
 * @version 2.0.1
 */
/*
Plugin Name: Stealth Update
Version: 2.0.1
Plugin URI: http://coffee2code.com/wp-plugins/stealth-update/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: stealth-update
Description: Adds the ability to update a post without updating the post_modified timestamp for the post.

Compatible with WordPress 2.9+, 3.0+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/stealth-update/

*/

/*
Copyright (c) 2009-2010 by Scott Reilly (aka coffee2code)

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

if ( !class_exists( 'c2c_StealthUpdate' ) ) :

class c2c_StealthUpdate {

	var $field = 'stealth_update';
	var $meta_key = '_stealth-update'; // Filterable via 'stealth_update_meta_key' filter
	var $prev_field = 'previous_last_modified';
	var $textdomain = 'stealth-update';
	var $textdomain_subdir = 'lang';

	function c2c_StealthUpdate() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * The stealth update capability is only exposed for non-draft posts/pages.
	 */
	function init() {
		global $pagenow, $post;
		$this->load_textdomain();
		$this->meta_key = esc_attr( apply_filters( 'stealth_update_meta_key', $this->meta_key ) );
//		if ( is_admin() && ( 'post.php' == $pagenow ) && !empty( $post->ID ) && ( 'draft' != $post->post_status ) )
			add_action( 'post_submitbox_misc_actions', array( &$this, 'add_ui' ) );
		add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post_data' ), 2, 2 );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	function load_textdomain() {
		$subdir = empty( $this->textdomain_subdir ) ? '' : '/'.$this->textdomain_subdir;
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Draws the UI to prompt user if stealth update should be present for the post.
	 *
	 * @since 2.0
	 *
	 * @return void (Text is echoed.)
	 */
	function add_ui() {
		global $post;
		$value = get_post_meta( $post->ID, $this->meta_key, true );
		$checked = checked( $value, '1', false );
		echo "<div class='misc-pub-section'><label class='selectit c2c-stealth-update' for='{$this->field}' title='";
		esc_attr_e( 'If checked, the post\'s modification date won\'t be updated to reflect the update when the post is saved.', $this->textdomain );
		echo "'>\n";
		echo "<input type='hidden' name='{$this->prev_field}' value='" . esc_attr( $post->post_modified ) . "' />\n";
		echo "<input id='{$this->field}' type='checkbox' $checked value='1' name='{$this->field}' />\n";
		_e( 'Stealth update?', $this->textdomain );
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
	function wp_insert_post_data( $data, $postarr ) {
		// Update the value of the stealth update custom field
		$new_value = isset( $postarr[$this->field] ) ? $postarr[$this->field] : '';
		update_post_meta( $postarr['ID'], $this->meta_key, $new_value );

		// Possibly revert the post_modified date to the previous post_modified date
		if ( isset( $postarr[$this->field] ) && $postarr[$this->field] && isset( $postarr[$this->prev_field] ) ) {
			$data['post_modified'] = $postarr[$this->prev_field];
			$data['post_modified_gmt'] = get_gmt_from_date( $data['post_modified'] );
		}

		return $data;
	}

} // end c2c_StealthUpdate

$GLOBALS['c2c_stealth_update'] = new c2c_StealthUpdate();

endif; // end if !class_exists()

?>