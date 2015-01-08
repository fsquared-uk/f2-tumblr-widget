=== F2 Tumblr Widget ===

Contributors: fsquared
Tags: widget, tumblr, feed
Requires at least: 3.3
Tested up to: 4.1
Stable tag: 0.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget displays recent posts from a tumblr blog.

== Description ==

The F2 Tumblr Widget displays recent posts from the provided tumblr blog.

It allows the user to select how many posts to display, to restrict which 
posts are shown by type and tag, and to render the posts as either a list 
or a slideshow.

Posts can be displayed in full, as title only, or with an excerpt. Photo,
video and audio posts will have the media displayed when the display type
is not 'title only'.

The audio player in posts can now be automatically resized to match the 
selected media width - this is enabled by default, but can be deselected
in the widget setup.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `f2-tumblr` folder to the `/wp-content/plugins/` directory, or install directly through the 'Add New' option in the 'Plugins' menu in WordPress
2. Activate the plugin through the 'Plugins' menu
3. Include the `F2 Tumblr Widget` widget on your pages through the 'Appearance' menu

== Frequently Asked Questions ==

= How is the title for a post determined? =

Tumblr does not allow all post types to have a formal title defined. For
those post types (such as photos), either the first HTML header in the caption
will be used (h1 to h3) or, if none can be found, the first sentence.

== Screenshots ==

1. Widget configuration

== Changelog ==

= 0.2.3 =
* Bug fix, to prevent the widget failing when processing a Tumblr photo post
  that contains no caption.
* Moved the widget script loading into the footer.

= 0.2.2 =
* Added the option to adjust the width of the player in audio posts to match
  the chosen media width. 

= 0.2.1 =
* Added CSS to ensure that media posts with short text are properly separated.
* Improved handling of the provided Tumblr URL to be less picky.

= 0.2.0 =
* First public release.

= 0.1.0 =
* Internal version.

== Upgrade Notice ==
