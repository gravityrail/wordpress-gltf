=== Plugin Name ===
Contributors: goldsounds
Tags: comments, spam
Requires at least: 4.7
Tested up to: 4.7
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to upload and render glTF 3D models in WordPress.

== Description ==

[glTF](https://github.com/KhronosGroup/glTF) is an emerging open standard for the transmission and storage of 3D models. This plugin allows you to upload these models in "Embedded JSON" and Binary formats. Currently, inline rendering is only supported for the JSON format.

The ultimate goal of this plugin is to provide a 3D "post type" (gltf_scene) which allows the user to compose an interactive scene populated by objects that represent other content from their site (e.g. a book containing posts in a particular category, a door leading to another page or site). It is also a goal to make this content work best in VR headsets, but with a workable fallback for browsers.

This plugin includes [Three.js](https://github.com/mrdoob/three.js/), a wonderful creation of [Mr Doob](http://mrdoob.com/) and many others. I am massively impressed by their work. I am also not so great at PHP or Javascript, so please forgive my missteps.

== Installation ==

1. Unzip this plugin in the `/wp-content/plugins/` directory
1. Activate the "glTF Media Type" plugin through the 'Plugins' menu in WordPress
1. Upload .gltf or .glb files via the Media browser, or via the Add Media button for posts/pages.
1. Embed the 3D object in posts using a shortcode, like `[gltf_model scale="1.0" url="http://mywordpresssite.com/wp-content/uploads/2016/12/model.gltf"]. Note: You may get that full URL from the Media browser by selecting the file and copying the link.
1. You may also create a "GLTF Scene" using the "Scene" post type, and attaching a 3D model on the post edit page.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

None yet.

== Changelog ==

= 1.3 = 
* Added WebVR support

= 1.2 = 
* Fetch gltf_scene data as post via REST API

= 1.1 =
* Add gltf_scene post type

= 1.0 =
* First version, just basic rendering