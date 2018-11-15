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

	public function add_child( $el, $attrs = null ) {
		if ( is_null( $this->children ) ) {
			$this->children = array();
		}

		// allow something like `add_child( 'a-mountain', array( 'height' => 10 ) );`
		if ( is_string( $el ) ) {
			$this->children[] = new AFrame_Element( $el, $attrs );
		} else {
			$this->children[] = $el;
		}
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
				} elseif ( is_array( $v ) ) {
					$v = $this->to_css( $v );
				}
				return $k .'="'. htmlspecialchars($v) .'"';
			},
			array_keys($attrs), $attrs
		)) .'>' . $content . '</' . $name . '>';
	}

	// TODO: escape the value here
	protected function to_css( $rules ) {
		return implode( '; ', array_map( function( $value, $key ) { return "$key: $value"; }, $rules, array_keys( $rules ) ) );
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

class AFrame_Asset_Item extends AFrame_Element {
	public function __construct( $attrs, $children = null ) {
		parent::__construct( 'a-asset-item', $attrs, $children );
	}
}

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
	private $id;
	private $assets;
	private $is_embedded;

	public function __construct( $id, $attrs = array(), $is_embedded = true ) {
		$this->assets = array();
		$this->id = $id;
		$this->is_embedded = $is_embedded;

		parent::__construct( 'a-scene',
			array_merge(
				array(
					'id' => $id,
					'embedded' => $is_embedded
				),
				$attrs
			) );
	}

	/**
	 * Returns the a-frame markup for the scene
	 */
	public function build() {
		$this->add_child( new AFrame_Assets( $this->assets ) );
		$html = parent::build();

		if ( $this->is_embedded ) {
			$html = $this->get_embedded_style() . $html;
		}

		return $html;
	}

	// a-entity type=gltf-model
	public function add_gltf_model( $id, $url ) {
		return $this->add_entity( array(
			'id' => $id,
			'gltf-model' => $url
		) );
	}

	public function add_entity( $attrs ) {
		return $this->add_child( new AFrame_Entity( $attrs ) );
	}

	public function add_asset( $el ) {
		$this->assets[] = $el;
		return $this;
	}

	// a-assets > a-cubemap
	public function add_cubemap( $id, $images ) {
		if ( ! count( $images ) === 6 ) {
			throw new Exception( "bad-input", "Cubemap requires 6 images" );
		}

		$children = array_map( function( $img_url ) {
			return new AFrame_Img( array( 'src' => $img_url ) );
		}, $images );

		$this->add_asset( new AFrame_Asset( 'a-cubemap', array( 'id' => $id ), $children ) );

		return $this;
	}

	private function get_embedded_style() {
		return <<<ENDSTYLE
		<style>
			#{$this->id} {
				width: 100%;
				height: auto;
				min-height: 300px;
			}
		</style>
ENDSTYLE;
	}
}