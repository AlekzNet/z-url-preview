=== Z-URL Preview ===
Tags: link preview, post, excerpt, Facebook type preview, linkedin type preview
Requires at least: 4.2
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to embed a preview of a link, similar to facebook

== Description ==
This plugin fetches an excerpt of an external website link. The excerpt contains the title, description and image.

V2.0.0 Added URL validation and XSS checks. If this breaks and valid URL's please let me know via the Wordpress support tab.

V1.5.2 Update: Added option to place the button in the "Media Button" row above the editor and changed the default for new install's to use PHP's HTML Dom instead of regex's.

v1.5.0 Update: Rewrote the source parser to use PHP's HTML Dom instead of regex's. The default mode is the original regex's, so you need to change the setting on the options page to enable this. The Dom mode always tries OpenGraph tags first, then fails back to regular meta tags, then to body content, i.e. img if no og:image, h1 if no og:title or title, 1st p if no og:description or meta description


The options page allows the following to be set:

- CSS to change the look and feel of the generated links.

- The article source label. (Default "Source:")

- Control of new window opening. Options are 'target="_blank"', 'target="newwindow"', 'rel="external"' and opening in the same window. (Default 'target="_blank"')

- Which TinyMCE button row the icon is added to (1,2 or 3) or the "Media Button" row above the editor.

- Select regex parse mode (the original and default) or PHP's HTML Dom mode

Added in 1.4.5: When adding a post, if the post title field is blank, the title of the retrieved page is inserted into the title field.

The defaults are designed to suit most people.

Added the cacert.pem which CURL uses for https sites from http://curl.haxx.se/ca/cacert.pem (the home of CURL).

ToDo: Longer term, configurable options for sites without an image and selecting from multiple OG images.

Thank you to Abhishek Saha for publishing the original URL Preview at https://wordpress.org/plugins/link-preview/ which this is based on and for the WP review team for their help in conforming to coding rules.

Note: Tested with "TinyMCE Advanced" and "Black Studio TinyMCE Widget" modules.

== Screenshots ==

1. Select the "add preview link" button (Tiny MCE Advanced).
2. Enter the URL in the pop-up.
3. Preview and edit, where needed, the contents.
4. Default presentation client-side.
5. Settings screen.
6. Button on Black Studio TinyMCE Widget 2.2.8.
7. Button with "Media Button" setting.

== Changelog ==

= 2.0.0 =
* Added URL filtering and XSS checks

= 1.6.2 =
* Updated SVN code structure

= 1.6.1 =
* Changed suppress header tag option to re-enable title text

= 1.6.0 =
* Added options to make main h2 header and/or image links

= 1.5.7 =
* Added option to suppress header tag

= 1.5.6 =
* Tested with WordPress 4.6 and TinyMCE Advanced Version 4.4.1

= 1.5.5 =
* Clean up spacing before insert

= 1.5.4 =
* Minor code clean up and tested with WordPress 4.5 and TinyMCE Advanced 4.3.8

= 1.5.3 =
* Added page/post type filter for the "Media Button" option.

= 1.5.2 =
* Added option to place the button in the "Media Button" row above the editor.
* Default to use PHP's HTML Dom instead of regex's for new install's.

= 1.5.1 =
* Updated processing for "//" and "://" links without http(s) references.
* Added CURL debugging.

= 1.5.0 =
* Rewrote the source parser to use PHP's HTML Dom instead of regex's. The default mode is the original, so you need to change the setting on the options page to enable this.

= 1.4.7 =
* Added option to select which TinyMCE button row the icon is added to (1,2 or 3).

= 1.4.6 =
* Adjust image path when site uses a relative link (without a leading slash) and the FQDN is entered without a trailing slash.

= 1.4.5 =
* When adding a post, if the post title field is blank, the title of the retrieved page is inserted into the title field.

= 1.4.4 =
* Adjusted title detection (due to issue with BBC News site)

= 1.4.3 =
* Added option to control how/if the link opens a new window

= 1.4.2 =
* Added source / link label option into settings

= 1.4.1 =
* Corrected css path

= 1.4 =
* First published version

= 1.3 =
* Fixes / Corrections to WP guidelines

= 1.2 =
* Fixes / Corrections

= 1.1 =
* Fixes / Corrections

= 1.0 =
* First version / Fork
