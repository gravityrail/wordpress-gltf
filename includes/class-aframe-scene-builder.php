<?php

class AFrame_Scene_Builder {
	private $id; // HTML DOM id of scene element
	private $classes; // HTML DOM classes of scene element
	private $entities;

	public function __construct( $id, $classes = '', $is_embedded = true ) {
		$this->id = $id;
		$this->classes = $classes;
		$this->entities = array();
	}

	/**
	 * Returns the a-frame markup for the scene
	 */
	public function build() {
		$entities = implode( '', array_map( array( $this, 'build_entity' ), $this->entities ) );

		$scene = $this->build_element( 'a-scene', array(
			'id' => $this->id,
			'class' => $this->classes,
			'embedded' => true
		), $entities );

		return $scene;
	}

	public function add_gltf_model( $id, $url ) {
		$this->add_entity( array(
			'gltf-model' => $url
		) );
	}

	public function add_entity( $entity ) {
		$this->entities[] = $entity;
	}

	public function build_entity( $entity ) {
		return $this->build_element( 'a-entity', $entity );
	}

	/**
	 * Build an element from a DOM name, attributes and content
	 */
	private function build_element( $name, $attrs, $content = '' ) {
		return '<' .$name . ' ' . implode(' ', array_map(
			function ($k, $v) {
				if ( true === $v ) {
					return $k;
				}
				return $k .'="'. htmlspecialchars($v) .'"';
			},
			array_keys($attrs), $attrs
		)) .'>' . $content . '</' . $name . '>';
	}
}