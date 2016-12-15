=== Plugin Name ===
Contributors: goldsounds
Tags: comments, spam
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to upload and render [glTF](https://github.com/KhronosGroup/glTF) models in WordPress.

== Description ==

glTF is an emerging open standard for the transmission and storage of 3D models. This plugin allows you to upload these models in "Embedded JSON" and Binary formats. Currently, inline rendering is only supported for the JSON format.

== Installation ==

1. Upload `gltf.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Upload .gltf or .glb files via the Media browser, or via the Add Media button for posts/pages.
1. Embed the 3D object in posts using a shortcode, like `[gltf_model scale="1.0" url="http://mywordpresssite.com/wp-content/uploads/2016/12/model.gltf"]

== Changelog ==

= 1.0 =
* First version, just basic rendering