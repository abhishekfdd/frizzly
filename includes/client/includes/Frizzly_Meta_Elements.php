<?php
/**
 * Frizzly Meta Elements.
 *
 * @package Frizzly
 */

/**
 * Frizzly Meta Elements.
 */
class Frizzly_Meta_Elements {

	/**
	 * Elements.
	 *
	 * @var mixed
	 */
	private $elements;

	/**
	 *
	 * Constructor.
	 */
	public function __construct() {
		$this->elements = array();
	}

	/**
	 *
	 * Add element.
	 *
	 * @param mixed $property Property.
	 * @param mixed $content Content.
	 */
	public function add_element( $property, $content ) {
		$this->elements[ $property ] = $content;
		return $this;
	}

	/**
	 *
	 * Get html.
	 */
	public function get_html() {
		$res = '';
		foreach ( $this->elements as $property => $content ) {
			$res .= sprintf( '<meta property="%s" content="%s" />', esc_attr( $property ), esc_attr( $content ) );
		}
		return $res;
	}
}
