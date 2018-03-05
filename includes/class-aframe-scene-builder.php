<?php

class AFrame_Element {
	public $type;
	public $attrs;
	public $children;

	public function __construct( $type, $attrs, $children = null ) {
		$this->type = $type;
		$this->attrs = $attrs;
		$this->children = $children;
	}

	public function build() {
		return $this->build_element( $this->type, $this->attrs, $this->children );
	}

	public function add_attributes( $attrs ) {
		$this->attrs = array_merge( $this->attrs, $attrs );
	}

	public function add_child( $el ) {
		if ( is_null( $this->children ) ) {
			$this->children = array();
		}

		$this->children[] = $el;
	}

	/**
	 * Utility function - build an element from a DOM name, attributes and content
	 */
	protected function build_element( $name, $attrs, $content = null ) {
		if ( is_array( $content ) ) {
			// if the child has element children, build them and combine the output
			$content = implode( '', array_map( function( $child ) { return is_a( $child, 'AFrame_Element' ) ? $child->build() : $child; }, $content ) );
		}

		return '<' .$name . ' ' . implode(' ', array_map(
			function ($k, $v) {
				if ( null === $v ) {
					return '';
				} elseif ( true === $v ) {
					return $k;
				}
				return $k .'="'. htmlspecialchars($v) .'"';
			},
			array_keys($attrs), $attrs
		)) .'>' . $content . '</' . $name . '>';
	}
}

/**
 * Cross-origin aware image tag
 */
class AFrame_Img extends AFrame_Element {
	public function __construct( $attrs ) {
		// detect cross-origin requests with crossorigin="anonymous"
		if ( ! $this->is_same_origin( $attrs['src'], $_SERVER['REQUEST_URI'] ) ) {
			$attrs['crossorigin'] = "anonymous";
		}
		parent::__construct( 'img', $attrs );
	}

	private function is_same_origin( $url1, $url2 ) {
		return $this->get_origin( $url1 ) === $this->get_origin( $url2 );
	}

	// this might need port and scheme too, not sure
	private function get_origin( $url ) {
		return parse_url( $url, PHP_URL_HOST );
	}
}

class AFrame_Asset extends AFrame_Element { }

class AFrame_Assets extends AFrame_Element {
	public function __construct( $children ) {
		parent::__construct( 'a-assets', array(), $children );
	}
}

class AFrame_Entity extends AFrame_Element {
	public function __construct( $attrs, $children = null ) {
		parent::__construct( 'a-entity', $attrs, $children );
	}
}

class AFrame_Scene_Builder extends AFrame_Element {
	private $assets;

	public function __construct( $id, $classes = '', $is_embedded = true ) {
		$this->assets = array();
		parent::__construct( 'a-scene', array(
			'id' => $id,
			'class' => $classes,
			'embedded' => $is_embedded
		) );
	}

	/**
	 * Returns the a-frame markup for the scene
	 */
	public function build() {
		$this->add_child( new AFrame_Assets( $this->assets ) );
		return parent::build();
	}

	// a-entity type=gltf-model
	public function add_gltf_model( $id, $url ) {
		return $this->add_child( new AFrame_Entity( array(
			'id' => $id,
			'gltf-model' => $url
		) ) );
	}

	// a-assets > a-cubemap
	public function add_cubemap( $id, $images ) {
		if ( ! count( $images ) === 6 ) {
			throw new Exception( "bad-input", "Cubemap requires 6 images" );
		}

		$children = array_map( function( $img_url ) {
			return new AFrame_Img( array( 'src' => $img_url ) );
		}, $images );

		$this->assets[] = new AFrame_Asset( 'a-cubemap', array( 'id' => $id ), $children );

		return $this;
	}
}