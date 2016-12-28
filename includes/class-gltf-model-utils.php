<?php

class Gltf_Model_Utils {
	static function enqueue_common_gltf_scripts( $version ) {
		wp_enqueue_script( 'threejs', plugin_dir_url( dirname( __FILE__ ) ) . 'js/three.min.js', null, $version, false );
		wp_enqueue_script( 'gltf-loader', plugin_dir_url( dirname( __FILE__ ) ) . 'js/loaders/GLTFLoader.js', array( 'threejs' ), $version, false );
		wp_enqueue_script( 'orbitcontrols', plugin_dir_url( dirname( __FILE__ ) ) . 'js/OrbitControls.js', array( 'threejs' ), $version, false );
	}

	static function enqueue_scripts( $version ) {
		self::enqueue_common_gltf_scripts( $version );
		wp_enqueue_script( 'gltf-model-preview', plugin_dir_url( dirname( __FILE__ ) ) . 'js/gltf-model-preview.js', array( 'jquery', 'gltf-loader', 'orbitcontrols' ), $version, false );
	}
}