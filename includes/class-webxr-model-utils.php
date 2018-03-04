<?php

class WebXR_Model_Utils {
	// @see https://github.com/KhronosGroup/glTF/tree/master/specification/1.0#mimetypes
	// @see https://github.com/KhronosGroup/glTF/tree/master/extensions/Khronos/KHR_binary_glTF#mime-type
	static $gltf_mime_types = array(
		'glb'   => 'model/gltf.binary',
		'gltf'  => 'model/gltf+json',
		'bin'   => 'application/octet-stream',
		'glsl'  => 'text/plain',
		'vert'  => 'text/x-glsl',
		'vsh'   => 'text/x-glsl',
		'gsh'   => 'text/x-glsl',
		'frag'  => 'text/x-glsl',
		'glsl'  => 'text/x-glsl',
		'shader'=> 'text/x-glsl',
	);

	static function is_gltf_model_type( $mime_type ) {
		return in_array( $mime_type, array_values( self::$gltf_mime_types ) );
	}
}