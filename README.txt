=== F2 Tumblr Widget ===

Contributors: fsquared
Tags: widget, tumblr, feed
Requires at least: 3.3
Tested up to: 4.7.2
Stable tag: 0.2.8
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

= Why do I see strange characters instead of quotes? =

If your tumblr posts contain 'smart quotes', these can sometimes be rendered
as strange characters to some users. The plugin will attempt to convert these
to plain quotes, unless the 'Replace "special" characters' option is unticked.

= My blog has an SSL certificate (https://) and I'm getting 'unsecured content' warnings? =

By default, this widget pulls data from Tumblr over http; this results in
displayed images also using this protocol. If you have an SSL certificate
installed on your blog, you may see warnings from your browser about 'unsecured
content' which refers to the images from Tumblr.

If you change your Tumblr URL setting to be preceded by 'https://', the widget
will access Tumblr over https and the resulting images will also be served
securely.

This will one day become the default approach, but for now is left up to the
user as it can potentially cause problems on badly configured web servers.

== Screenshots ==

1. Widget configuration

== Changelog ==

= 0.2.8 =
* Will how honour the HTTP protocol of the source tumblr, if provided. This
  means that if you have an SSL certificate on your blog, you can avoid
  the widget generating 'unsecured content' warnings by providing your Tumblr
  URL with a 'https://' prefix.

= 0.2.7 =
* Fixed a bug in the caching code, which resulted in excessive calls to 
  Tumblr.

= 0.2.6 =
* Added a new Content Tpe of 'Media Only', which shows photos / videos without
  displaying post titles.
* Added a new Display Type of 'Horizontal List' - this will show posts side
  by side if the widget has sufficient space. Works best with media-only
  posts.

= 0.2.5 =
* Fixed some bad comments in the stylesheet which prevented some styling from
  getting properly applied. Thanks to jchriscook for spotting this!

= 0.2.4 =
* Added an option to replace "special" characters such a smart quotes, which
  can cause display problems for some users. This is on by default, but can
  be unchecked if for some reason desired.
* Added a 'Title And Media' content type option; this shows just the title
  and any media - essentially it's 'Post Excerpt' but with no text. 

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
