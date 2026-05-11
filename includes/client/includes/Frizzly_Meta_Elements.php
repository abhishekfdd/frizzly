<?php

class Frizzly_Meta_Elements {

	private $elements;

	function __construct() {
		$this->elements = array();
	}

	function add_element( $property, $content ) {
		$this->elements[ $property ] = $content;
		return $this;
	}

	function get_html() {
		$res = '';
		foreach ( $this->elements as $property => $content ) {
			$res .= sprintf('<meta property="%s" content="%s" />', esc_attr( $property ), esc_attr( $content ));
		}
		return $res;
	}
}