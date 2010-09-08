=== Stealth Update ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: post, update, post_modified, latest, publish, edit, coffee2code
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 2.0
Version: 2.0

Adds the ability to update a post without updating the post_modified timestamp for the post.


== Description ==

Adds the ability to update a post without updating the post_modified timestamp for the post.

WordPress maintains a post field called post_modified which tracks the date the post was last edited.  This value is used by feeds to indicate the latest update to each post or to report the latest updated post.  Plugins and other manual uses of some of WordPress's template tags may also reference the post modification date to call attention to posts that have been updated.  However, if an update is such that you don't want it to be recorded in the post_modified date field (perhaps due to the update being a small formattting/editing change or fix, or if you just want to keep quiet about an update), then this plugin is for you.

This plugin adds a "Stealth udpate" checkbox to the "Publish" box of any post or pages "Edit Post" admin page.

Note: The fact that a post was stealth updated is not retained once the update completes.  You must re-check the "Stealth Update" checkbox for subsequent updates that you wish to also be stealthy.


== Installation ==

1. Unzip `stealth-update.zip` inside the `/wp-content/plugins/` directory (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Click the 'Stealth update' checkbox when updating a post to prevent the date of the update from being saved for the post.


== Screenshots ==

1. A screenshot of the 'Publish' sidebar box on the write/edit post admin page.  The 'Stealth update?' checkbox is integrated alongside the existing fields.
2. A screenshot of the 'Stealth Update?' checkbox displaying help text when hovering over the checkbox.


== Changelog ==

= 2.0 =
* Add checkbox labeled 'Stealth update?' to Publish meta_box rather than requiring direct use of custom fields
* Add class of 'c2c-stealth-update' to admin UI div containing checkbox
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

= 2.0 =
Recommended major update! Highlights: verified WP 3.0 compatibility.