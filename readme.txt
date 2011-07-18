=== Stealth Update ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: post, update, post_modified, latest, publish, edit, coffee2code
Requires at least: 2.9
Tested up to: 3.2.1
Stable tag: 2.2
Version: 2.2

Adds the ability to update a post without updating the post_modified timestamp for the post.


== Description ==

Adds the ability to update a post without updating the post_modified timestamp for the post.

WordPress maintains a post field called post_modified which tracks the date the post was last edited.  This value is used by feeds to indicate the latest update to each post or to report the latest updated post.  Plugins and other manual uses of some of WordPress's template tags may also reference the post modification date to call attention to posts that have been updated.  However, if an update is such that you don't want it to be recorded in the post_modified date field (perhaps due to the update being a small formatting/editing change or fix, or if you just want to keep quiet about an update), then this plugin is for you.

This plugin adds a "Stealth update?" checkbox to the "Publish" box of any post or pages "Edit Post" admin page.

Note: The fact that a post was stealth updated is not retained once the update completes.  You must re-check the "Stealth update?" checkbox for subsequent updates that you wish to also be stealthy (unless you configure the checkbox to be checked by default -- see FAQ).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/stealth-update/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `stealth-update.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Click the 'Stealth update?' checkbox when updating a post to prevent the date of the update from being saved for the post.


== Screenshots ==

1. A screenshot of the 'Publish' sidebar box on the Edit Post admin page.  The 'Stealth update?' checkbox is integrated alongside the existing fields.
2. A screenshot of the 'Stealth update?' checkbox displaying help text when hovering over the checkbox.


== Frequently Asked Questions ==

= Why would I want to stealth update a post? =

This update dates for posts are used by feeds to indicate the latest update to each post or to report the latest updated post.  Plugins and other manual uses of some of WordPress's template tags may also reference the post modification date to call attention to posts that have been updated.  However, if an update is such that you don't want it to be recorded in the post_modified date field (perhaps due to the update being a small formatting/editing change or fix, or if you just want to keep quiet about an update), then this plugin is for you.

= Can I have the checkbox checked by default? =

Yes. See the Filters section (under Other Notes) and look for the example using the 'c2c_stealth_update_default' filter. You'll have to put that code into your active theme's functions.php file.


== Filters ==

The plugin is further customizable via two filters. Typically, these customizations would be put into your active theme's functions.php file, or used by another plugin.

= stealth_update_meta_key (filter) =

The 'stealth_update_meta_key' filter allows you to override the name of the custom field key used by the plugin to store a post's stealth update status.  This isn't a common need.

Arguments:

* $custom_field_key (string): The custom field key to be used by the plugin.  By default this is '_stealth-update'.

Example:

`
add_filter( 'stealth_update_meta_key', 'override_stealth_update_key' );
function override_stealth_update_key( $custom_field_key ) {
	return '_my_custom_stealth-update';
}
`

= c2c_stealth_update_default (filter) =

The 'c2c_stealth_update_default' filter allows you to override the default state of the 'Stealth Update?' checkbox.

Arguments:

* $state (boolean): The default state of the checkbox. By default this is false.
* $post (WP_Post): The post currently being created/edited.

Example:

`
// Have the Stealth Update? checkbox checked by default.
add_filter( 'c2c_stealth_update_default', '__return_true' );
`


== Changelog ==

= 2.2 =
* Add filter 'c2c_stealth_update_default' to allow configuring checkbox to be checked by default
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

= 2.1 =
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public static and class variables private static
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 2.0.2 =
* Bugfix for auto-save losing value of stealth update status

= 2.0.1 =
* Define Text Domain plugin header
* Supply textdomain to localization functions
* Rename class from 'StealthUpdate' to 'c2c_StealthUpdate'
* Add Frequently Asked Questions and Filters sections to readme.txt
* Readme updates
* Add .pot file

= 2.0 =
* Add checkbox labeled 'Stealth update?' to Publish meta_box rather than requiring direct use of custom fields
* Add class of 'c2c-stealth-update' to admin UI div containing checkbox
* Add filter 'stealth_update_meta_key' to allow overriding custom field key name
* Re-implemented entire approach
* Remove functions add_js(), admin_menu(), add_meta_box()
* Store plugin instance in global variable, $c2c_stealth_update, to allow for external manipulation
* Full support for localization
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add PHPDoc documentation
* Note compatibility with WP 3.0+
* Drop compatibility with versions of WP older than 2.9
* Update screenshots
* Update copyright date
* Add package info to top of plugin file
* Add Changelog and Upgrade Notice sections to readme.txt
* First commit to WP plugin repository

= 1.0 =
* Initial release


== Upgrade Notice ==

= 2.2 =
Minor update: added new filter to allow making checkbox checked by default; noted compatibility through WP 3.2+

= 2.1 =
Minor update: implementation changes; noted compatibility with WP 3.1+ and updated copyright date.

= 2.0.2 =
Recommended bugfix release.  Fixes bug where auto-save can lose value of stealth update status.

= 2.0.1 =
Recommended major update! Highlights: re-implemented; add class for CSS customizations; full localization support; verified WP 3.0 compatibility; dropped compatibility with version of WP older than 2.9.
